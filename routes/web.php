<?php
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PublicUserController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\UserRegisterController;
use App\Http\Controllers\MCMCController;
use App\Http\Controllers\AgencyController;

// Welcome Page Route
Route::get('/', function () {
    return view('welcome');
        });
    

// Route to Login Page
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

// Route to Register Page
Route::middleware('guest')->group(function () {
    Route::get('/register', [UserRegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [UserRegisterController::class, 'register']);
});

// Protected Routes
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {

    // Direct to Dashboard Routes for each role
    Route::get('/dashboard', function () {
        $user = auth()->user();

        if ($user->isPublicUser()) {
            return redirect()->route('PublicUser.dashboard', ['user_id' => $user->id]);
        } elseif ($user->isMCMC()) {
            return redirect()->route('MCMC.dashboard', ['user_id' => $user->id]);
        } elseif ($user->isAgency()) {
            return redirect()->route('Agency.dashboard', ['user_id' => $user->id]);
        } else {
            return redirect('/');
        }
    })->name('dashboard');

    // User Logout
    Route::post('/logout', [LogoutController::class, 'logout'])->name('logout');

    Route::prefix('PublicUser/{user_id}')->name('PublicUser.')->group(function () {
        Route::get('/dashboard', function ($user_id) {
            if (!auth()->user()->isPublicUser() || auth()->id() != $user_id) {
                abort(403, 'Unauthorized action.');
            }
            return view('PublicUser.dashboard');
        })->name('dashboard');

        // Add this for the profile page
        Route::get('/profile', function ($user_id) {
            if (!auth()->user()->isPublicUser() || auth()->id() != $user_id) {
                abort(403, 'Unauthorized action.');
            }
            return view('PublicUser.profile');
        })->name('profile');

        // Add this for the inquiry form if not already defined
        Route::get('/inquiry-form', function ($user_id) {
            if (!auth()->user()->isPublicUser() || auth()->id() != $user_id) {
                abort(403, 'Unauthorized action.');
            }
            return view('PublicUser.InquiryForm');
        })->name('InquiryForm');

        // Inquiry History route
        Route::get('/inquiry-history', function ($user_id) {
            if (!auth()->user()->isPublicUser() || auth()->id() != $user_id) {
                abort(403, 'Unauthorized action.');
            }
            return view('PublicUser.InquiryHistory');
        })->name('InquiryHistory');
        // Public Inquiry route
        Route::get('/public-inquiry', function ($user_id) {
            if (!auth()->user()->isPublicUser() || auth()->id() != $user_id) {
                abort(403, 'Unauthorized action.');
            }
            return view('PublicUser.PublicInquiry');
        })->name('PublicInquiry');

        // Inquiry Status route
        Route::get('/inquiry-status', function ($user_id) {
            if (!auth()->user()->isPublicUser() || auth()->id() != $user_id) {
                abort(403, 'Unauthorized action.');
            }
            return view('PublicUser.InquiryStatus');
        })->name('InquiryStatus');

        Route::post('/inquiry-form', [\App\Http\Controllers\PublicUserController::class, 'storeInquiry'])->name('storeInquiry');

        Route::get('/dashboard', [\App\Http\Controllers\PublicUserController::class, 'dashboard'])->name('dashboard');

        Route::get('/inquiry-history', [\App\Http\Controllers\PublicUserController::class, 'inquiryHistory'])->name('InquiryHistory');
        
        Route::get('/inquiry/{inquiry_id}', [\App\Http\Controllers\PublicUserController::class, 'viewInquiry'])->name('inquiry.view');
    });
    
    });

    // MCMC Routes Group
    Route::prefix('MCMC/{user_id}')->name('MCMC.')->group(function () {

        Route::get('/dashboard', [MCMCController::class, 'dashboard'])->name('dashboard');

        // Route::get('/dashboard', function ($user_id) {
        //     if (!auth()->user()->isMCMC() || auth()->id() != $user_id) {
        //         abort(403, 'Unauthorized action.');
        //     }
        //     return view('MCMC.dashboard');
        // })->name('dashboard');
        Route::get('/profile', function ($user_id) {
            if (!auth()->user()->isMCMC() || auth()->id() != $user_id) {
                abort(403, 'Unauthorized action.');
            }
            return view('MCMC.profile');
        })->name('profile');

    
    // Profile (User Data)
    Route::get('/user-data', function ($user_id) {
        if (!auth()->user()->isMCMC() || auth()->id() != $user_id) {
            abort(403, 'Unauthorized action.');
        }
        return view('MCMC.UserData');
    })->name('UserData');

    // Inquiry List
    Route::get('/inquiries', [\App\Http\Controllers\MCMCController::class, 'inquiryList'])->name('InquiryList');

    // Assign Inquiry
    // View Assigned Inquiries (GET)
    Route::get('/assigned-inquiry', [MCMCController::class, 'assignedInquiry'])
        ->name('AssignedInquiry');

    // Process Inquiry Assignment (POST)
    Route::post('/assign-inquiry', [MCMCController::class, 'assignInquiry'])
        ->name('AssignInquiry');
    //Route::post('/assigned-inquiry/{inquiry_id}', [\App\Http\Controllers\MCMCController::class, 'assignInquiry'])->name('AssignedInquiry.post');
    });




    // Agency Routes Group
    Route::prefix('Agency/{user_id}')->name('Agency.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [AgencyController::class, 'dashboard'])->name('dashboard');

    // Profile
    Route::get('/profile', function ($user_id) {
        if (!auth()->user()->isAgency() || auth()->id() != $user_id) {
            abort(403, 'Unauthorized action.');
        }
        return view('Agency.profile');
    })->name('profile');

    // View single inquiry details
    Route::get('/inquiry/{inquiry_id}', [AgencyController::class, 'viewInquiry'])->name('viewInquiry');
    // Inquiry History
    Route::get('/inquiry-history', function ($user_id) {
        if (!auth()->user()->isAgency() || auth()->id() != $user_id) {
            abort(403, 'Unauthorized action.');
        }
        return view('Agency.InquiryHistory');
    })->name('InquiryHistory');

    // Inquiry List
    Route::get('/inquiry-list', function ($user_id) {
        if (!auth()->user()->isAgency() || auth()->id() != $user_id) {
            abort(403, 'Unauthorized action.');
        }
        return view('Agency.InquiryList');
    })->name('InquiryList');
});