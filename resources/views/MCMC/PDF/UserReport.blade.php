<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>User Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #333;
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #999;
            padding: 8px 10px;
            text-align: center;
        }
        th {
            background-color: #f0f0f0;
        }
    </style>
</head>
<body>
    <h2>User Report</h2>
    <table>
        <thead>
            <tr>
                <th>No.</th>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <!-- <th>Agency</th> -->
                <th>Registered At</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($users as $index => $user)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->role }}</td>
                    <!-- <td>
                        @if($user->role == 'Agency')
                            {{ optional($user->agency)->name ?? 'N/A' }}
                        @elseif($user->role == 'MCMC')
                            MCMC
                        @else
                            -
                        @endif
                    </td> -->
                    <td>{{ $user->created_at ? $user->created_at->format('d M Y') : 'N/A' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
