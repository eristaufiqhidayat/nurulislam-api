<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class UserOtp extends Model
{
    protected $table = 'user_otps';

    protected $fillable = [
        'user_id',
        'otp',
        'expired_at',
        'verified_at',
    ];

    protected $casts = [
        'expired_at' => 'datetime',
        'verified_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
