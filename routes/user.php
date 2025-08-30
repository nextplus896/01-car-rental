<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\KycController;
use App\Providers\Admin\BasicSettingsProvider;
use Pusher\PushNotifications\PushNotifications;
use App\Http\Controllers\User\ProfileController;
use App\Http\Controllers\User\AddMoneyController;
use App\Http\Controllers\User\BookingHistoryController;
use App\Http\Controllers\User\CarBookingController;
use App\Http\Controllers\User\SecurityController;
use App\Http\Controllers\User\DashboardController;
use App\Http\Controllers\User\SupportTicketController;

Route::prefix("user")->name("user.")->group(function(){
    Route::controller(DashboardController::class)->group(function(){
        Route::get('dashboard','index')->name('dashboard');
        Route::post('logout','logout')->name('logout');
    });

    Route::controller(ProfileController::class)->prefix("profile")->name("profile.")->group(function(){
        Route::get('/','index')->name('index');
        Route::put('password/update','passwordUpdate')->name('password.update')->middleware(['app.mode']);
        Route::put('update','update')->name('update')->middleware(['app.mode']);
        Route::post('delete-account/{id}','deleteAccount')->name('delete')->middleware(['app.mode']);
    });

    // car booking
    Route::controller(CarBookingController::class)->name('car.booking.')->group(function(){
        Route::get('/car-booking/{slug}','booking')->name('index');
        Route::post('store','store')->name('store');
        Route::get('preview/{token}/{id}','preview')->name('preview');
        Route::post('confirm/{token}','confirm')->name('confirm');
        Route::get('mail/{token}', 'showMailForm')->name('mail');
        Route::post('mail/verify/{token}','mailVerify')->name('mail.verify');
        Route::get('mail/resend/{token}', 'mailResendToken')->name('mail.resend');
        Route::get('success/response/{gateway}','success')->name('payment.success');
        Route::get("cancel/response/{gateway}",'cancel')->name('payment.cancel');

        // POST Route For Unauthenticated Request
        Route::post('cancel/response/{gateway}','postCancel')->name('payment.cancel')->withoutMiddleware(['auth','verification.guard','user.google.two.factor']);
        Route::post('success/response/{gateway}','postSuccess')->name('payment.success')->withoutMiddleware(['auth','verification.guard','user.google.two.factor']);

        Route::post("callback/response/{gateway}",'callback')->name('payment.callback')->withoutMiddleware(['web','auth','verification.guard','user.google.two.factor']);
        // redirect with HTML form route
        Route::get('redirect/form/{gateway}', 'redirectUsingHTMLForm')->name('payment.redirect.form')->withoutMiddleware(['auth','verification.guard','user.google.two.factor']);

        //redirect with Btn Pay
        Route::get('redirect/btn/checkout/{gateway}', 'redirectBtnPay')->name('payment.btn.pay')->withoutMiddleware(['auth','verification.guard','user.google.two.factor']);

        Route::get('manual/{token}','showManualForm')->name('manual.form');
        Route::post('manual/submit/{token}','manualSubmit')->name('manual.submit');

        Route::prefix('payment')->name('payment.')->group(function() {
            Route::get('crypto/address/{trx_id}','cryptoPaymentAddress')->name('crypto.address');
            Route::post('crypto/confirm/{trx_id}','cryptoPaymentConfirm')->name('crypto.confirm');
        });

        Route::get('repayment/{id}','showRepaymentForm')->name('repayment.form');
        Route::post('repayment/submit/{id}','repaymentSubmit')->name('repayment.submit');


    });

    Route::controller(SupportTicketController::class)->prefix("prefix")->name("support.ticket.")->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('create', 'create')->name('create');
        Route::post('store', 'store')->name('store');
        Route::get('conversation/{encrypt_id}','conversation')->name('conversation');
        Route::post('message/send','messageSend')->name('messaage.send');
    });

    Route::controller(SecurityController::class)->prefix("security")->name('security.')->group(function(){
        Route::get('google/2fa','google2FA')->name('google.2fa');
        Route::post('google/2fa/status/update','google2FAStatusUpdate')->name('google.2fa.status.update')->middleware(['app.mode']);
    });

    Route::controller(KycController::class)->prefix('kyc')->name('kyc.')->group(function() {
        Route::get('/','index')->name('index');
        Route::post('submit','store')->name('submit');
    });

    Route::controller(BookingHistoryController::class)->prefix('history')->name('history.')->group(function () {
        Route::get('index', 'index')->name('index');
    });

});


// Route For Pusher Beams Auth
Route::get('user/pusher/beams-auth', function (Request $request) {
    if(Auth::check() == false) {
        return response(['Inconsistent request'], 401);
    }
    $userID = Auth::user()->id;

    $basic_settings = BasicSettingsProvider::get();
    if(!$basic_settings) {
        return response('Basic setting not found!', 404);
    }

    $notification_config = $basic_settings->push_notification_config;

    if(!$notification_config) {
        return response('Notification configuration not found!', 404);
    }

    $instance_id    = $notification_config->instance_id ?? null;
    $primary_key    = $notification_config->primary_key ?? null;
    if($instance_id == null || $primary_key == null) {
        return response('Sorry! You have to configure first to send push notification.', 404);
    }
    $beamsClient = new PushNotifications(
        array(
            "instanceId" => $notification_config->instance_id,
            "secretKey" => $notification_config->primary_key,
        )
    );
    $publisherUserId =  make_user_id_for_pusher("user", $userID);
    try{
        $beamsToken = $beamsClient->generateToken($publisherUserId);
    }catch(Exception $e) {
        return response(['Server Error. Failed to generate beams token.'], 500);
    }

    return response()->json($beamsToken);
})->name('user.pusher.beams.auth');
