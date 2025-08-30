<?php
namespace App\Http\Helpers;

use App\Constants\GlobalConst;
use Exception;
use Illuminate\Support\Str;
use Jenssegers\Agent\Agent;
use App\Models\TemporaryData;
use Illuminate\Support\Facades\DB;
use App\Traits\PaymentGateway\Gpay;
use App\Traits\PaymentGateway\Paypal;
use App\Constants\PaymentGatewayConst;
use App\Models\Admin\BasicSettings;
use Illuminate\Support\Facades\Validator;
use App\Models\Admin\PaymentGatewayCurrency;
use App\Models\Transaction;
use App\Providers\Admin\CurrencyProvider;
use App\Traits\PaymentGateway\CoinGate;
use App\Traits\PaymentGateway\QRPay;
use App\Traits\PaymentGateway\Tatum;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\ValidationException;
use App\Models\Admin\PaymentGateway as PaymentGatewayModel;
use App\Models\CarBooking;
use App\Models\Vendor\VendorNotification;
use App\Notifications\User\carBookingNotification;
use App\Traits\PaymentGateway\Flutterwave;
use App\Traits\PaymentGateway\PerfectMoney;
use App\Traits\PaymentGateway\Razorpay;
use App\Traits\PaymentGateway\SslCommerz;
use App\Traits\PaymentGateway\Stripe;
use Carbon\Carbon;
use Illuminate\Support\Facades\Notification;

class PaymentGateway {

    use Paypal, Gpay, CoinGate, QRPay, Tatum, Stripe, Flutterwave, SslCommerz, Razorpay, PerfectMoney;

    protected $request_data;
    protected $output;
    protected $currency_input_name = "gateway_currency";
    protected $amount_input = "amount";
    protected $project_currency = PaymentGatewayConst::PROJECT_CURRENCY_MULTIPLE;
    protected $predefined_user_wallet;
    protected $predefined_guard;
    protected $predefined_user;

    public function __construct(array $request_data)
    {
        $this->request_data = $request_data;
    }

    public static function init(array $data) {
        return new PaymentGateway($data);
    }

    public function setProjectCurrency(string $type) {
        $this->project_currency = $type;
        return $this;
    }

    public function gateway() {
        $request_data = $this->request_data;
        if(empty($request_data)) throw new Exception("Gateway Information is not available. Please provide payment gateway currency alias");
        $validated = $this->validator($request_data)->validate();
        $gateway_currency = PaymentGatewayCurrency::where("alias",$validated[$this->currency_input_name])->first();

        if(!$gateway_currency || !$gateway_currency->gateway) {
            if(request()->acceptsJson()) throw new Exception("Gateway not available");
            throw ValidationException::withMessages([
                $this->currency_input_name = "Gateway not available",
            ]);
        }

        if($this->project_currency == PaymentGatewayConst::PROJECT_CURRENCY_SINGLE) {
            $default_currency = CurrencyProvider::default();
            if(!$default_currency) throw new Exception("Project currency does not have default value.");

        }else {

        }

        $this->output['gateway']            = $gateway_currency->gateway;
        $this->output['currency']           = $gateway_currency;
        $this->output['amount']             = $this->amount();
        $this->output['form_data']          = $this->request_data;

        if($gateway_currency->gateway->isAutomatic()) {
            $this->output['distribute']         = $this->gatewayDistribute($gateway_currency->gateway);
            $this->output['record_handler']     = $this->generateRecordHandler();
        }else {
            $this->output['distribute']         = "handleManualPayment";
            $this->output['gateway_type']       = PaymentGatewayConst::MANUAL;
        }

        // limit validation
        $this->limitValidation($this->output);

        return $this;
    }

    public function generateRecordHandler() {

        if($this->predefined_guard) {
            $guard = $this->predefined_guard;
        }else {
            $guard = get_auth_guard();
        }

        $method = "insertRecord". ucwords($guard);
        return $method;
    }


    public function validator($data) {
        $validator = Validator::make($data,[
            $this->currency_input_name  => "required|exists:payment_gateway_currencies,alias",
            $this->amount_input         => "sometimes|required|numeric|gt:0",
        ]);

        if(request()->acceptsJson()) {
            if($validator->fails()) {
                $errors = $validator->errors()->all();
                $first_error = $errors[0];
                throw new Exception($first_error);
            }
        }

        return $validator;
    }

    public function limitValidation($output) {
        $gateway_currency = $output['currency'];
        $requested_amount = $output['amount']->requested_amount;

        $min_limit = $gateway_currency->min_limit;
        $max_limit = $gateway_currency->max_limit;
        $exchange_rate = $gateway_currency->rate;


        if($requested_amount < ($min_limit/$exchange_rate) || $requested_amount > ($max_limit/$exchange_rate)) {
            if(request()->acceptsJson()) throw new Exception("Please follow the transaction limit");
            throw ValidationException::withMessages([
                $this->amount_input = "Please follow the transaction limit",
            ]);
        }
    }

    public function get() {
        return $this->output;
    }

    public function gatewayDistribute($gateway = null) {
        if(!$gateway) $gateway = $this->output['gateway'];
        $alias = Str::lower($gateway->alias);
        $method = PaymentGatewayConst::register($alias);
        if(method_exists($this,$method)) {
            return $method;
        }
        throw new Exception("Gateway(".$gateway->name.") Trait or Method (".$method."()) does not exists");
    }

    public function amount() {
        $currency = $this->output['currency'] ?? null;
        if(!$currency) throw new Exception("Gateway currency not found");
        return $this->chargeCalculate($currency);
    }

    public function chargeCalculate($currency,$receiver_currency = null) {

        $amount = $this->request_data[$this->amount_input];
        $sender_currency_rate = $currency->rate;

        ($sender_currency_rate == "" || $sender_currency_rate == null) ? $sender_currency_rate = 0 : $sender_currency_rate;
        ($amount == "" || $amount == null) ? $amount : $amount;

        if($currency != null) {
            $fixed_charges = $currency->fixed_charge;
            $percent_charges = $currency->percent_charge;
        }else {
            $fixed_charges = 0;
            $percent_charges = 0;
        }

        $fixed_charge_calc = ($sender_currency_rate * $fixed_charges);
        $percent_charge_calc = ($amount / 100 ) * $percent_charges;

        $total_charge = $fixed_charge_calc + $percent_charge_calc;

        if($receiver_currency) {
            $receiver_currency_rate = $receiver_currency->rate;
            ($receiver_currency_rate == "" || $receiver_currency_rate == null) ? $receiver_currency_rate = 0 : $receiver_currency_rate;
            $exchange_rate = ($receiver_currency_rate / $sender_currency_rate);
            $will_get = ($amount * $exchange_rate);

            $data = [
                'requested_amount'          => $amount,
                'sender_cur_code'           => $currency->currency_code,
                'sender_cur_rate'           => $sender_currency_rate ?? 0,
                'receiver_cur_code'         => $receiver_currency->currency_code,
                'receiver_cur_rate'         => $receiver_currency->rate ?? 0,
                'fixed_charge'              => $fixed_charge_calc,
                'percent_charge'            => $percent_charge_calc,
                'total_charge'              => $total_charge,
                'total_amount'              => $amount + $total_charge,
                'exchange_rate'             => $exchange_rate,
                'will_get'                  => $will_get,
                'default_currency'          => get_default_currency_code(),
            ];

        }else if($this->project_currency == PaymentGatewayConst::PROJECT_CURRENCY_SINGLE){
            $exchange_rate = $sender_currency_rate;
            $will_get = $amount;

            $fixed_charge_calc = $fixed_charges;

            $percent_charge_calc = ((($amount * $exchange_rate) / 100 ) * $percent_charges);

            $total_charge = $fixed_charge_calc + $percent_charge_calc;

            $total_amount = ($amount * $exchange_rate) + ($total_charge);

            $data = [
                'requested_amount'          => $amount,
                'sender_cur_code'           => $currency->currency_code,
                'sender_cur_rate'           => $sender_currency_rate ?? 0,
                'fixed_charge'              => $fixed_charge_calc,
                'percent_charge'            => $percent_charge_calc,
                'total_charge'              => $total_charge,
                'total_amount'              => $total_amount, // receive payment from gateway
                'exchange_rate'             => $exchange_rate,
                'will_get'                  => $will_get,
                'default_currency'          => get_default_currency_code(),
            ];
        }else {
            $exchange_rate = $sender_currency_rate;
            $will_get = ($amount * $exchange_rate);

            $data = [
                'requested_amount'          => $amount,
                'sender_cur_code'           => $currency->currency_code,
                'sender_cur_rate'           => $sender_currency_rate ?? 0,
                'fixed_charge'              => $fixed_charge_calc,
                'percent_charge'            => $percent_charge_calc,
                'total_charge'              => $total_charge,
                'total_amount'              => $amount + $total_charge,
                'exchange_rate'             => $exchange_rate,
                'will_get'                  => $will_get,
                'default_currency'          => get_default_currency_code(),
            ];
        }

        return (object) $data;
    }

    public function render() {
        $output = $this->output;
        if(isset($output['gateway_type']) && $output['gateway_type'] == PaymentGatewayConst::MANUAL) {
            return $this->get();
        }

        if(!is_array($output)) throw new Exception("Render Failed! Please call with valid gateway/credentials");

        $common_keys = ['gateway','currency','amount','distribute'];
        foreach($output as $key => $item) {
            if(!array_key_exists($key,$common_keys)) {
                $this->gateway();
                break;
            }
        }

        $distributeMethod = $this->output['distribute'];
        if(!method_exists($this,$distributeMethod)) throw new Exception("Something went wrong! Please try again.");
        return $this->$distributeMethod($output);
    }

    /**
     * Collect user data from temporary data and clears next routes
     */
    public function authenticateTempData()
    {
        $tempData = $this->request_data;
        if(empty($tempData) || empty($tempData['type'])) throw new Exception('Transaction failed. Record didn\'t saved properly. Please try again.');

        if($this->requestIsApiUser()) {
            $creator_table = $tempData['data']->creator_table ?? null;
            $creator_id = $tempData['data']->creator_id ?? null;
            $creator_guard = $tempData['data']->creator_guard ?? null;

            $api_authenticated_guards = PaymentGatewayConst::apiAuthenticateGuard();
            if(!array_key_exists($creator_guard,$api_authenticated_guards)) throw new Exception('Request user doesn\'t save properly. Please try again');

            if($creator_table == null || $creator_id == null || $creator_guard == null) throw new Exception('Request user doesn\'t save properly. Please try again');
            $creator = DB::table($creator_table)->where("id",$creator_id)->first();
            if(!$creator) throw new Exception("Request user doesn\'t save properly. Please try again");

            $api_user_login_guard = $api_authenticated_guards[$creator_guard];
            $this->output['api_login_guard'] = $api_user_login_guard;
            Auth::guard($api_user_login_guard)->loginUsingId($creator->id);
        }

        $currency_id = $tempData['data']->currency ?? "";
        $gateway_currency = PaymentGatewayCurrency::find($currency_id);
        if(!$gateway_currency) throw new Exception('Transaction failed. Gateway currency not available.');
        $requested_amount = $tempData['data']->amount->requested_amount ?? 0;
        $validator_data = [
            $this->currency_input_name  => $gateway_currency->alias,
            $this->amount_input         => $requested_amount
        ];
        $this->request_data = $validator_data;
        $this->gateway();
        $this->output['tempData'] = $tempData;
    }

    public function responseReceive() {
        $this->authenticateTempData();

        $method_name = $this->getResponseMethod($this->output['gateway']);
        if(method_exists($this,$method_name)) {
            return $this->$method_name($this->output);
        }
        throw new Exception("Response method ".$method_name."() does not exists.");
    }

    public function type($type) {
        $this->output['type']  = $type;
        return $this;
    }

    public function getRedirection() {
        $redirection = PaymentGatewayConst::registerRedirection();
        $guard = get_auth_guard();
        if(!array_key_exists($guard,$redirection)) {
            throw new Exception("Gateway Redirection URLs/Route Not Registered. Please Register in PaymentGatewayConst::class");
        }
        $gateway_redirect_route = $redirection[$guard];
        return $gateway_redirect_route;
    }

    public static function getToken(array $response, string $gateway) {
        switch($gateway) {
            case PaymentGatewayConst::PAYPAL:
                return $response['token'] ?? "";
                break;
            case PaymentGatewayConst::G_PAY:
                return $response['gpay_trans_id'] ?? "";
                break;
            case PaymentGatewayConst::COIN_GATE:
                return $response['token'] ?? "";
                break;
            case PaymentGatewayConst::QRPAY:
                return $response['token'] ?? "";
                break;
            case PaymentGatewayConst::STRIPE:
                return $response['token'] ?? "";
                break;
            case PaymentGatewayConst::FLUTTERWAVE:
                return $response['token'] ?? "";
                break;
            case PaymentGatewayConst::RAZORPAY:
                return $response['token'] ?? "";
                break;
            case PaymentGatewayConst::SSLCOMMERZ:
                return $response['token'] ?? "";
                break;
            case PaymentGatewayConst::PERFECT_MONEY:
                return $response['PAYMENT_ID'] ?? "";
                break;
            default:
                throw new Exception("Oops! Gateway not registered in getToken method");
        }
        throw new Exception("Gateway token not found!");
    }

    public function getResponseMethod($gateway) {

        $gateway_is = PaymentGatewayConst::registerGatewayRecognization();

        foreach($gateway_is as $method => $gateway_name) {
            if(method_exists($this,$method)) {
                if($this->$method($gateway)) {
                    return $this->generateSuccessMethodName($gateway_name);
                    break;
                }
            }
        }
        throw new Exception("Payment gateway response method not declared in generateResponseMethod");
    }

    public function getCallbackResponseMethod($gateway) {

        $gateway_is = PaymentGatewayConst::registerGatewayRecognization();
        foreach($gateway_is as $method => $gateway_name) {
            if(method_exists($this,$method)) {
                if($this->$method($gateway)) {
                    return $this->generateCallbackMethodName($gateway_name);
                    break;
                }
            }
        }

    }

    public function generateCallbackMethodName(string $name) {
        $name = $this->removeSpacialChar($name,"");
        return $name . "CallbackResponse";
    }

    public function generateSuccessMethodName(string $name) {
        $name = $this->removeSpacialChar($name,"");
        return $name . "Success";
    }

    public function generateBtnPayResponseMethod(string $gateway)
    {
        $name = $this->removeSpacialChar($gateway,"");
        return $name . "BtnPay";
    }

    function removeSpacialChar($string, $replace_string = "") {
        return preg_replace("/[^A-Za-z0-9]/",$replace_string,$string);
    }

    // Update Code (Need to check)
    public function createTransaction($output, $status = PaymentGatewayConst::STATUSSUCCESS, $temp_remove = true) {
        $record_handler = $output['record_handler'];
        $inserted_id = $this->$record_handler($output,$status);
        // $this->insertCharges($output,$inserted_id);
        $this->insertDevice($output,$inserted_id);
        if($temp_remove) {
            $this->removeTempData($output);
        }

        if($this->requestIsApiUser()) {
            // logout user
            $api_user_login_guard = $this->output['api_login_guard'] ?? null;
            if($api_user_login_guard != null) {
                auth()->guard($api_user_login_guard)->logout();
            }
        }
    }


    public function insertRecordWeb($output, $status) {

        if($this->predefined_user) {
            $user = $this->predefined_user;
        }else {
            $user = auth()->guard('web')->user();
        }

        $trx_id = generate_unique_string("transactions","trx_id",16);
        DB::beginTransaction();
        try{
            $id = DB::table("transactions")->insertGetId([
                'type'                          => $output['type'],
                'trx_id'                        => $trx_id,
                'user_type'                     => GlobalConst::USER,
                'user_id'                       => $user->id,
                'payment_gateway_currency_id'   => $output['currency']->id,
                'request_amount'                => $output['amount']->requested_amount,
                'request_currency'              => get_default_currency_code(),
                'exchange_rate'                 => $output['amount']->exchange_rate,
                'percent_charge'                => $output['amount']->percent_charge,
                'fixed_charge'                  => $output['amount']->fixed_charge,
                'total_charge'                  => $output['amount']->total_charge,
                'total_payable'                 => $output['amount']->total_amount,
                'receive_amount'                => $output['amount']->requested_amount,
                'available_balance'             => 0,
                'payment_currency'              => $output['currency']->currency_code,
                'remark'                        => ucwords(remove_special_char($output['type']," ")) . " With " . $output['gateway']->name,
                'details'                       => json_encode(['gateway_response' => $output['capture']]),
                'status'                        => $status,
                'callback_ref'                  => $output['callback_ref'] ?? null,
                'booking_token'                 => $output['tempData']['data']->booking_token,
                'created_at'                    => now(),
            ]);

            if($status === PaymentGatewayConst::STATUSSUCCESS) {

            }

            DB::commit();
        }catch(Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage());
        }
        return $id;
    }



    public function insertCharges($output,$id) {
        // DB::beginTransaction();
        // try{
        //     DB::table('transaction_charges')->insert([
        //         'transaction_id'    => $id,
        //         'percent_charge'    => $output['amount']->percent_charge,
        //         'fixed_charge'      => $output['amount']->fixed_charge,
        //         'total_charge'      => $output['amount']->total_charge,
        //         'created_at'        => now(),
        //     ]);
        //     DB::commit();
        // }catch(Exception $e) {
        //     DB::rollBack();
        //     throw new Exception($e->getMessage());
        // }
    }

    public function insertDevice($output,$id) {
        $client_ip = request()->ip() ?? false;
        $location = geoip()->getLocation($client_ip);
        $agent = new Agent();


        $mac = "";

        DB::beginTransaction();
        try{
            DB::table("transaction_devices")->insert([
                'transaction_id'=> $id,
                'ip'            => $client_ip,
                'mac'           => $mac,
                'city'          => $location['city'] ?? "",
                'country'       => $location['country'] ?? "",
                'longitude'     => $location['lon'] ?? "",
                'latitude'      => $location['lat'] ?? "",
                'timezone'      => $location['timezone'] ?? "",
                'browser'       => $agent->browser() ?? "",
                'os'            => $agent->platform() ?? "",
            ]);
            DB::commit();
        }catch(Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage());
        }
    }

    public function removeTempData($output) {
        try{
            $id = $output['tempData']['id'];
            TemporaryData::find($id)->delete();
        }catch(Exception $e) {
            // handle error
        }
    }

    public function api() {
        $output = $this->output;
        if(!$output) throw new Exception("Something went wrong! Gateway render failed. Please call gateway() method before calling api() method");
        $sources = $this->setSource(PaymentGatewayConst::APP);
        $url_params = $this->makeUrlParams($sources);
        $this->setUrlParams($url_params);
        return $this;
    }

    public function setSource(string $source) {
        $sources = [
            'r-source'  => $source,
        ];

        return $sources;
    }

    public function makeUrlParams(array $sources) {
        try{
            $params = http_build_query($sources);
        }catch(Exception $e) {
            throw new Exception("Something went wrong! Failed to make URL Params.");
        }
        return $params;
    }

    public function setUrlParams(string $url_params) {
        $output = $this->output;
        if(isset($output['url_params'])) {
            // if already param has
            $params = $this->output['url_params'];
            $update_params = $params . "&" . $url_params;
            $this->output['url_params'] = $update_params; // Update/ reassign URL Parameters
        }else {
            $this->output['url_params']  = $url_params; // add new URL Parameters;
        }
    }

    public function getUrlParams() {
        $output = $this->output;
        if(!$output || !isset($output['url_params'])) $params = "";
        $params = $output['url_params'] ?? "";
        return $params;
    }

    public function setGatewayRoute($route_name, $gateway, $params = null) {
        if(!Route::has($route_name)) throw new Exception('Route name ('.$route_name.') is not defined');
        if($params) {
            return route($route_name,$gateway."?".$params);
        }
        return route($route_name,$gateway);
    }

    public function requestIsApiUser() {
        $request_source = request()->get('r-source');
        if($request_source != null && $request_source == PaymentGatewayConst::APP) return true;
        if(request()->routeIs('api.*')) return true;
        return false;
    }

    public static function makePlainText($string) {
        $string = Str::lower($string);
        return preg_replace("/[^A-Za-z0-9]/","",$string);
    }

    public function searchWithReferenceInTransaction($reference) {

        $transaction = DB::table('transactions')->where('callback_ref',$reference)->first();

        if($transaction) {
            return $transaction;
        }

        return false;
    }

    public function handleCallback($reference,$callback_data,$gateway_name) {
        if($reference == PaymentGatewayConst::CALLBACK_HANDLE_INTERNAL) {
            $gateway = PaymentGatewayModel::gateway($gateway_name)->first();
            $callback_response_receive_method = $this->getCallbackResponseMethod($gateway);
            return $this->$callback_response_receive_method($callback_data, $gateway);
        }

        $transaction = Transaction::where('callback_ref',$reference)->first();
        $this->output['callback_ref']       = $reference;
        $this->output['capture']            = $callback_data;

        if($transaction) {
            $gateway_currency = $transaction->gateway_currency;
            $gateway = $gateway_currency->gateway;

            $requested_amount = $transaction->request_amount;
            $validator_data = [
                $this->currency_input_name  => $gateway_currency->alias,
                $this->amount_input         => $requested_amount
            ];


            $this->output['transaction']    = $transaction;

        }else {
            // find reference on temp table
            $tempData = TemporaryData::where('identifier',$reference)->first();
            if($tempData) {
                $gateway_currency_id = $tempData->data->currency ?? null;
                $gateway_currency = PaymentGatewayCurrency::find($gateway_currency_id);
                if($gateway_currency) {
                    $gateway = $gateway_currency->gateway;

                    $requested_amount = $tempData['data']->amount->requested_amount ?? 0;
                    $validator_data = [
                        $this->currency_input_name  => $gateway_currency->alias,
                        $this->amount_input         => $requested_amount
                    ];

                    $this->output['tempData'] = $tempData;
                }
            }
        }


        if(isset($gateway)) {

            $this->request_data = $validator_data;
            $this->gateway();

            $callback_response_receive_method = $this->getCallbackResponseMethod($gateway);
            return $this->$callback_response_receive_method($callback_data, $this->output);
        }

        logger("Gateway not found!!" , [
            "reference"     => $reference,
        ]);
    }

    public static function getValueFromGatewayCredentials($gateway, $keywords) {
        $result = "";
        $outer_break = false;
        foreach($keywords as $item) {
            if($outer_break == true) {
                break;
            }
            $modify_item = PaymentGateway::makePlainText($item);
            foreach($gateway->credentials ?? [] as $gatewayInput) {
                $label = $gatewayInput->label ?? "";
                $label = PaymentGateway::makePlainText($label);

                if($label == $modify_item) {
                    $result = $gatewayInput->value ?? "";
                    $outer_break = true;
                    break;
                }
            }
        }
        return $result;
    }

    public function generateLinkForRedirectForm($token, $gateway)
    {
        $redirection = $this->getRedirection();
        $form_redirect_route = $redirection['redirect_form'];
        return route($form_redirect_route, [$gateway, 'token' => $token]);
    }

    /**
     * Link generation for button pay (JS checkout)
     */
    public function generateLinkForBtnPay($token, $gateway)
    {
        $redirection = $this->getRedirection();
        $form_redirect_route = $redirection['btn_pay'];
        return route($form_redirect_route, [$gateway, 'token' => $token]);
    }

    /**
     * Handle Button Pay (JS Checkout) Redirection
     */
    public function handleBtnPay($gateway, $request_data)
    {
        if(!array_key_exists('token', $request_data)) throw new Exception("Requested with invalid token");
        $temp_token = $request_data['token'];

        $temp_data = TemporaryData::where('identifier', $temp_token)->first();
        if(!$temp_data) throw new Exception("Requested with invalid token");

        $this->request_data = $temp_data->toArray();
        $this->authenticateTempData();

        $method = $this->generateBtnPayResponseMethod($gateway);

        if(method_exists($this, $method)) {
            return $this->$method($temp_data);
        }

        throw new Exception("Button Pay response method [" . $method ."()] not available in this gateway");
    }

    public function insertProfit($charge) {
        DB::beginTransaction();
        try{
            DB::table('admin_profits')->insert([
                'percent_charge'    => 0,
                'fixed_charge'      => 0,
                'total_charge'      => $charge,
                'created_at'        => now(),
            ]);
            DB::commit();
        }catch(Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage());
        }
    }

    //  Data insert for callback methods
    public function insertForCallBack($output, $status) {


        $trx_id = generate_unique_string("transactions","trx_id",16);
        DB::beginTransaction();
        try{
            $id = DB::table("transactions")->insertGetId([
                'type'                          => $output['type'],
                'trx_id'                        => $trx_id,
                'user_type'                     => GlobalConst::USER,
                'user_id'                       => $output['tempData']->data->creator_id,
                'payment_gateway_currency_id'   => $output['currency']->id,
                'request_amount'                => $output['amount']->requested_amount,
                'request_currency'              => get_default_currency_code(),
                'exchange_rate'                 => $output['amount']->exchange_rate,
                'percent_charge'                => $output['amount']->percent_charge,
                'fixed_charge'                  => $output['amount']->fixed_charge,
                'total_charge'                  => $output['amount']->total_charge,
                'total_payable'                 => $output['amount']->total_amount,
                'receive_amount'                => $output['amount']->requested_amount,
                'available_balance'             => 0,
                'payment_currency'              => $output['currency']->currency_code,
                'remark'                        => ucwords(remove_special_char($output['type']," ")) . " With " . $output['gateway']->name,
                'details'                       => json_encode(['gateway_response' => $output['capture']]),
                'status'                        => $status,
                'callback_ref'                  => $output['callback_ref'] ?? null,
                'booking_token'                 => $output['tempData']->data->booking_token,
                'created_at'                    => now(),
            ]);

            $this->bookingConfirm($output['tempData']->data->booking_token,'online-payment',$trx_id);

            DB::commit();
        }catch(Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage());
        }
        return $id;
    }


    public function bookingConfirm($token, $type, $trx_id)
    {
        $temp_booking = TemporaryData::where('identifier', $token)->first();
        $basic_setting = BasicSettings::first();

        $temp_data = json_decode(json_encode($temp_booking->data), true);
        try {
            $booking_data = CarBooking::create([
                'car_id' => $temp_data['car_id'],
                'user_id' => $temp_data['user_id'] ?? null,
                'slug' => $temp_data['slug'] ?? $temp_data['car_slug'],
                'trx_id' => $trx_id,
                'payment_type' => $type,
                'phone' => $temp_data['phone'] ?? $temp_data['mobile'],
                'email' => $temp_data['email'] ?? $temp_data['credentials'],
                'location' => $temp_data['location'],
                'destination' => $temp_data['destination'],
                'trip_id' => generate_unique_code(),
                'pickup_time' => $temp_data['pickup_time'],
                'round_pickup_time' => $temp_data['round_pickup_time'],
                'amount' => $temp_data['total_rent'] ?? $temp_data['fees'],
                'charges' => $total_charge ?? 0,
                'distance' => $temp_data['distance'],
                'pickup_date' => $temp_data['pickup_date'],
                'round_pickup_date' => $temp_data['round_pickup_date'],
                'message' => $temp_data['message'] ?? '',
                'status' => 1,
            ]);


            $confirm_booking = CarBooking::with('cars')
                ->where('slug', $booking_data->slug)
                ->first();
            // $temp_booking->delete();

            $this->bookingNotification($confirm_booking, $basic_setting);

            logger('booking done');
        } catch (Exception $e) {

        }
    }


    public function bookingNotification($confirm_booking, $basic_setting)
    {
        try {
        Notification::route('mail', $confirm_booking->email)->notify(new carBookingNotification($confirm_booking));
            $notification_content = [
                'title' => __('Booking'),
                'message' => __('Your have a incoming booking request (Car Model: ') . $confirm_booking->cars->car_model . __(', Car Number: ') . $confirm_booking->cars->car_number . __(', Pick-up Date: ') . ($confirm_booking->pickup_date ? Carbon::parse($confirm_booking->pickup_date)->format('d-m-Y') : '') . __(', Pick-up Time: ') . ($confirm_booking->pickup_time ? Carbon::parse($confirm_booking->pickup_time)->format('h:i A') : '') . __(').'),
            ];
            VendorNotification::create([
                'vendor_id' => $confirm_booking->cars->vendor_id,
                'message' => $notification_content,
            ]);
        } catch (Exception $e) {
            logger($e);
        }
    }
}
