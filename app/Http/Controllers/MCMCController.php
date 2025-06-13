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

class MCMCController extends Controller
{
    public function dashboard($user_id)
    {
        $this->authorizeUser($user_id);
        
        $stats = Inquiry::getStats();
        $recent = Inquiry::with(['agency', 'user'])
            ->latest()
            ->take(10)
            ->get();

        return view('MCMC.dashboard', array_merge($stats, ['recent' => $recent]));
        
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
            'password' => 'required|string|min:6',
            'username' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            
        ]);

        // ✅ Call the model method instead of doing direct DB logic here
        Agency::createWithUser($validated);

        return redirect()
            ->route('MCMC.UserData', ['user_id' => $user_id])
            ->with('success', 'Agency user registered successfully.');
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

    // ✅ Format reportData as array of ['agency' => ..., 'total' => ...]
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

    // return $pdf->download('user_report.pdf');
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
}