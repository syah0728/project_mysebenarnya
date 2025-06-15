@extends('layouts.dashboard')
@section('content')
<div class="py-12">
    <div class="bg-gradient-to-tr from-indigo-200 via-purple-300 to-pink-300 rounded-2xl shadow-xl p-8">

        <h2 class="text-3xl font-extrabold text-center mb-8 text-gray-800 tracking-tight">Inquiry History</h2>

@forelse($inquiries as $inquiry)
    <div class="border rounded-lg p-4 mb-4 bg-gray-50">
        <h3 class="text-xl font-semibold text-gray-800">{{ $inquiry->NewsTitle }}</h3>
        <p class="text-sm text-gray-600">Submitted by: {{ $inquiry->publicUser->user->name ?? 'Unknown' }}</p>
        <p class="text-sm text-gray-600">Status: <strong>{{ $inquiry->InquiryStatus }}</strong></p>
        <p class="text-sm text-gray-500">Date: {{ $inquiry->created_at->format('d M Y, h:i A') }}</p>

        {{-- 🔗 ADD THE LINK HERE --}}
        <a href="{{ route('Agency.ViewInquiryDetails', ['user_id' => auth()->id(), 'inquiry_id' => $inquiry->id]) }}"
           class="text-indigo-600 hover:underline text-sm">
           🔍 View Full History
        </a>
    </div>
@empty
    <p class="text-gray-500">No inquiries found.</p>
@endforelse

    </div>
</div>
@endsection
