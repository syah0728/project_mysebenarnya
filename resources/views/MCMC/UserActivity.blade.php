@extends('layouts.dashboard')
@section('content')
<div class="py-12">
    <div class="bg-gradient-to-tr from-blue-100 via-purple-100 to-pink-100 rounded-2xl shadow-xl p-8">

        <!-- Back Button -->
        <div class="mb-6">
            <a href="{{ route('MCMC.UserData', ['user_id' => Auth::id()]) }}"
            class="inline-block px-4 py-2 bg-indigo-500 text-white font-bold rounded hover:bg-indigo-700">
                ← Back to User List
            </a>
        </div>

        <!-- User Profile Header -->
        <h2 class="text-3xl font-extrabold text-gray-800 mb-6 border-b pb-2">User Profile</h2>

        <!-- Basic User Info -->
        <div class="space-y-2 mb-6 text-gray-700">
            <p><strong>Name:</strong> {{ $user->name }}</p>
            <p><strong>Email:</strong> {{ $user->email }}</p>
            <p><strong>Role:</strong> {{ $user->role }}</p>
            <p><strong>Joined:</strong> {{ $user->created_at->format('d M Y') }}</p>
        </div>

        <!-- Role-Specific Activity -->
        @if($user->isPublicUser())
            <h3 class="text-xl font-semibold text-blue-700 mb-3">Submitted Inquiries</h3>
            <ul class="list-disc list-inside text-gray-700 mb-4">
                @forelse($inquiries as $inquiry)
                    <li>{{ $inquiry->NewsTitle }} – {{ $inquiry->created_at->format('d M Y') }}</li>
                @empty
                    <li>No inquiries submitted.</li>
                @endforelse
            </ul>
        @elseif($user->isAgency())
            <h3 class="text-xl font-semibold text-purple-700 mb-3">Assigned Inquiries</h3>
            <ul class="list-disc list-inside text-gray-700 mb-4">
                @forelse($assignments as $assignment)
                    <li>
                        Inquiry ID #{{ $assignment->Inquiry_id }} –
                        Assigned on {{ \Carbon\Carbon::parse($assignment->AssignmentDate)->format('d M Y') }}
                    </li>
                @empty
                    <li>No assignments received.</li>
                @endforelse
            </ul>
        @else
            <p class="text-gray-500 italic mt-4">No activity logs available for this user role.</p>
        @endif

    </div>
</div>
@endsection
