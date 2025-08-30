<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\User\Auth\LoginController;
use App\Http\Controllers\Api\V1\User\Auth\RegisterController;
use App\Http\Controllers\Api\V1\User\Auth\AuthorizationController;
use App\Http\Controllers\Api\V1\User\Auth\ForgotPasswordController;
use App\Http\Controllers\Api\V1\Vendor\Auth\RegisterController as VendorRegisterController;
use App\Http\Controllers\Api\V1\Vendor\Auth\LoginController as VendorLoginController;
use App\Http\Controllers\Api\V1\Vendor\Auth\AuthorizationController as VendorAuthorizationController;
use App\Http\Controllers\Api\V1\Vendor\Auth\ForgotPasswordController as VendorForgotPasswordController;

// User Auth Routes
Route::middleware(['api.user.auth.guard'])->group(function(){
    Route::controller(RegisterController::class)->group(function() {
        Route::post("register","register");
    });

    Route::controller(LoginController::class)->group(function(){
        Route::post("login","login");
    });

    // Forget password routes
    Route::controller(ForgotPasswordController::class)->prefix("password/forgot")->group(function(){
        Route::post('find/user','findUserSendCode');
        Route::post('verify/code','verifyCode');
        Route::get('resend/code','resendCode');
        Route::post('reset','resetPassword');
    });

});

Route::controller(AuthorizationController::class)->prefix("authorize")->middleware(['auth:api'])->group(function(){
    // Mail
    Route::prefix("mail")->group(function(){
        Route::get("send/code","sendCodeToMail");
        Route::get("resend/code","resendCodeToMail");
        Route::post("verify/code","verifyMailCode");
    });

});


// Vendor Auth Routes
Route::middleware(['api.vendor.auth.guard'])->prefix("vendor")->name('vendor')->group(function(){
    Route::controller(VendorRegisterController::class)->group(function() {
        Route::post("register","register");
    });

    Route::controller(VendorLoginController::class)->group(function(){
        Route::post("login","login");
    });

    // Forget password routes
    Route::controller(VendorForgotPasswordController::class)->prefix("password/forgot")->group(function(){
        Route::post('find/user','findUserSendCode');
        Route::post('verify/code','verifyCode');
        Route::get('resend/code','resendCode');
        Route::post('reset','resetPassword');
    });

});

Route::controller(VendorAuthorizationController::class)->prefix("vendor-authorize")->name('vendor.authorize')->middleware(['auth:vendor_api'])->group(function(){
    // Mail
    Route::prefix("mail")->group(function(){
        Route::get("send/code","sendCodeToMail");
        Route::get("resend/code","resendCodeToMail");
        Route::post("verify/code","verifyMailCode");
    });

    // Kyc
    Route::prefix("kyc")->group(function(){
        Route::get('input-fields','getKycInputFields');
        Route::post('submit','KycSubmit');
    });

    // google 2FA
    Route::prefix("google/2fa")->group(function(){
        Route::get("status", "get2FaStatus");
        Route::post('status-update', 'google2FAStatusUpdate')->middleware(['app.mode']);
        Route::post('verify', 'verifyGoogle2Fa');
    });

});
