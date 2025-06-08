<?php

namespace App\Http\Controllers;

use App\Models\Inquiry;
use App\Models\Agency;
use App\Models\Assignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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

    public function InquiryList()
    {
        $inquiries = Inquiry::with('publicUser')
                        ->where('InquiryStatus', 'Pending')
                        ->orderBy('created_at', 'desc')
                        ->get();
        
        $agencies = Agency::all();
        $user = auth()->user(); // Get the authenticated user
        
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
}