<!DOCTYPE html>
<html>
<head>
    <title>Welcome</title>
</head>
<body>
    <h2>Hello {{ $user->name }},</h2>
    <p>Welcome to the company! Your account has been created successfully.</p>
    <p>You can login with your email: <strong>{{ $user->email }}</strong></p>
    <p>Your temporary password is: <strong>{{ $password }}</strong></p>
    <p>We recommend changing it after logging in for the first time.</p>
</body>
</html>
