@extends('layouts.dashboard')

@section('content')
<div class="max-w-4xl mx-auto py-10 px-4">
    <div class="bg-gradient-to-tr from-blue-100 via-purple-100 to-pink-100 rounded-2xl shadow-xl p-8">
        <h2 class="text-2xl font-bold text-gray-800 mb-4">Inquiry Details</h2>

        <p class="mt-2"><strong>Sender:</strong> {{ $inquiry->publicUser?->name ?? 'N/A' }}</p>

        <p class="mt-2"><strong>Title:</strong> {{ strtoupper($inquiry->NewsTitle) }}</p>
        <p class="mt-2"><strong>Content:</strong><br> {{ $inquiry->NewsContent }}</p>
        <p class="mt-2"><strong>Source:</strong> {{ $inquiry->NewsSource }}</p>
        <p class="mt-2 text-sm text-gray-500">Submitted: {{ $inquiry->created_at->format('d M Y') }}</p>

        <div class="mt-4">
            <h3 class="font-semibold">Attachment:</h3>
            @if($inquiry->attachment)
                @php
                    $fileExtension = pathinfo($inquiry->attachment, PATHINFO_EXTENSION);
                @endphp

                @if(in_array($fileExtension, ['jpg', 'jpeg', 'png', 'gif']))
                    <img src="{{ asset('storage/' . $inquiry->attachment) }}" alt="Attachment" class="mt-2 max-w-full h-auto rounded">
                    <a href="{{ asset('storage/' . $inquiry->attachment) }}" target="_blank" class="text-blue-600 hover:underline">
                        Download Attachment
                    </a>
                @elseif(in_array($fileExtension, ['pdf']))
                    <embed src="{{ asset('storage/' . $inquiry->attachment) }}" type="application/pdf" class="mt-2 w-full h-96" />
                    <a href="{{ asset('storage/' . $inquiry->attachment) }}" target="_blank" class="text-blue-600 hover:underline">
                        Download Attachment
                    </a>
                @else
                    <a href="{{ asset('storage/' . $inquiry->attachment) }}" target="_blank" class="text-blue-600 hover:underline">
                        Download Attachment
                    </a>
                @endif
            @else
                <p class="text-gray-500">No attachment found.</p>
            @endif
        </div>

        <!-- Status Dropdown and Submit Button -->
        <div class="mt-6">
            <form method="POST" action="{{ route('MCMC.rejectInquiry', ['user_id' => auth()->id(), 'inquiry_id' => $inquiry->id]) }}">
                @csrf
                @method('PUT')
                <label for="status" class="block mb-2 font-medium text-gray-700">Update Inquiry Status:</label>
                <select name="status" id="status" required class="block w-full mb-4 border-gray-300 rounded-md shadow-sm">
                    <option value="">-- Select Status --</option>
                    <option value="Flagged">Flagged</option>
                    <!-- <option value="Rejected">Rejected</option> -->
                    <option value="Discarded">Discarded</option>
                </select>
                <div class="flex justify-end mt-6">
                <button type="submit"
                        class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                    Submit Status
                </button>
                </div>
            </form>
        </div>

        
        <a href="{{ route('MCMC.InquiryList', ['user_id' => auth()->id()]) }}"
            class="inline-block px-4 py-2 bg-indigo-500 text-white font-bold rounded hover:bg-indigo-700">
            ← Back to Inquiry List
        </a>

    </div>
</div>
@endsection
