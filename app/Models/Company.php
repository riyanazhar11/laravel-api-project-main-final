<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $fillable = ['name', 'owner_id'];

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function employees()
    {
        return $this->hasMany(User::class);
    }

    public function invitations()
    {
        return $this->hasMany(CompanyInvitation::class);
    }

    public function channels()
    {
        return $this->hasMany(Channel::class);
    }
}

