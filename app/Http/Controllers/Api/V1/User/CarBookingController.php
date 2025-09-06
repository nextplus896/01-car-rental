<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Constants\CarBookingConst;
use App\Constants\GlobalConst;
use App\Constants\PaymentGatewayConst;
use App\Http\Controllers\Controller;
use App\Http\Helpers\PushNotificationHelper;
use App\Http\Helpers\Response;
use App\Models\Admin\BasicSettings;
use App\Models\Admin\Cars\CarArea;
use App\Models\Admin\Cars\CarType;
use App\Models\Admin\PaymentGateway;
use App\Models\CarBooking;
use App\Models\TemporaryData;
use App\Models\Vendor\Cars\Car;
use App\Models\Vendor\VendorNotification;
use App\Notifications\User\carBookingNotification;
use App\Traits\ControlDynamicInputFields;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use App\Models\Admin\PaymentGatewayCurrency;
use App\Http\Helpers\PaymentGateway as PaymentGatewayHelper;
use App\Models\Admin\CryptoTransaction;
use App\Models\Admin\TransactionSetting;
use App\Models\Transaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CarBookingController extends Controller
{
    use ControlDynamicInputFields;

    public function bookingHistory() {
        $user = Auth::guard('api')->user();

        $bookings = CarBooking::where('user_id',$user->id)->with(['cars'])->get();

        return Response::success([__('History fetched successfully!')],['history' => $bookings ],200);
    }

    public function carArea()
    {
        $car_area = CarArea::all();
        $message = [__('Car Area Fetched Successfully!')];
        return Response::success($message, $car_area);
    }

    public function carType()
    {
        $car_type = CarType::all();
        $message = [__('Car Type Fetched Successfully!')];
        return Response::success($message, $car_type);
    }

    public function getAreaTypes(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'area' => 'required|integer',
        ]);
        if ($validator->fails()) {
            return Response::error($validator->errors()->all());
        }
        $area = CarArea::with([
            'types' => function ($type) {
                $type->with([
                    'type' => function ($car_type) {
                        $car_type->where('status', true);
                    },
                ]);
            },
        ])->find($request->area);
        if (!$area) {
            return Response::error([__('Area Not Found')], 404);
        }

        return Response::success([__('Types fetch successfully')], ['area' => $area], 200);
    }

    public function searchCar(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'car_area' => 'required',
            'car_type' => 'required',
            'pickup_time' => 'required',
            'pickup_date' => 'required',
        ]);
        if ($validator->fails()) {
            return Response::error($validator->errors()->all());
        }
        $validated = $validator->validate();

        $pickupDateTime = Carbon::parse($validated['pickup_date'] . ' ' . $validated['pickup_time']);

        if ($pickupDateTime->isPast()) {
            return Response::error([__('Pickup date and time must be in the future.')], []);
        }

        if (!empty($validated['round_pickup_date']) && !empty($validated['round_pickup_time'])) {
            $roundPickupDateTime = Carbon::parse($validated['round_pickup_date'] . ' ' . $validated['round_pickup_time']);
            if ($roundPickupDateTime->isPast()) {
                return Response::error([__('Round pickup date and time must be in the future.')], []);
            }

            if ($roundPickupDateTime->lte($pickupDateTime)) {
                return Response::error([__('Round pickup date and time must be greater than pickup date and time.')], []);
            }
        }
        $cars = Car::where('car_area_id', $request->car_area)
            ->where('car_type_id', $request->car_type)
            ->where('status', true)
            ->where('approval', true)
            ->whereDoesntHave('bookings', function ($query) use ($validated) {
                $query->where('pickup_date', Carbon::parse($validated['pickup_date']))->where('status', CarBookingConst::STATUONGOING);
            })
            ->get();
        $data_path = [
            'base_url' => url('/'),
            'image_path' => files_asset_path_basename('site-section'),
        ];

        try {
            $car_booking = TemporaryData::create([
                'identifier' => generate_unique_string('temporary_datas', 'identifier', 20),
                'type' => Str::slug(CarBookingConst::CAR_BOOKING),
                'data' => $validated,
            ]);
            return Response::success([__('Car search successful')], ['token' => $car_booking->identifier, 'cars' => $cars, 'data_path' => $data_path], 200);
        } catch (Exception $e) {
            return Response::error(['error' => [__('Something Went Wrong! Please try again.')]], [], 500);
        }
    }

    public function viewCar()
    {
        $cars = Car::where('status', true)
            ->whereHas('type', function ($query) {
                $query->where('status', true);
            })
            ->whereHas('branch', function ($query) {
                $query->where('status', true);
            })
            ->where(function ($query) {
                $query
                    ->whereHas('bookings', function ($subquery) {
                        $subquery->where('status', '=', 3)->orWhere('status', '=', 1);
                    })
                    ->orWhereDoesntHave('bookings');
            })
            ->get();
        $car_data = [
            'base_url' => url('/'),
            'image_path' => files_asset_path_basename('site-section'),
            'cars' => $cars,
        ];
        $message = [__('Cars Fetched Successfully!')];
        return Response::success($message, ['cars' => $car_data], 200);
    }

    public function preview(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'car_id' => 'required',
        ]);

        if ($validator->fails()) {
            return Response::error($validator->errors()->all(), []);
        }

        $validated = $validator->validate();

        $booking_details = TemporaryData::where('identifier', $validated['token'])->first();

        if (!$booking_details) {
            return Response::error([__('Something Went Wrong! Please try again.')], [], 500);
        }

        $car = Car::where('id', $validated['car_id'])->first();

        $validated_user = auth()->user();
        // $car = Car::where('id', $booking_details->data->car_id)->first();

        $payment_gateways = PaymentGateway::addMoney()->active()->with('currencies')->has('currencies')->get();
        $payment_gateways->makeHidden(['credentials', 'created_at', 'input_fields', 'last_edit_by', 'updated_at', 'supported_currencies', 'image', 'env', 'slug', 'title', 'alias', 'code']);

        return Response::success(
            [__('Booking data stored in the temporary table')],
            [
                'token' => $request->token,
                'booking_details' => $booking_details->data,
                'booking_currency' => get_default_currency_code(),
                'car' => $car,
                'user' => $validated_user,
                'payment-type' => [
                    'online-payment' => Str::Slug(PaymentGatewayConst::ONLINEPAYMENT),
                    'cash' => Str::Slug(PaymentGatewayConst::CASH),
                ],
                'payment_gateways' => $payment_gateways,
            ],
            200,
        );
    }

    public function confirm(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'car_id' => 'required',
            'car_slug' => 'required',
            'location' => 'required',
            'destination' => 'required',
            'distance' => 'required',
            'credentials' => 'required|email',
            'mobile' => 'nullable',
            'round_pickup_date' => 'nullable',
            'round_pickup_time' => 'nullable',
            'message' => 'nullable',
            'fees' => 'required',
            'token' => 'required',
            'payment' => 'required',
        ]);

        if ($validator->fails()) {
            return Response::error($validator->errors()->all(), []);
        }

        $validated = $validator->validate();
        $validated['user_id'] = auth()->guard('api')->user()->id;

        $payment = $request->payment;

        if ($payment == Str::slug(PaymentGatewayConst::CASH)) {
            $trx_id = generate_unique_string('car_bookings', 'trx_id', 16);
            return $this->bookingConfirm($validated, 'cash', $trx_id);
        } else {
            $validator = Validator::make($request->all(), [
                'gateway_currency' => 'required',
                'gateway_type' => 'required',
            ]);

            if ($validator->fails()) {
                return Response::error($validator->errors()->all(), []);
            }

            $temp_booking = TemporaryData::where('identifier', $request->token)->first();

            if (!$temp_booking) {
                return Response::error([__('Something went wrong! Please try again')], [], 400);
            }

            $temp_data = json_decode(json_encode($temp_booking->data), true);

            $temp_data['car_id'] = $validated['car_id'];
            $temp_data['car_slug'] = $validated['car_slug'];
            $temp_data['location'] = $validated['location'];
            $temp_data['destination'] = $validated['destination'];
            $temp_data['distance'] = $validated['distance'];
            $temp_data['credentials'] = $validated['credentials'];
            $temp_data['mobile'] = $validated['mobile'];
            $temp_data['round_pickup_date'] = $validated['round_pickup_date'];
            $temp_data['round_pickup_time'] = $validated['round_pickup_time'];
            $temp_data['message'] = $validated['message'];
            $temp_data['fees'] = $validated['fees'];
            $temp_data['payment'] = $validated['payment'];
            $temp_data['token'] = $validated['token'];
            $temp_data['user_id'] = $validated['user_id'];

            $temp_booking->update([
                'data' => $temp_data,
            ]);

            $request->merge(['amount' => $validated['fees'], 'token' => $request->token]);

            if ($request->gateway_type === PaymentGatewayConst::AUTOMATIC) {
                return $this->automaticSubmit($request);
            } else {
                $validator = Validator::make($request->all(), [
                    'transaction_id' => 'required',
                ]);

                if ($validator->fails()) {
                    return Response::error($validator->errors()->all(), []);
                }
                return $this->manualSubmit($request);
            }
        }
    }

    public function bookingConfirm($data, $type, $trx_id)
    {
        $temp_booking = TemporaryData::where('identifier', $data['token'])->first();
        $basic_setting = BasicSettings::first();
        if (!$temp_booking) {
            return Response::error([__('Something went wrong! Please try again')], [], 400);
        }
        $temp_data = json_decode(json_encode($temp_booking->data), true);

        if ($type === 'cash') {
            $charges = TransactionSetting::where('slug','cash')->first();
            $amount = $data['fees'];

            $fixed_charge_calc = $charges->fixed_charge;
            $percent_charge_calc = (($amount / 100) * $charges->percent_charge);

            $total_charge = $fixed_charge_calc + $percent_charge_calc;
        }

        try {
            $booking_data = CarBooking::create([
                'car_id' => $data['car_id'],
                'user_id' => $data['user_id'],
                'slug' => $data['car_slug'],
                'trx_id' => $trx_id,
                'payment_type' => $type,
                'phone' => $data['mobile'],
                'email' => $data['credentials'],
                'location' => $data['location'],
                'destination' => $data['destination'],
                'trip_id' => generate_unique_code(),
                'pickup_time' => $temp_data['pickup_time'],
                'pickup_date' => $temp_data['pickup_date'],
                'round_pickup_time' => $data['round_pickup_time'],
                'round_pickup_date' => $data['round_pickup_date'],
                'distance' => $data['distance'],
                'amount' => $data['fees'],
                'charges' => $total_charge ?? 0,
                'message' => $data['message'] ?? '',
                'status' => 1,
            ]);

            $confirm_booking = CarBooking::with('cars')
                ->where('slug', $booking_data->slug)
                ->first();
            // $temp_booking->delete();

            $this->bookingNotification($confirm_booking, $basic_setting);

            return Response::success([__('Booking Successful!')], [], 200);
        } catch (Exception $e) {
            return Response::error([__('Something went wrong! Please try again')], [], 400);
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

    public function automaticSubmit(Request $request)
    {
        try {
            $instance = PaymentGatewayHelper::init($request->all())
                ->type(PaymentGatewayConst::TYPEADDMONEY)
                ->setProjectCurrency(PaymentGatewayConst::PROJECT_CURRENCY_SINGLE)
                ->gateway()
                ->api()
                ->render();
        } catch (Exception $e) {
            return Response::error([$e->getMessage()], [], 500);
        }

        if ($instance instanceof RedirectResponse === false && isset($instance['gateway_type']) && $instance['gateway_type'] == PaymentGatewayConst::MANUAL) {
            return Response::error([__("Can't submit manual gateway in automatic link")], [], 400);
        }

        return Response::success(
            [__('Payment gateway response successful')],
            [
                'redirect_url' => $instance['redirect_url'],
                'redirect_links' => $instance['redirect_links'],
                'action_type' => $instance['type'] ?? false,
                'address_info' => $instance['address_info'] ?? [],
            ],
            200,
        );
    }

    public function success(Request $request, $gateway)
    {
        try {
            sleep(2);
            $token = PaymentGatewayHelper::getToken($request->all(), $gateway);
            $temp_data = TemporaryData::where('type', PaymentGatewayConst::TYPEADDMONEY)
                ->where('identifier', $token)
                ->first();

            if (!$temp_data) {
                if (Transaction::where('callback_ref', $token)->exists()) {
                    // return Response::success([__('Transaction request sended successfully!')], [], 400);

                } else {
                    return Response::error([__("Transaction failed. Record didn't saved properly. Please try again")], [], 400);
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

            $transaction_info = Transaction::where('booking_token', $temp_data['data']->booking_token)->first();
            $booking_info = TemporaryData::where('identifier', $temp_data['data']->booking_token)->first();
            $booking_info = json_decode(json_encode($booking_info->data), true);


            $car_booking = CarBooking::where('trx_id',$transaction_info->trx_id)->first();
            if(!$car_booking){
            $this->bookingConfirm($booking_info, 'online-payment', $transaction_info->trx_id);
            }

            // return $instance;
        } catch (Exception $e) {
            return Response::error([$e->getMessage()], [], 500);
        }
        return Response::success([__('Car Booked successfully!')], [], 200);
    }

    public function cancel(Request $request, $gateway)
    {
        $token = PaymentGatewayHelper::getToken($request->all(), $gateway);
        $temp_data = TemporaryData::where('type', PaymentGatewayConst::TYPEADDMONEY)
            ->where('identifier', $token)
            ->first();
        try {
            if ($temp_data != null) {
                $temp_data->delete();
            }
        } catch (Exception $e) {
            // Handel error
        }
        return Response::error([__('Payment process cancel successfully!')],[],400);
    }

    public function postSuccess(Request $request, $gateway)
    {
        try {
            $token = PaymentGatewayHelper::getToken($request->all(), $gateway);
            $temp_data = TemporaryData::where('type', PaymentGatewayConst::TYPEADDMONEY)
                ->where('identifier', $token)
                ->first();
            if ($temp_data && $temp_data->data->creator_guard != 'api') {
                Auth::guard($temp_data->data->creator_guard)->loginUsingId($temp_data->data->creator_id);
            }
        } catch (Exception $e) {
            return Response::error([$e->getMessage()]);
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
            if ($temp_data && $temp_data->data->creator_guard != 'api') {
                Auth::guard($temp_data->data->creator_guard)->loginUsingId($temp_data->data->creator_id);
            }
        } catch (Exception $e) {
            return Response::error([$e->getMessage()]);
        }

        return $this->cancel($request, $gateway);
    }

    public function manualInputFields(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'alias' => 'required|string|exists:payment_gateway_currencies',
        ]);

        if ($validator->fails()) {
            return Response::error($validator->errors()->all(), [], 400);
        }

        $validated = $validator->validate();
        $gateway_currency = PaymentGatewayCurrency::where('alias', $validated['alias'])->first();

        $gateway = $gateway_currency->gateway;

        if (!$gateway->isManual()) {
            return Response::error([__("Can't get fields. Requested gateway is automatic")], [], 400);
        }

        if (!$gateway->input_fields || !is_array($gateway->input_fields)) {
            return Response::error([__('This payment gateway is under constructions. Please try with another payment gateway')], [], 503);
        }

        try {
            $input_fields = json_decode(json_encode($gateway->input_fields), true);
            $input_fields = array_reverse($input_fields);
        } catch (Exception $e) {
            return Response::error([__('Something went wrong! Please try again')], [], 500);
        }

        return Response::success(
            [__('Payment gateway input fields fetch successfully!')],
            [
                'gateway' => [
                    'desc' => $gateway->desc,
                ],
                'input_fields' => $input_fields,
                'currency' => $gateway_currency->only(['alias']),
            ],
            200,
        );
    }

    public function manualSubmit(Request $request)
    {
        try {
            $instance = PaymentGatewayHelper::init($request->only(['gateway_currency', 'amount']))
                ->setProjectCurrency(PaymentGatewayConst::PROJECT_CURRENCY_SINGLE)
                ->gateway()
                ->get();
        } catch (Exception $e) {
            return Response::error([$e->getMessage()], [], 401);
        }

        // Check it's manual or automatic
        if (!isset($instance['gateway_type']) || $instance['gateway_type'] != PaymentGatewayConst::MANUAL) {
            return Response::error([__("Can't submit automatic gateway in manual link")], [], 400);
        }

        $gateway = $instance['gateway'] ?? null;
        $gateway_currency = $instance['currency'] ?? null;
        if (!$gateway || !$gateway_currency || !$gateway->isManual()) {
            return Response::error([__('Selected gateway is invalid')], [], 400);
        }

        $amount = $instance['amount'] ?? null;
        if (!$amount) {
            return Response::error([__('Transaction Failed. Failed to save information. Please try again')], [], 400);
        }

        $this->file_store_location = 'transaction';
        $dy_validation_rules = $this->generateValidationRules($gateway->input_fields);

        $validator = Validator::make($request->all(), $dy_validation_rules);
        if ($validator->fails()) {
            return Response::error($validator->errors()->all(), [], 400);
        }
        $validated = $validator->validate();
        $get_values = $this->placeValueWithFields($gateway->input_fields, $validated);
        $trx_id = generate_unique_string('transactions', 'trx_id', 16);

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
                'booking_token' => $request->token,
                'payment_currency' => $gateway_currency->currency_code,
                'details' => json_encode(['input_values' => $get_values]),
                'status' => PaymentGatewayConst::STATUSPENDING,
                'created_at' => now(),
            ]);

            DB::commit();

            $booking_info = TemporaryData::where('identifier', $request->token)->first();
            $booking_info = json_decode(json_encode($booking_info->data), true);
            $this->bookingConfirm($booking_info, 'online-payment', $trx_id);
        } catch (Exception $e) {
            DB::rollBack();
            return Response::error([__('Something went wrong! Please try again')], [], 500);
        }
        return Response::success([__('Transaction Success. Please wait for admin confirmation')], [], 200);
    }

    public function gatewayAdditionalFields(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'currency' => 'required|string|exists:payment_gateway_currencies,alias',
        ]);
        if ($validator->fails()) {
            return Response::error($validator->errors()->all(), [], 400);
        }
        $validated = $validator->validate();

        $gateway_currency = PaymentGatewayCurrency::where('alias', $validated['currency'])->first();

        $gateway = $gateway_currency->gateway;

        $data['available'] = false;
        $data['additional_fields'] = [];
        if (Gpay::isGpay($gateway)) {
            $gpay_bank_list = Gpay::getBankList();
            if ($gpay_bank_list == false) {
                return Response::error([__('Gpay bank list server response failed! Please try again')], [], 500);
            }
            $data['available'] = true;

            $gpay_bank_list_array = json_decode(json_encode($gpay_bank_list), true);

            $gpay_bank_list_array = array_map(function ($array) {
                $data['name'] = $array['short_name_by_gpay'];
                $data['value'] = $array['gpay_bank_code'];

                return $data;
            }, $gpay_bank_list_array);

            $data['additional_fields'][] = [
                'type' => 'select',
                'label' => 'Select Bank',
                'title' => 'Select Bank',
                'name' => 'bank',
                'values' => $gpay_bank_list_array,
            ];
        }

        return Response::success([__('Request response fetch successfully!')], $data, 200);
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
            return Response::error([__('Transaction hash is required for verify')]);
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
            return Response::error([__('Transaction hash is not valid! Please input a valid hash')], [], 404);
        }

        if ($crypto_transaction->amount >= $transaction->total_payable == false) {
            if (!$crypto_transaction) {
                Response::error([__('Insufficient amount added. Please contact with system administrator')], [], 400);
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

            $this->bookingConfirm($transaction->booking_token, 'online-payment', $transaction->id);

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            return Response::error([__('Something went wrong! Please try again')], [], 500);
        }

        return Response::success([__('Payment Confirmation Success!')], [], 200);
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
            return Response::error([$e->getMessage()], [], 500);
        }
    }

    // manual re-payment
    public function repaymentSubmit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'trx_id' => 'required|string|exists:transactions',
        ]);

        if ($validator->fails()) {
            return Response::error([$validator->errors()->all()], []);
        }

        $trx_id = $request->trx_id;

        $transaction = Transaction::where('trx_id', $trx_id)->first();

        if (!$transaction || !isset($transaction->payment_gateway_currency_id)) {
            return Response::error([__('Invalid request')], []);
        }

        $gateway_currency = PaymentGatewayCurrency::find($transaction->payment_gateway_currency_id);

        if (!$gateway_currency || !$gateway_currency->gateway->isManual()) {
            return Response::error([__('Selected gateway is invalid')], []);
        }

        $gateway = $gateway_currency->gateway;
        $amount = $transaction->total_payable ?? null;
        if (!$amount) {
            return Response::error([__('Transaction Failed. Failed to save information. Please try again')], []);
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
            return Response::error([__('Something went wrong! Please try again')], []);
        }
        return Response::success([__('Transaction Success. Please wait for admin confirmation')], []);
    }



    public function reManualInputFields(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'trx_id' => 'required|exists:transactions',
        ]);

        if ($validator->fails()) {
            return Response::error([$validator->errors()->all()], []);
        }

        $validated = $validator->validate();

        $transaction = Transaction::where('trx_id',$validated['trx_id'])->first();

        $currency = $transaction->gateway_currency->alias;

        $validated = $validator->validate();
        $gateway_currency = PaymentGatewayCurrency::where('alias', $currency)->first();

        $gateway = $gateway_currency->gateway;

        if (!$gateway->isManual()) {
            return Response::error([__("Can't get fields. Requested gateway is automatic")], [], 400);
        }

        if (!$gateway->input_fields || !is_array($gateway->input_fields)) {
            return Response::error([__('This payment gateway is under constructions. Please try with another payment gateway')], [], 503);
        }

        try {
            $input_fields = json_decode(json_encode($gateway->input_fields), true);
            $input_fields = array_reverse($input_fields);
        } catch (Exception $e) {
            return Response::error([__('Something went wrong! Please try again')], [], 500);
        }

        return Response::success(
            [__('Payment gateway input fields fetch successfully!')],
            [
                'reject-reason' => $transaction->reject_reason,
                'gateway' => [
                    'desc' => $gateway->desc,
                ],
                'input_fields' => $input_fields,
                'currency' => $gateway_currency->only(['alias']),
            ],
            200,
        );
    }
}
