<?php

namespace App\Http\Controllers\Api\V1\User;

use Exception;
use App\Constants\GlobalConst;
use App\Http\Helpers\Response;
use App\Models\Admin\Language;
use App\Models\Admin\SetupKyc;
use App\Models\Admin\UsefulLink;
use App\Models\Admin\AppSettings;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Route;
use App\Models\Admin\AppOnboardScreens;
use App\Providers\Admin\CurrencyProvider;
use App\Providers\Admin\BasicSettingsProvider;

class SettingController extends Controller
{
    public function basicSettings() {
        $basic_settings = BasicSettingsProvider::get()->only(['id','site_name','site_title','timezone','site_logo','site_logo_dark','site_fav','site_fav_dark']);

        $user_kyc_settings = SetupKyc::UserKyc()->first() ?? false;
        if($user_kyc_settings != false) {
            $user_kyc_settings = $user_kyc_settings->status;
        }

        $basic_settings['user_kyc_status'] = (boolean) $user_kyc_settings;

        $languages = Language::select(['id','name','code','status'])->get();

        $app_settings = AppSettings::select('splash_screen_image as image','version')->first();
        $onboard_screens = AppOnboardScreens::select("title","sub_title","image","status")->where("status",GlobalConst::ACTIVE)->orderByDesc('id')->get();
        $onboard_screens->makeHidden(['editData']);

        $base_cur = CurrencyProvider::default()->first();
        $base_cur->makeHidden(['admin_id','country','name','created_at','updated_at','type','flag','sender','receiver','default','status','editData']);

        $app_image_paths = [
            'base_url'          => url("/"),
            'path_location'     => files_asset_path_basename("app-images"),
            'default_image'     => files_asset_path_basename("default"),
        ];

        return Response::success([__("Basic settings fetch successfully!")],[
            'basic_settings'    => $basic_settings,
            'base_cur'          => $base_cur,
            'web_links'         => [
                'privacy-policy'    => setRoute('frontend.useful.links',UsefulLink::where('type',GlobalConst::USEFUL_LINK_PRIVACY_POLICY)->first()?->slug),
                'about-us'          => Route::has('frontend.about') ? route('frontend.about') : url('/'),
                'contact-us'        => Route::has('frontend.contact') ? route('frontend.contact') : url('/'),
            ],
            'languages'         => $languages,
            'splash_screen'     => $app_settings,
            'onboard_screens'   => $onboard_screens,
            'image_paths'       => [
                'base_path'         => url("/"),
                'path_location'     => files_asset_path_basename("image-assets"),
                'default_image'     => files_asset_path_basename("default"),
            ],
            'app_image_paths'   => $app_image_paths,
        ],200);
    }

    public function splashScreen() {
        $app_settings = AppSettings::select('splash_screen_image as image','version')->first();

        $image_paths = [
            'base_url'          => url("/"),
            'path_location'     => files_asset_path_basename("app-images"),
            'default_image'     => files_asset_path_basename("default"),
        ];

        return Response::success([__('Splash screen data fetch successfully!')],[
            'splash_screen' => $app_settings,
            'image_paths'   => $image_paths,
        ],200);
    }

    public function onboardScreens() {
        $onboard_screens = AppOnboardScreens::select("title","sub_title","image","status")->where("status",GlobalConst::ACTIVE)->orderByDesc('id')->get();
        $onboard_screens->makeHidden(['editData']);

        $image_paths = [
            'base_url'          => url("/"),
            'path_location'     => files_asset_path_basename("app-images"),
            'default_image'     => files_asset_path_basename("default"),
        ];

        return Response::success([__('Onboard screen data fetch successfully!')],[
            'onboard_screens'   => $onboard_screens,
            'image_paths'       => $image_paths,
        ],200);
    }

    public function getLanguages() {
        try{
            $api_languages = get_api_languages();
        }catch(Exception $e) {
            return Response::error([$e->getMessage()],[],500);
        }

        return Response::success([__("Language data fetch successfully!")],[
            'languages' => $api_languages,
        ],200);
    }
}
