<?php

namespace App\Http\Controllers\Api\V1\Vendor;

use App\Http\Controllers\Controller;
use App\Http\Helpers\Response;
use App\Models\CarBooking;
use Illuminate\Http\Request;

class BookingHistoryController extends Controller
{
    public function view(){
        $booking_history = CarBooking::with(['cars'])->where(function ($query) {
            $query->whereHas('cars', function ($subquery) {
                $subquery->where('vendor_id', auth()->guard('vendor_api')->user()->id);
            });
        })
        ->where('status','=',3)->orWhere('status','=',4)
        ->orderByDesc("id")
        ->get();

        if(!$booking_history){
            return Response::error([__('Oops! Something went wrong! Please try again')]);
        }

        $car_image_path = [
            'base_url' => url('/'),
            'image_path' => files_asset_path_basename('site-section'),
        ];

    return Response::success([__('History fetch successfully')],[
        'booking-history' =>  $booking_history,
        'image-path' => $car_image_path,
    ]);
    }
}
