<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Channel extends Model
{
    protected $fillable = [
        'company_id',
        'created_by',
        'name',
        'description',
        'type',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function members()
    {
        return $this->hasMany(ChannelMember::class);
    }

    public function invitations()
    {
        return $this->hasMany(ChannelInvitation::class);
    }

    public function isPublic()
    {
        return $this->type === 'public';
    }

    public function isPrivate()
    {
        return $this->type === 'private';
    }

    public function isVisibleTo(User $user)
    {
        // Public channels are visible to all company employees
        if ($this->isPublic()) {
            return $user->company_id === $this->company_id;
        }

        // Private channels are only visible to creator and company owner
        if ($this->isPrivate()) {
            return $user->id === $this->created_by || 
                   $user->id === $this->company->owner_id;
        }

        return false;
    }

    public function canJoin(User $user)
    {
        // User must be in the same company
        if ($user->company_id !== $this->company_id) {
            return false;
        }

        // User must be visible to the channel
        return $this->isVisibleTo($user);
    }
}
