@extends('layouts.dashboard')
@section('content')
<div class="max-w-xl mx-auto mt-10 bg-white p-8 rounded shadow">
    <h2 class="text-2xl font-bold mb-6 text-gray-800">Submit New Inquiry</h2>
    <form method="POST" action="{{ route('PublicUser.storeInquiry', ['user_id' => Auth::id()]) }}" enctype="multipart/form-data">        @csrf

        <div class="mb-4">
            <label for="title" class="block text-gray-700 font-semibold mb-2">Title</label>
            <input type="text" id="title" name="title" class="w-full border border-gray-300 rounded px-3 py-2" required>
        </div>

        <div class="mb-4">
            <label for="content" class="block text-gray-700 font-semibold mb-2">Content</label>
            <textarea id="content" name="content" rows="4" class="w-full border border-gray-300 rounded px-3 py-2" required></textarea>
        </div>
        <div class="mb-4">
            <label for="source" class="block text-gray-700 font-semibold mb-2">Source</label>
            <input type="text" id="source" name="source" class="w-full border border-gray-300 rounded px-3 py-2" required>
        </div>

        <div class="mb-4">
            <label for="proof" class="block text-gray-700 font-semibold mb-2">Proof (Attachment)</label>
            <input type="file" id="proof" name="proof" class="w-full border border-gray-300 rounded px-3 py-2" accept=".jpg,.jpeg,.png,.pdf,.doc,.docx">
        </div>

        <div class="flex justify-end">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded">
                Submit Inquiry
            </button>
        </div>
    </form>
</div>
@endsection