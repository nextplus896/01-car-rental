<?php

namespace App\Http\Controllers\Admin;

use App\Constants\CarBookingConst;
use App\Constants\NotificationConst;
use App\Constants\PaymentGatewayConst;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Helpers\Response;
use App\Models\User;
use App\Models\UserNotification;
use App\Notifications\User\AddMoney\ApprovedByAdminMail;
use Carbon\Carbon;
use Exception;
use App\Events\User\NotificationEvent as UserNotificationEvent;
use App\Models\Admin\BasicSettings;
use App\Models\Vendor\Vendor;
use App\Models\Vendor\VendorNotification;
use App\Notifications\User\AddMoney\RejectedByAdminMail;
use App\Providers\Admin\BasicSettingsProvider;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AddMoneyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $page_title = __('All Logs');
        $page_slug  = Str::slug(CarBookingConst::CAR_INDEX);
        $transactions = Transaction::with('user:id,firstname,email,username,mobile', 'payment_gateway:id,name')->where('type', 'add-money')->paginate(20);
        return view('admin.sections.add-money.index', compact('page_title','page_slug','transactions'));
    }

    /**
     * Pending Add Money Logs View.
     * @return view $pending-add-money-logs
     */
    public function pending()
    {
        $page_title = __('Pending Logs');
        $page_slug  = Str::slug(CarBookingConst::CAR_PENDING);
        $transactions = Transaction::with('user:id,firstname,email,username,mobile', 'payment_gateway:id,name')->where('type', 'add-money')->where('status', 2)->paginate(20);
        return view('admin.sections.add-money.index', compact('page_title','page_slug','transactions'));
    }

    /**
     * Complete Add Money Logs View.
     * @return view $complete-add-money-logs
     */
    public function complete()
    {
        $page_title = __('Complete Logs');
        $page_slug  = Str::slug(CarBookingConst::CAR_COMPLETE);
        $transactions = Transaction::with('user:id,firstname,email,username,mobile', 'payment_gateway:id,name')->where('type', 'add-money')->where('status', 1)->paginate(20);
        return view('admin.sections.add-money.index', compact('page_title','page_slug','transactions'));
    }

    /**
     * Canceled Add Money Logs View.
     * @return view $canceled-add-money-logs
     */
    public function canceled()
    {
        $page_title = __('Canceled Logs');
        $page_slug  = Str::slug(CarBookingConst::CAR_CENCEL);
        $transactions = Transaction::with('user:id,first_name,email,username,mobile', 'payment_gateway:id,name')->where('type', 'add-money')->where('status', 0)->paginate(20);
        return view('admin.sections.add-money.index', compact('page_title','page_slug','transactions'));
    }

    /**
     * Canceled Add Money Logs View.
     * @return view $canceled-add-money-logs
     */
    public function refund()
    {
        $page_title = __('Refund Logs');
        $page_slug  = Str::slug(CarBookingConst::CAR_REFUND);
        $transactions = Transaction::with('user:id,firstname,email,username,mobile', 'payment_gateway:id,name')
            ->where('type', 'add-money')
            ->where(function ($query) {
                $query->whereHas('bookings', function ($subquery) {
                    $subquery->where('status', '=', 4);
                });
            })
            ->paginate(20);
        return view('admin.sections.add-money.index', compact('page_title','page_slug','transactions'));
    }

    public function addMoneyDetails($id)
    {
        $data = Transaction::where('id', $id)->with('user:id,firstname,lastname,email,username,full_mobile', 'gateway_currency:id,name,alias,payment_gateway_id,currency_code,rate', 'payment_gateway:type')->where('type', 'add-money')->first();
        $page_title = __('Payment details for') . '  ' . $data->trx_id;
        return view('admin.sections.add-money.details', compact('page_title', 'data'));
    }
    public function approved(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer',
        ]);
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        $data = Transaction::where('id', $request->id)
            ->where('status', 2)
            ->where('type', 'add-money')
            ->first();

        try {
            if ($data->bookings->status == CarBookingConst::STATUSCOMPLETE) {
                $amount = $data->receive_amount;
                $vendor_wallet = $data->bookings->cars->vendor->wallets;
                //update transaction
                $vendor_wallet->update([
                    'balance' => $amount + $vendor_wallet->balance,
                ]);
            }
            $data->status = 1;
            $data->save();
            $user = User::where('id', $data->user_id)->first();



            // notification created
            $notification_content = [
                'title'   => "Payment Approved",
                'message' => "Admin approved your payment",
                'time'    => Carbon::now()->diffForHumans(),
                'image'   => files_asset_path('profile-default'),
            ];
            $basic_setting = BasicSettings::first();


        // User Notification //
            UserNotification::create([
                'type'    => NotificationConst::PAYMENT,
                'user_id' => $user->id,
                'message' => $notification_content,
            ]);

            // mail notification
            if ($basic_setting->email_notification == true) {
                $user->notify(new ApprovedByAdminMail($user,$data));

            }


        // Vendor Notification
            $notification_content['message'] = 'Due payment added to your wallet';
            VendorNotification::create([
                'type'      => NotificationConst::PAYMENT,
                'vendor_id' => $data->bookings->cars->vendor->id,
                'message'   => $notification_content,
            ]);

            // Push Notifications
            event(new UserNotificationEvent($notification_content,$user));
            send_push_notification(["user-".$user->id],[
                'title'     => $notification_content['title'],
                'body'      => $notification_content['message'],
                'icon'      => $notification_content['image'],
            ]);
        } catch (Exception $e) {
            return back()->with(['error' => [$e->getMessage()]]);
        }
        return redirect()
            ->back()
            ->with(['success' => ['Payment approved successfully']]);
    }
    public function rejected(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer',
            'reject_reason' => 'required|string|max:200',
        ]);
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        $data = Transaction::where('id', $request->id)
            ->where('status', 2)
            ->where('type', 'add-money')
            ->first();
        $data->status = 4;
        $data->reject_reason = $request->reject_reason;
        $vendor = $data->bookings->cars->vendor;
        try {
            $data->save();
            $user = User::where('id', $data->user_id)->first();

            // mail notification
            $user->notify(new RejectedByAdminMail($user,$data));

            // notification created
            $notification_content = [
                'title'   => "Payment Rejected",
                'message' => "Admin Rejected your payment",
                'time'    => Carbon::now()->diffForHumans(),
                'image'   => files_asset_path('profile-default'),
                'trx_id'  => $data->trx_id,
            ];
            UserNotification::create([
                'type'    => NotificationConst::PAYMENT_REJECT,
                'user_id' => $user->id,
                'message' => $notification_content,
            ]);
            //Push Notifications
            event(new UserNotificationEvent($notification_content,$user));
            send_push_notification(["user-".$user->id],[
                'title'     => $notification_content['title'],
                'body'      => $notification_content['message'],
                'icon'      => $notification_content['image'],
            ]);

            return redirect()
                ->back()
                ->with(['success' => ['Payment request rejected successfully']]);
        } catch (Exception $e) {
            return back()->with(['error' => [$e->getMessage()]]);
        }
    }
    public function updateStatus(Request $request){
        $validator = Validator::make($request->all(),[
            'target'       => "required|exists:transactions,trx_id",
            'status'       => "required",
        ]);

        if($validator->fails()) return back()->withErrors($validator)->withInput()->with('modal','status-change');
        $validated = $validator->validate();
        $info = Transaction::where('trx_id',$validated['target'])->first();
        try {
            $info->update([
                'refundable' => $validated['status'],
            ]);
            if ($validated['status'] == PaymentGatewayConst::STATUSSUCCESS) {
                $notification_content = [
                    'title'   => "Payment Refunded",
                    'message' => "Admin refunded your payment",
                    'time'    => Carbon::now()->diffForHumans(),
                    'image'   => files_asset_path('profile-default'),
                ];
                UserNotification::create([
                    'type'    => NotificationConst::PAYMENT_REFUNDED,
                    'user_id' => $info->user_id,
                    'message' => $notification_content,
                ]);
            }
        }catch (Exception $e) {
            return back()->with(['error' => [__('Something went wrong! Please try again')]]);
        }
        return back()->with(['success' => [__('Status updated successfully!')]]);
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

        $transactions = Transaction::with('user:id,firstname,email,username,mobile', 'payment_gateway:id,name')
            ->where('type', 'add-money')
            ->where('trx_id', 'like', '%' . $validated['text'] . '%')
            ->latest()
            ->paginate(20);
        return view('admin.components.search.add-money.transaction_search', compact('transactions'));
    }
}
