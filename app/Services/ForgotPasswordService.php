<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\PasswordResetRepository;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class ForgotPasswordService
{
    public function __construct(
        protected PasswordResetRepository $repo
    ) {}

    public function sendOtp(string $email): void
    {
        Log::info("REQUEST EMAIL: " . $email);

        $user = User::where('email', $email)->first();

        Log::info("USER RESULT:", ['user' => $user]);

        if ($user) {
            $otp = rand(100000, 999999);

            Log::info("OTP GENERATED: " . $otp);

            $this->repo->createOrUpdate($user, (string) $otp);

            Mail::raw(
                "Kode OTP reset password Anda: {$otp}\nBerlaku 10 menit.",
                function ($m) use ($user) {
                    $m->to($user->email)
                        ->subject('Reset Password');
                }
            );

            Log::info("EMAIL SENT TO: " . $user->email);
        } else {
            Log::warning("USER NOT FOUND");
        }
    }

    public function verifyOtp(string $email, string $otp): User
    {
        $user = User::where('email', $email)->firstOrFail();
        $record = $this->repo->findValid($user, $otp);

        if (!$record) {
            throw new \Exception('OTP tidak valid');
        }

        $this->repo->verify($record);
        return $user;
    }

    public function resetPassword(string $email, string $password): void
    {
        $user = User::where('email', $email)->firstOrFail();
        $user->update([
            'password' => Hash::make($password)
        ]);
    }
}
