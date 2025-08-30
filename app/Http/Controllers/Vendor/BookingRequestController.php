<?php

namespace App\Http\Controllers\Vendor;

use App\Constants\CarBookingConst;
use App\Constants\NotificationConst;
use App\Constants\PaymentGatewayConst;
use App\Http\Controllers\Controller;
use App\Http\Helpers\PushNotificationHelper;
use App\Models\Admin\BasicSettings;
use App\Models\Admin\TransactionSetting;
use App\Models\CarBooking;
use App\Models\UserNotification;
use App\Notifications\User\rideComplete;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Stripe\Balance;
use Illuminate\Support\Str;

class BookingRequestController extends Controller
{
    public function index()
    {
        $page_title = __('Booking Request');
        $car_bookings = CarBooking::with(['cars'])
            ->where('status', '!=', 3)
            ->Where('status', '!=', 4)
            ->where(function ($query) {
                $query->whereHas('cars', function ($subquery) {
                    $subquery->where('vendor_id', '=', auth()->guard('vendor')->user()->id);
                });
            })
            ->orderByDesc('id')
            ->paginate(7);

        return view('vendor-end.sections.booking.booking-request', compact('car_bookings', 'page_title'));
    }

    public function accept($id)
    {
        $charges = TransactionSetting::where('slug','cash')->first();
        $max_limit = $charges->max_limit;

        $info = CarBooking::where('id', $id)->first();
        if($max_limit <= $info->cars->vendor->wallets->due_payment){
            return back()->with(['warning' => [__('Please pay your due amount')]]);
        }

        $basic_setting = BasicSettings::first();
        try {
            $info->update(['status' => 2]);
            $notification_content = [
                'title'   => "Request Accepted",
                'message' => "Vendor accepted your request",
                'time'    => Carbon::now()->diffForHumans(),
                'image'   => files_asset_path('profile-default'),
            ];
            UserNotification::create([
                'type'    => NotificationConst::REQUEST_ACCEPT,
                'user_id' => $info->user_id,
                'message' => $notification_content,
            ]);

            try {
                if ($basic_setting->push_notification) {
                    (new PushNotificationHelper())
                        ->prepare(
                            [$info->user_id],
                            [
                                'title' => $notification_content['title'],
                                'desc' => $notification_content['message'],
                                'user_type' => 'user',
                            ],
                        )
                        ->send();
                }
            } catch (Exception $e) { }
        } catch (Exception $e) {
            return back()->with(['error' => [__('Oops! Something went wrong! Please try again')]]);
        }
        return back()->with(['success' => [__('Request Accepted Successfully')]]);
    }

    public function reject($id)
    {
        $booking_info = CarBooking::where('id', $id)->first();
        $basic_setting = BasicSettings::first();
        try {

            if ($booking_info->payment_type == Str::slug(CarBookingConst::ONLINE_PAYMENT) && $booking_info->transaction->status == PaymentGatewayConst::STATUSSUCCESS) {
                $booking_info->transaction->update([
                    'refundable' => PaymentGatewayConst::STATUSPENDING,
                ]);
            };
            $booking_info->update(['status' => 4]);
            $notification_content = [
                'title'   => "Request Rejected",
                'message' => "Vendor rejected your request",
                'time'    => Carbon::now()->diffForHumans(),
                'image'   => files_asset_path('profile-default'),
            ];
            UserNotification::create([
                'type'    => NotificationConst::REQUEST_REJECTED,
                'user_id' => $booking_info->user_id,
                'message' => $notification_content,
            ]);

            try {
                if ($basic_setting->push_notification) {
                    (new PushNotificationHelper())
                        ->prepare(
                            [$booking_info->user_id],
                            [
                                'title' => $notification_content['title'],
                                'desc' => $notification_content['message'],
                                'user_type' => 'user',
                            ],
                        )
                        ->send();
                }
            } catch (Exception $e) {
            }
        } catch (Exception $e) {
            return back()->with(['danger' => [__('Oops! Something went wrong! Please try again')]]);
        }

        return back()->with(['danger' => [__('Request Rejected')]]);
    }

    public function complete($id)
    {
        $info = CarBooking::where('id', $id)->first();
        $basic_setting = BasicSettings::first();

        if (!$info) {
            return back()->with(['danger' => [__('Oops! Something went wrong! Please try again')]]);
        }
        try {
            if ($info->payment_type == Str::slug(CarBookingConst::ONLINE_PAYMENT) && $info->transaction->status == PaymentGatewayConst::STATUSSUCCESS) {
                $wallet = auth()->guard('vendor')->user()->wallets;
                if (!$wallet) {
                    return back()->with(['danger' => [__("Vendor wallet couldn't found")]]);
                }

                if($wallet->due_payment != 0 && $wallet->due_payment == $info->transaction->receive_amount)
                {
                    $charge = $wallet->due_payment;
                    $this->insertProfit($charge);
                    $wallet->update([
                        'due_payment' => 0,
                    ]);
                }
                elseif($wallet->due_payment != 0 && $wallet->due_payment < $info->transaction->receive_amount)
                {
                    $charge = $wallet->due_payment;
                    $this->insertProfit($charge);
                    $wallet->update([
                        'balance' => $wallet->balance + ($info->transaction->receive_amount - $wallet->due_payment),
                        'due_payment' => 0,
                    ]);
                }
                elseif($wallet->due_payment != 0 && $wallet->due_payment > $info->transaction->receive_amount)
                {
                    $charge = $info->transaction->receive_amount;
                    $this->insertProfit($charge);
                    $wallet->update([
                        'due_payment' => $wallet->due_payment - $info->transaction->receive_amount,
                    ]);
                }
                elseif($wallet->due_payment == 0)
                {
                    $wallet->update([
                        'balance' => $wallet->balance + $info->transaction->receive_amount,
                    ]);
                }
            }

            if ($info->payment_type == Str::slug(CarBookingConst::CASH)) {
                $wallet = auth()->guard('vendor')->user()->wallets;
                if (!$wallet) {
                    return back()->with(['danger' => [__("Vendor wallet couldn't found")]]);
                }
                $wallet->update([
                    'due_payment' => $wallet->due_payment + $info->charges,
                ]);



            }

            $info->update([
                'status' => CarBookingConst::STATUSCOMPLETE,
            ]);
            $notification_content = [
                'title'   => "Ride Complete",
                'message' => "You have completed your ride",
                'time'    => Carbon::now()->diffForHumans(),
                'image'   => files_asset_path('profile-default'),
            ];
            UserNotification::create([
                'type'    => NotificationConst::RIDE_COMPLETE,
                'user_id' => $info->user_id,
                'message' => $notification_content,
            ]);
            try {
                if ($basic_setting->push_notification) {
                    (new PushNotificationHelper())
                        ->prepare(
                            [$info->user_id],
                            [
                                'title' => $notification_content['title'],
                                'desc' => $notification_content['message'],
                                'user_type' => 'user',
                            ],
                        )
                        ->send();
                }

                if ($basic_setting->email_notification) {

                    Notification::route('mail', $info->email)->notify(new rideComplete($info));
                }

            } catch (Exception $e) {
            }
        }catch (Exception $e) {
            return back()->with(['danger' => [__('Oops! Something went wrong! Please try again')]]);
        }
        return back()->with(['success' => [__('Tour Complete')]]);
    }


    public function insertProfit($charge) {
        DB::beginTransaction();
        try{
            DB::table('admin_profits')->insert([
                'percent_charge'    => 0,
                'fixed_charge'      => 0,
                'total_charge'      => $charge,
                'created_at'        => now(),
            ]);
            DB::commit();
        }catch(Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage());
        }
    }
}
