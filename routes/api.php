<?php

use App\Http\Controllers\API\Auth\AuthController;
use App\Http\Controllers\API\Auth\OtpAuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\Admin\RoleController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------

| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

//Authentication Routes
Route::post('mobile-login', [AuthController::class, 'TokenBasedLogin']);
Route::post('login', [AuthController::class, 'webLogin']);
Route::post('register-company-admin', [AuthController::class, 'companyAdminRegistration']);
Route::post('logout', [AuthController::class, 'logout']);
Route::post('recover-password', [AuthController::class, 'recoverPassword']);

//Registration Routes
Route::prefix('registration')->group(function () {
    Route::post('customer', [AuthController::class, 'customerRegistration']);
    Route::post('company', [AuthController::class, 'companyAdminRegistration']);
});

Route::middleware(['auth:sanctum'])->group(function () {
    //Otp authentication routes
    Route::middleware('isLoginToken')->group(function () {
        Route::post('verify-otp', [OtpAuthController::class, 'verifyOtp']);
        Route::post('resend-otp', [OtpAuthController::class, 'resendOtp']);
        Route::put('confirm-password-recovery', [AuthController::class, 'confirmPasswordRecovery']);
    });

    Route::middleware(['isVerifyOtpToken'])->group(function () {
        //Roles and permission resource
        Route::resource('roles', RoleController::class);

        //get user details
        Route::get('user/{id}', [AuthController::class, 'getUser']);
    });

    Route::get('test', function () {
        return "hello there";
    })->middleware('isLoginToken');
});