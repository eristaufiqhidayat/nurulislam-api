<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PasswordResetOtp extends Model
{
    protected $fillable = [
        'user_id',
        'otp',
        'expired_at',
        'verified_at',
    ];

    protected $dates = ['expired_at', 'verified_at'];
}
