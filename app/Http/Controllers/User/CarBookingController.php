<?php

namespace App\Http\Controllers\User;

use App\Constants\CarBookingConst;
use App\Constants\GlobalConst;
use Exception;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\TemporaryData;
use App\Constants\LanguageConst;
use App\Constants\PaymentGatewayConst;
use App\Models\Admin\SiteSections;
use App\Constants\SiteSectionConst;
use App\Http\Controllers\Controller;
use App\Http\Helpers\PushNotificationHelper;
use App\Models\Admin\PaymentGateway;
use App\Models\CarBooking;
use App\Models\Vendor\Cars\Car;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Notification;
use App\Notifications\User\carBookingNotification;
use App\Providers\Admin\BasicSettingsProvider;
use App\Http\Helpers\PaymentGateway as PaymentGatewayHelper;
use App\Models\Admin\BasicSettings;
use App\Models\Admin\CryptoTransaction;
use App\Models\Admin\PaymentGatewayCurrency;
use App\Models\Admin\TransactionSetting;
use App\Models\Transaction;
use App\Models\Vendor\VendorNotification;
use App\Traits\ControlDynamicInputFields;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class CarBookingController extends Controller
{
    use ControlDynamicInputFields;

    public function booking(BasicSettingsProvider $basic_settings, $slug)
    {
        $site_name = $basic_settings->get()?->site_name;
        $page_title = setPageTitle(__('Car Booking'));
        $car = Car::where('slug', $slug)->first();
        if (!$car) {
            abort(404);
        }
        $footer_slug = Str::slug(SiteSectionConst::FOOTER_SECTION);
        $footer = SiteSections::getData($footer_slug)->first();
        $validated_user = auth()->user();
        $default = LanguageConst::NOT_REMOVABLE;
        return view('user.sections.car-booking.car-booking', compact('site_name', 'page_title', 'car', 'footer', 'validated_user', 'default'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'area' => 'required',
            'type' => 'required',
            'location' => 'required',
            'destination' => 'required',
            'credentials' => 'required|email',
            'pickup_time' => 'required',
            'pickup_date' => 'required',
            'distance' => 'required|min:0',
            'mobile' => 'nullable',
            'round_pickup_date' => 'nullable',
            'round_pickup_time' => 'nullable',
            'message' => 'nullable',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput($request->all());
        }

        $validated = $validator->validate();
        $pickupDateTime = Carbon::parse($validated['pickup_date'] . ' ' . $validated['pickup_time']);

        if ($pickupDateTime->isPast()) {
            return back()->with(['error' => [__('Pickup date and time must be in the future.')]]);
        }

        if (!empty($validated['round_pickup_date']) && !empty($validated['round_pickup_time'])) {
            $roundPickupDateTime = Carbon::parse($validated['round_pickup_date'] . ' ' . $validated['round_pickup_time']);
            if ($roundPickupDateTime->isPast()) {
                return back()->with(['error' => [__('Round pickup date and time must be in the future.')]]);
            }

            if ($roundPickupDateTime->lte($pickupDateTime)) {
                return back()->with(['error' => [__('Round pickup date and time must be greater than pickup date and time.')]]);
            }
        }
        $validated['email'] = $validated['credentials'];
        $validated['phone'] = $validated['mobile'];
        $validated['slug'] = Str::uuid();

        if (auth()->check()) {
            $validated['user_id'] = auth()->user()->id;
        } else {
            $validated['user_id'] = null;
        }

        $cars = Car::where('car_area_id', $request->area)
            ->where('car_type_id', $request->type)
            ->where('status', true)
            ->where('approval', true)
            ->whereDoesntHave('bookings', function ($query) use ($validated) {
                $query->where('pickup_date', Carbon::parse($validated['pickup_date']))->where('status', CarBookingConst::STATUONGOING);
            })
            ->get();

        try {
            $car_booking = TemporaryData::create([
                'identifier' => generate_unique_string('temporary_datas', 'identifier', 20),
                'type' => Str::slug(CarBookingConst::CAR_BOOKING),
                'data' => $validated,
            ]);

            Session::forget('form_data');

            return redirect(route('frontend.cars'))->with(['cars' => $cars, 'token' => $car_booking->identifier]);

        } catch (Exception $e) {
            return back()->with(['error' => [__('Something Went Wrong! Please try again.')]]);
        }
    }

    public function preview(BasicSettingsProvider $basic_settings, $token, $id)
    {
        $page_title = setPageTitle(__('Booking Preview'));
        $site_name = $basic_settings->get()?->site_name;

        $footer_slug = Str::slug(SiteSectionConst::FOOTER_SECTION);
        $footer = SiteSections::getData($footer_slug)->first();

        $validated_user = auth()->user();
        $customer = TemporaryData::where('identifier', $token)->first();
        $car = Car::where('id', $id)->first();
        if ($customer->data->round_pickup_date) {
            $total_rent = $customer->data->distance * $car->fees * 2;
        } else {
            $total_rent = $customer->data->distance * $car->fees;
        }

        $temp_data = json_decode(json_encode($customer->data), true);

        $temp_data['car_id'] = $car->id;
        $temp_data['total_rent'] = $total_rent;

        try {
            $customer->update([
                'data' => $temp_data,
            ]);
        } catch (Exception $e) {
            return redirect(route('frontend.index'))->with(['error' => [__('Something Went Wrong! Please try again.')]]);
        }

        $default = LanguageConst::NOT_REMOVABLE;
        $payment_gateways = PaymentGateway::addMoney()->active()->with('currencies')->has('currencies')->get();

        return view('user.sections.car-booking.booking-preview', compact('page_title', 'site_name', 'customer', 'footer', 'car', 'payment_gateways', 'total_rent', 'default'));
    }

    public function confirm($token, Request $request)
    {
        $payment = $request->payment;
        if ($payment === Str::slug(PaymentGatewayConst::CASH)) {
            $trx_id = generate_unique_string('car_bookings', 'trx_id', 16);
            return $this->bookingConfirm($token, 'cash', $trx_id, $request->car_id, $request->total_rent);
        } else {
            $temp_booking = TemporaryData::where('identifier', $token)->first();
            if (!$temp_booking) {
                return redirect()
                    ->route('frontend.index')
                    ->with(['error' => [__('Something went wrong! Please try again')]]);
            }
            $temp_data = json_decode(json_encode($temp_booking->data), true);
            $request->merge(['amount' => $temp_data['total_rent'], 'token' => $token]);
            try {
                $instance = PaymentGatewayHelper::init($request->all())
                    ->type(PaymentGatewayConst::TYPEADDMONEY)
                    ->setProjectCurrency(PaymentGatewayConst::PROJECT_CURRENCY_SINGLE)
                    ->gateway()
                    ->render();

                if ($instance instanceof RedirectResponse === false && isset($instance['gateway_type']) && $instance['gateway_type'] == PaymentGatewayConst::MANUAL) {
                    $manual_handler = $instance['distribute'];
                    return $this->$manual_handler($instance);
                }
            } catch (Exception $e) {
                return back()->with(['error' => [$e->getMessage()]]);
            }
            return $instance;
        }
    }

    public function bookingConfirm($token, $type, $trx_id)
    {
        $temp_booking = TemporaryData::where('identifier', $token)->first();
        $basic_setting = BasicSettings::first();

        if (!$temp_booking) {
            return redirect()
                ->route('frontend.index')
                ->with(['error' => [__('Something went wrong! Please try again')]]);
        }
        $temp_data = json_decode(json_encode($temp_booking->data), true);
        try {
            if ($type === 'cash') {
                $charges = TransactionSetting::where('slug','cash')->first();
                $amount = $temp_data['total_rent'];

                $fixed_charge_calc = $charges->fixed_charge;
                $percent_charge_calc = (($amount / 100) * $charges->percent_charge);

                $total_charge = $fixed_charge_calc + $percent_charge_calc;
            }
            $booking_data = CarBooking::create([
                'car_id' => $temp_data['car_id'],
                'user_id' => auth()->user()->id ?? null,
                'slug' => $temp_data['slug'],
                'trx_id' => $trx_id,
                'payment_type' => $type,
                'phone' => $temp_data['phone'],
                'email' => $temp_data['email'],
                'location' => $temp_data['location'],
                'destination' => $temp_data['destination'],
                'trip_id' => generate_unique_code(),
                'pickup_time' => $temp_data['pickup_time'],
                'round_pickup_time' => $temp_data['round_pickup_time'],
                'amount' => $temp_data['total_rent'],
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
            $temp_booking->delete();

            $this->bookingNotification($confirm_booking, $basic_setting);

            return view('user.sections.car-booking.booking-complete');
        } catch (Exception $e) {
            return redirect()
                ->route('frontend.index', $token)
                ->with(['error' => [__('Something went wrong! Please try again')]]);
        }
    }

    public function bookingNotification($confirm_booking, $basic_setting)
    {
        if (auth()->check()) {
            $notification_content = [
                'title' => __('Booking'),
                'message' => __('Your have a incoming booking request (Car Model: ') . $confirm_booking->cars->car_model . __(', Car Number: ') . $confirm_booking->cars->car_number . __(', Pick-up Date: ') . ($confirm_booking->pickup_date ? Carbon::parse($confirm_booking->pickup_date)->format('d-m-Y') : '') . __(', Pick-up Time: ') . ($confirm_booking->pickup_time ? Carbon::parse($confirm_booking->pickup_time)->format('h:i A') : '') . __(').'),
            ];
            VendorNotification::create([
                'vendor_id' => $confirm_booking->cars->vendor_id,
                'message' => $notification_content,
            ]);
            try {
                if ($basic_setting->email_notification) {
                    Notification::route('mail', $confirm_booking->email)->notify(new carBookingNotification($confirm_booking));
                }
                if ($basic_setting->vendor_push_notification) {
                    (new PushNotificationHelper())
                        ->prepare(
                            [$confirm_booking->cars->vendor_id],
                            [
                                'title' => $notification_content['title'],
                                'desc' => $notification_content['message'],
                                'user_type' => 'vendor',
                            ],
                        )
                        ->send();
                }
            } catch (Exception $e) {
            }
        }
    }

    public function success(Request $request, $gateway)
    {
        try {
            $token = PaymentGatewayHelper::getToken($request->all(), $gateway);
            $temp_data = TemporaryData::where('type', PaymentGatewayConst::TYPEADDMONEY)
                ->where('identifier', $token)
                ->first();

            if (Transaction::where('callback_ref', $token)->exists()) {
                if (!$temp_data) {
                    return redirect()
                        ->route('frontend.index')
                        ->with(['success' => [__('Transaction request sended successfully!')]]);
                }
            } else {
                if (!$temp_data) {
                    return redirect()
                        ->route('frontend.index')
                        ->with(['error' => [__("Transaction failed. Record didn't saved properly. Please try again.")]]);
                }
            }

            $update_temp_data = json_decode(json_encode($temp_data->data), true);
            $update_temp_data['callback_data'] = $request->all();
            $temp_data->update([
                'data' => $update_temp_data,
            ]);
            $temp_data = $temp_data->toArray();
            $instance = PaymentGatewayHelper::init($temp_data)
                ->type(PaymentGatewayConst::TYPEADDMONEY)
                ->setProjectCurrency(PaymentGatewayConst::PROJECT_CURRENCY_SINGLE)
                ->responseReceive();
            if ($instance instanceof RedirectResponse) {
                return $instance;
            }
        } catch (Exception $e) {
            return back()->with(['error' => [$e->getMessage()]]);
        }
        $transaction_info = Transaction::where('booking_token', $temp_data['data']->booking_token)->first();

        $car_booking = CarBooking::where('trx_id',$transaction_info->trx_id)->first();

        if(!$car_booking){
            $this->bookingConfirm($temp_data['data']->booking_token, 'online-payment', $transaction_info->trx_id);
        }

        return view('user.sections.car-booking.booking-complete');
    }

    public function cancel(Request $request, $gateway)
    {
        $token = PaymentGatewayHelper::getToken($request->all(), $gateway);
        if (
            $temp_data = TemporaryData::where('type', PaymentGatewayConst::TYPEADDMONEY)
                ->where('identifier', $token)
                ->first()
        ) {
            $temp_data->delete();
        }

        return redirect()->route('frontend.index');
    }

    public function postSuccess(Request $request, $gateway)
    {
        try {
            $token = PaymentGatewayHelper::getToken($request->all(), $gateway);
            $temp_data = TemporaryData::where('type', PaymentGatewayConst::TYPEADDMONEY)
                ->where('identifier', $token)
                ->first();
            Auth::guard($temp_data->data->creator_guard)->loginUsingId($temp_data->data->creator_id);
        } catch (Exception $e) {
            return redirect()->route('frontend.index');
        }

        return $this->success($request, $gateway);
    }

    public function postCancel(Request $request, $gateway)
    {
        try {
            $token = PaymentGatewayHelper::getToken($request->all(), $gateway);
            $temp_data = TemporaryData::where('type', PaymentGatewayConst::TYPEADDMONEY)
                ->where('identifier', $token)
                ->first();
            Auth::guard($temp_data->data->creator_guard)->loginUsingId($temp_data->data->creator_id);
        } catch (Exception $e) {
            return redirect()->route('frontend.index');
        }

        return $this->cancel($request, $gateway);
    }

    public function callback(Request $request, $gateway)
    {
        $callback_data = $request->all();
        $callback_token = $callback_data['payload']['order']['entity']['receipt']??$callback_data['data']['reference']??$request->get('token');

        try {
            PaymentGatewayHelper::init([])
                ->type(PaymentGatewayConst::TYPEADDMONEY)
                ->setProjectCurrency(PaymentGatewayConst::PROJECT_CURRENCY_SINGLE)
                ->handleCallback($callback_token, $callback_data, $gateway);
        } catch (Exception $e) {
            // handle Error
            logger($e);
        }
    }

    public function cryptoPaymentAddress(Request $request, $trx_id)
    {
        $page_title = 'Crypto Payment Address';
        $transaction = Transaction::where('trx_id', $trx_id)->firstOrFail();

        if ($transaction->gateway_currency->gateway->isCrypto() && $transaction->details?->payment_info?->receiver_address ?? false) {
            return view('user.sections.add-money.payment.crypto.address', compact('transaction', 'page_title'));
        }

        return abort(404);
    }

    public function cryptoPaymentConfirm(Request $request, $trx_id)
    {
        $transaction = Transaction::where('trx_id', $trx_id)
            ->where('status', PaymentGatewayConst::STATUSWAITING)
            ->firstOrFail();

        $dy_input_fields = $transaction->details->payment_info->requirements ?? [];
        $validation_rules = $this->generateValidationRules($dy_input_fields);

        $validated = [];
        if (count($validation_rules) > 0) {
            $validated = Validator::make($request->all(), $validation_rules)->validate();
        }

        if (!isset($validated['txn_hash'])) {
            return back()->with(['error' => [__('Transaction hash is required for verify')]]);
        }

        $receiver_address = $transaction->details->payment_info->receiver_address ?? '';

        // check hash is valid or not
        $crypto_transaction = CryptoTransaction::where('txn_hash', $validated['txn_hash'])
            ->where('receiver_address', $receiver_address)
            ->where('asset', $transaction->gateway_currency->currency_code)
            ->where(function ($query) {
                return $query->where('transaction_type', 'Native')->orWhere('transaction_type', 'native');
            })
            ->where('status', PaymentGatewayConst::NOT_USED)
            ->first();

        if (!$crypto_transaction) {
            return back()->with(['error' => [__('Transaction hash is not valid! Please input a valid hash')]]);
        }

        if ($crypto_transaction->amount >= $transaction->total_payable == false) {
            if (!$crypto_transaction) {
                return back()->with(['error' => [__('Insufficient amount added. Please contact with system administrator')]]);
            }
        }

        DB::beginTransaction();
        try {
            // update crypto transaction as used
            DB::table($crypto_transaction->getTable())
                ->where('id', $crypto_transaction->id)
                ->update([
                    'status' => PaymentGatewayConst::USED,
                ]);

            // update transaction status
            $transaction_details = json_decode(json_encode($transaction->details), true);
            $transaction_details['payment_info']['txn_hash'] = $validated['txn_hash'];

            DB::table($transaction->getTable())
                ->where('id', $transaction->id)
                ->update([
                    'details' => json_encode($transaction_details),
                    'status' => PaymentGatewayConst::STATUSSUCCESS,
                ]);

            DB::commit();
            $this->bookingConfirm($transaction->booking_token, 'online-payment', $transaction->id);

        } catch (Exception $e) {
            DB::rollback();
            return back()->with(['error' => [__('Something went wrong! Please try again')]]);
        }

        return view('user.sections.car-booking.booking-complete');
    }

    public function redirectUsingHTMLForm(Request $request, $gateway)
    {
        $temp_data = TemporaryData::where('identifier', $request->token)->first();
        if (!$temp_data || $temp_data->data->action_type != PaymentGatewayConst::REDIRECT_USING_HTML_FORM) {
            return back()->with(['error' => ['Request token is invalid!']]);
        }
        $redirect_form_data = $temp_data->data->redirect_form_data;
        $action_url = $temp_data->data->action_url;
        $form_method = $temp_data->data->form_method;

        return view('payment-gateway.redirect-form', compact('redirect_form_data', 'action_url', 'form_method'));
    }

    /**
     * Redirect Users for collecting payment via Button Pay (JS Checkout)
     */
    public function redirectBtnPay(Request $request, $gateway)
    {
        try {
            return PaymentGatewayHelper::init([])
                ->setProjectCurrency(PaymentGatewayConst::PROJECT_CURRENCY_SINGLE)
                ->handleBtnPay($gateway, $request->all());
        } catch (Exception $e) {
            return redirect()
                ->route('frontend.index')
                ->with(['error' => [$e->getMessage()]]);
        }
    }

    public function handleManualPayment($payment_info)
    {
        // Insert temp data
        $data = [
            'type' => PaymentGatewayConst::TYPEADDMONEY,
            'identifier' => generate_unique_string('temporary_datas', 'identifier', 16),
            'data' => [
                'gateway_currency_id' => $payment_info['currency']->id,
                'amount' => $payment_info['amount'],
                'booking_token' => $payment_info['form_data']['token'],
            ],
        ];
        try {
            TemporaryData::create($data);
        } catch (Exception $e) {
            return redirect()
                ->route('frontend.index')
                ->with(['error' => [__('Failed to save data. Please try again')]]);
        }
        return redirect()->route('user.car.booking.manual.form', $data['identifier']);
    }

    public function showManualForm($token, BasicSettingsProvider $basic_settings)
    {
        $tempData = TemporaryData::search($token)->first();
        if (!$tempData || $tempData->data == null || !isset($tempData->data->gateway_currency_id)) {
            return redirect()
                ->route('frontend.index')
                ->with(['error' => [__('Invalid request')]]);
        }
        $gateway_currency = PaymentGatewayCurrency::find($tempData->data->gateway_currency_id);
        if (!$gateway_currency || !$gateway_currency->gateway->isManual()) {
            return redirect()
                ->route('frontend.index')
                ->with(['error' => [_('Selected gateway is invalid')]]);
        }
        $gateway = $gateway_currency->gateway;
        if (!$gateway->input_fields || !is_array($gateway->input_fields)) {
            return redirect()
                ->route('frontend.index')
                ->with(['error' => [__('This payment gateway is under constructions. Please try with another payment gateway')]]);
        }
        $amount = $tempData->data->amount;

        $footer_slug = Str::slug(SiteSectionConst::FOOTER_SECTION);
        $footer = SiteSections::getData($footer_slug)->first();

        $site_name = $basic_settings->get()?->site_name;

        $page_title = __('Payment Instructions');
        return view('user.sections.manual-payment.manual-payment-section', compact('site_name', 'gateway', 'page_title', 'token', 'amount', 'footer'));
    }

    public function manualSubmit(Request $request, $token)
    {
        $request->merge(['identifier' => $token]);
        $tempDataValidate = Validator::make($request->all(), [
            'identifier' => 'required|string|exists:temporary_datas',
        ])->validate();

        $tempData = TemporaryData::search($tempDataValidate['identifier'])->first();
        if (!$tempData || $tempData->data == null || !isset($tempData->data->gateway_currency_id)) {
            return redirect()
                ->route('user.add.money.index')
                ->with(['error' => [__('Invalid request')]]);
        }
        $gateway_currency = PaymentGatewayCurrency::find($tempData->data->gateway_currency_id);
        if (!$gateway_currency || !$gateway_currency->gateway->isManual()) {
            return redirect()
                ->route('user.add.money.index')
                ->with(['error' => [__('Selected gateway is invalid')]]);
        }
        $gateway = $gateway_currency->gateway;
        $amount = $tempData->data->amount ?? null;
        if (!$amount) {
            return redirect()
                ->route('frontend.index')
                ->with(['error' => [__('Transaction Failed. Failed to save information. Please try again')]]);
        }

        $this->file_store_location = 'transaction';
        $dy_validation_rules = $this->generateValidationRules($gateway->input_fields);

        $validated = Validator::make($request->all(), $dy_validation_rules)->validate();
        $get_values = $this->placeValueWithFields($gateway->input_fields, $validated);
        $trx_id = generate_unique_string('transactions', 'trx_id', 16);

        $booking_token = $tempData->data->booking_token;

        // Make Transaction
        DB::beginTransaction();
        try {
            $id = DB::table('transactions')->insertGetId([
                'type' => PaymentGatewayConst::TYPEADDMONEY,
                'trx_id' => $trx_id,
                'user_type' => GlobalConst::USER,
                'user_id' => auth()->user()->id,
                'payment_gateway_currency_id' => $gateway_currency->id,
                'request_amount' => $amount->requested_amount,
                'request_currency' => get_default_currency_code(),
                'exchange_rate' => $gateway_currency->rate,
                'percent_charge' => $amount->percent_charge,
                'fixed_charge' => $amount->fixed_charge,
                'total_charge' => $amount->total_charge,
                'total_payable' => $amount->total_amount,
                'receive_amount' => $amount->will_get,
                'available_balance' => 0,
                'booking_token' => $booking_token,
                'payment_currency' => $gateway_currency->currency_code,
                'details' => json_encode(['input_values' => $get_values]),
                'status' => PaymentGatewayConst::STATUSPENDING,
                'created_at' => now(),
            ]);

            $this->bookingConfirm($booking_token, 'online-payment', $trx_id);
            DB::table('temporary_datas')->where('identifier', $token)->delete();
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()
                ->route('user.car.booking.manual.form', $token)
                ->with(['error' => [__('Something went wrong! Please try again')]]);
        }

        return view('user.sections.car-booking.booking-complete');
    }

    public function showRepaymentForm(BasicSettingsProvider $basic_settings, $trx_id)
    {
        $page_title = __('Payment Instructions');
        $site_name = $basic_settings->get()?->site_name;
        $footer_slug = Str::slug(SiteSectionConst::FOOTER_SECTION);
        $footer = SiteSections::getData($footer_slug)->first();

        $transaction = Transaction::where('trx_id', $trx_id)->first();
        if (!$transaction || !isset($transaction->payment_gateway_currency_id)) {
            return redirect()
                ->route('frontend.index')
                ->with(['error' => [__('Invalid request')]]);
        }

        if ($transaction->status == PaymentGatewayConst::STATUSSUCCESS) {
            return redirect()
                ->route('frontend.index')
                ->with(['error' => [__('Transaction is already completed by admin')]]);
        }

        $gateway_currency = PaymentGatewayCurrency::find($transaction->payment_gateway_currency_id);
        if (!$gateway_currency || !$gateway_currency->gateway->isManual()) {
            return redirect()
                ->route('frontend.index')
                ->with(['error' => [_('Selected gateway is invalid')]]);
        }
        $gateway = $gateway_currency->gateway;
        if (!$gateway->input_fields || !is_array($gateway->input_fields)) {
            return redirect()
                ->route('frontend.index')
                ->with(['error' => [__('This payment gateway is under constructions. Please try with another payment gateway')]]);
        }
        $amount = $transaction->total_payable;
        $reject_reason = $transaction->reject_reason;

        return view('user.sections.manual-payment.manual-repayment-section', compact('site_name', 'reject_reason', 'gateway', 'page_title', 'trx_id', 'amount', 'footer'));
    }

    public function repaymentSubmit(Request $request, $trx_id)
    {
        $request->merge(['trx_id' => $trx_id]);
        $transactionValidate = Validator::make($request->all(), [
            'trx_id' => 'required|string|exists:transactions',
        ])->validate();

        $transaction = Transaction::where('trx_id', $trx_id)->first();
        if (!$transaction || !isset($transaction->payment_gateway_currency_id)) {
            return redirect()
                ->route('frontend.index')
                ->with(['error' => [__('Invalid request')]]);
        }

        $gateway_currency = PaymentGatewayCurrency::find($transaction->payment_gateway_currency_id);
        if (!$gateway_currency || !$gateway_currency->gateway->isManual()) {
            return redirect()
                ->route('frontend.index')
                ->with(['error' => [_('Selected gateway is invalid')]]);
        }

        $gateway = $gateway_currency->gateway;
        $amount = $transaction->total_payable ?? null;
        if (!$amount) {
            return redirect()
                ->route('frontend.index')
                ->with(['error' => [__('Transaction Failed. Failed to save information. Please try again')]]);
        }

        $this->file_store_location = 'transaction';
        $dy_validation_rules = $this->generateValidationRules($gateway->input_fields);

        $validated = Validator::make($request->all(), $dy_validation_rules)->validate();
        $get_values = $this->placeValueWithFields($gateway->input_fields, $validated);

        foreach ($transaction->details->input_values as $key => $value) {
            if ($value->type == 'file') {
                unlink('transaction/' . $value->value);
            }
        }
        DB::beginTransaction();
        try {
            $id = DB::table('transactions')
                ->where('trx_id', $trx_id)
                ->update([
                    'details' => json_encode(['input_values' => $get_values]),
                    'status' => PaymentGatewayConst::STATUSPENDING,
                ]);
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()
                ->route('user.car.booking.repayment.submit', $trx_id)
                ->with(['error' => [__('Something went wrong! Please try again')]]);
        }
        return redirect()
            ->route('frontend.index')
            ->with(['success' => [__('Transaction Success. Please wait for admin confirmation')]]);
    }
}
