<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\Vendor\ProfileController;
use App\Http\Controllers\Api\V1\User\AddMoneyController;
use App\Http\Controllers\Api\V1\Vendor\DashboardController;
use App\Http\Controllers\Api\V1\User\TransactionController;
use App\Http\Controllers\Api\V1\Vendor\BookingController;
use App\Http\Controllers\Api\V1\Vendor\BookingHistoryController;
use App\Http\Controllers\Api\V1\Vendor\CarController;
use App\Http\Controllers\Api\V1\Vendor\WithdrawController;

Route::prefix('vendor')->name('api.vendor.')->group(function () {
        Route::controller(ProfileController::class)->prefix('profile')->group(function () {
                Route::get('info', 'profileInfo');
                Route::post('info/update', 'profileInfoUpdate')->middleware(['app.mode']);
                Route::post('password/update', 'profilePasswordUpdate')->middleware(['app.mode']);
                Route::post('delete-account','deleteProfile')->middleware(['app.mode']);
            });

        // Logout Route
        Route::post('logout', [ProfileController::class, 'logout']);

        // Dashboard, Notification,
        Route::controller(DashboardController::class)->group(function () {
            Route::get('dashboard', 'dashboard');
            Route::get('notifications', 'notifications');
            Route::post('due/pay','duePay');
        });

        // Transaction
        Route::controller(TransactionController::class)->prefix('transaction')->group(function () {
            Route::get('log', 'log');
        });

        // Car routes
        Route::controller(CarController::class)->middleware(['kyc.verification.guard'])->prefix('car')->name("car")->group(function () {
            Route::get('area', 'carArea');
            Route::get('type', 'carType');
            Route::post('area/types', 'getAreaTypes');
            Route::post('store', 'store');
            Route::post('status', 'statusUpdate');
            Route::post('details', 'details');
            Route::post('update', 'update');
            Route::post('delete', 'delete');
        });

        // Booking routes
        Route::controller(BookingController::class)->middleware(['kyc.verification.guard'])->prefix('booking')->name("booking")->group(function () {
            Route::get('requests', 'bookings');
            Route::get('accept', 'accept');
            Route::get('reject', 'reject');
            Route::get('complete', 'complete');
        });

        // Booking History routes
        Route::controller(BookingHistoryController::class)->prefix('history')->name("history")->group(function () {
            Route::get('view', 'view');
        });

        //Withdraw Money Routes
        Route::controller(WithdrawController::class)->middleware(['kyc.verification.guard'])->prefix("withdraw")->name('withdraw.')->group(function () {
            Route::get("wallet-gateways", "walletGateways");
            Route::get("gateway/input-fields", "gatewayInputFields");
            Route::post("submit", "submit");
        });

});
