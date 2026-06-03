@extends('layouts.app')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4">
    <div class="max-w-md w-full bg-white rounded-2xl shadow-lg p-8 text-center">

        <div class="mx-auto mb-6 w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center">
            <svg class="w-8 h-8 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25H4.5a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5H4.5a2.25 2.25 0 00-2.25 2.25m19.5 0l-9.75 7.5-9.75-7.5"/>
            </svg>
        </div>

        <h2 class="text-2xl font-bold text-gray-800 mb-2">Check your inbox</h2>
        <p class="text-gray-500 text-sm mb-1">We sent a verification link to</p>
        <p class="font-semibold text-gray-800 mb-6">{{ Auth::user()->email }}</p>

        <p class="text-gray-400 text-sm mb-6">
            Click the link in the email to activate your account.
            The link will expire in <strong>60 minutes</strong>.
        </p>

        @if(session('resent'))
            <div class="mb-4 px-4 py-3 bg-green-100 text-green-700 rounded-lg text-sm font-medium">
                ✓ Verification email resent! Please check your inbox.
            </div>
        @endif

        @if($errors->any())
            <div class="mb-4 px-4 py-3 bg-red-100 text-red-600 rounded-lg text-sm">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit"
                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-4 rounded-lg transition text-sm">
                Resend verification email
            </button>
        </form>

        <p class="mt-5 text-xs text-gray-400">
            Wrong email?
            <a href="{{ route('logout') }}"
               onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
               class="text-blue-500 hover:underline">
                Sign out
            </a>
            and register again.
        </p>
        <form id="logout-form" method="POST" action="{{ route('logout') }}" class="hidden">@csrf</form>
    </div>
</div>
@endsection