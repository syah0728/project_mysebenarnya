@extends('layouts.dashboard')

@section('content')
<div class="py-12">
    <div class="bg-gradient-to-tr from-blue-100 via-purple-100 to-pink-100 rounded-2xl shadow-xl p-8">
        <div class="mb-6">
            <a href="{{ route('MCMC.InquiryProgress', ['user_id' => Auth::id()]) }}"
            class="inline-block px-4 py-2 bg-indigo-500 text-white font-bold rounded hover:bg-indigo-700">
                ← Back to Inquiry Progress
            </a>
        </div>
        <h2 class="text-2xl font-bold text-gray-800 mb-4">Agency Performance Report</h2>

        <form method="GET" class="flex flex-wrap gap-4 mb-6">
    <input type="date" name="start_date" value="{{ request('start_date') }}" class="p-2 border rounded" placeholder="Start Date">
    <input type="date" name="end_date" value="{{ request('end_date') }}" class="p-2 border rounded" placeholder="End Date">
    
    <select name="agency_id" class="p-2 border rounded">
        <option value="">All Agencies</option>
        @foreach(App\Models\Agency::with('user')->get() as $agency)
            <option value="{{ $agency->id }}" {{ request('agency_id') == $agency->id ? 'selected' : '' }}>
                {{ $agency->user->name ?? 'Unknown' }}
            </option>
        @endforeach
    </select>

    <select name="category" class="p-2 border rounded">
        <option value="">All Categories</option>
        <!-- <option value="Rumor" {{ request('category') == 'Rumor' ? 'selected' : '' }}>Rumor</option>
        <option value="Scam" {{ request('category') == 'Scam' ? 'selected' : '' }}>Scam</option> -->
        <!-- Add more categories as needed -->
    </select>

    <button type="submit" class="bg-indigo-500 text-white px-4 py-2 rounded hover:bg-indigo-700">Filter</button>

    <a href="{{ route('MCMC.DownloadPerfReportExcel', ['user_id' => Auth::id()] + request()->all()) }}" class="text-sm bg-green-600 text-white px-4 py-2 rounded hover:bg-green-800">
        Download Excel
    </a>

    <a href="{{ route('MCMC.DownloadPerfReportPDF', ['user_id' => Auth::id()] + request()->all()) }}" class="text-sm bg-red-600 text-white px-4 py-2 rounded hover:bg-red-800">
        Download PDF
    </a>
</form>

        <table class="min-w-full table-auto border border-gray-200 mb-6">
            <thead class="bg-gradient-to-r from-blue-400 to-purple-500 text-white">
                <tr>
                    <th class="p-3 text-left">Agency</th>
                    <th class="p-3 text-center">Assigned</th>
                    <th class="p-3 text-center">Resolved</th>
                    <th class="p-3 text-center">Pending</th>
                    <th class="p-3 text-center">Delayed</th>
                    <th class="p-3 text-center">Avg. Hours to Resolve</th>
                </tr>
            </thead>
            <tbody>
                @foreach($reportData as $row)
                    <tr class="text-gray-800 border-b hover:bg-gray-50">
                        <td class="p-3">{{ $row['agency'] }}</td>
                        <td class="p-3 text-center">{{ $row['assigned'] }}</td>
                        <td class="p-3 text-center">{{ $row['resolved'] }}</td>
                        <td class="p-3 text-center">{{ $row['pending'] }}</td>
                        <td class="p-3 text-center text-red-600">{{ $row['delayed'] }}</td>
                        <td class="p-3 text-center">{{ $row['average_hours'] }}h</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <canvas id="performanceChart" class="w-full max-w-4xl mx-auto"></canvas>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('performanceChart').getContext('2d');
    const chart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($reportData->pluck('agency')) !!},
            datasets: [
                {
                    label: 'Assigned',
                    data: {!! json_encode($reportData->pluck('assigned')) !!},
                    backgroundColor: '#6366F1'
                },
                {
                    label: 'Resolved',
                    data: {!! json_encode($reportData->pluck('resolved')) !!},
                    backgroundColor: '#10B981'
                },
                {
                    label: 'Pending',
                    data: {!! json_encode($reportData->pluck('pending')) !!},
                    backgroundColor: '#F59E0B'
                },
                {
                    label: 'Delayed',
                    data: {!! json_encode($reportData->pluck('delayed')) !!},
                    backgroundColor: '#EF4444'
                }
            ]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom',
                },
                title: {
                    display: true,
                    text: 'Agency Inquiry Performance'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Number of Inquiries'
                    }
                }
            }
        }
    });
</script>
@endpush
@endsection
