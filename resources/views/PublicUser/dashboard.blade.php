@extends('layouts.dashboard')
@section('content')
<div class="py-12 ">
  <div class="bg-gradient-to-tr from-cyan-200 via-blue-300 to-emerald-300 rounded-3xl shadow-2xl p-10">
        <div>
            <h2 class="text-2xl font-bold mb-6 text-gray-800">Welcome, {{ Auth::user()->name }}!</h2>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-blue-100 p-6 rounded-lg shadow text-center">
                    <div class="text-3xl font-bold text-blue-600 mb-2">{{ $total }}</div>
                    <div class="text-gray-700">Total Inquiries</div>
                </div>
                <div class="bg-yellow-100 p-6 rounded-lg shadow text-center">
                    <div class="text-3xl font-bold text-yellow-600 mb-2"> {{ $pending }}</div>
                    <div class="text-gray-700">Pending</div>
                </div>
                <div class="bg-orange-100 p-6 rounded-lg shadow text-center">
                    <div class="text-3xl font-bold text-orange-600 mb-2"> {{ $inProgress }}</div>
                    <div class="text-gray-700">In Progress</div>
                </div>
                <div class="bg-green-100 p-6 rounded-lg shadow text-center">
                    <div class="text-3xl font-bold text-green-600 mb-2"> {{ $resolved }}</div>
                    <div class="text-gray-700">Resolved</div>
                </div>
            </div>
        
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-800">Recent Inquiries</h3>
                    <a href="{{ route('PublicUser.InquiryForm', ['user_id' => Auth::id()]) }}"
                        aria-current="{{ request()->routeIs('PublicUser.InquiryForm') ? 'page' : '' }}"
                        class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Submit New Inquiry
                        </a>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Title</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                
                            </tr>
                        </thead>
                        
                        <tbody>
                            @forelse($recent as $inquiry)
                                <tr>
                                    <td class="px-4 py-2 text-gray-700">{{ $inquiry->NewsTitle }}</td>
                                    <td class="px-4 py-2 text-yellow-600">{{ $inquiry->InquiryStatus }}</td>
                                    <td class="px-4 py-2 text-gray-500">{{ $inquiry->created_at }}</td>
                                    <!-- <td class="px-4 py-2">
                                        <a href="{{ route('PublicUser.inquiry.view', ['user_id' => Auth::id(), 'inquiry_id' => $inquiry->id]) }}" class="text-blue-600 hover:underline">View</a>
                                    </td> -->
                                </tr>
                            @empty
                                <tr>
                                    <td class="px-4 py-2 text-gray-700" colspan="4">
                                        <div class="text-center py-8">
                                            <div class="text-gray-500 mb-4">
                                                <svg class="mx-auto h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                            </div>
                                            <h3 class="text-lg font-medium text-gray-900 mb-2">No inquiries found</h3>
                                            <p class="text-gray-500 mb-4">Get started by submitting your first inquiry.</p>
                                            <a href="{{ route('PublicUser.InquiryForm', ['user_id' => Auth::id()]) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                                Submit New Inquiry
                                            </a>
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