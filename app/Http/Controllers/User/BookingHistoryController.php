<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\CarBooking;
use Illuminate\Http\Request;

class BookingHistoryController extends Controller
{
    public function index(){
        $page_title = __('History');
        $car_bookings = CarBooking::with(['cars'])->where('user_id',auth()->user()->id)
        ->orderByDesc("id")
        ->paginate(10);

        return view('user.sections.car-booking.booking-history',compact(
            'page_title',
            'car_bookings',
        ));
    }
}
