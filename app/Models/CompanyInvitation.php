<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyInvitation extends Model
{
    protected $fillable = [
        'company_id',
        'email',
        'name',
        'invitation_token',
        'status',
        'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
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
