@extends('layouts.dashboard')
@section('content')
<div class="py-12">
    <div class="bg-gradient-to-tr from-blue-100 via-purple-100 to-pink-100 rounded-2xl shadow-xl p-8">
        <h2 class="text-3xl font-bold text-center text-gray-800 mb-6">User Report</h2>

        {{-- Filter Form --}}
        <form method="GET" action="{{ route('MCMC.UserReport', ['user_id' => Auth::id()]) }}" class="mb-8 grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="text-sm font-semibold text-gray-600">Start Date</label>
                <input type="date" name="start_date" class="w-full border rounded px-3 py-2" value="{{ request('start_date') }}">
            </div>
            <div>
                <label class="text-sm font-semibold text-gray-600">End Date</label>
                <input type="date" name="end_date" class="w-full border rounded px-3 py-2" value="{{ request('end_date') }}">
            </div>
            <div>
                <label class="text-sm font-semibold text-gray-600">User Role</label>
                <select name="role" class="w-full border rounded px-3 py-2">
                    <option value="">All</option>
                    <option value="MCMC" {{ request('role') == 'MCMC' ? 'selected' : '' }}>MCMC</option>
                    <option value="Agency" {{ request('role') == 'Agency' ? 'selected' : '' }}>Agency</option>
                    <option value="PublicUser" {{ request('role') == 'PublicUser' ? 'selected' : '' }}>PublicUser</option>
                </select>
            </div>
            <div>
                <label class="text-sm font-semibold text-gray-600">Agency</label>
                <select name="agency_id" class="w-full border rounded px-3 py-2">
                    <option value="">All</option>
                    @foreach($agencies as $agency)
                        <option value="{{ $agency->id }}" {{ request('agency_id') == $agency->id ? 'selected' : '' }}>
                            {{ $agency->user->name ?? 'Unnamed Agency' }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="md:col-span-4 text-right">
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white font-bold rounded hover:bg-indigo-700">
                    Apply Filters
                </button>
            </div>
        </form>

        {{-- Filtered Users Table --}}
        <div class="overflow-x-auto">
            <table class="min-w-full table-auto bg-white rounded-lg shadow">
                <thead class="bg-blue-500 text-white">
                    <tr>
                        <th class="px-4 py-3 text-center">No.</th>
                        <th class="px-4 py-3 text-center">Name</th>
                        <th class="px-4 py-3 text-center">Email</th>
                        <th class="px-4 py-3 text-center">Role</th>
                        <th class="px-4 py-3 text-center">Registered At</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $index => $user)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="px-4 py-3 text-center">{{ $index + 1 }}</td>
                            <td class="px-4 py-3 text-center">{{ $user->name }}</td>
                            <td class="px-4 py-3 text-center">{{ $user->email }}</td>
                            <td class="px-4 py-3 text-center">{{ $user->role }}</td>
                            <td class="px-4 py-3 text-center">{{ $user->created_at->format('d M Y') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-6 text-center text-gray-500">No users match the selected filters.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

        
        </div>
            <!-- Download Buttons -->
        <div class="flex justify-end gap-4 mb-6">
            <a href="{{ route('MCMC.DownloadUserReportPDF', ['user_id' => Auth::id()] + request()->query()) }}"
                class="bg-red-500 hover:bg-red-600 text-white font-semibold px-4 py-2 rounded shadow">
                    Download PDF
            </a>

            <a href="{{ route('MCMC.DownloadUserReportExcel',['user_id' => Auth::id()] + request()->query()) }}"
                class="bg-green-500 hover:bg-green-600 text-white font-semibold px-4 py-2 rounded shadow">
                    Download Excel
            </a>

        </div>
    </div>
</div>
@endsection
