@extends('layouts.dashboard')
@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-8">
            <h2 class="text-2xl font-bold mb-6 text-gray-800">Welcome, {{ Auth::user()->name }} (Agency)!</h2>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-blue-100 p-6 rounded-lg shadow text-center">
                    <div class="text-3xl font-bold text-blue-600 mb-2">{{ $total }}</div>
                    <div class="text-gray-700">Total Assigned Inquiries</div>
                </div>
                <div class="bg-yellow-100 p-6 rounded-lg shadow text-center">
                    <div class="text-3xl font-bold text-yellow-600 mb-2">{{ $pending }}</div>
                    <div class="text-gray-700">Pending</div>
                </div>
                <div class="bg-orange-100 p-6 rounded-lg shadow text-center">
                    <div class="text-3xl font-bold text-orange-600 mb-2">{{ $inProgress }}</div>
                    <div class="text-gray-700">In Progress</div>
                </div>
                <div class="bg-green-100 p-6 rounded-lg shadow text-center">
                    <div class="text-3xl font-bold text-green-600 mb-2">{{ $resolved }}</div>
                    <div class="text-gray-700">Resolved</div>
                </div>
            </div>

            <!-- Recent Assigned Inquiries Table -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-800">Recent Assigned Inquiries</h3>
                    <a href="{{ route('Agency.InquiryList', ['user_id' => Auth::id()]) }}" 
                    class="bg-gradient-to-r from-blue-500 to-purple-500 hover:from-blue-600 hover:to-purple-600 text-white font-bold py-2 px-4 rounded-lg shadow transition transform hover:scale-105">
                        View All
                    </a>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gradient-to-r from-blue-400 to-purple-400 text-white">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium uppercase">Title</th>
                                <th class="px-4 py-2 text-left text-xs font-medium uppercase">Status</th>
                                <th class="px-4 py-2 text-left text-xs font-medium uppercase">Submitted By</th>
                                <th class="px-4 py-2 text-left text-xs font-medium uppercase">Date</th>
                                <th class="px-4 py-2"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($assignedInquiries as $inquiry)
                                <tr class="hover:bg-blue-50 transition-colors">
                                    <td class="px-4 py-2">
                                        <div class="font-semibold text-gray-800">{{ $inquiry->NewsTitle }}</div>
                                    </td>
                                    <td class="px-4 py-2">
                                        <span class="inline-flex px-2 py-1 rounded-full text-xs font-semibold
                                            {{ $inquiry->InquiryStatus === 'Resolved' ? 'bg-green-100 text-green-800' : 
                                            ($inquiry->InquiryStatus === 'In Progress' ? 'bg-orange-100 text-orange-800' : 
                                            'bg-yellow-100 text-yellow-800') }}">
                                            {{ $inquiry->InquiryStatus }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-2">
                                        <span class="bg-blue-100 text-blue-800 rounded-full px-2 py-1 text-xs font-semibold">
                                            {{ $inquiry->user ? $inquiry->user->name : 'Unknown' }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-2 text-gray-500 text-sm">
                                        {{ \Carbon\Carbon::parse($inquiry->created_at)->format('d M Y') }}
                                    </td>
                                    <td class="px-4 py-2 text-right">
                                        <a href="{{ route('Agency.viewInquiry', ['user_id' => Auth::id(), 'inquiry_id' => $inquiry->id]) }}" 
                                        class="text-blue-600 hover:text-blue-800 font-medium text-sm">
                                            View Details
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-2 text-gray-700">
                                        <div class="text-center py-8">
                                            <div class="text-gray-500 mb-4">
                                                <svg class="mx-auto h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                            </div>
                                            <h3 class="text-lg font-medium text-gray-900 mb-2">No assigned inquiries found</h3>
                                            <p class="text-gray-500 mb-4">You have no assigned inquiries at the moment.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection