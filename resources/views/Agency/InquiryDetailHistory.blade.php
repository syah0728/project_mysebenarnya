@extends('layouts.dashboard')
@section('content')
<div class="py-12">
    <div class="bg-gradient-to-tr from-indigo-200 via-purple-300 to-pink-300 rounded-2xl shadow-xl p-8">

        <h2 class="text-3xl font-extrabold text-center mb-8 text-gray-800 tracking-tight">Full Inquiry History</h2>

        {{-- Inquiry Info --}}
        <div class="mb-6">
            <h3 class="text-lg font-semibold text-gray-700">🗂️ Inquiry Details</h3>
            <p><strong>Title:</strong> {{ $inquiry->NewsTitle }}</p>
            <p><strong>Source:</strong> {{ $inquiry->NewsSource }}</p>
            <p><strong>Status:</strong> <span class="font-semibold">{{ $inquiry->InquiryStatus }}</span></p>
            <p><strong>Submitted by:</strong> {{ $inquiry->publicUser->user->name ?? 'Unknown' }}</p>
            <p><strong>Agency Assigned:</strong> {{ $inquiry->agency->user->name ?? 'Not Assigned' }}</p>
            <p><strong>Submitted on:</strong> {{ $inquiry->created_at->format('d M Y, h:i A') }}</p>
            @if($inquiry->attachment)
                <p><strong>Attachment:</strong>
                    <a href="{{ asset('storage/' . $inquiry->attachment) }}" target="_blank" class="text-blue-600 underline">📎 View</a>
                </p>
            @endif
        </div>

        {{-- Progress Updates --}}
        <div class="mb-6">
            <h3 class="text-lg font-semibold text-indigo-700">📘 Progress Updates</h3>
            @forelse($inquiry->progressUpdates as $progress)
                <div class="bg-gray-100 p-4 rounded mb-4 border-l-4 border-indigo-500">
                    <p><strong>Status:</strong> {{ $progress->ProgressStatus }}</p>
                    <p><strong>Description:</strong> {{ $progress->ProgressDescription ?? '-' }}</p>
                    <p><strong>Reviewed by:</strong> {{ $progress->ReviewingOfficer ?? 'N/A' }}</p>
                    <p class="text-sm text-gray-500">
                        <strong>Date:</strong> {{ \Carbon\Carbon::parse($progress->created_at)->format('d M Y, h:i A') }}
                    </p>
                    @if($progress->SupportingDocument)
                        <p>
                            <a href="{{ asset('storage/' . $progress->SupportingDocument) }}" target="_blank"
                               class="text-blue-600 hover:underline text-sm">
                               📎 View Supporting Document
                            </a>
                        </p>
                    @endif
                </div>
            @empty
                <p class="italic text-gray-500">No progress updates found.</p>
            @endforelse
        </div>

        {{-- Back Button --}}
        <a href="{{ route('Agency.InquiryHistory', ['user_id' => auth()->id()]) }}"
           class="inline-block bg-gray-200 text-gray-700 px-4 py-2 rounded hover:bg-gray-300">
           ← Back to Inquiry List
        </a>
    </div>
</div>
@endsection
