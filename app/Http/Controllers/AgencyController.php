<?php
namespace App\Http\Controllers;

use App\Models\Inquiry;
use Illuminate\Http\Request;

class AgencyController extends Controller
{
    public function dashboard($user_id)
    {
        $user = auth()->user();
        if (!$user->isAgency() || $user->id != $user_id) {
            abort(403, 'Unauthorized access.');
        }

        // Get counts for stats
        $total = Inquiry::where('Agency_id', $user->id)->count();
        $pending = Inquiry::where('Agency_id', $user->id)
            ->where('InquiryStatus', 'Pending')
            ->count();
        $inProgress = Inquiry::where('Agency_id', $user->id)
            ->where('InquiryStatus', 'In Progress')
            ->count();
        $resolved = Inquiry::where('Agency_id', $user->id)
            ->where('InquiryStatus', 'Resolved')
            ->count();

        // Get all assigned inquiries for this agency
        $assignedInquiries = Inquiry::where('Agency_id', $user->id)
            ->with(['user'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('Agency.dashboard', compact('total', 'pending', 'inProgress', 'resolved', 'assignedInquiries'));
    }

    public function viewInquiry($user_id, $inquiry_id)
    {
        $user = auth()->user();
        if (!$user->isAgency() || $user->id != $user_id) {
            abort(403, 'Unauthorized access.');
        }

        $inquiry = Inquiry::where('Agency_id', $user->id)
            ->where('id', $inquiry_id)
            ->with(['user'])
            ->firstOrFail();

        return view('Agency.viewInquiry', compact('inquiry'));
    }

    
}