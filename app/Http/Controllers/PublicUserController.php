<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\PublicUser;
use App\Models\Inquiry;
use App\Models\User;
use App\Models\PublicUserInquiry;
use Illuminate\Support\Facades\Hash;


class PublicUserController extends Controller
{

    public function showProfile($user_id)
    {
        $this->authorizeUser($user_id);
        $user = auth()->user();
        return view('PublicUser.profile', compact('user'));
    }

    public function updateProfile(Request $request, $user_id)
    {
        $this->authorizeUser($user_id);

        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = auth()->user();
        $user->name = $request->name;
        if ($user->publicUser) {
            $user->publicUser->phone = $request->phone;
            $user->publicUser->save();
        }

        if ($request->hasFile('profile_picture')) {
            $filename = time() . '.' . $request->profile_picture->extension();
            $request->profile_picture->storeAs('profile_pictures', $filename, 'public');
            $user->profile_picture = 'profile_pictures/' . $filename;
        }

        $user->save();

        return back()->with('success', 'Profile updated successfully.');
    }

    public function changePassword(Request $request, $user_id)
    {
        $this->authorizeUser($user_id);

        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        $user = auth()->user();

        if (!$user->updatePassword($request->current_password, $request->new_password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        return back()->with('success', 'Password updated successfully.');
    }

    private function authorizeUser($user_id)
    {
        if (auth()->id() != $user_id || !auth()->user()->isPublicUser()) {
            abort(403);
        }
    }

    public function storeInquiry(Request $request, $user_id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'source' => 'required|string|max:255',
            'proof' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:2048',
        ]);

        $attachmentPath = null;
        if ($request->hasFile('proof')) {
            $file = $request->file('proof');
            if ($file->isValid()) {
                $attachmentPath = $file->store('attachments', 'public');
                \Log::info('File uploaded to: ' . $attachmentPath);
            } else {
                \Log::error('Uploaded file is not valid.');
            }
        }

        $user = Auth::user();
        $publicUser = PublicUser::where('user_id', $user->id)->first();
        Inquiry::create([
            'PublicUser_id' => $publicUser->id,
            'NewsTitle' => $request->title,
            'NewsContent' => $request->content,
            'NewsSource' => $request->source,
            'InquiryDate' => now(),
            'InquiryStatus' => 'Pending',
            'attachment' => $attachmentPath,
        ]);

        return redirect()->route('PublicUser.InquiryHistory', ['user_id' => $user_id])
            ->with('success', 'Inquiry submitted successfully!');
    }
    //display the inquiry history for a public user
    
    public function inquiryHistory($user_id)
    {
        // Check if user is authenticated and authorized
        $user = Auth::user();
        if (!$user || $user->id != $user_id) {
            abort(403, 'Unauthorized action.');
        }

        // Get the public user record
        $publicUser = PublicUser::where('user_id', $user_id)->first();
        if (!$publicUser) {
            abort(404, 'Public user not found.');
        }

        // Get all inquiries for this public user
        $inquiries = Inquiry::where('PublicUser_id', $publicUser->id)
                        ->orderBy('InquiryDate', 'desc')
                        ->get();

        return view('PublicUser.InquiryHistory', [
            'inquiries' => $inquiries,
            'user' => $user,
            'publicUser' => $publicUser
        ]);
    }

    // View a specific inquiry
    public function viewInquiry($user_id, $inquiry_id)
    {
        $inquiry = Inquiry::findByUser($inquiry_id, $user_id);

        if (!$inquiry) {
            abort(404, 'Inquiry not found.');
        }

        return view('PublicUser.InquiryDetail', compact('inquiry'));
    }
    

    // Display the dashboard for a public user
    public function dashboard($user_id)
    {
        $user = auth()->user();

        if (!$user || $user->id != $user_id || !$user->isPublicUser()) {
            abort(403, 'Unauthorized action.');
        }

        $total = Inquiry::countByUser($user->id);
        $pending = Inquiry::countByStatus($user->id, 'Pending');
        $inProgress = Inquiry::countByStatus($user->id, 'In Progress');
        $resolved = Inquiry::countByStatus($user->id, 'Resolved');
        $recent = Inquiry::recentByUser($user->id, 5);

        return view('PublicUser.dashboard', compact('total', 'pending', 'inProgress', 'resolved', 'recent'));
    }

    public function publicInquiry($user_id)
    {
        if (auth()->id() != $user_id || !auth()->user()->isPublicUser()) {
            abort(403, 'Unauthorized');
        }

        $inquiries = \App\Models\Inquiry::where('InquiryStatus', 'Verified')
                        ->orderBy('created_at', 'desc')
                        ->get();

        return view('PublicUser.PublicInquiry', compact('inquiries'));
    }


    }