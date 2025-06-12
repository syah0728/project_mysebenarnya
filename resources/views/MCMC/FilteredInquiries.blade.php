@extends('layouts.dashboard')
@section('content')
<div class="max-w-7xl mx-auto py-10 px-4">
    <div class="bg-gradient-to-tr from-blue-100 via-purple-100 to-pink-100 rounded-2xl shadow-xl p-8">
        <div class="mb-6">
            <a href="{{ route('MCMC.InquiryList', ['user_id' => Auth::id()]) }}"
            class="inline-block px-4 py-2 bg-indigo-500 text-white font-bold rounded hover:bg-indigo-700">
                ← Back to Inquiry List
            </a>
            <h2 class="text-3xl font-extrabold text-center mb-8 text-gray-800 tracking-tight">Filtered Inquiries</h2>
        </div>

        <!-- Filter Form -->
        <form method="GET" action="{{ route('MCMC.FilteredInquiries', ['user_id' => Auth::id()]) }}" class="mb-6 grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label for="date" class="block text-sm font-medium text-gray-700">Date</label>
                <input type="date" name="date" id="date" value="{{ request('date') }}"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
            </div>
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                <select name="status" id="status" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    <option value="">All</option>
                    <option value="Flagged" {{ request('status') == 'Flagged' ? 'selected' : '' }}>Flagged</option>
                    <!-- <option value="Rejected" {{ request('status') == 'Rejected' ? 'selected' : '' }}>Rejected</option> -->
                    <option value="Discarded" {{ request('status') == 'Discarded' ? 'selected' : '' }}>Discarded</option>
                    <!-- <option value="Resolved" {{ request('status') == 'Resolved' ? 'selected' : '' }}>Resolved</option>
                    <option value="Rejected" {{ request('status') == 'Rejected' ? 'selected' : '' }}>Rejected</option> -->
                </select>
            </div>
            <div>
                <label for="agency_id" class="block text-sm font-medium text-gray-700">Agency</label>
                <select name="agency_id" id="agency_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    <option value="">All</option>
                    @foreach($agencies as $agency)
                        <option value="{{ $agency->id }}" {{ request('agency_id') == $agency->id ? 'selected' : '' }}>
                            {{ $agency->username }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="self-end">
                <button type="submit"
                        class="px-4 py-2 bg-blue-500 text-white font-semibold rounded hover:bg-blue-600">
                    Filter
                </button>
            </div>
        </form>

        <!-- Inquiries Table -->
        <div class="overflow-x-auto rounded-lg shadow">
            <table class="min-w-full bg-white rounded-lg overflow-hidden">
                <thead class="bg-gradient-to-r from-blue-400 to-purple-400 text-white">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold">Sender</th>
                        <th class="px-4 py-3 text-left font-semibold">Title</th>
                        <th class="px-4 py-3 text-left font-semibold">Status</th>
                        <th class="px-4 py-3 text-left font-semibold">Date</th>
                        <th class="px-4 py-3 text-left font-semibold">Agency</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($filteredInquiries as $inquiry)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-2">{{ $inquiry->user->name ?? 'N/A' }}</td>
                            <td class="px-4 py-2">{{ $inquiry->NewsTitle }}</td>
                            <td class="px-4 py-2">{{ $inquiry->InquiryStatus }}</td>
                            <td class="px-4 py-2">{{ $inquiry->created_at->format('d M Y') }}</td>
                            <td class="px-4 py-2">{{ $inquiry->agency->username ?? 'Unassigned' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-4 text-center text-gray-500">No inquiries found for the selected filters.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
