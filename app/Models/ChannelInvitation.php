<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChannelInvitation extends Model
{
    protected $fillable = [
        'channel_id',
        'invited_by',
        'invited_user_id',
        'invitation_token',
        'status',
        'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    public function channel()
    {
        return $this->belongsTo(Channel::class);
    }

    public function invitedBy()
    {
        return $this->belongsTo(User::class, 'invited_by');
    }

    public function invitedUser()
    {
        return $this->belongsTo(User::class, 'invited_user_id');
    }

    public function isExpired()
    {
        return $this->expires_at->isPast();
    }

    public function isPending()
    {
        return $this->status === 'pending' && !$this->isExpired();
    }
}
