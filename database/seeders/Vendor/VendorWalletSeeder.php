<?php

namespace Database\Seeders\Vendor;

use App\Models\Vendor\VendorWallet;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class VendorWalletSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $vendor_wallets = array(
            array('id' => '1','vendor_id' => '1','currency_id' => '1','balance' => '13000.00000000','due_payment' => '0.00000000','status' => '1','created_at' => '2025-01-08 07:06:36','updated_at' => '2025-01-15 04:33:40'),
            array('id' => '2','vendor_id' => '2','currency_id' => '1','balance' => '10000.0000000','due_payment' => '0.00000000','status' => '1','created_at' => '2025-01-08 07:28:28','updated_at' => '2025-01-08 09:22:45'),
        );

        VendorWallet::insert($vendor_wallets);
    }
}
