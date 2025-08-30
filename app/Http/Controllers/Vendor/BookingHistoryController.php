<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\CarBooking;
use Illuminate\Http\Request;

class BookingHistoryController extends Controller
{
    public function index(){
        $page_title = __('History');
        $car_bookings = CarBooking::whereNot('status',1)->whereHas('cars', function ($subquery) {
            $subquery->where('vendor_id', auth()->guard()->user()->id);
        })
        ->orderByDesc("id")
        ->paginate(10);

        return view('vendor-end.sections.booking.booking-history',compact(
            'page_title',
            'car_bookings',
        ));
    }
}
