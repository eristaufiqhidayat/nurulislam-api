<?php

namespace App\Repositories;

use App\Models\User;
use App\Models\PasswordResetOtp;

class PasswordResetRepository
{
    public function createOrUpdate(User $user, string $otp): PasswordResetOtp
    {
        return PasswordResetOtp::updateOrCreate(
            ['user_id' => $user->id],
            [
                'otp' => $otp,
                'expired_at' => now()->addMinutes(10),
                'verified_at' => null,
            ]
        );
    }

    public function findValid(User $user, string $otp): ?PasswordResetOtp
    {
        return PasswordResetOtp::where('user_id', $user->id)
            ->where('otp', $otp)
            ->whereNull('verified_at')
            ->where('expired_at', '>', now())
            ->first();
    }

    public function verify(PasswordResetOtp $otp): void
    {
        $otp->update(['verified_at' => now()]);
    }
}
