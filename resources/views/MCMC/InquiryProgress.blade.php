@extends('layouts.dashboard')

@section('content')
<div class="py-12">
    <div class="bg-gradient-to-tr from-blue-100 via-purple-100 to-pink-100 rounded-2xl shadow-xl p-8">

        <div class="flex justify-between items-center mb-8">
            <h2 class="text-3xl font-extrabold text-gray-800 tracking-tight">Inquiry Progress Report</h2>
            <a href="{{ route('MCMC.AgencyPerfReport', ['user_id' => auth()->id()]) }}"
               class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-4 rounded-lg shadow">
                📈 View Agency Performance Report
            </a>
        </div>

        @forelse($inquiries as $inquiry)
            <div class="border border-gray-300 rounded-lg mb-6 p-5 shadow-md bg-white">
                <div class="mb-3">
                    <h3 class="text-xl font-semibold text-gray-800">{{ $inquiry->NewsTitle }}</h3>
                    <p class="text-sm text-gray-600">From: <span class="font-medium">{{ $inquiry->publicUser?->name ?? 'Unknown' }}</span></p>
                    <p class="text-sm text-gray-600">Assigned to: <span class="font-medium">{{ $inquiry->agency->user->name ?? 'Unknown Agency' }}</span></p>
                    <p class="text-sm text-gray-600">Submitted on: {{ $inquiry->created_at->format('d M Y') }}</p>
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
            <p class="text-center text-gray-600">No inquiries have been assigned yet.</p>
        @endforelse

    </div>
</div>
@endsection
