<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use App\Models\OtpCode;
use App\Models\User;
use App\Services\OtpAuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class OtpAuthController extends Controller
{

    protected $otpAuthService;
    public function __construct(OtpAuthService $otpAuthService)
    {
        $this->otpAuthService = $otpAuthService;
    }

    public function resendOtp(Request $request)
    {
        try {
            //validate the request
            $request->validate([
                'user_id' => 'required|string'
            ]);

            $user = User::find($request->input('user_id'));

            if ($user) {

                $verificationCode = $this->otpAuthService->generateOtp($user->id);
                $this->otpAuthService->sendOtp($verificationCode->otp, $user, 'both');

                return response()->json(['message' => 'OTP Code successfully sent', 'otp' => $verificationCode->otp, 'opt_expires_at' => $verificationCode->expire_at], 200);
            }
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'message' => 'something went wrong in OtpAuthController.generateOtp'
            ], 500);
        }
    }
    public function verifyOtp(Request $request)
    {
        try {
            //validate the otp code
            $request->validate([
                'otp' => "required|numeric|digits:6",
                'user_id' => 'required|string'
            ]);

            $otpCode = OtpCode::where('otp', $request->input('otp'))->where('user_id', $request->input('user_id'))->first();
            $user = User::find($request->input('user_id'));

            return $this->otpAuthService->otpVerificationHandler($otpCode, $user);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'message' => 'something went wrong in OtpAuthController.verifyOtp'
            ], 500);
        }
    }
}