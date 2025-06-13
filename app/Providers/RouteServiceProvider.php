<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Auth\Notifications\ResetPassword;

class RouteServiceProvider extends ServiceProvider
{
    public const HOME = '/dashboard';

    public function boot()
    {
        parent::boot();

        // Fix reset password URL to include full path
        ResetPassword::createUrlUsing(function ($user, string $token) {
            return url(route('password.reset', [
                'token' => $token,
                'email' => $user->email,
            ], false));
        });
    }

    public function map()
    {
        // Routing logic
    }
}
