@extends('layouts.dashboard')

@section('content')
<div class="max-w-4xl mx-auto py-10 px-4">
    <div class="bg-gradient-to-tr from-blue-100 via-purple-100 to-pink-100 rounded-2xl shadow-xl p-8">
        <h2 class="text-2xl font-bold text-gray-800 mb-4">Inquiry Details</h2>

        <p class="mt-2"><strong>Sender:</strong> {{ $inquiry->publicUser?->name ?? 'N/A' }}</p>

        <p class="mt-2"><strong>Title:</strong> {{ strtoupper($inquiry->NewsTitle) }}</p>
        <p class="mt-2"><strong>Content:</strong><br> {{ $inquiry->NewsContent }}</p>
        <p class="mt-2"><strong>Source:</strong> {{ $inquiry->NewsSource }}</p>
        <!-- <p class="mt-2"><strong>Status:</strong> 
            <span class="inline-flex px-2 py-1 rounded-full text-xs font-semibold
                {{ $inquiry->InquiryStatus === 'Resolved' ? 'bg-green-100 text-green-800' : 
                ($inquiry->InquiryStatus === 'In Progress' ? 'bg-orange-100 text-orange-800' : 
                'bg-yellow-100 text-yellow-800') }}">
                {{ $inquiry->InquiryStatus }}
            </span> -->
        <p class="mt-2 text-sm text-gray-500">Submitted: {{ $inquiry->created_at->format('d M Y') }}</p>

        <div class="mt-4">
            <h3 class="font-semibold">Attachment:</h3>
            @if($inquiry->attachment)
                @php
                    $fileExtension = pathinfo($inquiry->attachment, PATHINFO_EXTENSION);
                @endphp

                @if(in_array($fileExtension, ['jpg', 'jpeg', 'png', 'gif']))
                    <!-- Display image -->
                    <img src="{{ asset('storage/' . $inquiry->attachment) }}" alt="Attachment" class="mt-2 max-w-full h-auto rounded">
                    <a href="{{ asset('storage/' . $inquiry->attachment) }}" target="_blank" class="text-blue-600 hover:underline">
                        Download Attachment
                    </a>
                @elseif(in_array($fileExtension, ['pdf']))
                    <!-- Embed PDF -->
                    <embed src="{{ asset('storage/' . $inquiry->attachment) }}" type="application/pdf" class="mt-2 w-full h-96" />
                    <a href="{{ asset('storage/' . $inquiry->attachment) }}" target="_blank" class="text-blue-600 hover:underline">
                        Download Attachment
                    </a>    
                @else
                    <!-- Provide download link for other file types -->
                    <a href="{{ asset('storage/' . $inquiry->attachment) }}" target="_blank" class="text-blue-600 hover:underline">
                        Download Attachment
                    </a>
                @endif
            
            @else
                <p class="text-gray-500">No attachment found.</p>
            @endif
            
        </div>

        <div class="mt-6 flex justify-end">
            <form method="POST" action="{{ route('MCMC.rejectInquiry', ['user_id' => auth()->id(), 'inquiry_id' => $inquiry->id]) }}" class="mt-6">
                @csrf
                @method('PUT')
                <button type="submit"
                    class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                    Reject Inquiry
                </button>
            </form>
        </div>

        <a href="{{ route('MCMC.InquiryList', ['user_id' => auth()->id()]) }}"
            class="mt-6 inline-block text-blue-600 hover:underline">
                ← Back to Inquiry List
        </a>

        
        
    </div>
</div>
@endsection