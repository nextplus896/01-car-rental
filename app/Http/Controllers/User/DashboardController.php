<?php

namespace App\Http\Controllers\User;

use App\Constants\CarBookingConst;
use App\Http\Controllers\Controller;
use App\Models\CarBooking;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $page_title = "Dashboard";
        $breadcrumb = __("Dashboard");
        $total_booking      = CarBooking::where('user_id', auth()->user()->id)->where('status',CarBookingConst::STATUSCOMPLETE)->count();
        $round_trips        = CarBooking::whereNotNull('round_pickup_date')->where('user_id', auth()->user()->id)->count();
        $booking_rejects    = CarBooking::where('status', CarBookingConst::STATUSREJECTED)->where('user_id', auth()->user()->id)->count();
        $ride_complete      = CarBooking::where('status', CarBookingConst::STATUSCOMPLETE)->where('user_id', auth()->user()->id)->count();
        $bookings           = CarBooking::where('user_id', auth()->user()->id)->limit(5)->get();

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

            $booking = CarBooking::where('user_id',auth()->user()->id)
                ->whereBetween('created_at', [$start_of_month, $end_of_month])
                ->whereNot('status', 4)
                ->count();

            $booking_data[] = $booking;
            $months[] = $start->format('F');

            // Move to the next month
            $start->addMonth();
        }
        $year = Carbon::now()->year;


        $chart_one_data = [
            'booking_data' => $booking_data,
            'month'        => $months,
            'year'         => $year,
        ];
        $chartData = [
            'chart_one_data'    => $chart_one_data,
        ];
        
        return view('user.dashboard', compact("page_title", "breadcrumb","total_booking","round_trips", "booking_rejects", "bookings", "ride_complete","chartData"));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('user.login');
    }
}
