<?php

namespace App\Http\Controllers\Vendor;

use App\Constants\GlobalConst;
use App\Constants\PaymentGatewayConst;
use App\Constants\SiteSectionConst;
use App\Http\Controllers\Controller;
use App\Models\Admin\Currency;
use App\Models\Admin\PaymentGateway;
use App\Models\Admin\PaymentGatewayCurrency;
use App\Models\Admin\SiteSections;
use App\Models\TemporaryData;
use App\Models\Transaction;
use App\Models\UserWallet;
use App\Models\Vendor\VendorWallet;
use App\Providers\Admin\BasicSettingsProvider;
use App\Traits\ControlDynamicInputFields;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class WithdrawalController extends Controller
{
    use ControlDynamicInputFields;

    public function index()
    {
        $page_title = __("Withdraw Money");
        $breadcrumb = __("Withdraw Money");
        $vendor_wallets = VendorWallet::auth()->get();
        $user_currencies = Currency::whereIn('id',$vendor_wallets->pluck('currency_id')->toArray())->get();
        $payment_gateways = PaymentGatewayCurrency::where(function($query){
            $query->whereHas('gateway',function($subquery){
                $subquery->where('slug',Str::slug(PaymentGatewayConst::MONEYOUT));
            });
        })->get();
        $transactions = Transaction::vendorAuth()->moneyOut()->with('gateway_currency')->latest('id')->take(3)->get();
        return view('vendor-end.sections.money-out.index', compact('page_title','payment_gateways','transactions','vendor_wallets','breadcrumb'));
    }

    public function submit(Request $request) {
        $validated = $request->validate([
            'gateway_currency'  => "required|exists:payment_gateway_currencies,alias",
            'amount'            => "required|numeric|gt:0",
        ]);

        $user = auth()->guard('vendor')->user();
        $userWallet = VendorWallet::where(['vendor_id' => $user->id, 'status' => 1])->first();


        $gateway_currency = PaymentGatewayCurrency::where('alias',$validated['gateway_currency'])->first();
        if(!$gateway_currency->gateway->isManual()) return back()->with(['error' => [__("Gateway isn't available for this transaction")]]);

        $charges = $this->moneyOutCharges($validated['amount'],$gateway_currency,$userWallet); // Withdraw charge
        if($userWallet->balance < $charges->total_payable) return back()->with(['error' => [__('Your wallet balance is insufficient')]]);


        $exchange_request_amount    = get_amount($charges->request_amount,null,8);
        $gateway_min_limit          = get_amount($gateway_currency->min_limit / $charges->exchange_rate,null,8);
        $gateway_max_limit          = get_amount($gateway_currency->max_limit / $charges->exchange_rate,null,8);

        if($exchange_request_amount < $gateway_min_limit || $exchange_request_amount > $gateway_max_limit) return back()->with(['error' => [__('Please follow the transaction limit. (Min ').$gateway_min_limit . ' ' . $userWallet->currency->code .__(' - Max ').$gateway_max_limit. ' ' . $userWallet->currency->code . ')']]);

        // Store Temp Data
        try{
            $token = generate_unique_string("temporary_datas","identifier",16,'WM');
            TemporaryData::create([
                'type'          => PaymentGatewayConst::money_out_slug(),
                'identifier'    => $token,
                'data'          => [
                    'gateway_currency_id'   => $gateway_currency->id,
                    'wallet_id'             => $userWallet->id,
                    'charges'               => $charges,
                ],
            ]);
        }catch(Exception $e) {
            return back()->with(['error' => [__('Something went wrong! Please try again')]]);
        }

        return redirect()->route('vendor.withdraw.money.instruction',$token);

    }

    public function moneyOutCharges($amount,$currency,$wallet) {
        $data['exchange_rate']          = get_amount((1 / $wallet->currency->rate) * $currency->rate,null,8) ;
        $data['request_amount']         = get_amount($amount,null,8);
        $data['fixed_charge']           = get_amount($currency->fixed_charge / $data['exchange_rate'],null,8);
        $data['percent_charge']         = get_amount(((($amount * $currency->rate) / 100) * $currency->percent_charge) / $currency->rate,null,8);
        $data['gateway_currency_code']  = $currency->currency_code;
        $data['gateway_currency_id']    = $currency->id;
        $data['sender_currency_code']   = $wallet->currency->code;
        $data['sender_wallet_id']       = $wallet->id;
        $data['will_get']               = get_amount(($amount * $data['exchange_rate']),null,8);
        $data['receive_currency']       = $currency->currency_code;
        $data['sender_currency']        = $wallet->currency->code;
        $data['total_charge']           = get_amount($data['fixed_charge'] + $data['percent_charge'],null,8); // in sender currency
        $data['total_payable']          = get_amount($data['request_amount'] + $data['total_charge'],null,8); // in sender currency
        return (object) $data;
    }

    public function instruction($token,BasicSettingsProvider $basic_settings) {
        $site_name = $basic_settings->get()?->site_name;
        $footer_slug = Str::slug(SiteSectionConst::FOOTER_SECTION);
        $footer = SiteSections::getData($footer_slug)->first();

        $tempData = TemporaryData::where('identifier',$token)->first();
        $gateway_currency_id = $tempData->data->gateway_currency_id ?? "";
        if(!$gateway_currency_id) return redirect()->route('vendor.withdraw.money.index')->with(['error' => [__('Invalid Request!')]]);

        $gateway_currency = PaymentGatewayCurrency::find($gateway_currency_id);
        if(!$gateway_currency) return redirect()->route('vendor.withdraw.money.index')->with(['error' => [__('Payment gateway currency is invalid!')]]);
        $gateway = $gateway_currency->gateway;
        $input_fields = $gateway->input_fields;
        if($input_fields == null || !is_array($input_fields)) return redirect()->route('vendor.withdraw.money.index')->with(['error' => [__('This gateway is temporary pause or under maintenance!')]]);
        $amount = $tempData->data->charges;
        $page_title = __("Money Out");
        $breadcrumb = __("Money Out Instructions");
        return view('vendor-end.sections.money-out.instructions',compact('page_title','site_name','footer','gateway','token','amount','breadcrumb'));
    }

    public function instructionSubmit(Request $request,$token) {
        $tempData = TemporaryData::where('identifier',$token)->first();
        $gateway_currency_id = $tempData->data->gateway_currency_id ?? "";
        if(!$gateway_currency_id) return redirect()->route('vendor.withdraw.money.index')->with(['error' => [__('Invalid Request!')]]);

        $gateway_currency = PaymentGatewayCurrency::find($gateway_currency_id);
        if(!$gateway_currency) return redirect()->route('vendor.withdraw.money.index')->with(['error' => [__('Payment gateway currency is invalid!')]]);
        $gateway = $gateway_currency->gateway;

        $wallet_id = $tempData->data->wallet_id ?? null;
        $wallet = VendorWallet::auth()->active()->find($wallet_id);
        if(!$wallet) return redirect()->route('vendor.withdraw.money.index')->with(['error' => [__('Your wallet is invalid!')]]);

        $this->file_store_location = "transaction";
        $dy_validation_rules = $this->generateValidationRules($gateway->input_fields);

        $validated = Validator::make($request->all(),$dy_validation_rules)->validate();
        $get_values = $this->placeValueWithFields($gateway->input_fields,$validated);

        $amount = $tempData->data->charges;

        $wallet_balance = 0;
        $wallet_balance = $wallet->balance;

        // Make Transaction
        DB::beginTransaction();
        try{
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
                'available_balance'             => $wallet_balance - $amount->total_payable,
                'receive_amount'                => $amount->will_get,
                'payment_currency'              => $gateway_currency->currency_code,
                'details'                       => json_encode(['input_values' => $get_values,'charges' => $amount]),
                'status'                        => PaymentGatewayConst::STATUSPENDING,
                'created_at'                    => now(),
            ]);

            DB::table($wallet->getTable())->where("id",$wallet->id)->update([
                'balance'       => ($wallet->balance - $amount->total_payable),
            ]);
            DB::commit();
        }catch(Exception $e) {
            DB::rollBack();
            return redirect()->route('vendor.withdraw.money.instruction',$token)->with(['error' => [__('Something went wrong! Please try again')]]);
        }
        $tempData->delete();

        return redirect()->route('vendor.withdraw.money.index')->with(['success' => [__('Transaction success! Please wait for confirmation')]]);
    }

    public function withdrawLogs()
    {
        $page_title = __("Withdraw Money");
        $transactions = Transaction::vendorAuth()->moneyOut()->with('gateway_currency')->latest('id')->paginate(5);
        return view('vendor-end.sections.money-out.withdraw-logs', compact('page_title','transactions',));
    }
}
