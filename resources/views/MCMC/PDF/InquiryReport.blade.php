<!DOCTYPE html>
<html>
<head>
    <title>Inquiry Statistics Report</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 6px; text-align: left; }
        th { background-color: #f0f0f0; }
    </style>
</head>
<body>
    <h2>Inquiry Statistics Report</h2>

    <p>
        <strong>Filtered by:</strong>
        @if($month && $year)
            {{ \Carbon\Carbon::create()->month($month)->format('F') }} {{ $year }}

        @elseif($year)
            Year: {{ $year }}
        @else
            All Records
        @endif
    </p>

    @if($inquiries->count() > 0)
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Status</th>
                    <th>Submitted By</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                @foreach($inquiries as $inquiry)
                <tr>
                    <td>{{ $inquiry->id }}</td>
                    <td>{{ $inquiry->NewsTitle }}</td>
                    <td>{{ $inquiry->InquiryStatus }}</td>
                    <td>{{ $inquiry->publicUser->name ?? 'N/A' }}</td>
                    <td>{{ $inquiry->created_at->format('d M Y') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p>No inquiries found for the selected filter.</p>
    @endif
</body>
</html>
