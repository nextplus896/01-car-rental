<?php

namespace Database\Seeders\Admin;

use App\Models\Admin\VendorAppOnboardScreen;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class VendorAppOnBoardScreenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $vendor_app_on_board_screens = array(
            array('id' => '1','title' => 'Drive Your Business Forward','sub_title' => 'Rent out your cars and maximize your earnings effortlessly.','image' => 'c11cfe68-d093-41e4-a10b-9d972da0800d.webp','status' => '1','last_edit_by' => '1','created_at' => '2025-02-03 04:13:50','updated_at' => '2025-02-03 04:13:50')
          );
        VendorAppOnboardScreen::insert($vendor_app_on_board_screens);
    }
}
