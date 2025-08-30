<?php

namespace Database\Seeders\Admin;

use App\Models\Admin\AppSettings;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AppSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = array('id' => '1','version' => '1.1.0','splash_screen_image' => 'd577ce71-5a1f-4ba8-b5c7-6737fa14970a.webp','url_title' => NULL,'android_url' => 'https://play.google.com/','iso_url' => 'https://www.apple.com/app-store/','vendor_version' => '1.1.0','vendor_splash_screen_image' => 'aaa2d5d8-8940-45f9-abbf-66d317ef607b.webp','vendor_url_title' => NULL,'vendor_android_url' => 'https://play.google.com/','vendor_iso_url' => 'https://www.apple.com/','created_at' => '2025-01-29 03:55:12','updated_at' => '2025-02-03 04:03:24');

        AppSettings::firstOrCreate($data);
    }
}
