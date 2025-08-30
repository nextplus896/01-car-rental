<?php

namespace Database\Seeders\Vendor;

use App\Models\Vendor\Vendor;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class VendorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = array(
            array('id' => '1','firstname' => 'test','lastname' => 'vendor','username' => 'testvendor','email' => 'vendor@appdevs.net','mobile_code' => NULL,'mobile' => NULL,'full_mobile' => NULL,'password' => '$2y$10$gWmQa10HCb/hVwdiDZZTruRSX2XZ5Gxolnx/9Bgl2/dp3GGD2/QNa','referral_id' => NULL,'image' => NULL,'status' => '1','address' => NULL,'email_verified' => '1','sms_verified' => '1','kyc_verified' => '1','ver_code' => NULL,'ver_code_send_at' => NULL,'two_factor_verified' => '0','two_factor_status' => '0','two_factor_secret' => 'YGMGGNCELCZNDD3J','email_verified_at' => NULL,'remember_token' => NULL,'deleted_at' => NULL,'created_at' => '2025-01-08 07:06:36','updated_at' => '2025-01-08 12:00:15'),
            array('id' => '2','firstname' => 'vendor','lastname' => 'test2','username' => 'vendor','email' => 'vendor2@appdevs.net','mobile_code' => NULL,'mobile' => NULL,'full_mobile' => NULL,'password' => '$2y$10$pz4sASHd00Qr/KxwURGbTuZ63rIKGf.TNjyDSmQWdJWPVRTxwBHo.','referral_id' => NULL,'image' => NULL,'status' => '1','address' => NULL,'email_verified' => '1','sms_verified' => '1','kyc_verified' => '1','ver_code' => NULL,'ver_code_send_at' => NULL,'two_factor_verified' => '0','two_factor_status' => '0','two_factor_secret' => 'WBPNEUFEQEKNM5XH','email_verified_at' => NULL,'remember_token' => NULL,'deleted_at' => NULL,'created_at' => '2025-01-08 07:28:28','updated_at' => '2025-01-08 07:31:24'),
        );

        Vendor::insert($data);
    }
}
