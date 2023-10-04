<?php
namespace App\Services;

use App\Http\Resources\Auth\UserAuthenticationResource;
use App\Mail\OtpVerificationEmail;
use App\Models\OtpCode;
use App\Models\User;
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
            $verificationCode = $this->generateOtp($user);

            $this->sendOtp($verificationCode->otp, $user->email, 'email');

            if ($verificationCode) {
                $userToken = $user->createToken('login-token')->plainTextToken;
                $response = ['user' => $user, 'token' => $userToken, 'otp_expires_at' => $verificationCode->expire_at, 'otp' => $verificationCode->otp, 'permissions' => $user->getAllPermissions()];
                return new UserAuthenticationResource($response);
            }

        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'message' => 'something went wrong in OtpAuthController.generateOtp'
            ], 500);
        }
    }
    public function generateOtp($user)
    {
        try {

            # User Does not Have Any Existing OTP
            $verificationCode = OtpCode::where('user_id', $user->id)->latest()->first();

            //Get the current time
            $now = Carbon::now();

            //Check if the otp is existed and if not expired yet
            if ($verificationCode) {
                if ($now->isBefore($verificationCode->expire_at)) {
                    return $verificationCode;
                }
            }

            OtpCode::create([
                'user_id' => $user->id,
                'otp' => rand(123456, 999999),
                'expire_at' => $now->addMinutes(10)
            ]);

            return OtpCode::where('user_id', $user->id)->latest()->first();
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
                try {
                    // Mail::to($recipient)->send(new OtpVerificationEmail($otp));
                } catch (\Exception $e) {
                    return response()->json([
                        "error" => $e->getMessage(),
                        "message" => "Send email failed, try again"
                    ], 501);
                }
            } else {
                //Send otp via sms
                $response = Http::withHeaders([
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                    'Authorization' => 'Bearer ' . env('SMS_GATEWAY_TOKEN')
                ])->post('https://dev.hudumasms.com/api/send-sms', [
                            'sender_id' => env('SMS_SENDER_ID'),
                            'sms' => "Your verification code is $otp, you can use it within 10 minutes",
                            'schedule' => 'none',
                            'recipients' => array(['number' => $recipient])
                        ]);
            }

        } catch (\Exception $error) {
            return response()->json([
                'error' => $error->getMessage(),
                'message' => "wrong in sendOtp method"
            ]);
        }
    }

    public function otpVerificationHandler($otpCode, $user)
    {
        if ($otpCode && $user) {

            $userInstance = User::findOrFail($user->id);

            $now = Carbon::now();

            if ($now->isBefore($otpCode->expire_at)) {

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
    public function registrationHandler($request, $role)
    {
        try {
            $user = new User();
            $user->email = $request->input('email');
            $user->phone = $request->input('phone');
            $user->name = $request->input('name');
            $user->registration_verification = $role == 'mobile-user' ? 1 : 0;
            $user->status = $role == 'mobile-user' ? 1 : 0;
            $user->password = Hash::make($request->input('password'));

            $user->save();

            //Give permission to either mobile or dashboard to the system users
            $user->assignRole($role);

            //Returning the method called from the service above
            return $this->issueOtp($user);
        } catch (\Exception $e) {
            return response([
                'error' => $e->getMessage(),
                'message' => 'something went wrong in OtpAuthService.registrationHandler'
            ], 500);
        }
    }


}