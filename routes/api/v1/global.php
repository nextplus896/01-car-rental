<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Providers\Admin\BasicSettingsProvider;
use Pusher\PushNotifications\PushNotifications;
use App\Http\Controllers\Api\V1\User\SettingController;

// Settings
Route::controller(SettingController::class)->prefix("settings")->group(function(){
    Route::get("basic-settings","basicSettings");
    Route::get("splash-screen","splashScreen");
    Route::get("onboard-screens","onboardScreens");
    Route::get("languages","getLanguages");
    Route::get("countries","getCountries");
});
