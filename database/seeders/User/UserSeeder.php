<?php

namespace Database\Seeders\User;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = array(
            array('id' => '7', 'firstname' => 'Test', 'lastname' => 'User One', 'username' => 'testuser', 'email' => 'user@appdevs.net', 'mobile_code' => NULL, 'mobile' => NULL, 'full_mobile' => NULL, 'password' => '$2y$10$VoBL90Pl9z/uf3qS82umReOJPFDOhQIIZPiJes4t.876RY9CodGb2', 'referral_id' => NULL, 'image' => NULL, 'status' => '1', 'address' => NULL, 'email_verified' => '1', 'sms_verified' => '1', 'kyc_verified' => '0', 'ver_code' => NULL, 'ver_code_send_at' => NULL, 'two_factor_verified' => '0', 'two_factor_status' => '0', 'two_factor_secret' => NULL, 'email_verified_at' => NULL, 'remember_token' => NULL, 'deleted_at' => NULL, 'created_at' => '2024-11-08 06:56:48', 'updated_at' => '2024-11-11 04:28:46'),
            array('id' => '8', 'firstname' => 'Test', 'lastname' => 'User Two', 'username' => 'testuser2', 'email' => 'user2@appdevs.net', 'mobile_code' => '880', 'mobile' => '1315300224', 'full_mobile' => '8801315300224', 'password' => '$2y$10$VoBL90Pl9z/uf3qS82umReOJPFDOhQIIZPiJes4t.876RY9CodGb2', 'referral_id' => NULL, 'image' => '1406998a-139c-4ea0-b016-04e43af0be59.webp', 'status' => '1', 'address' => '{"country":"Bangladesh","state":"Bagerhat District","city":"j","zip":"888","address":"71\\/Farmgate, Testuribazar"}', 'email_verified' => '1', 'sms_verified' => '1', 'kyc_verified' => '0', 'ver_code' => NULL, 'ver_code_send_at' => NULL, 'two_factor_verified' => '0', 'two_factor_status' => '0', 'two_factor_secret' => NULL, 'email_verified_at' => NULL, 'remember_token' => NULL, 'deleted_at' => NULL, 'created_at' => '2024-11-11 05:27:37', 'updated_at' => '2024-11-15 12:49:42'),
            array('id' => '3', 'firstname' => 'Test', 'lastname' => 'User Three', 'username' => 'testuser3', 'email' => 'user3@appdevs.net', 'mobile_code' => NULL, 'mobile' => NULL, 'full_mobile' => NULL, 'password' => '$2y$10$VoBL90Pl9z/uf3qS82umReOJPFDOhQIIZPiJes4t.876RY9CodGb2', 'referral_id' => NULL, 'image' => NULL, 'status' => '1', 'address' => NULL, 'email_verified' => '1', 'sms_verified' => '1', 'kyc_verified' => '0', 'ver_code' => NULL, 'ver_code_send_at' => NULL, 'two_factor_verified' => '0', 'two_factor_status' => '0', 'two_factor_secret' => NULL, 'email_verified_at' => NULL, 'remember_token' => NULL, 'deleted_at' => NULL, 'created_at' => '2024-10-23 12:30:04', 'updated_at' => NULL),
            array('id' => '4', 'firstname' => 'Test', 'lastname' => 'User Four', 'username' => 'testuser4', 'email' => 'user4@appdevs.net', 'mobile_code' => NULL, 'mobile' => NULL, 'full_mobile' => NULL, 'password' => '$2y$10$crk43wx9kOFTIoTuku0y4uaakXWdI0rKoOmBbka0DPf9nA/vqC61q', 'referral_id' => NULL, 'image' => NULL, 'status' => '1', 'address' => NULL, 'email_verified' => '1', 'sms_verified' => '1', 'kyc_verified' => '0', 'ver_code' => NULL, 'ver_code_send_at' => NULL, 'two_factor_verified' => '0', 'two_factor_status' => '0', 'two_factor_secret' => NULL, 'email_verified_at' => NULL, 'remember_token' => NULL, 'deleted_at' => NULL, 'created_at' => '2024-10-23 12:30:04', 'updated_at' => NULL),
            array('id' => '5', 'firstname' => 'Test', 'lastname' => 'User Five', 'username' => 'testuser5', 'email' => 'user5@appdevs.net', 'mobile_code' => NULL, 'mobile' => NULL, 'full_mobile' => NULL, 'password' => '$2y$10$zQpDzxcOjsOJtC5e06mN6Oki7U/PQ7M1womcxSkvG3BY0GRVzW/ka', 'referral_id' => NULL, 'image' => NULL, 'status' => '1', 'address' => NULL, 'email_verified' => '1', 'sms_verified' => '1', 'kyc_verified' => '0', 'ver_code' => NULL, 'ver_code_send_at' => NULL, 'two_factor_verified' => '0', 'two_factor_status' => '0', 'two_factor_secret' => NULL, 'email_verified_at' => NULL, 'remember_token' => NULL, 'deleted_at' => NULL, 'created_at' => '2024-10-23 12:30:04', 'updated_at' => NULL),
        );

        User::insert($data);
    }
}
