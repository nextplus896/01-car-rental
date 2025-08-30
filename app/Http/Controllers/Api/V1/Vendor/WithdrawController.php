<?php

namespace App\Http\Controllers\Api\V1\Vendor;

use App\Constants\GlobalConst;
use App\Constants\PaymentGatewayConst;
use App\Http\Controllers\Controller;
use App\Http\Helpers\Response;
use App\Models\Admin\Currency;
use App\Models\Admin\PaymentGateway;
use App\Models\Admin\PaymentGatewayCurrency;
use App\Models\Vendor\VendorWallet;
use App\Traits\ControlDynamicInputFields;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class WithdrawController extends Controller
{
    use ControlDynamicInputFields;
    public function walletGateways() {
        $payment_gateways = PaymentGateway::moneyOut()->manual()->active()->has("currencies")->get()->map(function($item) {
            return [
                'id'        => $item->id,
                'type'      => $item->type,
                'name'      => $item->name,
                'alias'     => $item->alias,
                'desc'      => $item->desc,
                'status'    => $item->status,
                'currencies'      => [
                    [
                        'gateway_alias'                     => $item->alias,
                        'id'                                => $item->currencies->first()->id,
                        'alias'                             => $item->currencies->first()->alias,
                        'payment_gateway_id'                => $item->currencies->first()->payment_gateway_id,
                        'name'                              => $item->currencies->first()->name,
                        'currency_code'                     => $item->currencies->first()->currency_code,
                        'currency_symbol'                   => $item->currencies->first()->currency_symbol,
                        'image'                             => $item->currencies->first()->image,
                        'min_limit'                         => $item->currencies->first()->min_limit,
                        'max_limit'                         => $item->currencies->first()->max_limit,
                        'percent_charge'                    => $item->currencies->first()->percent_charge,
                        'fixed_charge'                      => $item->currencies->first()->fixed_charge,
                        'rate'                              => $item->currencies->first()->rate,
                        'created_at'                        => $item->currencies->first()->created_at,
                        'updated_at'                        => $item->currencies->first()->updated_at,
                    ]
                ],
            ];
        });

        $vendor_wallets = VendorWallet::authApi()->active()->with("currency:id,code,rate")->get();
        $vendor_wallets->makeHidden(['id','currency_id','created_at','updated_at']);

        return Response::success([__('Request data fetch successfully!')],[
            'vendor_wallets'            => $vendor_wallets,
            'payment_gateways'          => $payment_gateways,
        ],200);
    }

    public function gatewayInputFields(Request $request) {
        $validator = Validator::make($request->all(),[
            'currency'      => "required|string|exists:payment_gateway_currencies,alias"
        ]);
        if($validator->fails()) return Response::error($validator->errors()->all(),[]);
        $validated = $validator->validate();

        $gateway_currency = PaymentGatewayCurrency::where("alias",$validated['currency'])->whereHas("gateway",function($q) {
            $q->where("type",PaymentGatewayConst::MANUAL);
        })->first();

        if(!$gateway_currency) return Response::error([__('Payment gateway not found!')],[],404);
        $gateway = $gateway_currency->gateway;

        try{
            $input_fields = $gateway->input_fields ?? null;
            if(!$input_fields) return Response::error([__('Payment gateway is under maintenance. Please try with another gateway')]);

            if($input_fields != null) {
                $input_fields = json_decode(json_encode($input_fields),true);
            }

        }catch(Exception $e) {
            return Response::error([__('Something went wrong! Please try again')],[],500);
        }

        return Response::success([__('Payment gateway input field fetch successfully!')],[
            'gateway'           => [
                'desc'          => $gateway->desc,
            ],
            'input_fields'      => $input_fields,
        ],200);
    }

    public function submit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'gateway_currency'   => "required|exists:payment_gateway_currencies,alias",
            'amount'            => "required|numeric|gt:0",
            'wallet_currency'   => 'required|string|exists:currencies,code'
        ]);

        if ($validator->fails()) {
            return Response::error($validator->errors()->all());
        }

        $validated = $validator->validate();

        $user = auth()->guard('vendor_api')->user();
        $wallet_currency = Currency::where('code', $request->wallet_currency)->first();
        $wallet = VendorWallet::where(['vendor_id' => $user->id, 'currency_id' => $wallet_currency->id, 'status' => 1])->first();

        $gateway = PaymentGatewayCurrency::where("alias",$validated['gateway_currency'])->first()->gateway;

        if (!$gateway->isManual()) return back()->with(['error' => [__("Gateway isn't available for this transaction")]]);
        $gateway_currency = $gateway->currencies->first();

        $charges = $this->moneyOutCharges($validated['amount'], $gateway_currency, $wallet); // Withdraw charge

        if ($wallet->balance < $charges->total_payable) return Response::error([__('Your wallet balance is insufficient')], [], 400);


        $exchange_request_amount    = $charges->request_amount;
        $gateway_min_limit          = $gateway_currency->min_limit / $charges->exchange_rate;
        $gateway_max_limit          = $gateway_currency->max_limit / $charges->exchange_rate;

        if ($exchange_request_amount < $gateway_min_limit || $exchange_request_amount > $gateway_max_limit) return Response::error([__('Please follow the transaction limit. (Min ') . $gateway_min_limit . ' ' . $wallet->currency->code . __(' - Max ') . $gateway_max_limit . ' ' . $wallet->currency->code . ')'], [], 400);

        $this->file_store_location = "transaction";
        $dy_validation_rules = $this->generateValidationRules($gateway->input_fields);

        $dy_field_validation = Validator::make($request->all(), $dy_validation_rules);
        if ($dy_field_validation->fails()) {
            return Response::error($dy_field_validation->errors()->all());
        }
        $dy_field_validated = $dy_field_validation->validate();
        $get_values = $this->placeValueWithFields($gateway->input_fields, $dy_field_validated);

        $amount = $charges;

        DB::beginTransaction();
        try {
            DB::table("transactions")->insertGetId([
                'type'                          => PaymentGatewayConst::TYPEWITHDRAW,
                'trx_id'                        => generate_unique_string('transactions','trx_id',16, "WM"),
                'user_type'                     => GlobalConst::VENDOR,
                'vendor_id'                     => $wallet->vendor->id,
                'wallet_id'                     => $wallet->id,
                'payment_gateway_currency_id'   => $gateway_currency->id,
                'request_amount'                => $amount->request_amount,
                'request_currency'              => $wallet->currency->code,
                'exchange_rate'                 => $amount->exchange_rate,
                'percent_charge'                => $amount->percent_charge,
                'fixed_charge'                  => $amount->fixed_charge,
                'total_charge'                  => $amount->total_charge,
                'total_payable'                 => $amount->total_payable,
                'available_balance'             => $wallet->balance - $amount->total_payable,
                'receive_amount'                => $amount->will_get,
                'payment_currency'              => $gateway_currency->currency_code,
                'details'                       => json_encode(['input_values' => $get_values,'charges' => $amount]),
                'status'                        => PaymentGatewayConst::STATUSPENDING,
                'created_at'                    => now(),
            ]);

            DB::table($wallet->getTable())->where("id", $wallet->id)->update([
                'balance'       => ($wallet->balance - $amount->total_payable),
            ]);

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return Response::error([__('Something went wrong! Please try again')], [], 500);
        }


        return Response::success([__('Transaction success! Please wait for confirmation')], [], 200);
    }


    public function moneyOutCharges($amount, $currency, $wallet)
    {

        $data['exchange_rate']          = (1 / $wallet->currency->rate) * $currency->rate;
        $data['request_amount']         = $amount;
        $data['fixed_charge']           = $currency->fixed_charge / $data['exchange_rate'];
        $data['percent_charge']         = ((($amount * $currency->rate) / 100) * $currency->percent_charge) / $currency->rate;
        $data['gateway_currency_code']  = $currency->currency_code;
        $data['gateway_currency_id']    = $currency->id;
        $data['sender_currency_code']   = $wallet->currency->code;
        $data['sender_wallet_id']       = $wallet->id;
        $data['will_get']               = ($amount * $data['exchange_rate']);
        $data['receive_currency']       = $currency->currency_code;
        $data['sender_currency']        = $wallet->currency->code;
        $data['total_charge']           = $data['fixed_charge'] + $data['percent_charge']; // in sender currency
        $data['total_payable']          = $data['request_amount'] + $data['total_charge']; // in sender currency

        return (object) $data;
    }
}
