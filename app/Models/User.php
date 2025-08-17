<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'email_verification_token',
        'email_verification_token_expires_at',
        'email_verified_at',
        'api_token',
        'company_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'email_verification_token_expires_at' => 'datetime',
        ];
    }

    public function apiTokens()
    {
        return $this->hasMany(ApiToken::class);
    }

    public function isEmailVerified()
    {
        return !is_null($this->email_verified_at);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function channelMemberships()
    {
        return $this->hasMany(ChannelMember::class);
    }

    public function channels()
    {
        return $this->belongsToMany(Channel::class, 'channel_members');
    }

    public function createdChannels()
    {
        return $this->hasMany(Channel::class, 'created_by');
    }

    public function channelInvitations()
    {
        return $this->hasMany(ChannelInvitation::class, 'invited_user_id');
    }
}
