<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Channel Invitation</title>
</head>
<body>
    <div style="max-width: 600px; margin: 0 auto; padding: 20px; font-family: Arial, sans-serif;">
        <h2 style="color: #333;">You've been invited to join {{ $invitation->channel->name }}</h2>
        
        <p>Hello {{ $invitation->invitedUser->name }},</p>
        
        <p>You have been invited to join the channel <strong>{{ $invitation->channel->name }}</strong> in {{ $invitation->channel->company->name }}.</p>
        
        <div style="background-color: #f8f9fa; padding: 15px; border-radius: 5px; margin: 20px 0;">
            <h3 style="margin-top: 0; color: #495057;">Channel Details:</h3>
            <p><strong>Name:</strong> {{ $invitation->channel->name }}</p>
            <p><strong>Type:</strong> {{ ucfirst($invitation->channel->type) }}</p>
            @if($invitation->channel->description)
                <p><strong>Description:</strong> {{ $invitation->channel->description }}</p>
            @endif
            <p><strong>Created by:</strong> {{ $invitation->invitedBy->name }}</p>
        </div>
        
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
            This invitation was sent by {{ $invitation->invitedBy->name }}.
        </p>
    </div>
</body>
</html>
