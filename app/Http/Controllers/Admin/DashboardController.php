<?php

namespace App\Http\Controllers\Admin;

use App\Constants\CarBookingConst;
use App\Http\Controllers\Controller;
use App\Providers\Admin\BasicSettingsProvider;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Pusher\PushNotifications\PushNotifications;
use App\Models\Admin\AdminNotification;
use App\Constants\NotificationConst;
use App\Constants\PaymentGatewayConst;
use App\Http\Helpers\Response;
use App\Models\CarBooking;
use App\Models\Transaction;
use App\Models\UserSupportTicket;
use App\Models\Vendor\Vendor;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $page_title = __("Dashboard");
        $payment_request = Transaction::where('type', PaymentGatewayConst::TYPEADDMONEY)->count();
        $payment_success_request = Transaction::where('type', PaymentGatewayConst::TYPEADDMONEY)->complete()->count();
        $payment_pending_request = Transaction::where('type', PaymentGatewayConst::TYPEADDMONEY)->pending()->count();
        $total_booking           = CarBooking::count();
        $pending_booking         = CarBooking::where('status', CarBookingConst::STATUSSUCCESS)->count();
        $reject_booking          = CarBooking::where('status', CarBookingConst::STATUSREJECTED)->count();
        $complete_booking        = CarBooking::where('status', CarBookingConst::STATUSCOMPLETE)->count();
        $solved_tickets          = UserSupportTicket::where('status', 1)->count();
        $active_tickets          = UserSupportTicket::where('status', 2)->count();
        $pending_tickets         = UserSupportTicket::where('status', 0)->count();
        $total_ticket            = $solved_tickets + $active_tickets + $pending_tickets;
        $vendors               = Vendor::count();
        $active_vendors        = Vendor::where('status', 1)->count();
        $banned_vendors        = Vendor::where('status', 0)->count();
        $unverified_vendors    = Vendor::where('email_verified', 0)->count();
        $active_users            = User::where('status', 1)->count();
        $banned_users            = User::where('status', 0)->count();
        $unverified_users        = User::where('email_verified', 0)->count();
        $users                   = User::count();
        $admin_profit            = (DB::table('admin_profits')->sum('total_charge') + DB::table('transactions')->sum('total_charge'));
        $trx_add_money = Transaction::addMoney()->get();
        $trx_money_out = Transaction::moneyOut()->get();




        //total add money by currency
        $add_money_by_currency = Transaction::addMoney()->where('status', 1)
            ->selectRaw('request_currency, SUM(request_amount) as total_request_amount')
            ->groupBy('request_currency')
            ->get();


        $add_money_success  = $trx_add_money->where('status', payment_gateway_const()::STATUSSUCCESS)->count();



        $last_month_start = Carbon::now()->subMonth()->startOfMonth()->toDateString();
        $last_month_end = Carbon::now()->subMonth()->endOfMonth()->toDateString();
        $this_month_start = Carbon::now()->startOfMonth()->toDateString();
        $this_month_end = Carbon::now()->toDateString(); // Today's date
        $this_week = Carbon::now()->subWeek()->toDateString();
        $this_month = Carbon::now()->subMonth()->toDateString();
        $this_year = Carbon::now()->subYear()->toDateString();


        // Monthly Add Money and Withdrawals
        $start = Carbon::now()->startOfMonth();
        $end = Carbon::now()->endOfMonth();


        $pending_data = [];
        $success_data = [];
        $canceled_data = [];
        $hold_data = [];
        $pending_withdraw_data = [];
        $success_withdraw_data = [];
        $canceled_withdraw_data = [];
        $hold_withdraw_data = [];
        $invest_amount_data = [];
        $month_day = [];





        // Loop through each day of the month
        while ($start->lessThanOrEqualTo($end)) {
            $start_date = $start->toDateString();

            // Monthly add money
            $pending = Transaction::where('type', PaymentGatewayConst::TYPEADDMONEY)
                ->whereDate('created_at', $start_date)
                ->where('status', 2)
                ->count();
            $success = Transaction::where('type', PaymentGatewayConst::TYPEADDMONEY)
                ->whereDate('created_at', $start_date)
                ->where('status', 1)
                ->count();
            $canceled = Transaction::where('type', PaymentGatewayConst::TYPEADDMONEY)
                ->whereDate('created_at', $start_date)
                ->where('status', 4)
                ->count();
            $hold = Transaction::where('type', PaymentGatewayConst::TYPEADDMONEY)
                ->whereDate('created_at', $start_date)
                ->where('status', 3)
                ->count();

            // Monthly withdrawals
            $pending_withdraw = Transaction::where('type', PaymentGatewayConst::TYPEWITHDRAW)
                ->whereDate('created_at', $start_date)
                ->where('status', 2)
                ->count();
            $success_withdraw = Transaction::where('type', PaymentGatewayConst::TYPEWITHDRAW)
                ->whereDate('created_at', $start_date)
                ->where('status', 1)
                ->count();
            $canceled_withdraw = Transaction::where('type', PaymentGatewayConst::TYPEWITHDRAW)
                ->whereDate('created_at', $start_date)
                ->where('status', 4)
                ->count();
            $hold_withdraw = Transaction::where('type', PaymentGatewayConst::TYPEWITHDRAW)
                ->whereDate('created_at', $start_date)
                ->where('status', 3)
                ->count();
            // Store data for each day
            $pending_data[] = $pending;
            $success_data[] = $success;
            $canceled_data[] = $canceled;
            $hold_data[] = $hold;
            $pending_withdraw_data[] = $pending_withdraw;
            $success_withdraw_data[] = $success_withdraw;
            $canceled_withdraw_data[] = $canceled_withdraw;
            $hold_withdraw_data[] = $hold_withdraw;

            $month_day[] = $start_date;

            // Move to the next day
            $start->addDay();
        }

        // Chart four | User analysis
        $chart_four_data = [$active_users, $banned_users, $unverified_users, $users];

        // Chart five | Company Growth
        $chart_five_data = [$active_vendors, $banned_vendors, $unverified_vendors, $vendors];

        // Chart one
        $chart_one_data = [
            'pending_data'  => $pending_data,
            'success_data'  => $success_data,
            'canceled_data' => $canceled_data,
            'hold_data'     => $hold_data,
        ];

        // Chart three
        $chart_three_data = [
            'pending_data'  => $pending_withdraw_data,
            'success_data'  => $success_withdraw_data,
            'canceled_data' => $canceled_withdraw_data,
            'hold_data'     => $hold_withdraw_data,
        ];


        $chartData = [
            'chart_one_data'   => $chart_one_data,
            'chart_three_data'   => $chart_three_data,
            'chart_four_data'   => $chart_four_data,
            'chart_five_data'   => $chart_five_data,
            'month_day'        => $month_day,
        ];


        $transactions = Transaction::with(
            'user:id,email,username,full_mobile,image,firstname,lastname',
            'payment_gateway:id,name',
        )->where('type', PaymentGatewayConst::TYPEWITHDRAW)->limit(3)->get();


        return view('admin.sections.dashboard.index', compact(
            'page_title',
            'payment_request',
            'payment_success_request',
            'payment_pending_request',
            'total_booking',
            'pending_booking',
            'reject_booking',
            'complete_booking',
            'active_tickets',
            'pending_tickets',
            'solved_tickets',
            'total_ticket',
            'vendors',
            'active_vendors',
            'banned_vendors',
            'unverified_vendors',
            'active_users',
            'banned_users',
            'admin_profit',
            'users',
            'transactions',
            'chartData',
            'trx_add_money',
            'trx_money_out',
        ));
    }

    /**
     * Logout Admin From Dashboard
     * @return view
     */
    public function logout(Request $request)
    {

        $admin = auth()->user();
        pusher_unsubscribe("admin", $admin->id);

        try {
            $admin->update([
                'last_logged_out'   => now(),
                'login_status'      => false,
            ]);
        } catch (Exception $e) {
            // Handle Error
        }

        Auth::guard()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }

    /**
     * Function for clear admin notification
     */
    public function notificationsClear()
    {
        $admin = auth()->user();

        if (!$admin) {
            return false;
        }

        try {
            $admin->update([
                'notification_clear_at'     => now(),
            ]);
        } catch (Exception $e) {
            $error = ['error' => [__('Something went wrong! Please try again.')]];
            return Response::error($error, null, 404);
        }

        $success = ['success' => [__('Notifications clear successfully!')]];
        return Response::success($success, null, 200);
    }
}
