<?php

namespace App\Http\Controllers\Vendor;

use App\Constants\CarBookingConst;
use App\Constants\GlobalConst;
use App\Constants\NotificationConst;
use App\Constants\PaymentGatewayConst;
use App\Http\Controllers\Controller;
use App\Http\Helpers\PushNotificationHelper;
use App\Models\Admin\AdminNotification;
use App\Models\CarBooking;
use App\Models\Transaction;
use App\Models\Vendor\VendorNotification;
use App\Models\Vendor\VendorWallet;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class DashboardController extends Controller
{
    public function index()
    {
        $page_title = __('Dashboard');
        $breadcrumb = __('Dashboard');
        $round_trips = CarBooking::whereNotNull('round_pickup_date')
            ->whereHas('cars', function ($query) {
                $query->where('vendor_id', auth()->guard('vendor')->user()->id);
            })
            ->count();
        $booking_rejects = CarBooking::where('status', CarBookingConst::STATUSREJECTED)
            ->whereHas('cars', function ($query) {
                $query->where('vendor_id', auth()->guard('vendor')->user()->id);
            })
            ->count();
        $ride_complete = CarBooking::where('status', CarBookingConst::STATUSCOMPLETE)
            ->whereHas('cars', function ($query) {
                $query->where('vendor_id', auth()->guard('vendor')->user()->id);
            })
            ->count();
        $bookings = CarBooking::whereHas('cars', function ($query) {
            $query->where('vendor_id', auth()->guard('vendor')->user()->id);
        })
            ->limit(5)
            ->latest()
            ->get();

        $total_income = CarBooking::whereHas('cars', function ($query) {
                $query->where('vendor_id', auth()->guard('vendor')->user()->id);
        })
        ->sum('amount');

        $chart_one = [];

        $this_year_start = Carbon::now()->startOfYear();
        $this_year_end = Carbon::now()->endOfYear();

        $start = $this_year_start->copy();
        $end = $this_year_end;

        $booking_data = [];
        $months = [];

        while ($start->lessThanOrEqualTo($end)) {
            $start_of_month = $start->copy()->startOfMonth();
            $end_of_month = $start->copy()->endOfMonth();

            $booking = Transaction::vendorAuth()
                ->where('type', PaymentGatewayConst::TYPEWITHDRAW)
                ->where('status', PaymentGatewayConst::STATUSSUCCESS)
                ->whereBetween('created_at', [$start_of_month, $end_of_month])
                ->count();

            $booking_data[] = $booking;
            $months[] = $start->format('F');

            $start->addMonth();
        }
        $year = Carbon::now()->year;

        $chart_one_data = [
            'booking_data' => $booking_data,
            'month' => $months,
            'year' => $year,
        ];
        $chartData = [
            'chart_one_data' => $chart_one_data,
        ];

        // End of booking chart

        // Starting of withdraw chart

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
                $query->where('vendor_id', auth()->guard('vendor')->user()->id);
            })
                ->whereDate('created_at', $start_date)
                ->where('status', CarBookingConst::STATUONGOING)
                ->count();

            $booking_rejected = CarBooking::whereHas('cars', function ($query) {
                $query->where('vendor_id', auth()->guard('vendor')->user()->id);
            })
                ->whereDate('created_at', $start_date)
                ->where('status', CarBookingConst::STATUSREJECTED)
                ->count();

            $booking_completed = CarBooking::whereHas('cars', function ($query) {
                $query->where('vendor_id', auth()->guard('vendor')->user()->id);
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

        return view('vendor-end.sections.dashboard.index', compact('page_title', 'breadcrumb', 'round_trips', 'booking_rejects', 'bookings', 'ride_complete','total_income','chartData', 'booking_accept', 'booking_complete', 'booking_reject', 'month_day'));
    }

    public function duePay(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput()->with('modal', 'due-balance-update-modal');
        }

        $validated = $validator->validate();
        $user_wallet = VendorWallet::where('vendor_id', auth()->user()->id)->first();
        if (!$user_wallet) {
            return back()->with(['error' => [__('Vendor wallet not found!')]]);
        }

        DB::beginTransaction();

        try {
            if ($user_wallet->balance < $validated['amount'] || $user_wallet->due_payment == 0) {
                $message = $user_wallet->due_payment == 0 ? 'Insufficient Due Payment' : '';
                $message = $user_wallet->balance < $validated['amount'] ? 'Insufficient Balance' : '';
                $message = $user_wallet->due_payment < $validated['amount'] ? 'Insufficient Due Payment' : '';

                return back()->with(['error' => [__($message)]]);
            }

            $paid = 0;

            if($user_wallet->due_payment < $validated['amount'] || $user_wallet->due_payment == $validated['amount']){
                $user_wallet_balance = 0;
                $paid = $user_wallet->due_payment;
                $user_wallet->balance -= $user_wallet->due_payment;
                $user_wallet->due_payment = 0;
            }
            else{
                $user_wallet_balance = 0;
                $paid = $validated['amount'];
                $user_wallet->balance -= $validated['amount'];
                $user_wallet->due_payment -= $validated['amount'];
            }

            $inserted_id = DB::table('transactions')->insertGetId([
                'admin_id' => auth()->user()->id,
                'vendor_id' => $user_wallet->vendor->id,
                'wallet_id' => $user_wallet->id,
                'type' => PaymentGatewayConst::TYPEDUEPAY,
                'trx_id' => generate_unique_string('transactions', 'trx_id', 16),
                'request_amount' => $paid,
                'total_charge' => 0,
                'available_balance' => $user_wallet_balance,
                'status' => GlobalConst::SUCCESS,
                'request_currency' => $user_wallet->currency->code,
                'created_at' => now(),
            ]);

            DB::table('admin_profits')->insert([
                'percent_charge' => 0,
                'fixed_charge' => 0,
                'total_charge' => $paid,
                'created_at' => now(),
            ]);

            $user_wallet->save();

            $notification_content = [
                'title' => 'Paid Due Amount',
                'message' => 'You have successfully paid due payment',
                'time' => Carbon::now()->diffForHumans(),
                'image' => files_asset_path('profile-default'),
            ];

            VendorNotification::create([
                'type' => NotificationConst::BALANCE_UPDATE,
                'vendor_id' => $user_wallet->vendor->id,
                'message' => $notification_content,
            ]);
            //push notification
            try {
                (new PushNotificationHelper())
                    ->prepare(
                        [$user_wallet->vendor->id],
                        [
                            'title' => $notification_content['title'],
                            'desc' => $notification_content['message'],
                            'user_type' => 'user',
                        ],
                    )
                    ->send();
            } catch (Exception $e) {
            }

            //admin notification
            $notification_content['title'] = $user_wallet->vendor->username . "'s  paid his/her due amount " . $paid ?? '';
            AdminNotification::create([
                'type' => NotificationConst::BALANCE_UPDATE,
                'admin_id' => 1,
                'message' => $notification_content,
            ]);
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return back()->with(['error' => [__('Transaction Failed!')]]);
        }

        return back()->with(['success' => [__('Transaction success')]]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('vendor.login');
    }
}
