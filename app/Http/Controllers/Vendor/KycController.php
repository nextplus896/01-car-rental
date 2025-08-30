<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Constants\GlobalConst;
use App\Models\Admin\BasicSettings;
use App\Models\Admin\SetupKyc;
use Illuminate\Support\Facades\DB;
use App\Traits\ControlDynamicInputFields;
use Exception;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class KycController extends Controller
{
    use ControlDynamicInputFields;

    public function index()
    {
        $page_title = __("KYC Verification");
        $basic_settings = BasicSettings::first();
        if (!$basic_settings->vendor_kyc_verification) {
            return back()->with(['warning' => [__("KYC Verification isn't available")]]);
        }
        $user = auth()->guard('vendor')->user();
        $user_kyc = SetupKyc::vendorKyc()->first();
        if(!$user_kyc) return redirect()->route('vendor.dashboard.index');

        $kyc_data = $user_kyc->fields;
        $kyc_fields = [];
        if($kyc_data) {
            $kyc_fields = array_reverse($kyc_data);
        }
        $kyc_data = $user_kyc;

        if (auth()->user()->kyc_verified == global_const()::DEFAULT) {
            return view('vendor-end.sections.kyc.form',compact('page_title','user','kyc_fields','kyc_data'));
        }
        else{
            return view('vendor-end.sections.kyc.index',compact('page_title','user','kyc_fields','kyc_data'));
        }

    }

    public function reSubmit()
    {
        $page_title = __("KYC Verification");
        $user = auth()->guard('vendor')->user();
        $user_kyc = SetupKyc::vendorKyc()->first();

        if(!$user_kyc) return redirect()->route('vendor.dashboard.index');

        $kyc_data = $user_kyc->fields;
        $kyc_fields = [];

        if($kyc_data) {
            $kyc_fields = array_reverse($kyc_data);
        }
        $kyc_data = $user_kyc;

        return view('vendor-end.sections.kyc.form',compact('page_title','user','kyc_fields','kyc_data'));
    }

    public function store(Request $request) {

        $user = auth()->user();
        if($user->kyc_verified == GlobalConst::VERIFIED) return back()->with(['success' => ['You are already KYC Verified User']]);

        $user_kyc_fields = SetupKyc::vendorKyc()->first()->fields ?? [];
        $validation_rules = $this->generateValidationRules($user_kyc_fields);
        $validated = Validator::make($request->all(),$validation_rules)->validate();
        $get_values = $this->placeValueWithFields($user_kyc_fields,$validated);

        $create = [
            'user_id'       => auth()->user()->id,
            'data'          => json_encode($get_values),
            'created_at'    => now(),
        ];

        DB::beginTransaction();
        try{
            DB::table('vendor_kyc_data')->updateOrInsert(["user_id" => $user->id],$create);
            $user->update([
                'kyc_verified'  => GlobalConst::PENDING,
            ]);
            DB::commit();
        }catch(Exception $e) {
            DB::rollBack();
            $user->update([
                'kyc_verified'  => GlobalConst::DEFAULT,
            ]);
            $this->generatedFieldsFilesDelete($get_values);
            return back()->with(['error' => ['Something went wrong! Please try again']]);
        }

        return redirect()->route('vendor.kyc.index')->with(['success' => ['KYC information successfully submitted']]);
    }
}
