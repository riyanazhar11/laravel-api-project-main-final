<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Your Account Credentials</title>
</head>
<body>
    <div style="max-width: 600px; margin: 0 auto; padding: 20px; font-family: Arial, sans-serif;">
        <h2 style="color: #333;">Your Account Has Been Created</h2>
        
        <p>Hello {{ $user->name }},</p>
        
        <p>Your account has been successfully created and you are now an employee of <strong>{{ $user->company->name }}</strong>.</p>
        
        <div style="background-color: #f8f9fa; padding: 20px; border-radius: 5px; margin: 20px 0;">
            <h3 style="margin-top: 0; color: #495057;">Your Login Credentials:</h3>
            <p><strong>Email:</strong> {{ $user->email }}</p>
            <p><strong>Password:</strong> {{ $password }}</p>
        </div>
        
        <p><strong>Important:</strong> Please change your password after your first login for security purposes.</p>
        
        <p>You can now log in to your account using these credentials.</p>
        
        <hr style="margin: 30px 0; border: none; border-top: 1px solid #eee;">
        
        <p style="color: #666; font-size: 12px;">
            If you did not expect this email, please contact your company administrator.
        </p>
    </div>
</body>
</html>
