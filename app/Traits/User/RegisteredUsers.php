<?php

namespace App\Traits\User;

use App\Models\Admin\Currency;
use App\Models\UserWallet;
use App\Models\Vendor\VendorWallet;
use Exception;

trait RegisteredUsers {
    protected function createUserWallets($user) {
        $currencies = Currency::active()->roleHasOne()->pluck("id")->toArray();
        $wallets = [];
        foreach($currencies as $currency_id) {
            $wallets[] = [
                'vendor_id'     => $user->id,
                'currency_id'   => $currency_id,
                'balance'       => 0,
                'due_payment'   => 0,
                'status'        => true,
                'created_at'    => now(),
            ];
        }

        try{
            VendorWallet::insert($wallets);
        }catch(Exception $e) {
            // handle error
            throw new Exception("Failed to create wallet! Please try again");
        }
    }
}
