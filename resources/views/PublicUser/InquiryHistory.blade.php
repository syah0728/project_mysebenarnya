@extends('layouts.dashboard')
@section('content')
<div class="py-12">
<div class="bg-gradient-to-tr from-cyan-200 via-blue-300 to-emerald-300 rounded-2xl shadow-xl p-8">
    <h2 class="text-2xl font-bold mb-6 text-gray-800">Inquiry History</h2>
    <div x-data="{ open: false, inquiry: {} }" x-cloak>
        <table class="min-w-full divide-y divide-gray-200">
            <thead>
                <tr>
                    <th class="px-4 py-2 text-left text-xs font-bold text-black-500 uppercase">Title</th>
                    <th class="px-4 py-2 text-left text-xs font-bold text-black-500 uppercase">Status</th>
                    <th class="px-4 py-2 text-left text-xs font-bold text-black-500 uppercase">Date Submitted</th>
                    <th class="px-4 py-2 text-left text-xs font-bold text-black-500 uppercase">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($inquiries as $inquiry)
                    <tr>
                        <td class="px-4 py-2 text-gray-700">{{ $inquiry->NewsTitle }}</td>
                        <td class="px-4 py-2 text-yellow-600">{{ $inquiry->InquiryStatus }}</td>
                        <td class="px-4 py-2 text-gray-500">{{ $inquiry->created_at }}</td>
                        <td class="px-4 py-2">
                            <button
                                @click="open = true; inquiry = {{ json_encode([
                                    'NewsTitle' => $inquiry->NewsTitle,
                                    'InquiryStatus' => $inquiry->InquiryStatus,
                                    'created_at' => $inquiry->created_at,
                                    'NewsContent' => $inquiry->NewsContent,
                                    'NewsSource' => $inquiry->NewsSource,
                                    'attachment' => $inquiry->attachment,
                                    'agency_name' => $inquiry->agency ? $inquiry->agency->name : 'Not assigned'
                                ]) }}"
                                class="text-blue-600 hover:underline"
                                type="button">
                                View
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-8 text-center text-gray-400">
                            No inquiries found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Modal -->
        <div x-show="open" class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg shadow-lg max-w-lg w-full p-6 relative">
                <button @click="open = false" class="absolute top-2 right-2 text-gray-400 hover:text-gray-600 text-2xl">&times;</button>
                <h3 class="text-xl font-bold mb-4 text-gray-800">Inquiry Details</h3>
                <div class="mb-2"><strong>Title:</strong> <span x-text="inquiry.NewsTitle"></span></div>
                <div class="mb-2"><strong>Status:</strong> <span x-text="inquiry.InquiryStatus"></span></div>
                <div class="mb-2"><strong>Date Submitted:</strong> <span x-text="inquiry.created_at"></span></div>
                <div class="mb-2"><strong>Content:</strong> <span x-text="inquiry.NewsContent"></span></div>
                <div class="mb-2"><strong>Source:</strong> <span x-text="inquiry.NewsSource"></span></div>
                <div class="mb-2"><strong>Agency:</strong> <span x-text="inquiry.agency_name"></span></div>
                <template x-if="inquiry.attachment">
                    <div class="mb-2">
                        <strong>Attachment:</strong>
                        <a :href="'/storage/' + inquiry.attachment" target="_blank" class="text-blue-600 underline">View Attachment</a>
                    </div>
                </template>
            </div>
        </div>
    </div>
</div>
</div>
@endsection