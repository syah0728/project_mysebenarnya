@extends('layouts.dashboard')
@section('content')
<div class="py-12">
    <div class="bg-gradient-to-tr from-blue-100 via-purple-100 to-pink-100 rounded-2xl shadow-xl p-8">
        <div class="mb-6">
            <a href="{{ route('MCMC.UserData', ['user_id' => Auth::id()]) }}"
            class="inline-block px-4 py-2 bg-indigo-500 text-white font-bold rounded hover:bg-indigo-700">
                ← Back to User Data
            </a>
        </div>
        <h2 class="text-3xl font-extrabold text-center mb-8 text-gray-800 tracking-tight">Register New Agency User</h2>

        @if(session('success'))
            <div class="mb-4 p-4 rounded bg-green-100 text-green-800 font-semibold">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="mb-4 p-4 rounded bg-red-100 text-red-800">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('MCMC.RegisterUserPost', ['user_id' => Auth::id()]) }}">
            @csrf

        <!-- Full Name -->
        <div class="mb-4">
            <label for="name" class="block text-gray-700 font-bold mb-2">Full Name</label>
            <input type="text" name="name" id="name" class="w-full border-black border rounded px-3 py-2" required>
        </div>

        <!-- Email -->
        <div class="mb-4">
            <label for="email" class="block text-gray-700 font-bold mb-2">Email</label>
            <input type="email" name="email" id="email" class="w-full border-black border rounded px-3 py-2" required>
        </div>

        <!-- Password -->
        <div class="mb-4">
            <label for="password" class="block text-gray-700 font-bold mb-2">Password</label>
            <input type="password" name="password" id="password" class="w-full border-black border rounded px-3 py-2" required>
        </div>

        <!-- Username for Agency -->
        <div class="mb-4">
            <label for="username" class="block text-gray-700 font-bold mb-2">Agency Username</label>
            <input type="text" name="username" id="username" class="w-full border-black border rounded px-3 py-2" required>
        </div>

        <!-- Phone -->
        <div class="mb-4">
            <label for="phone" class="block text-gray-700 font-bold mb-2">Phone Number</label>
            <input type="text" name="phone" id="phone" class="w-full border-black border rounded px-3 py-2" required>
        </div>


            <div class="flex justify-end">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-5 py-2 rounded">
                    Register Agency
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
