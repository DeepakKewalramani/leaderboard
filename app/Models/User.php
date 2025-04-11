<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'full_name',
    ];

    public function activities()
    {
        return $this->hasMany(UserActivity::class);
    }

    public function points()
    {
        return $this->hasOne(UserPoint::class);
    }
}
