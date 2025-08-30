<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Helpers\Response;
use App\Models\CarBooking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BookingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $page_title = __("All Logs");
        $bookings = CarBooking::paginate(20);
        return view('admin.sections.booking.index',compact(
            'page_title',
            'bookings',
        ));
    }

    /**
     * Display All Pending Logs
     * @return view
     */
    public function pending() {
        $page_title = __("Pending Logs");
        $bookings = CarBooking::where('status', 1)->paginate(20);
        return view('admin.sections.booking.index',compact(
            'page_title',
            'bookings',
        ));
    }


    /**
     * Display All Complete Logs
     * @return view
     */
    public function complete() {
        $page_title = __("Complete Logs");
        $bookings = CarBooking::where('status', 3)->paginate(20);
        return view('admin.sections.booking.index',compact(
            'page_title',
            'bookings'
        ));
    }


    /**
     * Display All Canceled Logs
     * @return view
     */
    public function canceled() {
        $page_title = __("Canceled Logs");
        $bookings = CarBooking::where('status', 4)->paginate(20);
        return view('admin.sections.booking.index',compact(
            'page_title',
            'bookings'
        ));
    }

    public function search(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'text' => 'required|string',
        ]);
        if ($validator->fails()) {
            $error = ['error' => $validator->errors()];
            return Response::error($error, null, 400);
        }
        $validated = $validator->validate();
        $bookings = CarBooking::where('trip_id', 'like', '%' . $validated['text'] . '%')
        ->latest()
        ->paginate(20);
        return view('admin.components.search.car-booking.booking-search', compact('bookings'));
    }

    public function bookingDetails($trip_id){
        $page_title = __("Booking Details");
        $booking = CarBooking::where('trip_id',$trip_id)->first();

        return view('admin.sections.cars.car-booking.view',compact(
            'page_title',
            'booking'
        ));
    }
}
