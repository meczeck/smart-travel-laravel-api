<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\CormfirmChangePasswordRequest;
use App\Http\Requests\Registration\CompanyAdminRegistrationRequest;
use App\Http\Resources\Auth\GetUserResource;
use App\Http\Resources\Auth\UserAuthenticationResource;
use App\Models\OtpCode;
use App\Models\User;
use App\Services\OtpAuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\API\Auth\OtpAuthController;

class AuthController extends Controller
{
    protected $otpAuthService;
    public function __construct(OtpAuthService $otpAuthService)
    {
        $this->otpAuthService = $otpAuthService;
    }

    public function TokenBasedLogin(Request $request)
    {
        try {
            $user = User::where('email', $request->input('email'))->first();

            if ($user && $user->hasRole('mobile-user')) {

                if (Hash::check($request->input('password'), $user->password)) {
                  
                    return $this->otpAuthService->issueOtp($user);
                }
            }

            return response()->json(['error' => 'Invalid credentials'], 401);
        } catch (\Exception $e) {
            return response([
                'error' => $e->getMessage(),
                'message' => 'something went wrong in AuthController.login'
            ], 500);
        }
    }

    public function webLogin(Request $request)
    {
        try {
            $credentials = $request->validate([
                'email' => ['required', 'email'],
                'password' => ['required'],
            ]);

            $user = User::where('email', $credentials['email'])->first();

            if ($user) {
                if (Auth::attempt($credentials)) {
                    $request->session()->regenerate();

                    $otpVerification = $this->otpAuthService->generateOtp($user);
                    $this->otpAuthService->sendOtp($otpVerification->otp, $user->email, 'email');

                    $response = ['user' => $user, 'token' => '', 'expires_at' => $otpVerification->expire_at, 'otp' => $otpVerification->otp];
                    return new UserAuthenticationResource($response);
                }
            }

            return response()->json(['error' => 'Invalid credentials'], 401);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'message' => 'Something went wrong in AuthController.webLogin'
            ]);
        }

    }

    public function companyAdminRegistration(CompanyAdminRegistrationRequest $request)
    {
        $role = ['dashboard-user', 'company-admin'];
        return $this->otpAuthService->registrationHandler($request, $role);
    }

    public function customerRegistration(CompanyAdminRegistrationRequest $request)
    {
        $role = 'mobile-user';
        return $this->otpAuthService->registrationHandler($request, $role);
    }

    public function recoverPassword(Request $request)
    {

        $request->validate([
            'recovery_input' => 'required'
        ]);

        $userViaEmail = User::where('email', $request->input('recovery_input'))->first();
        $userViaPhone = User::where('phone', $request->input('recovery_input'))->first();

        if ($userViaEmail) {
            $verification = $this->otpAuthService->generateOtp($userViaEmail);
            $this->otpAuthService->sendOtp($verification->otp, $userViaEmail->email, 'email');
            return response()->json(["message" => "OTP sent via your email address", 'otp' => $verification->otp, 'otp_expires_at' => $verification->expire_at, 'token' => $userViaEmail->createToken('login-token')->plainTextToken], 200);
        }

        if ($userViaPhone) {
            $verification = $this->otpAuthService->generateOtp($userViaPhone);
            $this->otpAuthService->sendOtp($verification->otp, $userViaPhone->phone, 'phone');
            return response()->json(["message" => "OTP sent via your Phone Number", 'otp' => $verification->otp, 'otp_expires_at' => $verification->expire_at, 'token' => $userViaPhone->createToken('login-token')->plainTextToken], 200);
        }

        return response()->json(['error' => 'Provide valid information'], 403);
    }

    public function confirmPasswordRecovery(CormfirmChangePasswordRequest $request)
    {
        $data = $request->validated();
        $user = User::where('email', $data['email'])->first();

        if ($user) {
            $otpCode = OtpCode::where('otp', $data['otp'])->where('user_id', $user->id)->first();

            $verifyOtp = $this->otpAuthService->otpVerificationHandler($otpCode, $user);

            if (is_array($verifyOtp)) {
                $user->password = Hash::make($data['new_password']);
                $user->save();

                return response()->json(['message' => "Password changed successfully", 'token' => $verifyOtp['token']], 200);
            } else {
                return response()->json(['error' => 'Invalid OTP Code'], 403);
            }
        }
    }

    public function getUser($id) {
        $user = User::find($id);
        $response = ['user' => $user, 'permissions' => $user->getAllPermissions()];
    
        return new GetUserResource($response);
    }

    public function logout(Request $request)
    {
        $user = User::findOrFail($request->input('user_id'));
        $user->otp_verification = false;
        $user->save();

        $user->tokens()->delete();

        return response()->json(['message' => 'Logout successfully'], 200);
    }
}