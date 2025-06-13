@extends('layouts.guest')

@section('content')
<div class="min-h-screen flex flex-col items-center justify-center bg-gradient-to-br from-blue-500 via-purple-500 to-pink-500 px-4 py-12">
    <div class="flex items-center justify-center">
            <img src="{{ asset('image/MCMC.png.webp') }}" class="h-40 w-40 object-contain" />
        </div>
    <div class="w-full max-w-md bg-white/90 backdrop-blur-md rounded-2xl shadow-xl p-8">
        <h2 class="text-2xl font-bold text-center text-gray-800 mb-6">Forgot Your Password?</h2>

        @if (session('status'))
            <div class="mb-4 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <div class="mb-4">
                <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
                <input id="email" name="email" type="email" required autofocus
                    class="w-full border border-gray-300 px-4 py-2 rounded-lg shadow-sm mt-1" />
            </div>

            <div>
                <button type="submit"
                    class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white 
                    bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 
                    focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Send Password Reset Link
                </button>
            </div>
        </form>

        <div class="text-sm text-center mt-4">
            <a href="{{ route('login') }}" class="text-blue-600 hover:underline">Back to Login</a>
        </div>
    </div>
</div>
@endsection
