@extends('layouts.dashboard')
@section('content')
<div class="max-w-7xl mx-auto py-10 px-4">
    <div class="bg-gradient-to-tr from-blue-100 via-purple-100 to-pink-100 rounded-2xl shadow-xl p-8">
        <h2 class="text-3xl font-extrabold text-center mb-8 text-gray-800 tracking-tight">Assigned Inquiries</h2>
        <div class="overflow-x-auto rounded-lg shadow">
            <table class="min-w-full bg-white rounded-lg overflow-hidden">
                <thead class="bg-gradient-to-r from-blue-400 to-purple-400 text-white sticky top-0 z-10">
                    <tr>
                        <th class="px-4 py-3 text-center font-semibold">No.</th>
                        <th class="px-4 py-3 text-center font-semibold">Title</th>
                        <th class="px-4 py-3 text-center font-semibold">Assigned Agency</th>
                        <th class="px-4 py-3 text-center font-semibold">Status</th>
                        <th class="px-4 py-3 text-center font-semibold">Date Assigned</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($assignedInquiries as $index => $inquiry)
                    <tr class="transition hover:bg-blue-50">
                        <td class="px-4 py-3 text-center text-gray-700 font-semibold">{{ $index + 1 }}</td>
                        <td class="px-4 py-3 text-center">
                            <span class="font-bold text-blue-700">{{ $inquiry->NewsTitle }}</span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="bg-purple-200 text-purple-800 rounded-full px-3 py-1 text-xs font-bold">
                                {{ $inquiry->agency ? $inquiry->agency->name : 'N/A' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="inline-block px-3 py-1 rounded-full text-xs font-bold
                                {{ $inquiry->InquiryStatus === 'Resolved' ? 'bg-green-100 text-green-700' : ($inquiry->InquiryStatus === 'In Progress' ? 'bg-orange-100 text-orange-700' : 'bg-yellow-100 text-yellow-700') }}">
                                {{ $inquiry->InquiryStatus }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center text-gray-600">
                            {{ \Carbon\Carbon::parse($inquiry->updated_at)->format('d M Y') }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-4 py-10 text-center text-gray-400 text-lg">
                            No assigned inquiries found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection