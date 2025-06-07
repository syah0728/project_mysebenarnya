@extends('layouts.dashboard')
@section('content')
<div class="py-12">
    <div class="bg-gradient-to-tr from-blue-100 via-purple-100 to-pink-100 rounded-2xl shadow-xl p-8">
        <!-- <div class="bg-gradient-to-tr from-blue-100 via-purple-100 to-pink-100 rounded-2xl shadow-xl p-8"> -->
            <h2 class="text-2xl font-bold mb-6 text-gray-800">Welcome, {{ Auth::user()->name }} (MCMC)!</h2>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-blue-100 p-6 rounded-lg shadow text-center">
                    <div class="text-3xl font-bold text-blue-600 mb-2">{{ $total }}</div>
                    <div class="text-gray-700">Total Inquiries</div>
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

            <!-- Recent Inquiries Table -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-800">Recent Inquiries</h3>
                    <a href="{{ route('MCMC.InquiryList', ['user_id' => Auth::id()]) }}"
                    class="bg-gradient-to-r from-blue-500 to-purple-500 hover:from-blue-600 hover:to-purple-600 text-white font-bold py-2 px-6 rounded-full shadow transition transform hover:scale-105">
                        View All
                    </a>
                </div>
                <div class="overflow-x-auto rounded-lg shadow">
                    <table class="min-w-full bg-white rounded-lg overflow-hidden">
                        <thead class="bg-gradient-to-r from-blue-400 to-purple-400 text-white sticky top-0 z-10">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium uppercase">Title</th>
                                <th class="px-4 py-2 text-left text-xs font-medium uppercase">Status</th>
                                <th class="px-4 py-2 text-left text-xs font-medium uppercase">Date</th>
                                <th class="px-4 py-2 text-left text-xs font-medium uppercase">Submitted By</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recent as $inquiry)
                                <tr class="transition hover:bg-blue-50">
                                    <td class="px-4 py-2 text-gray-700 font-semibold">{{ $inquiry->NewsTitle }}</td>
                                    <td class="px-4 py-2">
                                        <span class="inline-block px-3 py-1 rounded-full text-xs font-bold
                                            {{ $inquiry->InquiryStatus === 'Resolved' ? 'bg-green-100 text-green-700' : ($inquiry->InquiryStatus === 'In Progress' ? 'bg-orange-100 text-orange-700' : 'bg-yellow-100 text-yellow-700') }}">
                                            {{ $inquiry->InquiryStatus }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-2 text-gray-500">{{ \Carbon\Carbon::parse($inquiry->created_at)->format('d M Y') }}</td>
                                    <td class="px-4 py-2 text-gray-700">
                                        <span class="bg-blue-200 text-blue-800 rounded-full px-3 py-1 text-xs font-bold">
                                            {{ $inquiry->publicUser ? $inquiry->publicUser->name : 'N/A' }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="px-4 py-2 text-gray-700 text-center" colspan="4">
                                        <div class="text-center py-8">
                                            <div class="text-gray-500 mb-4">
                                                <svg class="mx-auto h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                            </div>
                                            <h3 class="text-lg font-medium text-gray-900 mb-2">No inquiries found</h3>
                                            <p class="text-gray-500 mb-4">There are currently no inquiries assigned to you.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        <!-- </div> -->
    </div>
</div>
@endsection