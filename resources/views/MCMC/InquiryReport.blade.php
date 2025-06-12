@extends('layouts.dashboard')
@section('content')
<div class="py-12">
    <div class="bg-gradient-to-tr from-blue-100 via-purple-100 to-pink-100 rounded-2xl shadow-xl p-8">
        <div class="mb-6">
            <a href="{{ route('MCMC.InquiryList', ['user_id' => Auth::id()]) }}"
            class="inline-block px-4 py-2 bg-indigo-500 text-white font-bold rounded hover:bg-indigo-700">
                ← Back to Inquiry List
            </a>
        </div>
        <h2 class="text-3xl font-extrabold text-gray-800 mb-6">Inquiry Report</h2>

        <!-- Filter Form -->
        <form method="GET" class="flex gap-4 mb-6">
            <div>
                <label for="month">Month</label>
                <select name="month" id="month" class="form-select mt-1">
                    <option value="">All</option>
                    @foreach(range(1,12) as $m)
                        <option value="{{ $m }}" {{ request('month') == $m ? 'selected' : '' }}>
                            {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="year">Year</label>
                <input type="number" name="year" id="year" value="{{ request('year', now()->year) }}"
                       class="form-input mt-1" placeholder="e.g., 2025">
            </div>
            <div class="self-end">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                    Filter
                </button>
            </div>
        </form>

        <!-- Chart -->
        <canvas id="inquiryChart" height="100"></canvas>

        <!-- Download Buttons -->
        <div class="mb-6 text-right">
            <a href="{{ route('MCMC.DownloadInquiryReportPDF', [
                'user_id' => Auth::id(),
                'month' => request('month'),
                'year' => request('year')
            ]) }}"
            class="bg-red-500 hover:bg-red-600 text-white font-semibold px-4 py-2 rounded shadow">
                Download PDF
            </a>

            <a href="{{ route('MCMC.DownloadInquiryReportExcel', [
                'user_id' => Auth::id(),
                'month' => request('month'),
                'year' => request('year')
            ]) }}"
            class="bg-green-500 hover:bg-green-600 text-white font-semibold px-4 py-2 rounded shadow">
                Download Excel
            </a>
        </div>


        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full table-auto bg-white rounded-lg shadow">
                <thead class="bg-blue-500 text-white">
                <thead>
                    <tr>
                        <th class="px-4 py-2 text-left">Sender</th>
                        <th class="px-4 py-2 text-left">Title</th>
                        <th class="px-4 py-2 text-left">Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($inquiries as $inq)
                        <tr>
                            <td class="px-4 py-2 text-left">{{ $inq->publicUser->name ?? 'N/A' }}</td>
                            <td class="px-4 py-2 text-left">{{ $inq->NewsTitle }}</td>
                            <td class="px-4 py-2 text-left">{{ $inq->created_at->format('d M Y') }}</td>
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
    const monthNames = {
        1:'Jan',2:'Feb',3:'Mar',4:'Apr',5:'May',6:'Jun',
        7:'Jul',8:'Aug',9:'Sep',10:'Oct',11:'Nov',12:'Dec'
    };
    const rawMonths = {!! json_encode($monthlyCounts->keys()) !!};
    const labels = rawMonths.map(m => monthNames[m]);
    const data = {!! json_encode($monthlyCounts->values()) !!};

    new Chart(document.getElementById('inquiryChart'), {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Inquiries',
                data: data,
                backgroundColor: 'rgba(54, 162, 235, 0.6)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: { beginAtZero: true }
            }
        }
    });
</script>
@endpush
