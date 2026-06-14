<?php
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use App\Http\Controllers\PublicUserController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\UserRegisterController;
use App\Http\Controllers\MCMCController;
use App\Http\Controllers\AgencyController;
use App\Http\Controllers\NewPasswordController;

//Perfective Maintenance: Email Verification
use Illuminate\Http\Request;
//

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


//Perfective Maintenance: Email Verification
Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (Request $request, $id, $hash) {
    // Cari user by id
    $user = \App\Models\User::findOrFail($id);

    // Verify hash
    if (! hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
        abort(403, 'Invalid verification link.');
    }

    // Mark as verified kalau belum
    if (! $user->hasVerifiedEmail()) {
        $user->markEmailAsVerified();
    }

    // Login user
    Auth::login($user);

    // return redirect()->route('PublicUser.dashboard', ['user_id' => $user->id])
    //                  ->with('success', 'Email verified! Welcome aboard.');
})->middleware(['signed'])->name('verification.verify');
// Resend email
Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('resent', true);
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();

    return view('auth.verified-success');
})->middleware(['auth', 'signed'])->name('verification.verify');
//

//Route::get('/reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
//Route::post('/reset-password', [NewPasswordController::class, 'store'])->name('password.update');


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

    // Public User Routes Group
    Route::prefix('PublicUser/{user_id}')->name('PublicUser.')->group(function () {
        Route::get('/dashboard', function ($user_id) {
            if (!auth()->user()->isPublicUser() || auth()->id() != $user_id) {
                abort(403, 'Unauthorized action.');
            }
            return view('PublicUser.dashboard');
        })->name('dashboard');

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
        
        Route::get('/public-inquiry', [\App\Http\Controllers\PublicUserController::class, 'publicInquiry'])->name('PublicInquiry');

        Route::get('/profile', [PublicUserController::class, 'showProfile'])->name('profile');
        Route::post('/profile/update', [PublicUserController::class, 'updateProfile'])->name('updateProfile');
        Route::post('/profile/password', [PublicUserController::class, 'changePassword'])->name('changePassword');

        Route::get('/inquiry-progress', [PublicUserController::class, 'inquiryProgress'])
         ->name('InquiryProgress');
        });
    
    });

// MCMC Routes Group
Route::prefix('MCMC/{user_id}')->name('MCMC.')->group(function () {

    Route::get('/dashboard', [MCMCController::class, 'dashboard'])->name('dashboard');

    Route::get('/user-data', function ($user_id) {
        if (!auth()->user()->isMCMC() || auth()->id() != $user_id) {
            abort(403, 'Unauthorized action.');
        }
        return view('MCMC.UserData');
    })->name('UserData');

    Route::get('/inquiry-progress', [MCMCController::class, 'inquiryProgress'])->name('InquiryProgress');


    // Inquiry List
    Route::get('/inquiry', [MCMCController::class, 'inquiryList'])->name('InquiryList');

    // View Assigned Inquiries
    Route::get('/assigned-inquiry', [MCMCController::class, 'assignedInquiry'])->name('AssignedInquiry');

    // Process Inquiry Assignment (POST)
    Route::post('/assign-inquiry', [MCMCController::class, 'assignInquiry'])->name('AssignInquiry');

    // View Single Inquiry Details
    Route::get('/inquiry/{inquiry_id}/review', [MCMCController::class, 'inquiryReview'])->name('InquiryReview');

    // Reject Inquiry
    Route::put('/inquiry/{inquiry_id}/reject', [MCMCController::class, 'rejectInquiry'])->name('rejectInquiry');

    Route::get('/user-data', [MCMCController::class, 'UserData'])->name('UserData');

    Route::get('/user-activity/{target_user_id}', [MCMCController::class, 'ViewUserActivity'])->name('ViewUserActivity');

    Route::get('/register-user', [MCMCController::class, 'RegisterUser'])->name('RegisterUser');
    Route::post('/register-user', [MCMCController::class, 'RegisterUserPost'])->name('RegisterUserPost');

    Route::get('/filtered-inquiries', [MCMCController::class, 'filteredInquiries'])->name('FilteredInquiries');
    // Inquiry Assign Report
    Route::get('/inquiry-assign-report', [MCMCController::class, 'InquiryAssignReport'])
        ->name('InquiryAssignReport');

    Route::get('/inquiry-assign-report/pdf', [MCMCController::class, 'DownloadInquiryAssignReportPDF'])
        ->name('DownloadInquiryAssignReportPDF');

    Route::get('/inquiry-assign-report/excel', [MCMCController::class, 'DownloadInquiryAssignReportExcel'])
        ->name('DownloadInquiryAssignReportExcel');
    // Inquiry Report
    Route::get('/inquiry-report', [MCMCController::class, 'inquiryReport'])->name('InquiryReport');

    Route::get('/inquiry-report/pdf', [MCMCController::class, 'DownloadInquiryReportPDF'])->name('DownloadInquiryReportPDF');

    Route::get('/inquiry-report/excel', [MCMCController::class, 'DownloadInquiryReportExcel'])->name('DownloadInquiryReportExcel');
    // User Report
    Route::get('/user-report', [MCMCController::class, 'generateUserReport'])->name('UserReport');

    Route::get('/user-report/pdf', [MCMCController::class, 'downloadUserReportPDF'])->name('DownloadUserReportPDF');

    Route::get('/user-report/excel', [MCMCController::class, 'downloadUserReportExcel'])->name('DownloadUserReportExcel');

    Route::get('/agency-performance-report', [MCMCController::class, 'agencyPerformanceReport'])->name('AgencyPerfReport');

    Route::get('/agency-performance-report/pdf', [MCMCController::class, 'DownloadPerfReportPDF'])->name('DownloadPerfReportPDF');
    Route::get('/agency-performance-report/excel', [MCMCController::class, 'DownloadPerfReportExcel'])->name('DownloadPerfReportExcel');

    //SEM Module 3
    Route::post('/bulk-assign-inquiry', [MCMCController::class, 'bulkAssignInquiry'])->name('BulkAssignInquiry');
});

// Agency Routes Group
Route::prefix('Agency/{user_id}')->name('Agency.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [AgencyController::class, 'dashboard'])->name('dashboard');
    
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
    Route::get('/inquiries', [AgencyController::class, 'inquiryList'])->name('InquiryList');

    Route::get('/inquiry/{inquiry_id}/review', [AgencyController::class, 'inquiryReview'])->name('InquiryReview');

    Route::put('/inquiry/{inquiry_id}/reject', [AgencyController::class, 'rejectInquiry'])->name('RejectInquiry');

    Route::get('/profile', [AgencyController::class, 'showProfile'])->name('profile');
    Route::post('/profile/update', [AgencyController::class, 'updateProfile'])->name('updateProfile');
    Route::post('/profile/password', [AgencyController::class, 'changePassword'])->name('changePassword');

    // Show the form to verify an inquiry
    Route::get('/inquiry/{inquiry_id}/verify-inquiry', [AgencyController::class, 'verifyInquiry'])->name('VerifyInquiry');

    Route::post('/inquiry/{inquiry_id}/verify', [AgencyController::class, 'submitVerification'])->name('SubmitVerification');

    Route::get('/inquiry-history', [AgencyController::class, 'inquiryHistoryList'])->name('InquiryHistory');

    Route::get('/inquiry-history/{inquiry_id}', [AgencyController::class, 'viewInquiryHistory'])->name('ViewInquiryDetails');

});