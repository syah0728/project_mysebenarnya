<?php

namespace App\Http\Controllers;

use App\Models\Inquiry;
use App\Models\Agency;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Assignment;
use App\Models\User;
use App\Mail\InquiryRejectedMail;
use Illuminate\Support\Facades\Mail;



class AgencyController extends Controller
{
    public function dashboard()
    {
        
        // Get the current logged in agency
        $agency = Agency::where('user_id', Auth::id())->first();
        
        // Get inquiries assigned to this agency
        $assignedInquiries = Inquiry::where('Agency_id', $agency->id)
            ->with(['publicUser']) // Eager load relationships
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Get counts for different statuses
        $total = Inquiry::where('Agency_id', $agency->id)->count();
        $pending = Inquiry::where('Agency_id', $agency->id)
            ->where('InquiryStatus', 'Assigned')
            ->count();
        $inProgress = Inquiry::where('Agency_id', $agency->id)
            ->where('InquiryStatus', 'In Progress')
            ->count();
        $resolved = Inquiry::where('Agency_id', $agency->id)
            ->where('InquiryStatus', 'Resolved')
            ->count();

        return view('Agency.dashboard', compact(
            'agency',
            'assignedInquiries',
            'total',
            'pending',
            'inProgress',
            'resolved'
        ));
    }

    public function inquiryList()
    {
        // Get the logged-in agency
        $agency = Agency::where('user_id', Auth::id())->first();

        if (!$agency) {
            abort(403, 'Unauthorized or agency not found.');
        }

        // Fetch all inquiries assigned to this agency
        $inquiries = Inquiry::with('publicUser')
            ->where('Agency_id', $agency->id)
            ->whereNotIn('InquiryStatus', ['Rejected', 'Discarded'])
            ->orderBy('created_at', 'desc')
            ->get();

        $user = Auth::user(); // Get the authenticated user

        return view('Agency.InquiryList', compact('inquiries', 'user'));
    }

    private function authorizeUser($user_id)
    {
        if (!auth()->user()->isAgency() || auth()->id() != $user_id) {
            abort(403, 'Unauthorized action.');
        }
    }

    public function inquiryReview($user_id, $inquiry_id)
    {
        $this->authorizeUser($user_id); // ✅ No more error

        $inquiry = Inquiry::with('publicUser')->findOrFail($inquiry_id);

        return view('Agency.InquiryReview', compact('inquiry'));
    }

    public function rejectInquiry(Request $request, $user_id, $inquiry_id)
{
    $this->authorizeUser($user_id);

    // Validate input
    $validated = $request->validate([
        'status' => 'required|in:Flagged,Rejected,Discarded',
        'reason' => 'required|string|max:500',
    ]);

    // Find assignment record
    $agency = auth()->user()->Agency; // capital A if your model is Agency
    if (!$agency) {
        return back()->with('error', 'Agency not found.');
    }

    $assignment = Assignment::where('Inquiry_id', $inquiry_id)
        ->where('Agency_id', $agency->id)
        ->first();

    if (!$assignment) {
        return back()->with('error', 'Assignment not found.');
    }

    // Update assignment and inquiry
    $assignment->AssignmentStatus = 'Rejected';
    $assignment->rejection_reason = $validated['reason'];
    $assignment->save();

    $inquiry = Inquiry::findOrFail($inquiry_id);
    $inquiry->InquiryStatus = $validated['status'];
    $inquiry->Agency_id = null;
    $inquiry->save();

    // Notify MCMC by email
    $mcmcUsers = User::where('role', 'MCMC')->get();
    foreach ($mcmcUsers as $mcmc) {
        Mail::to($mcmc->email)->send(new InquiryRejectedMail($inquiry, $validated['reason']));
    }

    return redirect()
        ->route('Agency.InquiryList', ['user_id' => $user_id])
        ->with('success', 'Inquiry rejected successfully.');
}


}