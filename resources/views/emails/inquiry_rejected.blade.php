<!DOCTYPE html>
<html>
<head>
    <title>Inquiry Rejected</title>
</head>
<body>
    <h2>Inquiry Rejected by Agency</h2>
    <p><strong>Inquiry ID:</strong> {{ $inquiry->id }}</p>
    <p><strong>Inquiry Title:</strong> {{ $inquiry->NewsTitle }}</p>
    <!-- <p><strong>Agency:</strong> {{ $inquiry->agency->user->name ?? 'Unknown Agency' }}</p> -->
    <p><strong>Title:</strong> {{ $inquiry->NewsTitle }}</p>
    <p><strong>Reason for Rejection:</strong> {{ $reason }}</p>
    <p>Please review and reassign if necessary.</p>
</body>
</html>
