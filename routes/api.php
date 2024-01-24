<?php

use App\Http\Controllers\API\Auth\AuthController;
use App\Http\Controllers\API\Auth\OtpAuthController;
use App\Http\Controllers\API\BusCompany\BusCompanyAgentController;
use App\Http\Controllers\API\BusCompany\BusCompanyController;
use App\Http\Controllers\API\BusCompany\Location\PlaceController;
use App\Http\Controllers\API\User\ProfileController;
use App\Http\Controllers\API\BusCompany\RouteController;
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

//Authentication Routes
Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout']);
Route::post('recover-password', [AuthController::class, 'recoverPassword']);

//Registration Routes
Route::prefix('registration')->group(function () {
    Route::post('customer', [AuthController::class, 'customerRegistration']);
    Route::post('company', [AuthController::class, 'companyAdminRegistration']);
    Route::post('company-admin', [AuthController::class, 'companyAdminRegistration']);
});

Route::middleware(['auth:sanctum'])->group(function () {
    //Otp authentication routes
    Route::middleware('isLoginToken')->group(function () {
        Route::post('verify-otp', [OtpAuthController::class, 'verifyOtp']);
        Route::post('resend-otp', [OtpAuthController::class, 'resendOtp']);
        Route::put('confirm-password-recovery', [AuthController::class, 'confirmPasswordRecovery']);
    });

    Route::middleware(['isVerifyOtpToken'])->group(function () {
        //User profile informations
        Route::get('user/{id}', [ProfileController::class, 'show']);
        Route::put('update-profile/{id}', [ProfileController::class, 'updateProfile']);
        Route::put('user/{id}', [ProfileController::class, 'changePassword']);

        //Roles and permission resource
        Route::resource('roles', RoleController::class)->middleware(['role:super-admin']);

        Route::prefix('company-registration')->middleware(['role:super-admin|company-registrars'])->group(function () {
            Route::get('verify/{id}', [BusCompanyController::class, 'verifyCompanyReg']);
            Route::get('disprove/{id}', [BusCompanyController::class, 'disproveCompanyReg']);
        });

        Route::get('bus-companies/{id}', [BusCompanyController::class, 'getSingleCompany'])->middleware(['role:company-admin']);
        Route::resource('bus-companies', BusCompanyController::class)->middleware('role:dashboard-user')->middleware(['role:company-admin']);
        Route::get('bus-companies', [BusCompanyController::class, 'index'])->middleware(['role:super-admin']);


        //Bus Company agents
        Route::resource('company-agents', BusCompanyAgentController::class)->middleware(['role:compy-agents-management']);

        //regions and districts routes
        Route::get('regions', [PlaceController::class, 'getAllRegions']);
        Route::get('districts/{id}', [PlaceController::class, 'getDistrictsByRegion']);

        //Company Routes
        Route::get('company-routes/{id}', [RouteController::class, 'getCompanyRoutes']);
        Route::resource('routes', RouteController::class);
    });
});