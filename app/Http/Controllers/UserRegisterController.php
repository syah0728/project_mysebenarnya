<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\PublicUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserRegisterController extends Controller
{
    // Show the registration form
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    // Handle registration
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|confirmed|min:8',
            'phone' => 'nullable|string|max:255', // Add phone validation if needed
        ]);

        // Use database transaction to ensure both records are created together
        DB::beginTransaction();
        
        try {
            // Create user record
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'PublicUser', // Set default role
            ]);

            // Create corresponding PublicUser record
            PublicUser::create([
                'user_id' => $user->id,
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone ?? '', // Use empty string if phone not provided
            ]);

            // Commit the transaction
            DB::commit();

            // Log in the user
            Auth::login($user);

            return redirect()->route('PublicUser.dashboard', ['user_id' => $user->id]);
            
        } catch (\Exception $e) {
            // Rollback the transaction if something goes wrong
            DB::rollback();
            
            return back()->withErrors(['error' => 'Registration failed. Please try again.'])->withInput();
        }
    }
}