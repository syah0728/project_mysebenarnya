<?php


namespace App\Http\Controllers;

use App\Models\Inquiry;
use App\Models\Agency;
use Illuminate\Http\Request;

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

    public function inquiryList($user_id)
    {
        $this->authorizeUser($user_id);

        $inquiries = Inquiry::with(['user', 'agency'])
            ->unassigned()
            ->latest()
            ->get();
        $agencies = Agency::all();

        return view('MCMC.InquiryList', compact('inquiries', 'agencies'));
    }

    public function assignInquiry(Request $request, $user_id)
    {
        $this->authorizeUser($user_id);

        // Validate request
        $request->validate([
            'inquiry_id' => 'required|exists:inquiry,id',
            'agency_id' => 'required|exists:agency,id',
        ]);

        try {
            $inquiry = Inquiry::findOrFail($request->inquiry_id);
            $agency = Agency::findOrFail($request->agency_id);
            
            // Assign inquiry to agency
            $inquiry->assignTo($agency);

            return redirect()
                ->back()
                ->with('success', 'Inquiry assigned successfully.');
        } catch (\Exception $e) {
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

    private function authorizeUser($user_id)
    {
        if (!auth()->user()->isMCMC() || auth()->id() != $user_id) {
            abort(403, 'Unauthorized access.');
        }
    }
}