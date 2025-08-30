<?php

namespace App\Http\Controllers\Admin;

use App\Constants\NotificationConst;
use App\Constants\PaymentGatewayConst;
use App\Http\Controllers\Controller;
use App\Http\Helpers\Response;
use App\Models\Admin\BasicSettings;
use App\Models\Transaction;
use App\Models\UserNotification;
use App\Models\Vendor\VendorNotification;
use App\Notifications\Vendor\MoneyOut\ApprovedByAdminMail;
use App\Notifications\Vendor\MoneyOut\RejectedByAdminMail;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class MoneyOutController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $page_title = __('All Logs');
        $transactions = Transaction::with('vendor:id,email,username,full_mobile,image,firstname,lastname', 'payment_gateway:id,name')
            ->where('type', PaymentGatewayConst::TYPEWITHDRAW)
            ->paginate(20);
        return view('admin.sections.money-out.index', compact('page_title', 'transactions'));
    }

    /**
     * Display All Pending Logs
     * @return view
     */
    public function pending()
    {
        $page_title = __('Pending Logs');
        $transactions = Transaction::with('vendor:id,email,username,full_mobile,image,firstname,lastname', 'payment_gateway:id,name')
            ->where('type', PaymentGatewayConst::TYPEWITHDRAW)
            ->where('status', 2)
            ->paginate(20);
        return view('admin.sections.money-out.index', compact('page_title', 'transactions'));
    }

    /**
     * Display All Complete Logs
     * @return view
     */
    public function complete()
    {
        $page_title = __('Complete Logs');
        $transactions = Transaction::with('vendor:id,email,username,full_mobile,image,firstname,lastname', 'payment_gateway:id,name')
            ->where('type', PaymentGatewayConst::TYPEWITHDRAW)
            ->where('status', 1)
            ->paginate(20);
        return view('admin.sections.money-out.index', compact('page_title', 'transactions'));
    }

    /**
     * Display All Canceled Logs
     * @return view
     */
    public function canceled()
    {
        $page_title = __('Canceled Logs');
        $transactions = Transaction::with('vendor:id,email,username,full_mobile,image,firstname,lastname','payment_gateway:id,name')
            ->where('type', PaymentGatewayConst::TYPEWITHDRAW)
            ->where('status', 4)
            ->paginate(20);
        return view('admin.sections.money-out.index', compact('page_title', 'transactions'));
    }

    /**
     * This method for show details of add money
     * @return view $details-withdraw-money-logs
     */
    public function withdrawMoneyDetails($id)
    {
        $transaction = Transaction::where('id', $id)
            ->with('vendor:id,firstname,lastname,image,email,username,full_mobile', 'payment_gateway:id,name')
            ->where('type', PaymentGatewayConst::TYPEWITHDRAW)
            ->first();
        $page_title = __('Transaction Details');
        return view('admin.sections.money-out.details', compact('page_title', 'transaction'));
    }

    /**
     * This method for approved withdraw money
     * @method PUT
     * @param Illuminate\Http\Request $request
     * @return Illuminate\Http\Request Response
     */
    public function approved(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'target' => 'required|string|exists:transactions,trx_id',
        ])->validate();
        $basic_setting = BasicSettings::first();
        $transaction = Transaction::where('trx_id', $validated['target'])->first();
        if (!$transaction) {
            return back()->with(['error' => [__('Transaction not found!')]]);
        }
        if ($transaction->status == PaymentGatewayConst::STATUSSUCCESS) {
            return back()->with(['warning' => [__('This transaction is already approved')]]);
        }
        if ($transaction->status != PaymentGatewayConst::STATUSPENDING) {
            return back()->with(['warning' => [__('Action Denied!')]]);
        }

        try {
            $transaction->update([
                'status' => PaymentGatewayConst::STATUSSUCCESS,
                'reject_reason' => null,
            ]);
        } catch (Exception $e) {
            return back()->with(['error' => [__('Something went wrong. Please try again')]]);
        }
        try {
            $notification_content = [
                'title' => __('Money Out'),
                'message' => __('Your Money Out request approved by admin ') . get_amount(@$transaction->total_payable, @$transaction->request_currency) . __(' successful.'),
                'image' => files_asset_path('profile-default'),
            ];

            if ($transaction->vendor_id != null) {
                $this->createVendorNotification($transaction->vendor, $notification_content);
                if ($basic_setting->vendor_email_notification == true) {
                    $user = $transaction->vendor;
                    $user->notify(new ApprovedByAdminMail($user, $transaction));
                }
            }
        } catch (Exception $e) {
        }
        return back()->with(['success' => [__('Transaction successfully approved!')]]);
    }

    public function rejected(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'target' => 'required|string|exists:transactions,trx_id',
            'reason' => 'required|string|max:1000',
        ]);
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput()->with('modal', 'reject-modal');
        }
        $validated = $validator->validate();
        $basic_setting = BasicSettings::first();

        $transaction = Transaction::where('trx_id', $validated['target'])->first();
        if (!$transaction) {
            return back()->with(['error' => [__('Transaction not found!')]]);
        }
        if ($transaction->status == PaymentGatewayConst::STATUSREJECTED) {
            return back()->with(['warning' => [__('This transaction is already rejected')]]);
        }
        if ($transaction->status != PaymentGatewayConst::STATUSPENDING) {
            return back()->with(['error' => [__('Action Denied!')]]);
        }

        DB::beginTransaction();
        try {
            DB::table($transaction->getTable())
                ->where('id', $transaction->id)
                ->update([
                    'reject_reason' => $validated['reason'],
                    'status' => PaymentGatewayConst::STATUSREJECTED,
                    'available_balance' => $transaction->total_payable + $transaction->available_balance,
                ]);

            DB::table($transaction->creator_wallet->getTable())
                ->where('id', $transaction->creator_wallet->id)
                ->increment('balance', $transaction->total_payable);
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return back()->with(['error' => [__('Something went wrong! Please try again')]]);
        }
        try {
            $notification_content = [
                'title' => __('Money Out'),
                'message' => __('Your Money Out request rejected by admin ') . get_amount(@$transaction->total_payable, @$transaction->request_currency),
                'image' => files_asset_path('profile-default'),
            ];

            if ($transaction->vendor_id != null) {
                $this->createVendorNotification($transaction->vendor, $notification_content);
                if ($basic_setting->vendor_email_notification == true) {
                    $user = $transaction->vendor;
                    $user->notify(new RejectedByAdminMail($user, $transaction));
                }
            }
        } catch (Exception $e) {
        }
        return back()->with(['success' => [__('Transaction rejected successfully!')]]);
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
        $transactions = Transaction::moneyOut()
            ->search($validated['text'])
            ->limit(10)
            ->get();
        return view('admin.components.search.money-out.transaction_search', compact('transactions'));
    }

    /**
     *this method is used to create company notification
     */
    public function createVendorNotification($vendor, $message)
    {
        VendorNotification::create([
            'type' => NotificationConst::MONEY_OUT,
            'vendor_id' => $vendor->id,
            'message' => $message,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
