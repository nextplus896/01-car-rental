<?php

namespace App\Http\Controllers\Api\V1\Vendor;

use App\Constants\CarBookingConst;
use Illuminate\Http\Request;
use App\Constants\GlobalConst;
use App\Constants\NotificationConst;
use App\Http\Helpers\Response;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Constants\PaymentGatewayConst;
use App\Http\Helpers\PushNotificationHelper;
use App\Models\Admin\AdminNotification;
use App\Models\CarBooking;
use App\Models\Vendor\Cars\Car;
use App\Models\Vendor\VendorNotification;
use App\Models\Vendor\VendorWallet;
use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;

class DashboardController extends Controller
{
    public function dashboard() {

        $wallets_balance = auth()->guard('vendor_api')->user()->wallets->balance;
        $wallets_currency = auth()->guard('vendor_api')->user()->wallets->currency;
        $due_payment = auth()->guard('vendor_api')->user()->wallets->due_payment;

        // // User Information
        $user_info = auth()->user()->only([
            'id',
            'firstname',
            'lastname',
            'fullname',
            'username',
            'address',
            'email',
            'image',
            'mobile_code',
            'mobile',
            'full_mobile',
            'email_verified',
            'kyc_verified',
            'two_factor_verified',
            'two_factor_status',
            'two_factor_secret',
        ]);

        $profile_image_paths = [
            'base_url'          => secure_url("/"),
            'path_location'     => files_asset_path_basename("user-profile"),
            'default_image'     => files_asset_path_basename("profile-default"),
        ];


        $round_trips = CarBooking::whereNotNull('round_pickup_date')
            ->whereHas('cars', function ($query) {
                $query->where('vendor_id', auth()->guard('vendor_api')->user()->id);
            })
            ->count();
        $booking_rejects = CarBooking::where('status', CarBookingConst::STATUSREJECTED)
            ->whereHas('cars', function ($query) {
                $query->where('vendor_id', auth()->guard('vendor_api')->user()->id);
            })
            ->count();
        $ride_complete = CarBooking::where('status', CarBookingConst::STATUSCOMPLETE)
            ->whereHas('cars', function ($query) {
                $query->where('vendor_id', auth()->guard('vendor_api')->user()->id);
            })
            ->count();
        $bookings =  CarBooking::with(['cars'])
            ->where('status', '!=', 3)
            ->Where('status', '!=', 4)
            ->where(function ($query) {
                $query->whereHas('cars', function ($subquery) {
                    $subquery->where('vendor_id', '=', auth()->guard('vendor_api')->user()->id);
                });
            })
            ->orderByDesc('id')
            ->get();

            if(!$bookings){
                return Response::error([__('Oops! Something went wrong! Please try again')]);
            }

            $my_cars = Car::where('vendor_id',auth()->guard('vendor_api')->user()->id)->get();

            $car_image_path = [
                'base_url' => secure_url('/'),
                'image_path' => files_asset_path_basename('site-section'),
            ];

        $month = Carbon::now()->month;
        $start_of_month = Carbon::parse(now()->startOfMonth());
        $end_of_month = Carbon::parse(now()->endOfMonth());

        $start_month = $start_of_month->copy();
        $end_month = $end_of_month;

        $booking_accept = [];
        $booking_complete = [];
        $booking_reject = [];
        $month_day = [];

        while ($start_month->lte($end_month)) {
            $start_date = $start_month->toDateString(); // Get date in Y-m-d format

            $booking_accepted = CarBooking::whereHas('cars', function ($query) {
                $query->where('vendor_id', auth()->guard('vendor_api')->user()->id);
            })
                ->whereDate('created_at', $start_date)
                ->where('status', CarBookingConst::STATUONGOING)
                ->count();

            $booking_rejected = CarBooking::whereHas('cars', function ($query) {
                $query->where('vendor_id', auth()->guard('vendor_api')->user()->id);
            })
                ->whereDate('created_at', $start_date)
                ->where('status', CarBookingConst::STATUSREJECTED)
                ->count();

            $booking_completed = CarBooking::whereHas('cars', function ($query) {
                $query->where('vendor_id', auth()->guard('vendor_api')->user()->id);
            })
                ->whereDate('created_at', $start_date)
                ->where('status', CarBookingConst::STATUSCOMPLETE)
                ->count();

            $booking_accept[] = $booking_accepted;
            $booking_complete[] = $booking_completed;
            $booking_reject[] = $booking_rejected;
            $month_day[] = $start_date;

            $start_month->addDay(); // Move to next day
        }

        return Response::success([__('Vendor dashboard data fetch successfully!')],[

            'user_info'     => $user_info,
            'wallets'       => $wallets_balance,
            'wallets_currency' => $wallets_currency->code,
            'due_payment'      => $due_payment,
            'profile_image_paths'   => $profile_image_paths,

            'round_trips'       => $round_trips,
            'booking_rejects'   => $booking_rejects,
            'ride_complete'     => $ride_complete,
            'bookings'          => $bookings,
            'my_cars'          => $my_cars,
            'car_image_path'    => $car_image_path,
            'booking_accept'    => $booking_accept,
            'booking_complete'  => $booking_complete,
            'booking_reject'   => $booking_reject,
            'month_day'         => $month_day,

        ]);
    }

    public function notifications() {
        $notifications = get_vendor_notifications();
        return Response::success([__('Notification fetch successfully!')],[
            'notification'  => $notifications,
        ]);
    }

    public function duePay(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'amount'    => "required|numeric",
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            return Response::error($errors);
        }

        $validated = $validator->validate();
        $user_wallet = VendorWallet::where('vendor_id',auth()->guard('vendor_api')->user()->id)->first();

        if(!$user_wallet) return Response::error([__("Vendor wallet not found!")]);

        DB::beginTransaction();

        try{

            if ($user_wallet->balance < $validated['amount'] || $user_wallet->due_payment == 0) {
                $message = $user_wallet->due_payment == 0 ? 'Insufficient Due Payment' : '';
                $message = $user_wallet->balance < $validated['amount'] ? 'Insufficient Balance' : '';
                $message = $user_wallet->due_payment < $validated['amount'] ? 'Insufficient Due Payment' : '';

                return back()->with(['error' => [__($message)]]);
            }

            if($user_wallet->due_payment < $validated['amount'] || $user_wallet->due_payment == $validated['amount']){
                $user_wallet_balance = 0;
                $user_wallet->balance -= $user_wallet->due_payment;
                $user_wallet->due_payment = 0;
            }
            else{
                $user_wallet_balance = 0;
                $user_wallet->balance -= $validated['amount'];
                $user_wallet->due_payment -= $validated['amount'];

            }



            $inserted_id = DB::table("transactions")->insertGetId([
                'admin_id'          => auth()->user()->id,
                'vendor_id'         => $user_wallet->vendor->id,
                'wallet_id'         => $user_wallet->id,
                'type'              => PaymentGatewayConst::TYPEDUEPAY,
                'trx_id'            => generate_unique_string("transactions","trx_id",16),
                'request_amount'    => $validated['amount'],
                'total_charge'      => $validated['amount'],
                'available_balance' => $user_wallet_balance,
                'status'            => GlobalConst::SUCCESS,
                'request_currency'  => $user_wallet->currency->code,
                'created_at'        => now(),
            ]);

            DB::table('admin_profits')->insert([
                'percent_charge'    => 0,
                'fixed_charge'      => 0,
                'total_charge'      => $validated['amount'],
                'created_at'        => now(),
            ]);


            $user_wallet->save();

            $notification_content = [
                'title'         => "Paid Due Amount",
                'message'       => "You have successfully paid due payment",
                'time'          => Carbon::now()->diffForHumans(),
                'image'         => files_asset_path('profile-default'),
            ];

            VendorNotification::create([
                'type'      => NotificationConst::BALANCE_UPDATE,
                'vendor_id'  => $user_wallet->vendor->id,
                'message'   => $notification_content,
            ]);
             //push notification
           try{
                (new PushNotificationHelper())->prepare([$user_wallet->vendor->id],[
                    'title' => $notification_content['title'],
                    'desc'  => $notification_content['message'],
                    'user_type' => 'user',
                ])->send();
            }catch(Exception $e) {}


            //admin notification
             $notification_content['title'] = $user_wallet->vendor->username."'s  paid his/her due amount ". $validated['amount'] ??"";
            AdminNotification::create([
                'type'      => NotificationConst::BALANCE_UPDATE,
                'admin_id'  => 1,
                'message'   => $notification_content,
            ]);
            DB::commit();
        }catch(Exception $e) {
            DB::rollBack();
            return Response::error([__("Transaction Failed!")]);
        }

        return Response::success([__("Transaction success")]);
    }
}
