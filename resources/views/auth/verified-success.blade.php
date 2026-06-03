@extends('layouts.app')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4">
    <div class="max-w-md w-full bg-white rounded-2xl shadow-lg p-8 text-center">

        <div class="mx-auto mb-6 w-16 h-16 bg-green-100 rounded-full flex items-center justify-center">
            <svg class="w-8 h-8 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
            </svg>
        </div>

        <h2 class="text-2xl font-bold text-gray-800 mb-3">
            Email Verified Successfully
        </h2>

        <p class="text-gray-500 text-sm mb-6">
            Your email address has been verified. You can now access the system.
        </p>

        <a href="{{ route('dashboard') }}"
           class="block w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-4 rounded-lg transition text-sm">
            Continue to Dashboard
        </a>

    </div>
</div>
@endsection