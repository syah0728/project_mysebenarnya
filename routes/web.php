<?php
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PublicUserController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\UserRegisterController;
use App\Http\Controllers\MCMCController;
use App\Http\Controllers\AgencyController;
use App\Http\Controllers\NewPasswordController;

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
        });
    
    });

    // MCMC Routes Group
    Route::prefix('MCMC/{user_id}')->name('MCMC.')->group(function () {

    Route::get('/dashboard', [MCMCController::class, 'dashboard'])->name('dashboard');

    Route::get('/profile', function ($user_id) {
        if (!auth()->user()->isMCMC() || auth()->id() != $user_id) {
            abort(403, 'Unauthorized action.');
        }
        return view('MCMC.profile');
    })->name('profile');

    Route::get('/user-data', function ($user_id) {
        if (!auth()->user()->isMCMC() || auth()->id() != $user_id) {
            abort(403, 'Unauthorized action.');
        }
        return view('MCMC.UserData');
    })->name('UserData');

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

    Route::get('/inquiry-assign-report', [MCMCController::class, 'InquiryAssignReport'])
        ->name('InquiryAssignReport');

    Route::get('/inquiry-assign-report/pdf', [MCMCController::class, 'DownloadInquiryAssignReportPDF'])
        ->name('DownloadInquiryAssignReportPDF');

    Route::get('/inquiry-assign-report/excel', [MCMCController::class, 'DownloadInquiryAssignReportExcel'])
        ->name('DownloadInquiryAssignReportExcel');

    Route::get('/inquiry-report', [MCMCController::class, 'inquiryReport'])->name('InquiryReport');

    Route::get('/inquiry-report/pdf', [MCMCController::class, 'DownloadInquiryReportPDF'])->name('DownloadInquiryReportPDF');

    Route::get('/inquiry-report/excel', [MCMCController::class, 'DownloadInquiryReportExcel'])->name('DownloadInquiryReportExcel');



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
    Route::get('/inquiries', [AgencyController::class, 'inquiryList'])->name('InquiryList');

    Route::get('/inquiry/{inquiry_id}/review', [AgencyController::class, 'inquiryReview'])->name('InquiryReview');

    Route::put('/inquiry/{inquiry_id}/reject', [AgencyController::class, 'rejectInquiry'])->name('RejectInquiry');

});