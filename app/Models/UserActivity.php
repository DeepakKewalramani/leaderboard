<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserActivity extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'date',
        'points',
    ];

    public const ACTIVITY_TYPES = ['Running', 'Walking', 'Cycling', 'Swimming', 'Yoga'];


    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
