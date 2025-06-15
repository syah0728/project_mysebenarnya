@extends('layouts.dashboard')
@section('content')
<div class="py-12">
<div class="bg-gradient-to-tr from-cyan-200 via-blue-300 to-emerald-300 rounded-2xl shadow-xl p-8">

        <div class="mb-8">
            <form method="GET" action="{{ route('PublicUser.InquiryProgress', ['user_id' => auth()->id()]) }}" class="mb-6 flex flex-wrap items-center gap-4">
                <input type="text" name="search" placeholder="Search by title..." value="{{ request('search') }}"
                    class="px-4 py-2 border rounded w-full md:w-1/3" />

                <select name="status" class="px-4 py-2 border rounded w-full md:w-1/4">
                    <option value="">All Statuses</option>
                    <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                    <option value="Assigned" {{ request('status') == 'Assigned' ? 'selected' : '' }}>Assigned</option>
                    <option value="In Progress" {{ request('status') == 'In Progress' ? 'selected' : '' }}>In Progress</option>
                    <option value="Resolved" {{ request('status') == 'Resolved' ? 'selected' : '' }}>Resolved</option>
                    <option value="Rejected" {{ request('status') == 'Rejected' ? 'selected' : '' }}>Rejected</option>
                </select>

                <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
                    🔍 Search
                </button>
            </form>

            <h2 class="text-2xl font-bold mb-6 text-gray-800">My Inquiry Progress</h2>
        </div>

        @forelse($inquiries as $inquiry)
            <div class="border border-gray-300 rounded-lg mb-6 p-5 shadow-md bg-white">
                <div class="mb-3">
                    <h3 class="text-xl font-semibold text-gray-800">{{ $inquiry->NewsTitle }}</h3>
                    <p class="text-sm text-gray-600">Submitted on: {{ $inquiry->created_at->format('d M Y') }}</p>
                    <p class="text-sm text-gray-600">Handled by: 
                        <span class="font-medium">{{ $inquiry->agency->user->name ?? 'Not Assigned Yet' }}</span>
                    </p>
                    <p class="text-sm text-gray-600">Current Status: 
                        <span class="font-semibold">{{ $inquiry->InquiryStatus }}</span>
                    </p>
                </div>

                <div class="mt-4">
                    <h4 class="font-semibold text-indigo-600 mb-2">Progress Updates:</h4>

                    @forelse($inquiry->progressUpdates as $progress)
                        <div class="bg-gray-50 border-l-4 border-indigo-400 p-4 mb-3 rounded">
                            <p class="text-sm"><strong>Status:</strong> {{ $progress->ProgressStatus }}</p>
                            <p class="text-sm text-gray-700"><strong>Comment:</strong> {{ $progress->ProgressDescription ?? '-' }}</p>
                            <p class="text-xs text-gray-500 mt-1">
                                <strong>Reviewed by:</strong> {{ $progress->ReviewingOfficer ?? 'N/A' }} |
                                <strong>Date:</strong> {{ \Carbon\Carbon::parse($progress->created_at)->format('d M Y h:i A') }}
                            </p>

                            @if($progress->SupportingDocument)
                                <p class="text-xs mt-2">
                                    <a href="{{ asset('storage/' . $progress->SupportingDocument) }}" target="_blank" class="text-blue-600 hover:underline">
                                        📎 View Supporting Document
                                    </a>
                                </p>
                            @endif
                        </div>
                    @empty
                        <p class="text-sm text-gray-500 italic">No progress updates yet.</p>
                    @endforelse
                </div>
            </div>
        @empty
            <p class="text-center text-gray-600">You haven’t submitted any inquiries yet.</p>
        @endforelse

    </div>
</div>
@endsection
