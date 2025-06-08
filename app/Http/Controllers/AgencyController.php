<?php

namespace App\Http\Controllers;

use App\Models\Inquiry;
use App\Models\Agency;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;


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
}