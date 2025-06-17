@extends('layouts.dashboard')

@section('content')
<div class="py-12">
    <div class="bg-gradient-to-tr from-cyan-200 via-blue-300 to-emerald-300 rounded-2xl shadow-xl p-8">
        <h2 class="text-2xl font-bold mb-6 text-gray-800 text-center">Public Verified Inquiries</h2>

        <table class="min-w-full table-auto bg-white rounded shadow">
            <thead class="bg-blue-500 text-white">
                <tr>
                    <th class="px-4 py-2 text-left">Title</th>
                    <th class="px-4 py-2 text-left">Date</th>
                    <th class="px-4 py-2 text-left">Verification Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($inquiries as $inquiry)
                    @php
                        $latestProgress = $inquiry->progress->sortByDesc('created_at')->first();
                    @endphp
                    <tr class="border-t hover:bg-gray-50">
                        <td class="px-4 py-2 font-medium text-gray-800">{{ $inquiry->NewsTitle }}</td>
                        <td class="px-4 py-2 text-gray-600">{{ $inquiry->created_at->format('d M Y') }}</td>
                        <td class="px-4 py-2">
                            @if($latestProgress)
                                @if($latestProgress->ProgressStatus === 'Verified as True')
                                    <span class="text-green-600 font-semibold">Verified True</span>
                                @elseif($latestProgress->ProgressStatus === 'Identified as Fake')
                                    <span class="text-red-600 font-semibold">Fake News</span>
                                @else
                                    <span class="text-gray-600">{{ $latestProgress->ProgressStatus }}</span>
                                @endif
                            @else
                                <span class="text-gray-400 italic">No status</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-4 py-4 text-center text-gray-500">No verified inquiries available.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
