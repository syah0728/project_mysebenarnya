<!DOCTYPE html>
<html>
<head>
    <title>Temporary Password</title>
</head>
<body>
    <p>Hello {{ $user->name }},</p>

    <p>You have been registered as an agency user in the MCMC system.</p>

    <p><strong>Username:</strong> {{ $user->agency->username }}</p>
    <p><strong>Temporary Password:</strong> {{ $tempPassword }}</p>

    <p>Please log in and change your password immediately.</p>

    <p>Regards,<br>MCMC Admin</p>
</body>
</html>
