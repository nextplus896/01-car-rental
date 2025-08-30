<?php

namespace App\Http\Controllers\Api\V1\User;

use Exception;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Http\Helpers\Response;
use App\Http\Controllers\Controller;
use App\Constants\PaymentGatewayConst;
use Illuminate\Support\Facades\Validator;

class TransactionController extends Controller
{
    public function slugValue($slug) {
        $values =  [
            'add-money'         => PaymentGatewayConst::TYPEADDMONEY,
            'money-out'         => PaymentGatewayConst::TYPEMONEYOUT,
            'money-transfer'    => PaymentGatewayConst::TYPETRANSFERMONEY,
            'money-exchange'    => PaymentGatewayConst::TYPEMONEYEXCHANGE,
            'make-payment'      => PaymentGatewayConst::TYPEMAKEPAYMENT,
            'withdraw'          => PaymentGatewayConst::TYPEWITHDRAW,
        ];

        if(!array_key_exists($slug,$values)) return abort(404);
        return $values[$slug];
    }

    public function log(Request $request) {

        $validator = Validator::make($request->all(),[
            'slug'      => "nullable|string|in:add-money,money-out,money-transfer,money-exchange,withdraw,make-payment",
        ]);
        if($validator->fails()) return Response::error($validator->errors()->all(),[]);

        $validated = $validator->validate();

        try{

            if(isset($validated['slug']) && $validated['slug'] != "") {
                $transactions = Transaction::auth()->where("type",$this->slugValue($validated['slug']))->orderByDesc("id")->get();
            }else {
                $transactions = Transaction::auth()->orderByDesc("id")->get();
            }

            $transactions->makeHidden([
                'id',
                'user_type',
                'user_id',
                'wallet_id',
                'payment_gateway_currency_id',
                'request_amount',
                'exchange_rate',
                'percent_charge',
                'fixed_charge',
                'total_charge',
                'total_payable',
                'receiver_type',
                'receiver_id',
                'available_balance',
                'payment_currency',
                'input_values',
                'details',
                'reject_reason',
                'remark',
                'stringStatus',
                'updated_at',
                'gateway_currency',
            ]);

        }catch(Exception $e) {
            return Response::error([__('Something went wrong! Please try again')],[],500);
        }

        return Response::success([__('Transactions fetch successfully!')],[
            'instructions'  => [
                'slug'      => "add-money,money-transfer,withdraw",
                'status'    => "1: Success, 2: Pending, 3: Hold, 4: Rejected, 5: Waiting"
            ],
            'transaction_types' => [
                PaymentGatewayConst::TYPEADDMONEY,
                PaymentGatewayConst::TYPETRANSFERMONEY,
                PaymentGatewayConst::TYPEWITHDRAW,
            ],
            'transactions'  => $transactions,
        ],200);
    }
}
