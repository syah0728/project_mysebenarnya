@extends('layouts.dashboard')
@section('content')
<div class="py-12">
    <div class="bg-gradient-to-tr from-indigo-200 via-purple-300 to-pink-300 rounded-2xl shadow-xl p-8">

        <h2 class="text-3xl font-extrabold text-center mb-8 text-gray-800 tracking-tight">Assigned Inquiries</h2>
        @if(session('success'))
            <div class="mb-6 px-4 py-3 rounded bg-green-100 text-green-800 font-semibold shadow">
                {{ session('success') }}
            </div>
        @endif
        <div class="overflow-x-auto rounded-lg shadow">
            <table class="min-w-full bg-white rounded-lg overflow-hidden">
                <thead class="bg-gradient-to-r from-blue-400 to-purple-400 text-white sticky top-0 z-10">
                    <tr>
                        <th class="px-4 py-3 text-center font-semibold">No.</th>
                        <th class="px-4 py-3 text-center font-semibold">Sender Name</th>
                        <th class="px-4 py-3 text-center font-semibold">Inquiry</th>
                        <th class="px-4 py-3 text-center font-semibold">Date</th>
                        <th class="px-4 py-3 text-center font-semibold">Review</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($inquiries as $index => $inquiry)
                    <tr class="transition hover:bg-blue-50">
                        <td class="px-4 py-3 text-center text-gray-700 font-semibold">{{ $index + 1 }}</td>
                        <td class="px-4 py-3 text-center">
                            <span class="inline-flex items-center gap-2">
                                <span class="bg-blue-200 text-blue-800 rounded-full px-3 py-1 text-xs font-bold">
                                    {{ $inquiry->publicUser ? $inquiry->publicUser->name : 'Unknown' }}
                                </span>
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="font-bold text-blue-700 mb-1">{{ $inquiry->NewsTitle }}</div>
                            <div class="text-gray-600 text-sm">{{ \Illuminate\Support\Str::limit($inquiry->NewsContent, 80) }}</div>
                        </td>
                        <td class="px-4 py-3 text-center text-gray-600">
                            {{ \Carbon\Carbon::parse($inquiry->created_at)->format('d M Y') }}
                        </td>
                        <td>
                            <a href="{{ route('Agency.InquiryReview', ['user_id' => $user->id, 'inquiry_id' => $inquiry->id]) }}"
                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                    Review
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-4 py-10 text-center text-gray-400 text-lg">
                            No inquiries available.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection