<?php

namespace Database\Seeders\Admin;

use Illuminate\Database\Seeder;
use App\Models\Admin\AppOnboardScreens;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class AppOnBoardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $app_onboard_screens = array(
            array('id' => '1','title' => 'Easy & Fast Booking','sub_title' => 'Book your ride in just a few taps and get on the road hassle-free.','image' => 'b276f831-2428-459f-a8c0-cb2da085d751.webp','status' => '1','last_edit_by' => '1','created_at' => '2025-01-31 13:32:15','updated_at' => '2025-01-31 13:32:15'),
            array('id' => '2','title' => 'Safe & Reliable Rides','sub_title' => 'Verified drivers, real-time tracking, and secure payments for a stress-free journey.','image' => 'f5503b97-81af-4a20-be39-cbfb9577e53d.webp','status' => '1','last_edit_by' => '1','created_at' => '2025-01-31 13:32:35','updated_at' => '2025-01-31 13:32:35'),
            array('id' => '3','title' => 'Ride Anytime, Anywhere','sub_title' => 'Whether it\'s day or night, get a ride when you need it.','image' => '7b6e2dde-ddce-448b-8bd4-5834e8af161f.webp','status' => '1','last_edit_by' => '1','created_at' => '2025-01-31 13:32:56','updated_at' => '2025-01-31 13:32:56')
        );
        AppOnboardScreens::insert($app_onboard_screens);
    }
}
