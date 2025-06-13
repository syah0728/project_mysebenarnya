@extends('layouts.guest')

@section('content')
<div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gradient-to-br from-blue-500 via-purple-500 to-pink-500">
    <div class="absolute inset-0 bg-pattern opacity-20"></div>
    
    <div class="relative z-10 mb-6">
        <div class="flex items-center justify-center">
            <img src="{{ asset('image/MCMC.png.webp') }}" class="h-40 w-40 object-contain" />
        </div>
        <div class="flex items-center justify-center my-2">
            <div class="w-24 h-1 bg-gradient-to-r from-blue-500 via-purple-500 to-pink-500 rounded"></div>
        </div>
        <div class="flex items-center justify-center">
            <h1 class="text-4xl font-bold text-white">MySebenarnya System</h1>
        </div>
    </div>

    <div class="w-full sm:max-w-md relative z-10">
        <div class="bg-white/90 backdrop-blur-md px-8 py-6 shadow-xl rounded-2xl">
            <h2 class="text-2xl font-semibold text-gray-800 mb-6 text-center">Sign In to Your Account</h2>

            @if (session('error'))
                <div class="mb-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-md relative" role="alert">
                    {{ session('error') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-6">
                @csrf

                <!-- Role -->
                <div>
                    <label for="role" class="block text-sm font-medium text-gray-700">
                        <i class="fas fa-user-tag mr-2"></i>Login As
                    </label>
                    <select id="role" name="role" required onchange="toggleLoginField()"
                        class="mt-1 w-full border border-gray-300 px-3 py-2.5 rounded-lg shadow-sm">
                        <option value="">Select Role</option>
                        <option value="PublicUser" {{ old('role') == 'PublicUser' ? 'selected' : '' }}>PublicUser</option>
                        <option value="MCMC" {{ old('role') == 'MCMC' ? 'selected' : '' }}>MCMC</option>
                        <option value="Agency" {{ old('role') == 'Agency' ? 'selected' : '' }}>Agency</option>
                    </select>
                </div>

                <!-- Email -->
                <div id="emailField" style="display: none;">
                    <label for="email" class="block text-sm font-medium text-gray-700 mt-4">
                        <i class="far fa-envelope mr-2"></i>Email Address
                    </label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}"
                        class="w-full border border-gray-300 px-3 py-2 rounded-lg shadow-sm">
                </div>

                <!-- Username -->
                <div id="usernameField" style="display: none;">
                    <label for="username" class="block text-sm font-medium text-gray-700 mt-4">
                        <i class="fas fa-user mr-2"></i>Username
                    </label>
                    <input type="text" id="username" name="username" value="{{ old('username') }}"
                        class="w-full border border-gray-300 px-3 py-2 rounded-lg shadow-sm">
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mt-4">
                        <i class="fas fa-lock mr-2"></i>Password
                    </label>
                    <input id="password" type="password" name="password" required
                        class="w-full border border-gray-300 px-3 py-2 rounded-lg shadow-sm">
                </div>

                <!-- Forgot password -->
                <div class="text-right text-sm mt-2">
                    <a href="{{ route('password.request') }}" class="text-blue-600 hover:underline">Forgot your password?</a>
                </div>

                <!-- Submit -->
                <div>
                    <button type="submit"
                        class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white 
                               bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 
                               focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <i class="fas fa-sign-in-alt mr-2"></i>
                        Sign in
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
@endpush

<script>
    function toggleLoginField() {
        const role = document.getElementById('role').value;
        document.getElementById('emailField').style.display = role === 'PublicUser' ? 'block' : 'none';
        document.getElementById('usernameField').style.display = (role === 'MCMC' || role === 'Agency') ? 'block' : 'none';
    }

    // Initialize on page load if old value exists
    window.onload = toggleLoginField;
</script>
@endsection
