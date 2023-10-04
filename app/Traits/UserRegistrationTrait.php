<?php

namespace App\Traits;

use App\Models\User;
use App\Services\OtpAuthService;
use Illuminate\Support\Facades\Hash;


trait UserRegistrationTrait
{
    protected $otpAuthService;
    public function __construct(OtpAuthService $otpAuthService) {
        $this->otpAuthService = $otpAuthService;
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
            return $this->otpAuthService->issueOtp($user);
        } catch (\Exception $e) {
            return response([
                'error' => $e->getMessage(),
                'message' => 'something went wrong in OtpAuthService.registrationHandler'
            ], 500);
        }
    }
}


