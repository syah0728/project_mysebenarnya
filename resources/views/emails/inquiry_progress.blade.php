<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Inquiry Progress Update</title>
</head>
<body>
    <h2>Hello {{ $progress->inquiry->publicUser->user->name ?? 'User' }},</h2>
    <p>Your inquiry titled <strong>"{{ $progress->inquiry->NewsTitle }}"</strong> has a new update.</p>
    <p><strong>Status:</strong> {{ $progress->ProgressStatus }}</p>
    <p><strong>Comment:</strong> {{ $progress->ProgressDescription ?? 'No comments.' }}</p>
    <p><strong>Reviewed by:</strong> {{ $progress->ReviewingOfficer }}</p>
    <p><strong>Updated at:</strong> {{ $progress->created_at->format('d M Y h:i A') }}</p>

    <!-- <p><a href="{{ url('/my-inquiries') }}">Click here to view it</a></p> -->

    <p>Thank you,<br>Your Team</p>
</body>
</html>
