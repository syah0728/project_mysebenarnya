@extends('layouts.app')

@section('content')
<div style="padding: 20px; font-family: Arial, sans-serif;">
    <h2 style="text-align: center; font-size: 24px; font-weight: bold; margin-bottom: 20px;">
        Agency Performance Report
    </h2>

    <table width="100%" style="border-collapse: collapse; font-size: 14px;">
        <thead>
            <tr style="background-color: #f0f0f0;">
                <th style="border: 1px solid #ddd; padding: 8px;">Agency</th>
                <th style="border: 1px solid #ddd; padding: 8px; text-align: center;">Assigned</th>
                <th style="border: 1px solid #ddd; padding: 8px; text-align: center;">Resolved</th>
                <th style="border: 1px solid #ddd; padding: 8px; text-align: center;">Pending</th>
                <th style="border: 1px solid #ddd; padding: 8px; text-align: center;">Delayed</th>
                <th style="border: 1px solid #ddd; padding: 8px; text-align: center;">Avg. Hours to Resolve</th>
            </tr>
        </thead>
        <tbody>
            @foreach($reportData as $row)
            <tr>
                <td style="border: 1px solid #ddd; padding: 8px;">{{ $row['agency'] }}</td>
                <td style="border: 1px solid #ddd; padding: 8px; text-align: center;">{{ $row['assigned'] }}</td>
                <td style="border: 1px solid #ddd; padding: 8px; text-align: center;">{{ $row['resolved'] }}</td>
                <td style="border: 1px solid #ddd; padding: 8px; text-align: center;">{{ $row['pending'] }}</td>
                <td style="border: 1px solid #ddd; padding: 8px; text-align: center; color: red;">{{ $row['delayed'] }}</td>
                <td style="border: 1px solid #ddd; padding: 8px; text-align: center;">{{ $row['average_hours'] }} h</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
