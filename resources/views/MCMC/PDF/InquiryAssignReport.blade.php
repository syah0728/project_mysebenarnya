<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Inquiry Assignment Report</title>
    <style>
        body { font-family: sans-serif; font-size: 14px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h2>Inquiry Assignment Report</h2>
    <!-- @if($startDate && $endDate)
        <p>From: {{ $startDate }} — To: {{ $endDate }}</p>
    @endif -->

    <table>
        <thead>
            <tr>
                <th>Agency</th>
                <th>Total Inquiries Assigned</th>
            </tr>
        </thead>
        <tbody>
            @foreach($reportData as $row)
                <tr>
                    <td>{{ $row['agency'] }}</td>
                    <td>{{ $row['total'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
