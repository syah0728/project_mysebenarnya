@extends('layouts.guest')

@section('content')
<div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gradient-to-br from-blue-500 via-purple-500 to-pink-500">
    <div class="absolute inset-0 bg-pattern opacity-20"></div>

    <!-- Logo & Heading -->
    <div class="relative z-10 mb-6">
        <div class="flex items-center justify-center">
            <img src="{{ asset('image/MCMC.png.webp') }}" class="h-20 w-20 object-contain" />
        </div>
        <div class="flex items-center justify-center my-2">
            <div class="w-24 h-1 bg-gradient-to-r from-blue-500 via-purple-500 to-pink-500 rounded"></div>
        </div>
        <div class="flex items-center justify-center">
            <h1 class="text-4xl font-bold text-white">MySebenarnya System</h1>
        </div>
    </div>

    <!-- Registration Form -->
    <div class="w-full sm:max-w-md relative z-10">
        <div class="bg-white/90 backdrop-blur-md px-8 py-6 shadow-xl rounded-2xl">
            <h2 class="text-2xl font-semibold text-gray-800 mb-6 text-center">Create Your Account</h2>

            @if ($errors->any())
                <div class="mb-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-md" role="alert">
                    <ul class="list-disc list-inside text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}" class="space-y-6">
                @csrf

                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">
                        <i class="fas fa-user mr-2"></i>Name
                    </label>
                    <input id="name" name="name" type="text" value="{{ old('name') }}" required autofocus
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring focus:ring-blue-300 focus:border-blue-500 sm:text-sm">
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">
                        <i class="fas fa-envelope mr-2"></i>Email
                    </label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" required
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring focus:ring-blue-300 focus:border-blue-500 sm:text-sm">
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">
                        <i class="fas fa-lock mr-2"></i>Password
                    </label>
                    <input id="password" name="password" type="password" required
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring focus:ring-blue-300 focus:border-blue-500 sm:text-sm">
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700">
                        <i class="fas fa-lock mr-2"></i>Confirm Password
                    </label>
                    <input id="password_confirmation" name="password_confirmation" type="password" required
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring focus:ring-blue-300 focus:border-blue-500 sm:text-sm">
                </div>

                <div>
                    <button type="submit"
                        class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white 
                               bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 
                               focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 
                               transform transition duration-150 hover:scale-[1.02]">
                        <i class="fas fa-user-plus mr-2"></i>Register
                    </button>
                </div>
            </form>

            <div class="mt-4 text-center text-sm text-gray-600">
                Already registered?
                <a href="{{ route('login') }}" class="underline text-blue-600 hover:text-blue-800 font-medium">
                    Sign in
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Background pattern (optional) -->
<style>
    .bg-pattern {
        background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.1'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
    }
</style>

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
@endpush
@endsection
