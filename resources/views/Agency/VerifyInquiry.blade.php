@extends('layouts.dashboard')

@section('content')
<div class="py-12">
    <div class="bg-gradient-to-tr from-indigo-200 via-purple-300 to-pink-300 max-w-4xl mx-auto rounded-2xl shadow-xl p-8">
        <h2 class="text-3xl font-extrabold text-center mb-8 text-gray-800 tracking-tight">Verify Inquiry</h2>

        <!-- Inquiry Summary -->
        <div class="mb-6">
            <p><strong>Title:</strong> {{ strtoupper($inquiry->NewsTitle) }}</p>
            <p class="mt-2"><strong>Submitted by:</strong> {{ $inquiry->publicUser?->name ?? 'N/A' }}</p>
            <p class="mt-2"><strong>Source:</strong> {{ $inquiry->NewsSource }}</p>
            <p class="mt-2 text-sm text-gray-500">Submitted: {{ $inquiry->created_at->format('d M Y') }}</p>
        </div>

        <!-- Attachment -->
        @if($inquiry->attachment)
            <div class="mb-6">
                <h4 class="font-semibold mb-2">Attachment:</h4>
                @php
                    $ext = pathinfo($inquiry->attachment, PATHINFO_EXTENSION);
                @endphp

                @if(in_array($ext, ['jpg', 'jpeg', 'png', 'gif']))
                    <img src="{{ asset('storage/' . $inquiry->attachment) }}" class="rounded shadow max-w-full h-auto mb-2">
                @elseif($ext === 'pdf')
                    <embed src="{{ asset('storage/' . $inquiry->attachment) }}" type="application/pdf" class="w-full h-96 mb-2">
                @endif
                <a href="{{ asset('storage/' . $inquiry->attachment) }}" target="_blank" class="text-indigo-600 hover:underline">Download Attachment</a>
            </div>
        @endif

        <!-- Verification Form -->
        <form method="POST" action="{{ route('Agency.SubmitVerification', ['user_id' => auth()->id(), 'inquiry_id' => $inquiry->id]) }}" enctype="multipart/form-data">
            @csrf

            <!-- Verification Status -->
            <div class="mb-4">
                <label for="verification_status" class="block font-semibold text-sm text-gray-700 mb-1">Verification Result <span class="text-red-500">*</span></label>
                <select name="verification_status" id="verification_status" class="w-full p-2 border rounded @error('verification_status') border-red-500 @enderror" required>
                    <option value="">-- Select --</option>
                    <option value="Under Investigation">Under Investigation</option>
                    <option value="Verified as True">Verified as True</option>
                    <option value="Identified as Fake">Identified as Fake</option>
                    <option value="Rejected">Rejected</option>
                </select>
                @error('verification_status')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Reviewing Officer -->
            <div class="mb-4">
                <label for="reviewing_officer" class="block font-semibold text-sm text-gray-700 mb-1">Reviewing Officer Name <span class="text-red-500">*</span></label>
                <input type="text" name="reviewing_officer" id="reviewing_officer"
                       class="w-full p-2 border rounded @error('reviewing_officer') border-red-500 @enderror"
                       placeholder="Enter officer's full name" required>
                @error('reviewing_officer')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Supporting Document Upload -->
            <div class="mb-4">
                <label for="supporting_document" class="block font-semibold text-sm text-gray-700 mb-1">Supporting Document (optional)</label>
                <input type="file" name="supporting_document" id="supporting_document"
                       class="w-full p-2 border rounded @error('supporting_document') border-red-500 @enderror"
                       accept=".pdf,.jpg,.jpeg,.png,.gif">
                @error('supporting_document')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Description -->
            <div class="mb-6">
                <label for="description" class="block font-semibold text-sm text-gray-700 mb-1">Description (optional)</label>
                <textarea name="description" id="description" rows="4" class="w-full p-2 border rounded" placeholder="Explain your decision (optional)..."></textarea>
                @error('description')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit Button -->
            <div class="flex justify-between items-center">
                <a href="{{ route('Agency.InquiryList', ['user_id' => auth()->id()]) }}" class="text-sm text-gray-600 hover:underline">
                    ← Back to Inquiry List
                </a>
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-6 rounded-lg shadow">
                    Submit Verification
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
