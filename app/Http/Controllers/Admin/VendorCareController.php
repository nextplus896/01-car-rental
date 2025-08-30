<?php

namespace App\Http\Controllers\Admin;

use App\Constants\CarBookingConst;
use App\Constants\GlobalConst;
use App\Constants\NotificationConst;
use App\Constants\PaymentGatewayConst;
use App\Http\Controllers\Controller;
use App\Http\Helpers\PushNotificationHelper;
use App\Models\Vendor\Vendor;
use App\Models\Vendor\VendorLoginLog;
use App\Models\Vendor\VendorMailLog;
use Illuminate\Support\Facades\Notification;
use App\Notifications\User\SendMail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Arr;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Helpers\Response;
use App\Models\Admin\AdminNotification;
use App\Models\CarBooking;
use App\Models\Vendor\VendorNotification;
use App\Models\Vendor\VendorWallet;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Jenssegers\Agent\Agent;

class VendorCareController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $page_title = __('All Vendor');
        $users = Vendor::orderBy('id', 'desc')->paginate(12);
        return view('admin.sections.vendor-care.index', compact('page_title', 'users'));
    }

    /**
     * Display Active Users
     * @return view
     */
    public function active()
    {
        $page_title = __('Active Vendor');
        $users = Vendor::active()->orderBy('id', 'desc')->paginate(12);
        return view('admin.sections.vendor-care.index', compact('page_title', 'users'));
    }

    /**
     * Display Banned Users
     * @return view
     */
    public function banned()
    {
        $page_title = __('Banned Vendors');
        $users = Vendor::banned()->orderBy('id', 'desc')->paginate(12);
        return view('admin.sections.vendor-care.index', compact('page_title', 'users'));
    }

    /**
     * Display Email Unverified Users
     * @return view
     */
    public function emailUnverified()
    {
        $page_title = __('Email Unverified Vendors');
        $users = Vendor::active()->orderBy('id', 'desc')->emailUnverified()->paginate(12);
        return view('admin.sections.vendor-care.index', compact('page_title', 'users'));
    }

    /**
     * Display SMS Unverified Users
     * @return view
     */
    public function SmsUnverified()
    {
        $page_title = __('SMS Unverified Vendors');
        return view('admin.sections.vendor-care.index', compact('page_title'));
    }

    /**
     * Display KYC Unverified Users
     * @return view
     */
    public function KycUnverified()
    {
        $page_title = __('KYC Unverified Vendors');
        $users = Vendor::kycUnverified()->orderBy('id', 'desc')->paginate(8);
        return view('admin.sections.vendor-care.index', compact('page_title', 'users'));
    }

    /**
     * Display Send Email to All Users View
     * @return view
     */
    public function emailAllUsers()
    {
        $page_title = __('Email To Vendors');
        return view('admin.sections.vendor-care.email-to-users', compact('page_title'));
    }

    /**
     * Display Specific User Information
     * @return view
     */
    public function userDetails($username)
    {
        $page_title = __('Vendor Details');
        $user = Vendor::where('username', $username)->first();
        $round_trips = CarBooking::whereNotNull('round_pickup_date')
        ->whereHas('cars', function ($query) use ($user) {
            $query->where('vendor_id', $user->id);
        })
        ->count();
        $booking_rejects = CarBooking::where('status', CarBookingConst::STATUSREJECTED)
            ->whereHas('cars', function ($query) use ($user) {
                $query->where('vendor_id', $user->id);
            })
            ->count();
        $ride_complete = CarBooking::where('status', CarBookingConst::STATUSCOMPLETE)
            ->whereHas('cars', function ($query) use ($user) {
                $query->where('vendor_id', $user->id);
            })
            ->count();
        $bookings = CarBooking::whereHas('cars', function ($query) use ($user) {
            $query->where('vendor_id', $user->id);
        })
            ->limit(5)
            ->get();
        if (!$user) {
            return back()->with(['error' => [__('Oops! Vendor does not exists')]]);
        }
        return view('admin.sections.vendor-care.details', compact('page_title', 'user', 'round_trips', 'booking_rejects', 'bookings', 'ride_complete'));
    }

    public function sendMailUsers(Request $request)
    {
        $request->validate([
            'user_type' => 'required|string|max:30',
            'subject' => 'required|string|max:250',
            'message' => 'required|string|max:2000',
        ]);

        $users = [];
        switch ($request->user_type) {
            case 'active':
                $users = Vendor::active()->get();
                break;
            case 'all':
                $users = Vendor::get();
                break;
            case 'email_verified':
                $users = Vendor::emailVerified()->get();
                break;
            case 'kyc_verified':
                $users = Vendor::kycVerified()->get();
                break;
            case 'banned':
                $users = Vendor::banned()->get();
                break;
        }

        try {
            Notification::send($users, new SendMail((object) $request->all()));
        } catch (Exception $e) {
            return back()->with(['error' => [__('Something went wrong! Please try again')]]);
        }

        return back()->with(['success' => [__('Email successfully sended')]]);
    }

    public function sendMail(Request $request, $username)
    {
        $request->merge(['username' => $username]);
        $validator = Validator::make($request->all(), [
            'subject' => 'required|string|max:200',
            'message' => 'required|string|max:2000',
            'username' => 'required|string|exists:vendors,username',
        ]);
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput()->with('modal', 'email-send');
        }
        $validated = $validator->validate();
        $user = Vendor::where('username', $username)->first();
        $validated['user_id'] = $user->id;
        $validated = Arr::except($validated, ['username']);
        $validated['method'] = 'SMTP';
        try {
            VendorMailLog::create($validated);
            $user->notify(new SendMail((object) $validated));
        } catch (Exception $e) {
            return back()->with(['error' => [__('Something went wrong! Please try again')]]);
        }
        return back()->with(['success' => [__('Mail successfully sended')]]);
    }

    public function userDetailsUpdate(Request $request, $username)
    {
        $request->merge(['username' => $username]);
        $validator = Validator::make($request->all(), [
            'username' => 'required|exists:vendors,username',
            'firstname' => 'required|string|max:60',
            'lastname' => 'required|string|max:60',
            'mobile_code' => 'required|string|max:10',
            'mobile' => 'required|string|max:20',
            'address' => 'nullable|string|max:250',
            'country' => 'nullable|string|max:50',
            'state' => 'nullable|string|max:50',
            'city' => 'nullable|string|max:50',
            'zip_code' => 'nullable|numeric|max_digits:8',
            'email_verified' => 'required|boolean',
            'two_factor_status' => 'required|boolean',
            'kyc_verified' => 'required|boolean',
            'status' => 'required|boolean',
        ]);
        $validated = $validator->validate();
        $validated['address'] = [
            'country' => $validated['country'] ?? '',
            'state' => $validated['state'] ?? '',
            'city' => $validated['city'] ?? '',
            'zip' => $validated['zip_code'] ?? '',
            'address' => $validated['address'] ?? '',
        ];
        $validated['mobile_code'] = remove_speacial_char($validated['mobile_code']);
        $validated['mobile'] = remove_speacial_char($validated['mobile']);
        $validated['full_mobile'] = $validated['mobile_code'] . $validated['mobile'];

        $user = Vendor::where('username', $username)->first();
        if (!$user) {
            return back()->with(['error' => [__('Oops! Vendor does not exists')]]);
        }

        try {
            $user->update($validated);
        } catch (Exception $e) {
            return back()->with(['error' => [__('Something went wrong! Please try again')]]);
        }

        return back()->with(['success' => [__('Profile Information Updated Successfully!')]]);
    }

    public function loginLogs($username)
    {
        $page_title = __('Login Logs');
        $user = Vendor::where('username', $username)->first();
        if (!$user) {
            return back()->with(['error' => [__("Oops! Vendor doesn't exists")]]);
        }
        $logs = VendorLoginLog::where('user_id', $user->id)->paginate(12);
        return view('admin.sections.vendor-care.login-logs', compact('logs', 'page_title'));
    }

    public function mailLogs($username)
    {
        $page_title = __('Vendor Email Logs');
        $user = Vendor::where('username', $username)->first();
        if (!$user) {
            return back()->with(['error' => [__("Oops! Vendor doesn't exists")]]);
        }
        $logs = VendorMailLog::where('user_id', $user->id)->paginate(12);
        return view('admin.sections.vendor-care.mail-logs', compact('page_title', 'logs'));
    }

    public function loginAsMember(Request $request, $username)
    {
        $request->merge(['username' => $username]);
        $request->validate([
            'target' => 'required|string|exists:vendors,username',
            'username' => 'required_without:target|string|exists:vendors',
        ]);

        try {
            $user = Vendor::where('username', $request->username)->first();
            Auth::guard('vendor')->login($user);
        } catch (Exception $e) {
            return back()->with(['error' => [$e->getMessage()]]);
        }
        return redirect()->intended(route('vendor.dashboard.index'));
    }

    public function kycDetails($username)
    {
        $user = Vendor::where('username', $username)->first();
        if (!$user) {
            return back()->with(['error' => [__("Oops! Vendor doesn't exists")]]);
        }

        $page_title = __('KYC Profile');
        return view('admin.sections.vendor-care.kyc-details', compact('page_title', 'user'));
    }

    public function kycApprove(Request $request, $username)
    {
        $request->merge(['username' => $username]);
        $request->validate([
            'target' => 'required|exists:vendors,username',
            'username' => 'required_without:target|exists:vendors,username',
        ]);
        $user = Vendor::where('username', $request->target)
            ->orWhere('username', $request->username)
            ->first();
        if ($user->kyc_verified == GlobalConst::VERIFIED) {
            return back()->with(['warning' => [__('Vendor already KYC verified')]]);
        }
        if ($user->kyc == null) {
            return back()->with(['error' => [__('Vendor KYC information not found')]]);
        }

        try {
            $user->update([
                'kyc_verified' => GlobalConst::APPROVED,
            ]);
        } catch (Exception $e) {
            $user->update([
                'kyc_verified' => GlobalConst::PENDING,
            ]);
            return back()->with(['error' => [__('Something went wrong! Please try again')]]);
        }
        return back()->with(['success' => [__('Vendor KYC successfully approved')]]);
    }

    public function kycReject(Request $request, $username)
    {
        $request->validate([
            'target' => 'required|exists:vendors,username',
            'reason' => 'required|string|max:500',
        ]);
        $user = Vendor::where('username', $request->target)->first();
        if (!$user) {
            return back()->with(['error' => [__("Oops! Vendor doesn't exists")]]);
        }
        if ($user->kyc == null) {
            return back()->with(['error' => [__('Vendor KYC information not found')]]);
        }

        try {
            $user->update([
                'kyc_verified' => GlobalConst::REJECTED,
            ]);
            $user->kyc->update([
                'reject_reason' => $request->reason,
            ]);
        } catch (Exception $e) {
            $user->update([
                'kyc_verified' => GlobalConst::PENDING,
            ]);
            $user->kyc->update([
                'reject_reason' => null,
            ]);

            return back()->with(['error' => [__('Something went wrong! Please try again')]]);
        }

        return back()->with(['success' => [__('Vendor KYC information is rejected')]]);
    }

    public function search(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'text' => 'required|string',
        ]);

        if ($validator->fails()) {
            $error = ['error' => $validator->errors()];
            return Response::error($error, null, 400);
        }

        $validated = $validator->validate();
        $users = Vendor::search($validated['text'])
            ->limit(10)
            ->get();
        return view('admin.components.search.user-search', compact('users'));
    }

    public function walletBalanceUpdate(Request $request,$username) {
        $validator = Validator::make($request->all(),[
            'type'      => "required|string|in:add,subtract",
            'wallet'    => "required|numeric|exists:user_wallets,id",
            'amount'    => "required|numeric",
            'remark'    => "required|string|max:200",
        ]);

        if($validator->fails()) {
            return back()->withErrors($validator)->withInput()->with('modal','wallet-balance-update-modal');
        }

        $validated = $validator->validate();
        $user_wallet = VendorWallet::whereHas('vendor',function($q) use ($username){
            $q->where('username',$username);
        })->find($validated['wallet']);
        if(!$user_wallet) return back()->with(['error' => [__("Vendor wallet not found!")]]);
        DB::beginTransaction();
        try{

            $user_wallet_balance = 0;

            switch($validated['type']){
                case "add":
                    $type = "Added";
                    $user_wallet_balance = $user_wallet->balance + $validated['amount'];
                    $user_wallet->balance += $validated['amount'];
                    break;

                case "subtract":
                    $type = "Subtracted";
                    if($user_wallet->balance >= $validated['amount']) {
                        $user_wallet_balance = $user_wallet->balance - $validated['amount'];
                        $user_wallet->balance -= $validated['amount'];
                    }else {
                        return back()->with(['error' => [__("Vendor do not have sufficient balance")]]);
                    }
                    break;
            }

            $inserted_id = DB::table("transactions")->insertGetId([
                'admin_id'          => auth()->user()->id,
                'user_id'           => $user_wallet->vendor->id,
                'wallet_id'         => $user_wallet->id,
                'type'              => PaymentGatewayConst::TYPEADDSUBTRACTBALANCE,
                'trx_id'            => generate_unique_string("transactions","trx_id",16),
                'request_amount'    => $validated['amount'],
                'total_charge'      => $validated['amount'],
                'available_balance' => $user_wallet_balance,
                'remark'            => $validated['remark'],
                'status'            => GlobalConst::SUCCESS,
                'request_currency'  => $user_wallet->currency->code,
                'created_at'                    => now(),
            ]);




            $client_ip = request()->ip() ?? false;
            $location = geoip()->getLocation($client_ip);
            $agent = new Agent();

            $mac = "";

            DB::table("transaction_devices")->insert([
                'transaction_id'=> $inserted_id,
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

            $user_wallet->save();

            $notification_content = [
                'title'         => "Update Balance",
                'message'       => "Your Wallet (".$user_wallet->currency->code.") Balance Has Been ". $type??"",
                'time'          => Carbon::now()->diffForHumans(),
                'image'         => files_asset_path('profile-default'),
            ];

            VendorNotification::create([
                'type'      => NotificationConst::BALANCE_UPDATE,
                'vendor_id'  => $user_wallet->vendor->id,
                'message'   => $notification_content,
            ]);
             //push notification
           try{
                (new PushNotificationHelper())->prepare([$user_wallet->vendor->id],[
                    'title' => $notification_content['title'],
                    'desc'  => $notification_content['message'],
                    'user_type' => 'user',
                ])->send();
            }catch(Exception $e) {}


            //admin notification
             $notification_content['title'] = $user_wallet->vendor->username."'s  Wallet (".$user_wallet->currency->code.") Balance Has Been ". $type??"";
            AdminNotification::create([
                'type'      => NotificationConst::BALANCE_UPDATE,
                'admin_id'  => 1,
                'message'   => $notification_content,
            ]);
            DB::commit();
        }catch(Exception $e) {
            DB::rollBack();
            return back()->with(['error' => [__("Transaction Failed!")]]);
        }

        return back()->with(['success' => [__("Transaction success")]]);
    }
}
