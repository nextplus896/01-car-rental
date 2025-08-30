<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\User\ProfileController;
use App\Http\Controllers\Api\V1\User\AddMoneyController;
use App\Http\Controllers\Api\V1\User\CarBookingController;
use App\Http\Controllers\Api\V1\User\DashboardController;
use App\Http\Controllers\Api\V1\User\TransactionController;

Route::prefix('user')
    ->name('api.user.')
    ->group(function () {
        Route::controller(ProfileController::class)
            ->prefix('profile')
            ->group(function () {
                Route::get('info', 'profileInfo');
                Route::post('info/update', 'profileInfoUpdate')->middleware(['app.mode']);
                Route::post('password/update', 'profilePasswordUpdate')->middleware(['app.mode']);
                Route::post('delete-account','deleteProfile')->middleware(['app.mode']);
            });

        // Logout Route
        Route::post('logout', [ProfileController::class, 'logout']);

        // // Dashboard, Notification,
        Route::controller(DashboardController::class)->group(function () {
            Route::get('dashboard', 'dashboard');
            Route::get('notifications', 'notifications');
        });

        // // Transaction
        Route::controller(TransactionController::class)
            ->prefix('transaction')
            ->group(function () {
                Route::get('log', 'log');
            });

        // // car booking
        Route::controller(CarBookingController::class)->prefix('car-booking')->name('car.booking.')->group(function () {
                Route::get('booking/history', 'bookingHistory');
                Route::get('area', 'carArea');
                Route::get('type', 'carType');
                Route::post('area/types', 'getAreaTypes');
                Route::post('search/car', 'searchCar');
                Route::get('car/details', 'viewCar');
                Route::post('temp/store', 'store');
                Route::get('preview', 'preview');
                Route::post('confirm', 'confirm');
                // Automatic Gateway Response Routes
                Route::get('success/response/{gateway}', 'success')->withoutMiddleware(['auth:api'])->name('payment.success');
                Route::get('cancel/response/{gateway}', 'cancel')->withoutMiddleware(['auth:api'])->name('payment.cancel');

                // POST Route For Unauthenticated Request
                Route::post('success/response/{gateway}', 'postSuccess')
                    ->name('payment.success')
                    ->withoutMiddleware(['auth:api']);
                Route::post('cancel/response/{gateway}', 'postCancel')
                    ->name('payment.cancel')
                    ->withoutMiddleware(['auth:api']);

                //redirect with Btn Pay
                Route::get('redirect/btn/checkout/{gateway}', 'redirectBtnPay')
                    ->name('payment.btn.pay')
                    ->withoutMiddleware(['auth:api']);

                Route::get('manual/input-fields', 'manualInputFields');
                Route::get('re-manual/input-fields', 'reManualInputFields');

                // Submit with manual gateway
                Route::post('manual/submit', 'manualSubmit');

                // Automatic gateway additional fields
                Route::get('payment-gateway/additional-fields', 'gatewayAdditionalFields');

                Route::prefix('payment')
                    ->name('payment.')
                    ->group(function () {
                        Route::post('crypto/confirm/{trx_id}', 'cryptoPaymentConfirm')->name('crypto.confirm');
                    });
                Route::post('repayment/submit','repaymentSubmit')->name('repayment.submit');
            });
    });
