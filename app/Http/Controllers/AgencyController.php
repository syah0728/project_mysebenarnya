<?php

namespace App\Http\Controllers;

use App\Models\Inquiry;
use App\Models\Agency;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Assignment;
use App\Models\User;
use App\Models\Progress;
use App\Mail\InquiryRejectedMail;
use App\Mail\InquiryProgressMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;


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

public function updateProfile(Request $request, $user_id)
{
    $user = User::findOrFail($user_id);

    $request->validate([
        'name' => 'required|string|max:255',
        'phone' => 'nullable|string|max:20',
        'profile_picture' => 'nullable|image|max:2048',
    ]);

    $user->name = $request->name;

    // Update profile picture
    if ($request->hasFile('profile_picture')) {
        if ($user->profile_picture && Storage::exists($user->profile_picture)) {
            Storage::delete($user->profile_picture);
        }

        $user->profile_picture = $request->file('profile_picture')->store('profile_pictures', 'public');
    }

    $user->save();

    // Update phone if available in agency relation
    if ($user->agency) {
        $user->agency->phone = $request->phone;
        $user->agency->save();
    }

    return redirect()->back()->with('success', 'Profile updated successfully!');
}

public function showProfile()
{
    $user = auth()->user();
    return view('Agency.profile', compact('user'));
}

public function changePassword(Request $request, $user_id)
{
    $request->validate([
        'current_password' => 'required',
        'new_password' => 'required|string|min:8|confirmed',
    ]);

    $user = auth()->user();

    if (!Hash::check($request->current_password, $user->password)) {
        return back()->withErrors(['current_password' => 'Current password is incorrect.']);
    }

    $user->password = Hash::make($request->new_password);
    $user->save();

    return back()->with('success', 'Password updated successfully.');
}

public function VerifyInquiry($user_id, $inquiry_id)
{
    if (!auth()->user()->isAgency() || auth()->id() != $user_id) {
        abort(403, 'Unauthorized access.');
    }

    $inquiry = Inquiry::findOrFail($inquiry_id);
    return view('Agency.VerifyInquiry', compact('inquiry'));
}

public function submitVerification(Request $request, $user_id, $inquiry_id)
{
    if (!auth()->user()->isAgency() || auth()->id() != $user_id) {
        abort(403, 'Unauthorized action.');
    }

    $request->validate([
        'verification_status' => 'required|string|max:255',
        'reviewing_officer' => 'required|string|max:255',
        'description' => 'nullable|string',
        'supporting_document' => 'nullable|file|mimes:pdf,jpg,jpeg,png,gif|max:5120', // 5MB max
    ]);

    $inquiry = Inquiry::findOrFail($inquiry_id);

    // Store file if uploaded
    $documentPath = null;
    if ($request->hasFile('supporting_document')) {
        $documentPath = $request->file('supporting_document')->store('supporting_documents', 'public');
    }

    // Update inquiry
    $inquiry->update([
        'verification_status' => $request->verification_status,
        'InquiryStatus' => match ($request->verification_status) {
            'Under Investigation' => 'In Progress',
            'Verified as True' => 'Resolved',
            'Identified as Fake' => 'Resolved',
            default => 'Rejected',
        },
    ]);

    // Log into progress
    $progressUpdate = $inquiry->progressUpdates()->create([
        'UpdateDate' => now()->toDateString(),
        'ProgressStatus' => $request->verification_status,
        'ProgressDescription' => $request->description,
        'ReviewingOfficer' => $request->reviewing_officer,
        'SupportingDocument' => $documentPath,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    // Send email to public user
    $publicUserEmail = $inquiry->publicUser->user->email ?? null;
    if ($publicUserEmail) {
        Mail::to($publicUserEmail)->send(new InquiryProgressMail($progressUpdate)); 
    }


    return redirect()->route('Agency.InquiryList', $user_id)
                     ->with('success', 'Verification submitted successfully.');
}


public function inquiryHistoryList($user_id)
{
    $this->authorizeUser($user_id);

    $agency = Agency::where('user_id', $user_id)->firstOrFail();

    $inquiries = Inquiry::with(['publicUser.user', 'agency.user'])
        ->where('Agency_id', $agency->id)
        ->orderBy('created_at', 'desc')
        ->get();

    return view('Agency.InquiryHistory', compact('inquiries'));
}

public function viewInquiryHistory($user_id, $inquiry_id)
{
    $this->authorizeUser($user_id);

    $inquiry = Inquiry::with(['progressUpdates', 'publicUser.user', 'agency.user'])
                ->findOrFail($inquiry_id);

    return view('Agency.InquiryDetailHistory', compact('inquiry'));
}


}