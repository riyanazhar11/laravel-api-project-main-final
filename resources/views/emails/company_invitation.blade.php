<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Company Invitation</title>
</head>
<body>
    <div style="max-width: 600px; margin: 0 auto; padding: 20px; font-family: Arial, sans-serif;">
        <h2 style="color: #333;">You've been invited to join {{ $invitation->company->name }}</h2>
        
        <p>Hello {{ $invitation->name }},</p>
        
        <p>You have been invited to join <strong>{{ $invitation->company->name }}</strong> as an employee.</p>
        
        <p>To accept this invitation, please click the link below:</p>
        
        <p style="text-align: center; margin: 30px 0;">
            <a href="{{ $acceptUrl }}" 
               style="color: #007bff; text-decoration: underline; font-size: 16px;">
                {{ $acceptUrl }}
            </a>
        </p>
        
        <p><strong>Note:</strong> This invitation will expire on {{ $invitation->expires_at->format('F j, Y \a\t g:i A') }}.</p>
        
        <p>If you don't want to accept this invitation, you can simply ignore this email.</p>
        
        <hr style="margin: 30px 0; border: none; border-top: 1px solid #eee;">
        
        <p style="color: #666; font-size: 12px;">
            This invitation was sent by {{ $invitation->company->owner->name }}.
        </p>
    </div>
</body>
</html>
