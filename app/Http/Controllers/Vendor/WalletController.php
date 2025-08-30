<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Http\Helpers\Response;
use App\Models\Vendor\VendorWallet;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class WalletController extends Controller
{
    public function balance(Request $request) {

        $validator = Validator::make($request->all(),[
            'target'        => "required|string",
            'balance_type' => "nullable|string|in:c_balance,p_balance",
        ]);

        if($validator->fails()) {
            return Response::error($validator->errors(),null,400);
        }

        $validated = $validator->validate();

        try{
            $wallet = VendorWallet::auth()->whereHas("currency",function($q) use ($validated) {
                $q->where("code",$validated['target']);
            })->first();
        }catch(Exception $e) {
            $error = ['error' => [__('Something went wrong!. Please try again.')]];
            return Response::error($error,null,500);
        }

        if(!$wallet) {
            $error = ['error' => [__('Your ').($validated['target']).__(' wallet not found.')]];
            return Response::error($error,null,404);
        }

        $balance = isset($validated['balance_type']) && $validated['balance_type'] == 'p_balance' ? $wallet->profit_balance : $wallet->balance;

        $success = ['success' => [__('Data collected successfully!')]];
        return Response::success($success, $balance, 200);

    }
}
