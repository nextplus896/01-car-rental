<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Constants\CarBookingConst;
use App\Http\Helpers\Response;
use App\Http\Controllers\Controller;
use App\Models\CarBooking;

class DashboardController extends Controller
{
    public function dashboard() {

        // User Information
        $user_info = auth()->user()->only([
            'id',
            'firstname',
            'lastname',
            'fullname',
            'username',
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

        $total_amount       = CarBooking::where('user_id', auth()->user()->id)->where('status',CarBookingConst::STATUSCOMPLETE)->sum('amount');
        $round_trips        = CarBooking::whereNotNull('round_pickup_date')->where('user_id', auth()->user()->id)->count();
        $booking_rejects    = CarBooking::where('status', CarBookingConst::STATUSREJECTED)->where('user_id', auth()->user()->id)->count();
        $ride_complete      = CarBooking::where('status', CarBookingConst::STATUSCOMPLETE)->where('user_id', auth()->user()->id)->count();

        return Response::success([__('User dashboard data fetch successfully!')],[
            'instructions'  => [
                'user_info'         => [
                    'kyc_verified'  => "0: Default, 1: Approved, 2: Pending, 3:Rejected",
                ]
            ],

            'user_info'     => $user_info,
            'profile_image_paths'   => $profile_image_paths,
            'total_amount'   => $total_amount,
            'round_trips'   => $round_trips,
            'booking_rejects'   => $booking_rejects,
            'ride_complete'   => $ride_complete,
        ]);
    }

    public function notifications() {
        $notifications = get_user_notifications();
        return Response::success([__('Notification fetch successfully!')],[
            'notification'  => $notifications,
        ]);
    }
}
