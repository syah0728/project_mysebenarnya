@extends('layouts.dashboard')

@section('content')
<div class="py-12">
    <div class="bg-gradient-to-tr from-blue-100 via-purple-100 to-pink-100 rounded-2xl shadow-xl p-8">
        <div class="mb-6">
            <a href="{{ route('MCMC.AssignedInquiry', ['user_id' => Auth::id()]) }}"
            class="inline-block px-4 py-2 bg-indigo-500 text-white font-bold rounded hover:bg-indigo-700">
                ← Back to Assigned Inquiry
            </a>
        </div>
        <h2 class="text-3xl font-extrabold text-gray-800 mb-6">Inquiry Assignment Report</h2>

        <!-- Filter Form -->
        <form method="GET" action="{{ route('MCMC.InquiryAssignReport', ['user_id' => Auth::id()]) }}" class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div>
                <label for="from_date" class="block text-sm font-medium text-gray-700">From Date</label>
                <input type="date" id="from_date" name="from_date" value="{{ request('from_date') }}" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm">
            </div>
            <div>
                <label for="to_date" class="block text-sm font-medium text-gray-700">To Date</label>
                <input type="date" id="to_date" name="to_date" value="{{ request('to_date') }}" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm">
            </div>
            <div>
                <label for="agency_id" class="block text-sm font-medium text-gray-700">Agency</label>
                <select name="agency_id" id="agency_id" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm">
                    <option value="">All Agencies</option>
                    @foreach($agencies as $agency)
                        <option value="{{ $agency->id }}" {{ request('agency_id') == $agency->id ? 'selected' : '' }}>{{ $agency->user->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="self-end">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Generate Report</button>
            </div>
        </form>

        <!-- Chart -->
        <div class="mb-6">
            <canvas id="inquiryChart" height="100"></canvas>
        </div>

        <!-- Download Buttons -->
        <div class="mb-6 text-right">
            <a href="{{ route('MCMC.DownloadInquiryReportPDF', ['user_id' => Auth::id(), 'start_date' => $startDate, 'end_date' => $endDate, 'agency_id' => $agencyId]) }}"
                class="bg-red-500 hover:bg-red-600 text-white font-semibold px-4 py-2 rounded shadow">
                    Download PDF
            </a>

            <a href="{{ route('MCMC.DownloadInquiryReportExcel', ['user_id' => Auth::id(), 'start_date' => $startDate, 'end_date' => $endDate, 'agency_id' => $agencyId]) }}"
                class="bg-green-500 hover:bg-green-600 text-white font-semibold px-4 py-2 rounded shadow">
                    Download Excel
            </a>

        </div>

        <!-- Data Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full table-auto bg-white rounded-lg shadow">
                <thead class="bg-blue-500 text-white">
                    <tr>
                        <th class="px-4 py-2 text-left">Agency</th>
                        <th class="px-4 py-2 text-left">Inquiries Assigned</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($reportData as $row)
                        <tr class="border-t hover:bg-gray-50">
                            <td class="px-4 py-2">{{ $row['agency'] }}</td>
                            <td class="px-4 py-2">{{ $row['total'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('inquiryChart');
    const chart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: {!! json_encode(array_column($reportData, 'agency')) !!},
            datasets: [{
                label: 'Inquiries Assigned',
                data: {!! json_encode(array_column($reportData, 'total')) !!},
                backgroundColor: 'rgba(54, 162, 235, 0.6)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>
@endpush
