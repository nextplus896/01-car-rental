<?php

use App\Http\Controllers\Admin\Auth\LoginController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\Auth\ForgotPasswordController;
use App\Http\Controllers\Admin\Auth\ResetPasswordController;
use App\Http\Controllers\User\Auth\ForgotPasswordController as UserForgotPasswordController;
use App\Http\Controllers\Vendor\Auth\ForgotPasswordController as VendorForgotPasswordController;
use App\Http\Controllers\User\Auth\LoginController as UserLoginController;
use App\Http\Controllers\User\Auth\RegisterController as UserRegisterController;
use App\Http\Controllers\Vendor\Auth\RegisterController as VendorRegisterController;
use App\Http\Controllers\Vendor\Auth\LoginController as VendorLoginController;
use App\Http\Controllers\User\Auth\SocialAuthentication;
use App\Http\Controllers\User\AuthorizationController;
use App\Http\Controllers\Vendor\AuthorizationController as VendorAuthorizationController;
use App\Http\Controllers\Admin\AuthorizationController as AdminAuthorizationController;

// Admin Authentication Route
Route::middleware(['guest','admin.login.guard'])->prefix('admin')->name('admin.')->group(function(){
    Route::get('/',function(){
        return redirect()->route('admin.login');
    });
    Route::get('login',[LoginController::class,"showLoginForm"])->name('login');
    Route::post('login/submit',[LoginController::class,"login"])->name('login.submit');

    Route::get('password/forgot',[ForgotPasswordController::class,"showLinkRequestForm"])->withoutMiddleware(['admin.login.guard','guest'])->name('password.forgot');
    Route::post('password/forgot',[ForgotPasswordController::class,"sendResetLinkEmail"])->withoutMiddleware(['admin.login.guard','guest'])->name('password.forgot.request');

    Route::get('password/reset/{token}',[ResetPasswordController::class,"showResetForm"])->withoutMiddleware(['admin.login.guard','guest'])->name('password.reset');
    Route::post('password/update',[ResetPasswordController::class,'reset'])->withoutMiddleware(['admin.login.guard','guest'])->name('password.update');

    Route::controller(AdminAuthorizationController::class)->prefix("authorize")->middleware(['auth:admin'])->withoutMiddleware(['admin.login.guard','guest'])->name('authorize.')->group(function(){
        Route::get('google/2fa','showGoogle2FAForm')->name('google.2fa');
        Route::post('google/2fa/submit','google2FASubmit')->name('google.2fa.submit');
    });

});

Route::name('user.')->group(function(){
    Route::get('login',[UserLoginController::class,"showLoginForm"])->name('login');
    Route::post('login',[UserLoginController::class,"login"])->name('login.submit');

    Route::get('register/{refer?}',[UserRegisterController::class,"showRegistrationForm"])->middleware(['registration.permission:user'])->name('register');
    Route::post('register',[UserRegisterController::class,"register"])->name('register.submit');

    Route::controller(UserForgotPasswordController::class)->prefix("password")->name("password.")->group(function(){
        Route::get('forgot','showForgotForm')->name('forgot');
        Route::post('forgot/send/code','sendCode')->name('forgot.send.code');
        Route::get('forgot/code/verify/form/{token}','showVerifyForm')->name('forgot.code.verify.form');
        Route::post('forgot/verify/{token}','verifyCode')->name('forgot.verify.code');
        Route::get('forgot/resend/code/{token}','resendCode')->name('forgot.resend.code');
        Route::get('forgot/reset/form/{token}','showResetForm')->name('forgot.reset.form');
        Route::post('forgot/reset/{token}','resetPassword')->name('reset');
    });

    Route::controller(SocialAuthentication::class)->prefix('oauth')->name('social.auth.')->group(function(){
        Route::get('google','google')->name('google');
        Route::get('google/response','googleResponse');
        Route::get('facebook','facebook')->name('facebook');
        Route::get('facebook/response','facebookResponse');
    });

    Route::controller(AuthorizationController::class)->prefix("authorize")->name('authorize.')->middleware("auth")->group(function(){
        Route::get('user/mail/{token}','showMailFrom')->name('mail');
        Route::post('user/mail/verify/{token}','mailVerify')->name('mail.verify');
        Route::get('kyc','showKycFrom')->name('kyc');
        Route::post('kyc/submit','kycSubmit')->name('kyc.submit');
        Route::get('google/2fa','showGoogle2FAForm')->name('google.2fa');
        Route::post('google/2fa/submit','google2FASubmit')->name('google.2fa.submit');
    });
});


Route::name('vendor.')->group(function(){
    Route::get('vendor/login',[VendorLoginController::class,"showLoginForm"])->name('login');
    Route::post('vendor/login',[VendorLoginController::class,"login"])->name('login.submit');

    Route::get('vendor/register',[VendorRegisterController::class,"showRegistrationForm"])->middleware(['registration.permission:vendor'])->name('register');
    Route::post('vendor/register',[VendorRegisterController::class,"register"])->name('register.submit');

    Route::controller(VendorForgotPasswordController::class)->prefix("password")->name("password.")->group(function(){
        Route::get('vendor/forgot','showForgotForm')->name('forgot');
        Route::post('vendor/forgot/send/code','sendCode')->name('forgot.send.code');
        Route::get('vendor/forgot/code/verify/form/{token}','showVerifyForm')->name('forgot.code.verify.form');
        Route::post('vendor/forgot/verify/{token}','verifyCode')->name('forgot.verify.code');
        Route::get('vendor/forgot/resend/code/{token}','resendCode')->name('forgot.resend.code');
        Route::get('vendor/forgot/reset/form/{token}','showResetForm')->name('forgot.reset.form');
        Route::post('vendor/forgot/reset/{token}','resetPassword')->name('reset');
    });

    // Route::controller(SocialAuthentication::class)->prefix('oauth')->name('social.auth.')->group(function(){
    //     Route::get('google','google')->name('google');
    //     Route::get('google/response','googleResponse');
    //     Route::get('facebook','facebook')->name('facebook');
    //     Route::get('facebook/response','facebookResponse');
    // });

    Route::controller(VendorAuthorizationController::class)->prefix("authorize")->name('authorize.')->middleware("auth:vendor")->group(function(){
        Route::get('mail/{token}','showMailFrom')->name('mail');
        Route::post('mail/verify/{token}','mailVerify')->name('mail.verify');
        Route::get('kyc','showKycFrom')->name('kyc');
        Route::post('kyc/submit','kycSubmit')->name('kyc.submit');
        Route::get('vendor/google/2fa','showGoogle2FAForm')->name('google.2fa');
        Route::post('vendor/google/2fa/submit','google2FASubmit')->name('google.2fa.submit');
    });
});
