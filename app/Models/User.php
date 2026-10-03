<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use App\Models\UserOtp;
use App\Models\Role;

class User extends Authenticatable implements JWTSubject
{
    // ...
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'email_verified_at',
        'no_hp', // Contoh kolom tambahan untuk nomor HP
        // Tambahkan kolom lain jika ada (contoh: 'phone')
    ];
    protected $hidden = [
        'password',
        'remember_token',
    ];
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
    public function role()
    {
        return $this->belongsTo(Role::class);
    }
    public function otp()
    {
        return $this->hasOne(UserOtp::class);
    }
}
