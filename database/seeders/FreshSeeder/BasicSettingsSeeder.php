<?php

namespace Database\Seeders\FreshSeeder;

use App\Models\Admin\BasicSettings;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BasicSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $basic_settings = array(
            array('id' => '1','site_name' => 'CarBo','site_title' => 'Car Rental & Booking Full Solution','base_color' => '#15b887','secondary_color' => '#ffffff', 'vendor_base_color' => '#2980B9','vendor_secondary_color' => '#ffffff','otp_exp_seconds' => '3600','timezone' => 'Asia/Dhaka','user_registration' => '1','secure_password' => '0','agree_policy' => '0','force_ssl' => '0','email_verification' => '0','sms_verification' => '0','email_notification' => '0','push_notification' => '0','kyc_verification' => '1','site_logo_dark' => '1ed342a8-e6aa-48ea-bf3c-f5cca25e4f71.webp','site_logo' => '270b9bf4-99d3-48d3-ae77-63faab60872e.webp','site_fav_dark' => '1bac8ff7-5e08-4cc5-816c-d3e2ca37d1fe.webp','site_fav' => '651f7e15-286e-4155-b908-eb29d0bd9451.webp','vendor_logo_dark' => 'a7e7dfe9-ec09-44f4-a6fc-570aaf9ad6a1.webp','vendor_logo' => 'f25fa20e-c1c5-4196-8079-ef10dbbb05d1.webp','vendor_fav_dark' => '35859547-40ab-46dd-95cd-8602ba7c0bab.webp','vendor_fav' => 'e605065b-286a-4443-9072-15c3de160211.webp','preloader_image' => NULL,'mail_config' => '{"method":"smtp","host":"appdevs.team","port":"465","encryption":"ssl","username":"","password":"","from":"noreply@appdevs.team","app_name":"Carbo"}','mail_activity' => NULL,'push_notification_config' => '{"method":"pusher","instance_id":"","primary_key":""}','push_notification_activity' => NULL,'broadcast_config' => '{"method":"pusher","app_id":"","primary_key":"","secret_key":"","cluster":""}','broadcast_activity' => NULL,'sms_config' => NULL,'sms_activity' => NULL,'web_version' => '1.1.0','admin_version' => '2.5.0','created_at' => '2024-10-23 12:30:03','updated_at' => '2024-11-13 10:16:14')
          );

        BasicSettings::insert($basic_settings);
    }
}
