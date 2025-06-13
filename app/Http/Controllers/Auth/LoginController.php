<?php
namespace App\Http\Controllers\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\MCMC;
use App\Models\Agency;

class LoginController extends Controller
{
    //To display the login form and redirect to the login page
    public function showLoginForm()
    {
        return view('auth.login');
    }

    //To authenticate the user and redirect to the appropriate dashboard
public function login(Request $request)
{
    $role = $request->input('role');

    if ($role === 'PublicUser') {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password, 'role' => 'PublicUser'])) {
            return redirect()->route('PublicUser.dashboard', ['user_id' => Auth::id()]);
        }

    } elseif ($role === 'MCMC') {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        $mcmc = MCMC::where('username', $request->username)->first();

        if ($mcmc && $mcmc->user) {
            $user = $mcmc->user;
            if (
                $user->role === 'MCMC' &&
                Auth::attempt(['email' => $user->email, 'password' => $request->password])
            ) {
                return redirect()->route('MCMC.dashboard', ['user_id' => $user->id]);
            }
        }

    } elseif ($role === 'Agency') {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        $agency = Agency::where('username', $request->username)->first();

        if ($agency && $agency->user) {
            $user = $agency->user;
            if (
                $user->role === 'Agency' &&
                Auth::attempt(['email' => $user->email, 'password' => $request->password])
            ) {
                return redirect()->route('Agency.dashboard', ['user_id' => $user->id]);
            }
        }
    }

    return back()->with('error', 'Invalid credentials or role.');
}

    //To redirect the user to the appropriate dashboard based on their role
    protected function redirectTo()
    {
        if (Auth::user()->isPublicUser()) {
            return route('PublicUser.dashboard', ['user_id' => Auth::user()->id]);
        } elseif (Auth::user()->isMCMC()) {
            return route('MCMC.dashboard', ['user_id' => Auth::user()->id]);
        } elseif (Auth::user()->isAgency()) {
            return route('Agency.dashboard', ['user_id' => Auth::id()]);
        }

        return RouteServiceProvider::HOME;
    }

    //To redirect the user to the appropriate dashboard based on their role
    protected function authenticated(Request $request, $user)
    {
        if ($user->isPublicUser()) {
            return redirect()->route('PublicUser.dashboard', ['user_id' => $user->id]);
        } elseif ($user->isMCMC()) {
            return redirect()->route('MCMC.dashboard', ['user_id' => $user->id]);
        } elseif ($user->isAgency()) {
            return redirect()->route('Agency.dashboard', ['user_id' => $user->id]);
        }

        return redirect('/');
    }

    //To display the change password form
    public function showChangePasswordForm()
    {
        return view('auth.change-password');
    }

    //To change the password of the user
    public function changePassword(Request $request)
    {
        //Validate the request to ensure the current password and new password are present
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        $user = Auth::user();

        // Check if current password matches
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'The current password is incorrect.']);
        }

        // Update password
        $user->password = Hash::make($request->new_password);
        $user->save();

        return back()->with('success', 'Password changed successfully.');
    }
}