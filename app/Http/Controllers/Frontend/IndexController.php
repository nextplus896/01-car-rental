<?php

namespace App\Http\Controllers\Frontend;

use App\Constants\LanguageConst;
use Exception;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\Admin\Language;
use App\Models\Admin\UsefulLink;
use App\Models\Admin\SiteSections;
use App\Models\Frontend\Subscribe;
use App\Constants\SiteSectionConst;
use App\Http\Controllers\Controller;
use App\Http\Helpers\Response;
use App\Models\Admin\AdminNotification;
use App\Models\Admin\AppSettings;
use App\Models\Admin\Cars\CarArea;
use App\Models\Admin\Cars\CarType;
use App\Models\Admin\InvestmentPlan;
use App\Models\Frontend\Announcement;
use App\Models\Frontend\AnnouncementCategory;
use App\Models\Frontend\ContactRequest;
use App\Models\Vendor\Cars\Car;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use App\Providers\Admin\BasicSettingsProvider;
use Illuminate\Support\Facades\Auth;

class IndexController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(BasicSettingsProvider $basic_settings)
    {
        $site_name = $basic_settings->get()?->site_name;
        $page_title = $basic_settings->get()?->site_name . " | " . $basic_settings->get()?->site_title;
        $banned_slug = Str::slug(SiteSectionConst::BANNER_SECTION);
        $banner = SiteSections::getData($banned_slug)->first();
        $find_slug = Str::slug(SiteSectionConst::FIND_SECTION);
        $find_car = SiteSections::getData($find_slug)->first();
        $feature_slug = Str::slug(SiteSectionConst::FEATURE_SECTION);
        $feature = SiteSections::getData($feature_slug)->first();
        $security_slug = Str::slug(SiteSectionConst::SECURITY_SECTION);
        $security = SiteSections::getData($security_slug)->first();
        $choose_slug = Str::slug(SiteSectionConst::CHOOSE_US_SECTION);
        $choose = SiteSections::getData($choose_slug)->first();
        $stat_slug = Str::slug(SiteSectionConst::STATISTICS_SECTION);
        $stat = SiteSections::getData($stat_slug)->first();
        $app_slug = Str::slug(SiteSectionConst::APP_SECTION);
        $app = SiteSections::getData($app_slug)->first();
        $app_link = AppSettings::first();
        $footer_slug = Str::slug(SiteSectionConst::FOOTER_SECTION);
        $footer = SiteSections::getData($footer_slug)->first();


        $cars = Car::where('status', true)
            ->where('approval', true)
            ->whereHas('type', function ($query) {
                $query->where('status', true);
            })
            ->whereHas('branch', function ($query) {
                $query->where('status', true);
            })
            ->where(function ($query) {
                $query->whereHas('bookings', function ($subquery) {
                    $subquery->where('status', '=', 3)->orWhere('status', '=', 1);
                })->orWhereDoesntHave('bookings');
            })
            ->get();
        $areas = CarArea::where('status', true)->get();
        $types = CarType::where('status', true)->get();

        return view('frontend.pages.index', compact(
            'site_name',
            'page_title',
            'banner',
            'find_car',
            'feature',
            'security',
            'choose',
            'stat',
            'app',
            'app_link',
            'cars',
            'areas',
            'types',
            'footer',
        ));
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function findCar(BasicSettingsProvider $basic_settings)
    {
        $site_name = $basic_settings->get()?->site_name;
        $page_title = setPageTitle(__("Find Car"));
        $default = LanguageConst::NOT_REMOVABLE;
        $cars = Car::where('status', true)
            ->where('approval', true)
            ->whereHas('type', function ($query) {
                $query->where('status', true);
            })
            ->whereHas('branch', function ($query) {
                $query->where('status', true);
            })
            ->where(function ($query) {
                $query->whereHas('bookings', function ($subquery) {
                    $subquery->where('status', '=', 3)->orWhere('status', '=', 1)->orWhere('status', '=', 4);
                })->orWhereDoesntHave('bookings');
            })
            ->limit(6)
            ->get();
        $areas = CarArea::where('status', true)->get();
        $types = CarType::where('status', true)->get();
        $app_slug = Str::slug(SiteSectionConst::APP_SECTION);
        $app = SiteSections::getData($app_slug)->first();
        $footer_slug = Str::slug(SiteSectionConst::FOOTER_SECTION);
        $footer = SiteSections::getData($footer_slug)->first();
        return view('frontend.pages.find-car', compact(
            'site_name',
            'page_title',
            'cars',
            'areas',
            'types',
            'app',
            'footer',
            'default'
        ));

        return view('frontend.pages.find-car', compact('site_name', 'page_title', 'footer'));
    }

    public function cars(BasicSettingsProvider $basic_settings)
    {
        $site_name = $basic_settings->get()?->site_name;
        $page_title = setPageTitle(__("Cars"));
        $default = LanguageConst::NOT_REMOVABLE;
        $cars = session('cars');
        $token = session('token');
        $footer_slug = Str::slug(SiteSectionConst::FOOTER_SECTION);
        $footer = SiteSections::getData($footer_slug)->first();
        return view('frontend.pages.cars', compact(
            'site_name',
            'page_title',
            "token",
            "cars",
            'footer',
            'default'
        ));
    }

    public function searchCar(BasicSettingsProvider $basic_settings, Request $request)
    {
        $page_title = setPageTitle(__("Cars"));

        $validator = Validator::make($request->all(), [
            'area'   => 'nullable',
            'type'   => 'nullable',
        ]);
        if ($validator->fails()) {
            return back()->with(['error' => [__('Something went wrong! Please try again.')]]);
        }
        if ($request->area && $request->type) {

            $cars = Car::where('car_area_id', $request->area)
                ->where('car_type_id', $request->type)
                ->where('status', true)
                ->where('approval', true)
                ->where(function ($query) {
                    $query->whereHas('bookings', function ($subquery) {
                        $subquery->where('status', '=', 3)->orWhere('status', '=', 1)->orWhere('status', '=', 4);
                    })->orWhereDoesntHave('bookings');
                })
                ->get();
        } else {
            $cars = Car::where('status', true)
                ->where('approval', true)
                ->whereHas('type', function ($query) {
                    $query->where('status', true);
                })
                ->whereHas('branch', function ($query) {
                    $query->where('status', true);
                })
                ->where(function ($query) {
                    $query->whereHas('bookings', function ($subquery) {
                        $subquery->where('status', '=', 3)->orWhere('status', '=', 1)->orWhere('status', '=', 4);
                    })->orWhereDoesntHave('bookings');
                })
                ->get();
        }
        $site_name = $basic_settings->get()?->site_name;
        $areas = CarArea::where('status', true)->get();
        $searchArea = $request->area;
        $searchType = $request->type;
        $app_slug = Str::slug(SiteSectionConst::APP_SECTION);
        $app = SiteSections::getData($app_slug)->first();
        $footer_slug = Str::slug(SiteSectionConst::FOOTER_SECTION);
        $footer = SiteSections::getData($footer_slug)->first();
        return view('frontend.pages.find-car', compact(
            'site_name',
            'page_title',
            'cars',
            'searchArea',
            'searchType',
            'areas',
            'app',
            'footer',
        ));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function vendor(BasicSettingsProvider $basic_settings)
    {
        $site_name = $basic_settings->get()?->site_name;
        $page_title = $basic_settings->get()?->site_name . " | " . $basic_settings->get()?->site_title;
        $basic_setting = $basic_settings->get();
        $vendor_banner_slug = Str::slug(SiteSectionConst::VENDOR_BANNER_SECTION);
        $vendor_banner = SiteSections::getData($vendor_banner_slug)->first();
        $vendor_require_slug = Str::slug(SiteSectionConst::VENDOR_REQUIREMENTS_SECTION);
        $vendor_require = SiteSections::getData($vendor_require_slug)->first();
        $vendor_safety_slug = Str::slug(SiteSectionConst::VENDOR_SAFETY_SECTION);
        $vendor_safety = SiteSections::getData($vendor_safety_slug)->first();
        $footer_slug = Str::slug(SiteSectionConst::FOOTER_SECTION);
        $footer = SiteSections::getData($footer_slug)->first();
        return view('frontend.pages.vendor', compact('site_name', 'page_title','basic_setting', 'vendor_banner', 'vendor_require', 'vendor_safety', 'footer'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function aboutUs(BasicSettingsProvider $basic_settings)
    {
        $site_name = $basic_settings->get()?->site_name;
        $page_title = $basic_settings->get()?->site_name . " | " . $basic_settings->get()?->site_title;
        $about_slug = Str::slug(SiteSectionConst::ABOUT_US_SECTION);
        $about = SiteSections::getData($about_slug)->first();
        $faq_slug = Str::slug(SiteSectionConst::FAQ_SECTION);
        $faq = SiteSections::getData($faq_slug)->first();
        $items = json_decode(json_encode($faq->value->items), true);
        $totalItems = count($items);
        $half = ceil($totalItems / 2);
        $app_slug = Str::slug(SiteSectionConst::APP_SECTION);
        $app = SiteSections::getData($app_slug)->first();
        $app_link = AppSettings::first();
        $footer_slug = Str::slug(SiteSectionConst::FOOTER_SECTION);
        $footer = SiteSections::getData($footer_slug)->first();
        return view('frontend.pages.about-us', compact('site_name', 'page_title', 'about', 'faq', 'half', 'app', 'app_link','footer'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function services(BasicSettingsProvider $basic_settings)
    {
        $site_name = $basic_settings->get()?->site_name;
        $page_title = $basic_settings->get()?->site_name . " | " . $basic_settings->get()?->site_title;
        $service_slug = Str::slug(SiteSectionConst::SERVICES_SECTION);
        $service = SiteSections::getData($service_slug)->first();
        $app_slug = Str::slug(SiteSectionConst::APP_SECTION);
        $app = SiteSections::getData($app_slug)->first();
        $app_link = AppSettings::first();
        $footer_slug = Str::slug(SiteSectionConst::FOOTER_SECTION);
        $footer = SiteSections::getData($footer_slug)->first();
        return view('frontend.pages.services', compact('site_name', 'page_title', 'service', 'app', 'app_link', 'footer'));
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function blog(BasicSettingsProvider $basic_settings)
    {
        $site_name = $basic_settings->get()?->site_name;
        $page_title = $basic_settings->get()?->site_name . " | " . $basic_settings->get()?->site_title;
        $blog_slug = Str::slug(SiteSectionConst::ANNOUNCEMENT_SECTION);
        $blog_sec = SiteSections::getData($blog_slug)->first();
        $blogs = Announcement::get();
        $app_slug = Str::slug(SiteSectionConst::APP_SECTION);
        $app = SiteSections::getData($app_slug)->first();
        $app_link = AppSettings::first();
        $footer_slug = Str::slug(SiteSectionConst::FOOTER_SECTION);
        $footer = SiteSections::getData($footer_slug)->first();
        return view('frontend.pages.blog', compact('site_name', 'page_title', 'blog_sec', 'blogs', 'app', 'app_link', 'footer'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function blogDetail(BasicSettingsProvider $basic_settings, $id)
    {
        $site_name = $basic_settings->get()?->site_name;
        $page_title = $basic_settings->get()?->site_name . " | " . $basic_settings->get()?->site_title;
        $blog_categories = AnnouncementCategory::get();
        $blog = Announcement::where('id', $id)->first();
        $recent_blogs = Announcement::latest()->get();
        $footer_slug = Str::slug(SiteSectionConst::FOOTER_SECTION);
        $footer = SiteSections::getData($footer_slug)->first();

        return view('frontend.pages.blog-details', compact('site_name', 'page_title', 'blog_categories', 'blog', 'recent_blogs', 'footer'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function categoryBlog(BasicSettingsProvider $basic_settings, $id)
    {
        $site_name = $basic_settings->get()?->site_name;
        $page_title = $basic_settings->get()?->site_name . " | " . $basic_settings->get()?->site_title;

        $blog_categories = AnnouncementCategory::get();
        $blogs = Announcement::where('announcement_category_id', $id)->latest()->get();
        $recent_blogs = Announcement::latest()->get();

        $footer_slug = Str::slug(SiteSectionConst::FOOTER_SECTION);
        $footer = SiteSections::getData($footer_slug)->first();

        return view('frontend.pages.category-blog', compact('site_name', 'page_title', 'blog_categories', 'blogs', 'recent_blogs', 'footer'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function contact(BasicSettingsProvider $basic_settings)
    {
        $site_name = $basic_settings->get()?->site_name;
        $page_title = $basic_settings->get()?->site_name . " | " . $basic_settings->get()?->site_title;

        $contact_slug = Str::slug(SiteSectionConst::CONTACT_US_SECTION);
        $contact = SiteSections::getData($contact_slug)->first();

        $app_slug = Str::slug(SiteSectionConst::APP_SECTION);
        $app = SiteSections::getData($app_slug)->first();
        $app_link = AppSettings::first();

        $footer_slug = Str::slug(SiteSectionConst::FOOTER_SECTION);
        $footer = SiteSections::getData($footer_slug)->first();

        return view('frontend.pages.contact', compact('site_name', 'page_title', 'contact', 'app', 'app_link', 'footer'));
    }


    public function subscribe(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'     => "required|string|email|max:255|unique:subscribes",
        ]);

        if ($validator->fails()) return redirect('/#subscribe-form')->withErrors($validator)->withInput();

        $validated = $validator->validate();
        try {
            Subscribe::create([
                'email'         => $validated['email'],
                'created_at'    => now(),
            ]);
        } catch (Exception $e) {
            return redirect('/#subscribe-form')->with(['error' => ['Failed to subscribe. Try again']]);
        }

        return redirect(url()->previous() . '/#subscribe-form')->with(['success' => ['Subscription successful!']]);
    }

    public function contactMessageSend(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'name'      => "required|string|max:255",
            'email'     => "required|email|string|max:255",
            'message'   => "required|string|max:5000",
        ])->validate();
        try {
            ContactRequest::create($validated);
        } catch (Exception $e) {
            return back()->with(['error' => ['Failed to send message. Please Try again']]);
        }

        return back()->with(['success' => ['Message send successfully!']]);
    }

    public function usefulLink($slug)
    {
        $useful_link = UsefulLink::where("slug", $slug)->first();
        if (!$useful_link) abort(404);

        $basic_settings = BasicSettingsProvider::get();

        $app_local = get_default_language_code();
        $page_title = $useful_link->title?->language?->$app_local?->title ?? $basic_settings->site_name;

    }


    public function languageSwitch(Request $request)
    {
        $code = $request->target;
        $language = Language::where("code", $code)->first();
        if (!$language) {
            return back()->with(['error' => ['Oops! Language Not Found!']]);
        }
        Session::put('local', $code);
        Session::put('local_dir', $language->dir);

        return back()->with(['success' => ['Language Switch to ' . $language->name]]);
    }

    public function subscribersStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'     => 'required|email|string',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $validated = $validator->validate();

        $validated['created_at'] = now();
        $validated['reply'] = 0;
        try {
            $message = Subscribe::create($validated);
            $notification_content = [
                'title'         => "Subscriber",
                'message'       => __("A User Has subscribed!"),
                'email'         => $validated['email'],
            ];
            AdminNotification::create([
                'admin_id' => 1,
                'type'     => "SIDE_NAV",
                'message'   => $notification_content,
            ]);
        } catch (Exception $e) {
            return back()->withErrors($validator)->withInput()->with(['error' => [__('Something went wrong! Please try again.')]]);
        }

        return back()->with(['success' => [__('Subscribed Successfully!')]]);
    }

    public function getAreaTypes(Request $request)
    {
        $validator    = Validator::make($request->all(), [
            'area'  => 'required|integer',
        ]);
        if ($validator->fails()) {
            return Response::error($validator->errors()->all());
        }
        $area = CarArea::with(['types' => function ($type) {
            $type->with(['type' => function ($car_type) {
                $car_type->where('status', true);
            }]);
        }])->find($request->area);
        if (!$area) return Response::error([__('Area Not Found')], 404);

        return Response::success([__('Data fetch successfully')], ['area' => $area], 200);
    }
}
