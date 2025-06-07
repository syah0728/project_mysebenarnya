@extends('layouts.dashboard')
@section('content')
<div class="max-w-7xl mx-auto py-10 px-4">
    <div class="bg-gradient-to-tr from-blue-100 via-purple-100 to-pink-100 rounded-2xl shadow-xl p-8">
        <h2 class="text-3xl font-extrabold text-center mb-8 text-gray-800 tracking-tight">Unassigned Inquiries</h2>
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
                        <th class="px-4 py-3 text-center font-semibold">Assign To</th>
                        <th class="px-4 py-3 text-center font-semibold">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($inquiries as $index => $inquiry)
                    <tr class="transition hover:bg-blue-50">
                        <td class="px-4 py-3 text-center text-gray-700 font-semibold">{{ $index + 1 }}</td>
                        <td class="px-4 py-3 text-center">
                            <span class="inline-flex items-center gap-2">
                                <span class="bg-blue-200 text-blue-800 rounded-full px-3 py-1 text-xs font-bold">
                                    {{ $inquiry->publicUser ? $inquiry->publicUser->name : 'N/A' }}
                                </span>
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="font-bold text-blue-700 mb-1">{{ strtoupper($inquiry->NewsTitle) }}</div>
                            <div class="text-gray-600 text-sm">{{ \Illuminate\Support\Str::limit($inquiry->NewsContent, 80) }}</div>
                        </td>
                        <td class="px-4 py-3 text-center text-gray-600">
                            {{ \Carbon\Carbon::parse($inquiry->created_at)->format('d M Y') }}
                        </td>
                        <td class="px-4 py-3">
                            <form method="POST" action="{{ route('MCMC.AssignInquiry', ['user_id' => Auth::id()]) }}">
                                @csrf
                                <input type="hidden" name="inquiry_id" value="{{ $inquiry->id }}">
                                <select name="agency_id" class="w-full border border-gray-300 rounded-lg px-2 py-1 text-sm focus:ring-2 focus:ring-blue-400 focus:outline-none transition">
                                    <option value="">-- Select Agency --</option>
                                    @foreach($agencies as $agency)
                                        <option value="{{ $agency->id }}" {{ $inquiry->Agency_id == $agency->id ? 'selected' : '' }}>
                                            {{ strtoupper($agency->name) }}
                                        </option>
                                    @endforeach
                                </select>
                        </td>
                        <td class="px-4 py-3 text-center">
                                <button type="submit" class="bg-gradient-to-r from-blue-500 to-purple-500 hover:from-blue-600 hover:to-purple-600 text-white font-bold py-1.5 px-5 rounded-lg shadow transition transform hover:scale-105">
                                    Assign
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-10 text-center text-gray-400 text-lg">
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