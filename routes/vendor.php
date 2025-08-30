<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Vendor\KycController;
use App\Providers\Admin\BasicSettingsProvider;
use Pusher\PushNotifications\PushNotifications;
use App\Http\Controllers\Vendor\ProfileController;
use App\Http\Controllers\User\AddMoneyController;
use App\Http\Controllers\Vendor\BookingHistoryController;
use App\Http\Controllers\Vendor\BookingRequestController;
use App\Http\Controllers\Vendor\CarController;
use App\Http\Controllers\Vendor\DashboardController;
use App\Http\Controllers\Vendor\SecurityController;
use App\Http\Controllers\Vendor\SupportTicketController;
use App\Http\Controllers\Vendor\WalletController;
use App\Http\Controllers\Vendor\WithdrawalController;

Route::prefix("vendor")->name("vendor.")->middleware("auth:vendor")->group(function(){
    Route::controller(DashboardController::class)->group(function(){
        Route::get('dashboard','index')->name('dashboard.index');
        Route::post('due/pay','duePay')->name('due.pay');
        Route::post('logout','logout')->name('logout');
    });

    Route::controller(ProfileController::class)->prefix("profile")->name("profile.")->group(function(){
        Route::get('/','index')->name('index');
        Route::put('password/update','passwordUpdate')->name('password.update')->middleware(['app.mode']);
        Route::put('update','update')->name('update')->middleware(['app.mode']);
        Route::post('delete-account/{id}','deleteAccount')->name('delete')->middleware(['app.mode']);
    });

    Route::controller(SupportTicketController::class)->prefix("prefix")->name("support.ticket.")->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('create', 'create')->name('create');
        Route::post('store', 'store')->name('store');
        Route::get('conversation/{encrypt_id}','conversation')->name('conversation');
        Route::post('message/send','messageSend')->name('messaage.send');
    });

    //car section
    Route::controller(CarController::class)->middleware(['kyc.verification.guard'])->prefix('car')->name('car.')->group(function () {
        Route::get('index', 'index')->name('index');
        Route::get('create', 'create')->name('create');
        Route::post('store', 'store')->name('store');
        Route::get('edit/{id}', 'edit')->name('edit');
        Route::put('status/update', 'statusUpdate')->name('status.update');
        Route::put('update/{id}', 'update')->name('update');
        Route::delete('delete', 'delete')->name('delete');
        Route::post('get/area/types', 'getAreaTypes')->name('get.area.types');
    });

    //Booking section
    Route::controller(BookingRequestController::class)->middleware(['kyc.verification.guard'])->prefix('booking')->name('booking.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('status/accept/{id}', 'accept')->name('accept');
        Route::get('status/reject/{id}', 'reject')->name('reject');
        Route::get('status/complete/{id}', 'complete')->name('complete');
    });
    //Withdraw Money
    Route::controller(WithdrawalController::class)->middleware(['kyc.verification.guard'])->prefix('withdraw-money')->name('withdraw.money.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('submit', 'submit')->name('submit');
        Route::get('instruction/{token}', 'instruction')->name('instruction');
        Route::post('instruction/submit/{token}', 'instructionSubmit')->name('instruction.submit');
        Route::get('/logs', 'withdrawLogs')->name('logs');
    });

    Route::controller(WalletController::class)->prefix("wallets")->name("wallets.")->group(function () {
        Route::post("balance", "balance")->name("balance");
    });

    //car section
    Route::controller(BookingHistoryController::class)->prefix('history')->name('history.')->group(function () {
        Route::get('/', 'index')->name('index');
    });

    Route::controller(SecurityController::class)->prefix("security")->name('security.')->group(function(){
        Route::get('google/2fa','google2FA')->name('google.2fa');
        Route::post('google/2fa/status/update','google2FAStatusUpdate')->name('google.2fa.status.update')->middleware(['app.mode']);
    });

    Route::controller(KycController::class)->prefix('kyc')->name('kyc.')->group(function() {
        Route::get('/','index')->name('index');
        Route::get('/re-submit','reSubmit')->name('re-submit');
        Route::post('submit','store')->name('submit');
    });

});


// Route For Pusher Beams Auth
Route::get('vendor/pusher/beams-auth', function (Request $request) {
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
    $publisherUserId = make_user_id_for_pusher("vendor", $userID);
    try{
        $beamsToken = $beamsClient->generateToken($publisherUserId);
    }catch(Exception $e) {
        return response(['Server Error. Failed to generate beams token.'], 500);
    }

    return response()->json($beamsToken);
})->name('vendor.pusher.beams.auth');
