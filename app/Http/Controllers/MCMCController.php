<?php

namespace App\Http\Controllers;

use App\Models\Inquiry;
use App\Models\Agency;
use App\Models\Assignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\InquiryAssignExport;
use App\Exports\InquiryExport;
use App\Exports\UsersExport;
use App\Exports\AgencyPerfExport;
use Illuminate\Support\Str;
use App\Mail\SendTempPasswordMail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class MCMCController extends Controller
{
    public function dashboard($user_id)
{
    // Ensure user is authorized and MCMC
    if (!auth()->user()->isMCMC() || auth()->id() != $user_id) {
        abort(403, 'Unauthorized action.');
    }

    // Inquiry statistics
    $total = Inquiry::count();
    $assigned = Inquiry::where('InquiryStatus', 'Assigned')->count();
    $inProgress = Inquiry::where('InquiryStatus', 'In Progress')->count();
    $resolved = Inquiry::where('InquiryStatus', 'Resolved')->count();

    // Load recent 10 inquiries (including agency and public user info)
    $recent = Inquiry::with(['agency.user', 'publicUser.user'])
        ->latest()
        ->take(10)
        ->get();

    // Return to dashboard view
    return view('MCMC.dashboard', compact('total', 'assigned', 'inProgress', 'resolved', 'recent'));
}


    public function InquiryList($user_id)
    {
        $this->authorizeUser($user_id);

        $inquiries = Inquiry::with(['publicUser', 'user'])
            ->where(function ($query) {
                $query->where('InquiryStatus', 'Pending')
                    ->orWhere('InquiryStatus', 'Rejected') // ✅ Include rejected
                    ->orWhere('InquiryStatus', 'Reviewed');
            })
            ->whereNull('Agency_id') // ✅ Ensure it's not currently assigned
            ->orderBy('created_at', 'desc')
            ->get();

        $agencies = Agency::with('user')->get();
        $user = auth()->user();

        return view('MCMC.InquiryList', compact('inquiries', 'agencies', 'user'));
    }


    public function assignInquiry(Request $request, $user_id)
    {
        try {
            $this->authorizeUser($user_id);

            // Validate request
            $validated = $request->validate([
                'inquiry_id' => 'required|exists:inquiry,id',
                'agency_id' => 'required|exists:agency,id',
                'due_date' => 'required|date|after:today',
                'comments' => 'required|string|max:500'
            ]);

            // Find the inquiry and agency
            $inquiry = Inquiry::findOrFail($request->inquiry_id);
            $agency = Agency::findOrFail($request->agency_id);
            
            // Use the model method to handle assignment
            $inquiry->assignTo($agency, $request->due_date, $request->comments);

            return redirect()
                ->back()
                ->with('success', 'Inquiry assigned successfully.');

        } catch (\Exception $e) {
            \Log::error('Assignment Error: ', ['exception' => $e]);
            return redirect()
                ->back()
                ->with('error', 'Failed to assign inquiry: ' . $e->getMessage());
        }
    }

    public function assignedInquiry($user_id)
    {
        $user = auth()->user();
        if (!$user->isMCMC() || $user->id != $user_id) {
            abort(403, 'Unauthorized access.');
        }

        $assignedInquiries = Inquiry::with('agency')
            ->whereNotNull('Agency_id')
            ->orderBy('updated_at', 'desc')
            ->get();

        return view('MCMC.AssignedInquiry', compact('assignedInquiries'));
    }

    public function inquiryReview($user_id, $inquiry_id)
    {
        $this->authorizeUser($user_id);

        // Retrieve the inquiry with the public user
        $inquiry = Inquiry::with('publicUser')->findOrFail($inquiry_id);

        return view('MCMC.InquiryReview', compact('inquiry'));
    }


    private function authorizeUser($user_id)
    {
        if (!auth()->user()->isMCMC() || auth()->id() != $user_id) {
            abort(403, 'Unauthorized access.');
        }
    }

    public function rejectInquiry(Request $request, $user_id, $inquiry_id)
    {
        $this->authorizeUser($user_id);

        $validated = $request->validate([
            'status' => 'required|in:Reviewed,Discarded',
        ]);

        $inquiry = Inquiry::findOrFail($inquiry_id);
        $inquiry->InquiryStatus = $validated['status'];
        $inquiry->save();

        return redirect()->route('MCMC.InquiryList', ['user_id' => $user_id])
                        ->with('success', 'Inquiry reviewed successfully.');
    }

    public function UserData($user_id) {
        $this->authorizeUser($user_id);
        $users = User::all();
        return view('MCMC.UserData', compact('users'));
    }

    public function RegisterUser($user_id)
    {
        $this->authorizeUser($user_id);
        return view('MCMC.RegisterUser'); // make sure this Blade file exists
    }
    // public function ViewUserActivity($user_id, $target_user_id)
    // {
    //     $this->authorizeUser($user_id);
    //     $user = User::findOrFail($target_user_id);
    //     // Fetch activity logs or dummy data
    //     return view('MCMC.UserActivity', compact('user'));
    // }
    public function ViewUserActivity($user_id, $target_user_id)
    {
        $this->authorizeUser($user_id);

        $user = User::with(['PublicUser', 'MCMC', 'Agency'])->findOrFail($target_user_id);

        // Activity logs (example: inquiries if user is PublicUser, assignments if Agency)
        $inquiries = $user->isPublicUser() ? $user->PublicUser->inquiries ?? [] : [];
        $assignments = $user->isAgency() ? $user->Agency->assignments ?? [] : [];

        return view('MCMC.UserActivity', compact('user', 'inquiries', 'assignments'));
    }

    public function RegisterUserPost(Request $request, $user_id)
{
    $this->authorizeUser($user_id);

    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|unique:users,email',
        'username' => 'required|string|max:255|unique:agency,username',
        'phone' => 'required|string|max:20',
    ]);

    // ✅ Generate a temporary password
    $tempPassword = Str::random(10);

    // ✅ Create user
    $user = User::create([
        'name' => $validated['name'],
        'email' => $validated['email'],
        
        'password' => Hash::make($tempPassword),
        'role' => 'Agency',
    ]);

    // ✅ Create agency profile
    Agency::create([
        'user_id' => $user->id,
        'username' => $validated['username'],
        'phone' => $validated['phone'],
    ]);

    // ✅ Send email with temporary password
    Mail::to($user->email)->send(new SendTempPasswordMail($user, $tempPassword));

    return redirect()
        ->route('MCMC.UserData', ['user_id' => $user_id])
        ->with('success', 'Agency user registered and email sent successfully.');
}

    public function filteredInquiries(Request $request, $user_id)
    {
        $this->authorizeUser($user_id);

        $query = Inquiry::with(['publicUser', 'agency'])
            ->whereIn('InquiryStatus', ['Reviewed', 'Discarded']);

        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        if ($request->filled('status')) {
            $query->where('InquiryStatus', $request->status);
        }

        if ($request->filled('agency_id')) {
            $query->where('Agency_id', $request->agency_id);
        }

        $filteredInquiries = $query->orderBy('created_at', 'desc')->get();
        $agencies = \App\Models\Agency::all();

        return view('MCMC.FilteredInquiries', compact('filteredInquiries', 'agencies'));
    }


    
public function InquiryAssignReport(Request $request, $user_id)
{
    $this->authorizeUser($user_id);

    $startDate = $request->input('from_date');
    $endDate = $request->input('to_date');
    $agencyId = $request->input('agency_id');

    $query = Inquiry::with('agency')
        ->whereNotNull('Agency_id');

    if ($startDate) {
        $query->whereDate('created_at', '>=', $startDate);
    }

    if ($endDate) {
        $query->whereDate('created_at', '<=', $endDate);
    }

    if ($agencyId) {
        $query->where('Agency_id', $agencyId);
    }

    $inquiries = $query->get();

    
    $reportData = $inquiries->groupBy('agency.user.name')->map(function ($group, $agencyName) {
        return [
            'agency' => $agencyName,
            'total' => $group->count(),
        ];
    })->values()->all(); // Convert to plain array for Blade compatibility

    $agencies = \App\Models\Agency::all();

    return view('MCMC.InquiryAssignReport', [
        'reportData' => $reportData,
        'agencies' => $agencies,
        'startDate' => $startDate,
        'endDate' => $endDate,
        'agencyId' => $agencyId,
    ]);
}

public function DownloadInquiryAssignReportPDF(Request $request, $user_id)
{
    $this->authorizeUser($user_id);

    $startDate = $request->start_date;
    $endDate = $request->end_date;
    $agencyId = $request->agency_id;

    $query = Inquiry::with('agency')->whereNotNull('Agency_id');

    if ($startDate) {
        $query->whereDate('created_at', '>=', $startDate);
    }
    if ($endDate) {
        $query->whereDate('created_at', '<=', $endDate);
    }
    if ($agencyId) {
        $query->where('Agency_id', $agencyId);
    }

    $inquiries = $query->get();

    $reportData = $inquiries->groupBy('agency.user.name')->map(function ($group, $agencyName) {
        return [
            'agency' => $agencyName,
            'total' => $group->count(),
        ];
    })->values()->all();

    $pdf = Pdf::loadView('MCMC.PDF.InquiryAssignReport', compact('reportData', 'startDate', 'endDate'));

    return $pdf->download('Inquiry_Assign_Report.pdf');
}

public function DownloadInquiryAssignReportExcel(Request $request, $user_id)
{
    $this->authorizeUser($user_id);

    $from = $request->start_date;
    $to = $request->end_date;
    $agencyId = $request->agency_id;

    $query = Inquiry::with('agency.user')->whereNotNull('Agency_id');

    if ($from) {
        $query->whereDate('created_at', '>=', $from);
    }
    if ($to) {
        $query->whereDate('created_at', '<=', $to);
    }
    if ($agencyId) {
        $query->where('Agency_id', $agencyId);
    }

    $inquiries = $query->get();

    $reportData = $inquiries->groupBy('agency.user.name')->map(function ($group, $agencyName) {
        return [
            'agency' => $agencyName,
            'total' => $group->count(),
        ];
    })->values()->all();

    return Excel::download(new InquiryAssignExport(collect($reportData)), 'inquiry_assign_report.xlsx');

}


public function inquiryReport(Request $request, $user_id)
{
    $this->authorizeUser($user_id);

    $month = $request->input('month');
    $year = $request->input('year');

    // Ensure month is an integer
    $month = is_numeric($month) ? (int) $month : null;

    $query = Inquiry::query();

    if ($month && $year) {
        $query->whereMonth('created_at', $month)
              ->whereYear('created_at', $year);
    } elseif ($year) {
        $query->whereYear('created_at', $year);
    }

    $inquiries = $query->get();

    $monthlyCounts = Inquiry::selectRaw('MONTH(created_at) as month, COUNT(*) as total')
        ->whereYear('created_at', $year ?? now()->year)
        ->groupBy('month')
        ->pluck('total', 'month');

    return view('MCMC.InquiryReport', compact('inquiries', 'monthlyCounts', 'month', 'year'));
}


public function DownloadInquiryReportPDF(Request $request, $user_id)
{
    $this->authorizeUser($user_id);

    $month = $request->month;
    $year = $request->year;

    // ✅ Ensure $month is numeric or null
    $month = is_numeric($month) ? (int) $month : null;

    $query = Inquiry::with('publicUser');

    if ($month && $year) {
        $query->whereMonth('created_at', $month)
              ->whereYear('created_at', $year);
    } elseif ($year) {
        $query->whereYear('created_at', $year);
    }

    $inquiries = $query->get();

    $pdf = \PDF::loadView('MCMC.PDF.InquiryReport', [
        'inquiries' => $inquiries,
        'month' => $month,
        'year' => $year
    ]);

    return $pdf->download('Inquiry_Report.pdf');
}


public function DownloadInquiryReportExcel(Request $request, $user_id)
{
    $this->authorizeUser($user_id);

    $month = $request->input('month');
    $year = $request->input('year');

    // ✅ Sanitize month input
    $month = is_numeric($month) ? (int) $month : null;

    return Excel::download(new InquiryExport($month, $year), 'Inquiry_Report.xlsx');
}

public function generateUserReport(Request $request)
{
    $agencies = Agency::with('user')->get();

    $query = User::query();

    // Filters
    if ($request->filled('start_date')) {
        $query->whereDate('created_at', '>=', $request->start_date);
    }

    if ($request->filled('end_date')) {
        $query->whereDate('created_at', '<=', $request->end_date);
    }

    if ($request->filled('role')) {
        $query->where('role', $request->role);
    }

    if ($request->filled('agency_id')) {
        $agencyUserIDs = Agency::where('id', $request->agency_id)
                               ->with('user')
                               ->get()
                               ->pluck('user.id');
        $query->whereIn('id', $agencyUserIDs);
    }

    $users = $query->get();

    return view('MCMC.UserReport', compact('users', 'agencies'));
}

// Export filtered users as Excel
public function DownloadUserReportExcel(Request $request, $user_id)
{
    $this->authorizeUser($user_id);

    $startDate = $request->input('start_date');
    $endDate   = $request->input('end_date');
    $role      = $request->input('role');
    $agencyId  = $request->input('agency_id');

    return Excel::download(new UsersExport($startDate, $endDate, $role, $agencyId), 'User_Report.xlsx');
}



// Export filtered users as PDF
public function DownloadUserReportPDF(Request $request, $user_id)
{
    $users = $this->getFilteredUsers($request);
    $agencies = Agency::with('user')->get();
    $pdf = Pdf::loadView('MCMC.PDF.UserReport', compact('users', 'agencies'));

    return $pdf->download('user_report.pdf');
}
// Helper function to filter users
private function getFilteredUsers(Request $request)
{
    $query = User::query();

    if ($request->filled('start_date')) {
        $query->whereDate('created_at', '>=', $request->start_date);
    }

    if ($request->filled('end_date')) {
        $query->whereDate('created_at', '<=', $request->end_date);
    }

    if ($request->filled('role')) {
        $query->where('role', $request->role);
    }

    if ($request->filled('agency_id')) {
        $agency = Agency::find($request->agency_id);
        if ($agency && $agency->user) {
            $query->where('id', $agency->user->id);
        }
    }

    return $query->get();
}

public function inquiryProgress($user_id)
{
    if (!auth()->user()->isMCMC() || auth()->id() != $user_id) {
        abort(403, 'Unauthorized access.');
    }

    $inquiries = Inquiry::with([
        'publicUser',
        'agency.user',
        'progressUpdates' => function ($query) {
            $query->orderBy('created_at', 'asc');
        }
    ])
    ->whereNotNull('Agency_id')
    ->orderBy('updated_at', 'desc')
    ->get();

    return view('MCMC.InquiryProgress', compact('inquiries'));
}

public function agencyPerformanceReport(Request $request, $user_id)
    {
        if (!auth()->user()->isMCMC() || auth()->id() != $user_id) abort(403);

        $reportData = $this->getFilteredPerformance($request);
        return view('MCMC.AgencyPerfReport', compact('reportData'));
    }

    public function DownloadPerfReportPDF(Request $request, $user_id)
    {
        if (!auth()->user()->isMCMC() || auth()->id() != $user_id) abort(403);

        $reportData = $this->getFilteredPerformance($request);
        $pdf = Pdf::loadView('MCMC.PDF.AgencyPerfReport', compact('reportData'));
        return $pdf->download('Agency_Performance_Report.pdf');
    }

    public function DownloadPerfReportExcel(Request $request, $user_id)
    {
        if (!auth()->user()->isMCMC() || auth()->id() != $user_id) abort(403);

        return Excel::download(new AgencyPerfExport($request), 'Agency_Performance_Report.xlsx');
    }

    private function getFilteredPerformance(Request $request)
    {
        $start = $request->input('start_date');
        $end = $request->input('end_date');
        $agencyFilter = $request->input('agency_id');
        $category = $request->input('category');

        $agencies = Agency::with(['user', 'inquiries.progressUpdates'])->get();

        return $agencies->map(function ($agency) use ($start, $end, $agencyFilter, $category) {
            if ($agencyFilter && $agency->id != $agencyFilter) return null;

            $inquiries = $agency->inquiries->filter(function ($inq) use ($start, $end, $category) {
                $valid = true;

                if ($start) $valid = $valid && $inq->created_at >= $start;
                if ($end) $valid = $valid && $inq->created_at <= $end;
                if ($category) $valid = $valid && $inq->category === $category; // optional field

                return $valid;
            });

            $resolved = $inquiries->filter(fn($inq) =>
                $inq->progressUpdates->contains(fn($p) => in_array($p->ProgressStatus, ['Verified as True', 'Identified as Fake']))
            );

            $pending = $inquiries->reject(fn($inq) =>
                $inq->progressUpdates->contains(fn($p) => in_array($p->ProgressStatus, ['Verified as True', 'Identified as Fake']))
            );

            $avgResolveTime = $resolved->map(function ($inq) {
                $start = $inq->created_at;
                $end = $inq->progressUpdates
                    ->whereIn('ProgressStatus', ['Verified as True', 'Identified as Fake'])
                    ->sortByDesc('created_at')
                    ->first();
                return $end ? $start->diffInHours($end->created_at) : null;
            })->filter()->avg();

            $delayed = $resolved->filter(function ($inq) {
                $start = $inq->created_at;
                $end = $inq->progressUpdates
                    ->whereIn('ProgressStatus', ['Verified as True', 'Identified as Fake'])
                    ->sortByDesc('created_at')
                    ->first();
                return $end && $start->diffInDays($end->created_at) > 3;
            });

            return [
                'agency' => $agency->user->name ?? 'Unknown',
                'assigned' => $inquiries->count(),
                'resolved' => $resolved->count(),
                'pending' => $pending->count(),
                'delayed' => $delayed->count(),
                'average_hours' => round($avgResolveTime ?? 0, 2),
            ];
        })->filter();
    }

     //SEM module 3
    public function bulkAssignInquiry(Request $request, $user_id)
    {
        $user = auth()->user();
        if (!$user->isMCMC() || $user->id != $user_id) {
            abort(403, 'Unauthorized access.');
        }
    
        $request->validate([
            'inquiry_ids'   => 'required|array|min:1',
            'inquiry_ids.*' => 'exists:inquiry,id',
            'agency_id'     => 'required|exists:agency,id',
            'due_date'      => 'required|date|after_or_equal:today',
            'comments'      => 'required|string',
        ]);
    
        foreach ($request->inquiry_ids as $inquiryId) {
            $inquiry = Inquiry::findOrFail($inquiryId);
    
            // masuk data ke table assignment
            \App\Models\Assignment::create([
                'Inquiry_id'       => $inquiryId,
                'Agency_id'        => $request->agency_id,
                'PublicUser_id'    => $inquiry->PublicUser_id, 
                'AssignmentDate'   => now()->toDateString(),
                'due_date'         => $request->due_date,
                'comments'         => $request->comments,
                'AssignmentStatus' => 'Assigned',
            ]);
    
            $inquiry->update(['InquiryStatus' => 'Assigned', 'Agency_id' => $request->agency_id]);
        }
    
        return redirect()->back()->with('success', count($request->inquiry_ids) . ' inquiries assigned successfully.');
    }
}