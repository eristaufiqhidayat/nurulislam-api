<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ForgotPasswordService;

class ForgotPasswordController extends Controller
{
    public function __construct(
        protected ForgotPasswordService $service
    ) {}

    public function sendOtp(Request $r)
    {
        $r->validate(['email' => 'required|email']);
        $this->service->sendOtp($r->email);
        return response()->json(['message' => 'OTP dikirim']);
    }

    public function verifyOtp(Request $r)
    {
        $r->validate([
            'email' => 'required|email',
            'otp' => 'required'
        ]);

        $this->service->verifyOtp($r->email, $r->otp);
        return response()->json(['message' => 'OTP valid']);
    }

    public function resetPassword(Request $r)
    {
        $r->validate([
            'email' => 'required|email',
            'password' => 'required|min:6'
        ]);

        $this->service->resetPassword($r->email, $r->password);
        return response()->json(['message' => 'Password berhasil diubah']);
    }
}
