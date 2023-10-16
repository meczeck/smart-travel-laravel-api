<?php
namespace App\Services;

use App\Http\Resources\Auth\UserAuthenticationResource;
use App\Mail\OtpVerificationEmail;
use App\Mail\Registration\SendOtpMail;
use App\Models\OtpCode;
use App\Models\User;
use Exception;
use Hash;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;

class OtpAuthService
{
    //Issue otp
    public function issueOtp($user)
    {
        try {

            $user = $user::findOrFail($user->id);

            # Generate An OTP
            $verificationCode = $this->generateOtp($user->id);

            $this->sendOtp($verificationCode->otp, $user->phone, 'email');

            if ($verificationCode) {
                $userToken = $user->createToken('login-token')->plainTextToken;
                $response = ['user' => $user, 'token' => $userToken, 'otp_expires_at' => $verificationCode->expires_at, 'otp' => $verificationCode->otp, 'permissions' => $user->getAllPermissions()];
                return new UserAuthenticationResource($response);
            }

        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'message' => 'something went wrong in OtpAuthController.generateOtp'
            ], 500);
        }
    }
    public function generateOtp($userId)
    {
        try {

            # User Does not Have Any Existing OTP
            $verificationCode = OtpCode::where('user_id', $userId)->latest()->first();

            //Get the current time
            $now = Carbon::now();

            //Check if the otp is existed and if not expired yet
            if ($verificationCode) {
                if ($now->isBefore($verificationCode->expires_at)) {
                    return $verificationCode;
                }
            }

            OtpCode::create([
                'user_id' => $userId,
                'otp' => rand(123456, 999999),
                'expires_at' => $now->addMinutes(10)
            ]);

            return OtpCode::where('user_id', $userId)->latest()->first();
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'message' => 'something went wrong in OtpAuthController.generateOtp'
            ], 500);
        }
    }

    public function sendOtp($otp, $recipient, $sendMethod = 'email')
    {
        try {
            if ($sendMethod == 'email') {
                $this->sendOtpViaEmail($otp, $recipient);
            } elseif ($sendMethod == 'phone') {
                //Send otp via sms
                $this->sendOtpViaSms($otp, $recipient);
            } else {
                //Send otp via both sms and email notification
                $this->sendOtpViaEmail($otp, $recipient->email);
                $this->sendOtpViaSms($otp, $recipient->phone);
            }

        } catch (\Exception $error) {
            return response()->json([
                'error' => $error->getMessage(),
                'message' => "wrong in sendOtp method"
            ]);
        }
    }

    public function sendOtpViaSms($otp, $recipient)
    {
        try {
            Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . env('SMS_GATEWAY_TOKEN')
            ])->post('https://dev.hudumasms.com/api/send-sms', [
                        'sender_id' => env('SMS_SENDER_ID'),
                        'sms' => "Your verification code is $otp, you can use it within 10 minutes",
                        'schedule' => 'none',
                        'recipients' => array(['number' => $recipient])
                    ]);
        } catch (Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'message' => "something went wrong in OtpAuthService.sendOtpViaSms"
            ]);
        }
    }

    public function sendOtpViaEmail($otp, $recipient)
    {
        try {
            // Mail::to($recipient)->send(new OtpVerificationEmail($otp));
            Mail::to($recipient)->send(new SendOtpMail($otp));
        } catch (Exception $e) {
            return response()->json([
                "error" => $e->getMessage(),
                "message" => "Send email failed, try again"
            ], 501);
        }
    }

    public function otpVerificationHandler($otpCode, $user)
    {
        if ($otpCode && $user) {

            $userInstance = User::findOrFail($user->id);

            $now = Carbon::now();

            if ($now->isBefore($otpCode->expires_at)) {

                $userInstance->otp_verification = 1;
                $userInstance->save();

                $userInstance->tokens()->delete();

                $token = $userInstance->createToken('verify-otp-token')->plainTextToken;

                return ['message' => 'OPT verified successfully', 'token' => $token, 'isOtpVerified' => $userInstance->otp_verification];
            } else {
                return response()->json(['errors' => 'OTP Code Expired'], 401);
            }
        }

        return response()->json(['errors' => 'OTP Code is invalid'], 401);
    }

}