<?php

namespace App\Http\Controllers\Admin;

use Exception;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Constants\GlobalConst;
use App\Models\Admin\Language;
use App\Constants\LanguageConst;
use App\Models\Admin\SiteSections;
use App\Constants\SiteSectionConst;
use App\Http\Controllers\Controller;
use App\Models\Frontend\Announcement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Validator;
use App\Models\Frontend\AnnouncementCategory;
use Illuminate\Support\Facades\DB;

class SetupSectionsController extends Controller
{
    protected $languages;

    public function __construct()
    {
        $this->languages = Language::get();
    }

    /**
     * Register Sections with their slug
     * @param string $slug
     * @param string $type
     * @return string
     */
    public function section($slug, $type)
    {
        $sections = [
            'banner'    => [
                'view'      => "bannerView",
                'update'    => "bannerUpdate",
            ],
            'vendor-banner'    => [
                'update'        => "vendorBannerUpdate",
                'itemStore'     => "vendorItemStore",
                'itemUpdate'    => "vendorItemUpdate",
                'itemDelete'    => "vendorItemDelete",
            ],
            'find-car'  => [
                'view'          => "findCarView",
                'update'        => "findCarUpdate",
            ],
            'about-us'  => [
                'view'          => "aboutUsView",
                'update'        => "aboutUsUpdate",
            ],
            'features'  => [
                'view'          => "featuresView",
                'update'        => "featuresUpdate",
                'itemStore'     => "featuresItemStore",
                'itemUpdate'    => "featuresItemUpdate",
                'itemDelete'    => "featuresItemDelete",
            ],
            'faq'  => [
                'view'          => "faqView",
                'update'        => "faqUpdate",
                'itemStore'     => "faqItemStore",
                'itemUpdate'    => "faqItemUpdate",
                'itemDelete'    => "faqItemDelete",
            ],

            'security'  => [
                'view'          => "securityView",
                'update'        => "securityUpdate",
                'itemStore'     => "securityItemStore",
                'itemUpdate'    => "securityItemUpdate",
                'itemDelete'    => "securityItemDelete",
            ],

            'chooseUs'  => [
                'view'          => "chooseUsView",
                'update'        => "chooseUsUpdate",
                'itemStore'     => "chooseUsItemStore",
                'itemUpdate'    => "chooseUsItemUpdate",
                'itemDelete'    => "chooseUsItemDelete",
            ],

            'statistics'  => [
                'view'          => "statisticsView",
                'update'        => "statisticsUpdate",
                'itemStore'     => "statisticsItemStore",
                'itemUpdate'    => "statisticsItemUpdate",
                'itemDelete'    => "statisticsItemDelete",
            ],

            'app'  => [
                'view'          => "appView",
                'update'        => "appUpdate",
            ],

            'vendor-safety'    => [
                'view'          => "vendorSafetyView",
                'update'        => "vendorSafetyUpdate",
                'itemStore'     => "vendorSafetyItemStore",
                'itemUpdate'    => "vendorSafetyItemUpdate",
                'itemDelete'    => "vendorSafetyItemDelete",
            ],
            'vendor-require'  => [
                'view'          => "vendorRequirementsView",
                'update'        => "vendorRequirementsUpdate",
                'itemStore'     => "vendorRequirementsItemStore",
                'itemUpdate'    => "vendorRequirementsItemUpdate",
                'itemDelete'    => "vendorRequirementsItemDelete",
                'item'          => "vendorRequirementsItemDelete",
            ],
            'require-details'  => [
                'itemUpdate'    => "requirementsDetailsItemUpdate",
            ],
            'services'  => [
                'view'          => "servicesView",
                'update'        => "servicesUpdate",
                'itemStore'     => "servicesItemStore",
                'itemUpdate'    => "servicesItemUpdate",
                'itemDelete'    => "servicesItemDelete",
            ],
            'announcement' => [
                'view'          => "announcementView",
                'update'        => "announcementUpdate",
            ],
            'contact-us' => [
                'view'          => "contactUsView",
                'update'        => "contactUsUpdate",
            ],
            'footer' => [
                'view'          => "footerView",
                'update'        => "footerUpdate",
                'itemStore'     => "footerItemStore",
                'itemUpdate'    => "footerItemUpdate",
                'itemDelete'    => "footerItemDelete",
            ],
            'auth'  => [
                'view'          => "authView",
                'update'        => "authUpdate",
            ],
            'vendor-auth'  => [
                'view'          => "vendorAuthView",
                'update'        => "vendorAuthUpdate",
            ],
        ];

        if (!array_key_exists($slug, $sections)) abort(404);
        if (!isset($sections[$slug][$type])) abort(404);
        $next_step = $sections[$slug][$type];
        return $next_step;
    }

    /**
     * Method for getting specific step based on incoming request
     * @param string $slug
     * @return method
     */
    public function sectionView($slug)
    {
        $section = $this->section($slug, 'view');

        return $this->$section($slug);
    }

    /**
     * Method for distribute store method for any section by using slug
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     * @return method
     */
    public function sectionItemStore(Request $request, $slug)
    {
        $section = $this->section($slug, 'itemStore');
        return $this->$section($request, $slug);
    }

    /**
     * Method for distribute update method for any section by using slug
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     * @return method
     */
    public function sectionItemUpdate(Request $request, $slug)
    {
        $section = $this->section($slug, 'itemUpdate');
        return $this->$section($request, $slug);
    }

    /**
     * Method for distribute delete method for any section by using slug
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     * @return method
     */
    public function sectionItemDelete(Request $request, $slug)
    {
        $section = $this->section($slug, 'itemDelete');
        return $this->$section($request, $slug);
    }

    /**
     * Method for distribute update method for any section by using slug
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     * @return method
     */
    public function sectionUpdate(Request $request, $slug)
    {
        $section = $this->section($slug, 'update');
        return $this->$section($request, $slug);
    }

    /**
     * Method for show banner section page
     * @param string $slug
     * @return view
     */
    public function bannerView($slug)
    {
        $page_title = __("Banner Section");
        $section_slug = Str::slug(SiteSectionConst::BANNER_SECTION);
        $data = SiteSections::getData($section_slug)->first();
        $vendor_slug = Str::slug(SiteSectionConst::VENDOR_BANNER_SECTION);
        $vendor_banner = SiteSections::getData($vendor_slug)->first();
        $languages = $this->languages;

        return view('admin.sections.setup-sections.banner-section', compact(
            'page_title',
            'data',
            'languages',
            'slug',
            'vendor_banner',
        ));
    }

    /**
     * Method for update banner section information
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function bannerUpdate(Request $request, $slug)
    {

        $basic_field_name = [
            'heading' => "required|string|max:100",
            'sub_heading' => "required|string|max:500",
            'button_name_one' => "required|string|max:50",
            'button_name_two' => "required|string|max:50",
        ];
        $validator = Validator::make($request->all(), [
            'button_link_one'      => "required|string|max:255",
            'button_link_two'      => "required|string|max:255",
        ]);

        if ($validator->fails()) return back()->withErrors($validator)->withInput();

        $validated = $validator->validate();

        $slug = Str::slug(SiteSectionConst::BANNER_SECTION);
        $section = SiteSections::where("key", $slug)->first();

        $data['language']  = $this->contentValidate($request, $basic_field_name);
        $data['button_link_one'] = $validated['button_link_one'];
        $data['button_link_two'] = $validated['button_link_two'];
        $update_data['value']  = $data;
        $update_data['key']    = $slug;

        try {
            SiteSections::updateOrCreate(['key' => $slug], $update_data);
        } catch (Exception $e) {
            return back()->with(['error' => [__('Something went wrong! Please try again.')]]);
        }

        return back()->with(['success' => [__('Section updated successfully!')]]);
    }


    /**
     * Method for update banner section information
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function vendorBannerUpdate(Request $request, $slug)
    {
        $basic_field_name = [
            'heading' => "required|string|max:100",
            'sub_heading' => "required|string|max:500",
            'button_name_one' => "required|string|max:50",
            'button_name_two' => "required|string|max:50",
        ];
        $validator = Validator::make($request->all(), [
            'button_link_one'      => "required|string|max:255",
            'button_one_icon'      => "required|string",
            'button_link_two'      => "required|string|max:255",
        ]);

        if ($validator->fails()) return back()->withErrors($validator)->withInput();

        $validated = $validator->validate();

        $slug = Str::slug(SiteSectionConst::VENDOR_BANNER_SECTION);
        $section = SiteSections::where("key", $slug)->first();


        $data['image'] = $section->value->image ?? null;

        if ($request->hasFile("vendor_image")) {
            $data['image']      = $this->imageValidate($request, "vendor_image", $section->value->image ?? null);
        }

        $data['items']     = $section->value->items;
        $data['language']  = $this->contentValidate($request, $basic_field_name);
        $data['button_link_one'] = $validated['button_link_one'];
        $data['button_one_icon'] = $validated['button_one_icon'];
        $data['button_link_two'] = $validated['button_link_two'];
        $update_data['value']  = $data;
        $update_data['key']    = $slug;

        try {
            SiteSections::updateOrCreate(['key' => $slug], $update_data);
        } catch (Exception $e) {
            return back()->with(['error' => [__('Something went wrong! Please try again.')]]);
        }

        return back()->with(['success' => [__('Section updated successfully!')]]);
    }

    /**
     * Method for store brand item
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function vendorItemStore(Request $request, $slug)
    {
        $basic_field_name = [
            'title'         => "required|string|max:255",
            'description'   => "required|string|max:500",
        ];

        $validator = Validator::make($request->all(), [
            'icon'      => "required|string|max:255",
        ]);

        if ($validator->fails()) return back()->withErrors($validator)->withInput()->with('modal', 'vendor-item-add');

        $validated = $validator->validate();

        $language_wise_data = $this->contentValidate($request, $basic_field_name, "vendor-item-add");

        if ($language_wise_data instanceof RedirectResponse) return $language_wise_data;

        $slug = Str::slug(SiteSectionConst::VENDOR_BANNER_SECTION);
        $section = SiteSections::where("key", $slug)->first();

        if ($section != null) {
            $section_data = json_decode(json_encode($section->value), true);
        } else {
            $section_data = [];
        }

        $unique_id = uniqid();

        $section_data['items'][$unique_id]['language'] = $language_wise_data;
        $section_data['items'][$unique_id]['id'] = $unique_id;
        $section_data['items'][$unique_id]['icon'] = $validated['icon'];

        $update_data['key'] = $slug;
        $update_data['value']   = $section_data;

        try {
            SiteSections::updateOrCreate(['key' => $slug], $update_data);
        } catch (Exception $e) {
            return back()->with(['error' => [__('Something went wrong! Please try again.')]]);
        }

        return back()->with(['success' => [__('Section item added successfully!')]]);
    }

    /**
     * Method for update about us item
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function vendorItemUpdate(Request $request, $slug)
    {

        $request->validate([
            'target'        => "required|string",
            'icon_edit'     => "required|string|max:255",

        ]);
        $basic_field_name = [
            'title_edit'            => "required|string|max:255",
            'description_edit'      => "required|string|max:500",
        ];

        $slug = Str::slug(SiteSectionConst::VENDOR_BANNER_SECTION);
        $section = SiteSections::getData($slug)->first();

        if (!$section) return back()->with(['error' => [__('Section not found!')]]);

        $section_values = json_decode(json_encode($section->value), true);

        if (!isset($section_values['items'])) return back()->with(['error' => [__('Section item not found!')]]);

        if (!array_key_exists($request->target, $section_values['items'])) return back()->with(['error' => [__('Section item is invalid!')]]);

        $language_wise_data = $this->contentValidate($request, $basic_field_name, "vendor-item-edit");

        if ($language_wise_data instanceof RedirectResponse) return $language_wise_data;

        $language_wise_data = array_map(function ($language) {
            return replace_array_key($language, "_edit");
        }, $language_wise_data);

        $section_values['items'][$request->target]['language']  = $language_wise_data;
        $section_values['items'][$request->target]['icon']      = $request->icon_edit;

        try {
            $section->update([
                'value' => $section_values,
            ]);
        } catch (Exception $e) {
            return back()->with(['error' => [__('Something went wrong! Please try again.')]]);
        }

        return back()->with(['success' => [__('Section Item updated successfully!')]]);
    }

    /**
     * Method for delete about us item
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function vendorItemDelete(Request $request, $slug)
    {
        $request->validate([
            'target'    => 'required|string',
        ]);
        $slug = Str::slug(SiteSectionConst::VENDOR_BANNER_SECTION);
        $section = SiteSections::getData($slug)->first();

        if (!$section) return back()->with(['error' => [__('Section not found!')]]);

        $section_values = json_decode(json_encode($section->value), true);

        if (!isset($section_values['items'])) return back()->with(['error' => [__('Section item not found!')]]);

        if (!array_key_exists($request->target, $section_values['items'])) return back()->with(['error' => [__('Section item is invalid!')]]);

        try {
            unset($section_values['items'][$request->target]);
            $section->update([
                'value'     => $section_values,
            ]);
        } catch (Exception $e) {
            return back()->with(['error' => [__('Something went wrong! Please try again.')]]);
        }

        return back()->with(['success' => [__('Section item deleted successfully!')]]);
    }

    /**
     * Method for show about us section page
     * @param string $slug
     * @return view
     */
    public function featuresView($slug)
    {
        $page_title = __("Features Section");
        $section_slug = Str::slug(SiteSectionConst::FEATURE_SECTION);
        $data = SiteSections::getData($section_slug)->first();
        $languages = $this->languages;

        return view('admin.sections.setup-sections.features-section', compact(
            'page_title',
            'data',
            'languages',
            'slug',
        ));
    }

    /**
     * Method for update about section information
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function featuresUpdate(Request $request, $slug)
    {
        $basic_field_name = [
            'section_title'         => "required|string|max:100",
            'heading'               => "required|string|max:100",
            'sub_heading'           => "required|string|max:255",
            'details_title'         => "required|string|max:255",
            'details'               => "required|string",
            'details_button'        => "required|string|max:255",
        ];

        $request->validate([
            'details_button_link' => "required",
        ]);

        $slug = Str::slug(SiteSectionConst::FEATURE_SECTION);
        $section = SiteSections::where("key", $slug)->first();

        if ($section != null) {
            $data = json_decode(json_encode($section->value), true);
        } else {
            $data = [];
        }

        $data['details_button_link'] = $request->details_button_link;
        $data['language']  = $this->contentValidate($request, $basic_field_name);
        $update_data['value']  = $data;
        $update_data['key']    = $slug;

        try {
            SiteSections::updateOrCreate(['key' => $slug], $update_data);
        } catch (Exception $e) {
            return back()->with(['error' => [__('Something went wrong! Please try again.')]]);
        }

        return back()->with(['success' => [__('Section updated successfully!')]]);
    }

    /**
     * Method for store about us item
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function featuresItemStore(Request $request, $slug)
    {
        $basic_field_name = [
            'feature'         => "required|string|max:255",
        ];

        $language_wise_data = $this->contentValidate($request, $basic_field_name, "features-item-add");

        if ($language_wise_data instanceof RedirectResponse) return $language_wise_data;

        $slug = Str::slug(SiteSectionConst::FEATURE_SECTION);
        $section = SiteSections::where("key", $slug)->first();

        if ($section != null) {
            $section_data = json_decode(json_encode($section->value), true);
        } else {
            $section_data = [];
        }

        $unique_id = uniqid();
        $section_data['items'][$unique_id]['language'] = $language_wise_data;
        $section_data['items'][$unique_id]['id'] = $unique_id;
        $update_data['key'] = $slug;
        $update_data['value']   = $section_data;

        try {
            SiteSections::updateOrCreate(['key' => $slug], $update_data);
        } catch (Exception $e) {
            return back()->with(['error' => [__('Something went wrong! Please try again.')]]);
        }

        return back()->with(['success' => [__('Section item added successfully!')]]);
    }

    /**
     * Method for update about us item
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function featuresItemUpdate(Request $request, $slug)
    {

        $request->validate([
            'target'        => "required|string",
        ]);

        $basic_field_name = [
            'feature_edit'     => "required|string|max:255",
        ];

        $slug = Str::slug(SiteSectionConst::FEATURE_SECTION);
        $section = SiteSections::getData($slug)->first();

        if (!$section) return back()->with(['error' => [__('Section not found!')]]);

        $section_values = json_decode(json_encode($section->value), true);

        if (!isset($section_values['items'])) return back()->with(['error' => [__('Section item not found!')]]);

        if (!array_key_exists($request->target, $section_values['items'])) return back()->with(['error' => [__('Section item is invalid!')]]);

        $language_wise_data = $this->contentValidate($request, $basic_field_name, "features-item-edit");

        if ($language_wise_data instanceof RedirectResponse) return $language_wise_data;

        $language_wise_data = array_map(function ($language) {
            return replace_array_key($language, "_edit");
        }, $language_wise_data);



        $section_values['items'][$request->target]['language'] = $language_wise_data;

        try {
            $section->update([
                'value' => $section_values,
            ]);
        } catch (Exception $e) {
            return back()->with(['error' => [__('Something went wrong! Please try again.')]]);
        }

        return back()->with(['success' => [__('Section Item updated successfully!')]]);
    }

    /**
     * Method for delete about us item
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function featuresItemDelete(Request $request, $slug)
    {
        $request->validate([
            'target'    => 'required|string',
        ]);
        $slug = Str::slug(SiteSectionConst::FEATURE_SECTION);
        $section = SiteSections::getData($slug)->first();

        if (!$section) return back()->with(['error' => [__('Section not found!')]]);

        $section_values = json_decode(json_encode($section->value), true);

        if (!isset($section_values['items'])) return back()->with(['error' => [__('Section item not found!')]]);

        if (!array_key_exists($request->target, $section_values['items'])) return back()->with(['error' => [__('Section item is invalid!')]]);

        try {
            unset($section_values['items'][$request->target]);
            $section->update([
                'value'     => $section_values,
            ]);
        } catch (Exception $e) {
            return back()->with(['error' => [__('Something went wrong! Please try again.')]]);
        }

        return back()->with(['success' => [__('Section item deleted successfully!')]]);
    }


    /**
     * Method for show about us section page
     * @param string $slug
     * @return view
     */
    public function faqView($slug)
    {
        $page_title = __("FAQ Section");
        $section_slug = Str::slug(SiteSectionConst::FAQ_SECTION);
        $data = SiteSections::getData($section_slug)->first();
        $languages = $this->languages;

        return view('admin.sections.setup-sections.faq-section', compact(
            'page_title',
            'data',
            'languages',
            'slug',
        ));
    }

    /**
     * Method for update about section information
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function faqUpdate(Request $request, $slug)
    {
        $basic_field_name = [
            'section_title'               => "required|string|max:100",
            'heading'               => "required|string|max:100",
        ];


        $slug = Str::slug(SiteSectionConst::FAQ_SECTION);
        $section = SiteSections::where("key", $slug)->first();

        if ($section != null) {
            $data = json_decode(json_encode($section->value), true);
        } else {
            $data = [];
        }

        $data['language']       = $this->contentValidate($request, $basic_field_name);
        $update_data['value']   = $data;
        $update_data['key']     = $slug;

        try {
            SiteSections::updateOrCreate(['key' => $slug], $update_data);
        } catch (Exception $e) {
            return back()->with(['error' => [__('Something went wrong! Please try again.')]]);
        }

        return back()->with(['success' => [__('Section updated successfully!')]]);
    }

    /**
     * Method for store about us item
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function faqItemStore(Request $request, $slug)
    {
        $basic_field_name = [
            'question'         => "required|string|max:500",
            'answer'           => "required|string|max:500",
        ];

        $language_wise_data = $this->contentValidate($request, $basic_field_name, "faq-item-add");

        if ($language_wise_data instanceof RedirectResponse) return $language_wise_data;

        $slug = Str::slug(SiteSectionConst::FAQ_SECTION);
        $section = SiteSections::where("key", $slug)->first();

        if ($section != null) {
            $section_data = json_decode(json_encode($section->value), true);
        } else {
            $section_data = [];
        }

        $unique_id = uniqid();
        $section_data['items'][$unique_id]['language'] = $language_wise_data;
        $section_data['items'][$unique_id]['id'] = $unique_id;
        $update_data['key'] = $slug;
        $update_data['value']   = $section_data;

        try {
            SiteSections::updateOrCreate(['key' => $slug], $update_data);
        } catch (Exception $e) {
            return back()->with(['error' => [__('Something went wrong! Please try again.')]]);
        }

        return back()->with(['success' => [__('Section item added successfully!')]]);
    }

    /**
     * Method for update about us item
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function faqItemUpdate(Request $request, $slug)
    {

        $request->validate([
            'target'        => "required|string",
        ]);

        $basic_field_name = [
            'question_edit'         => "required|string|max:500",
            'answer_edit'           => "required|string|max:500",
        ];

        $slug = Str::slug(SiteSectionConst::FAQ_SECTION);
        $section = SiteSections::getData($slug)->first();

        if (!$section) return back()->with(['error' => [__('Section not found!')]]);

        $section_values = json_decode(json_encode($section->value), true);

        if (!isset($section_values['items'])) return back()->with(['error' => [__('Section item not found!')]]);

        if (!array_key_exists($request->target, $section_values['items'])) return back()->with(['error' => [__('Section item is invalid!')]]);

        $language_wise_data = $this->contentValidate($request, $basic_field_name, "faq-item-edit");

        if ($language_wise_data instanceof RedirectResponse) return $language_wise_data;

        $language_wise_data = array_map(function ($language) {
            return replace_array_key($language, "_edit");
        }, $language_wise_data);



        $section_values['items'][$request->target]['language'] = $language_wise_data;

        try {
            $section->update([
                'value' => $section_values,
            ]);
        } catch (Exception $e) {
            return back()->with(['error' => [__('Something went wrong! Please try again.')]]);
        }

        return back()->with(['success' => [__('Section Item updated successfully!')]]);
    }

    /**
     * Method for delete about us item
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function faqItemDelete(Request $request, $slug)
    {
        $request->validate([
            'target'    => 'required|string',
        ]);
        $slug = Str::slug(SiteSectionConst::FAQ_SECTION);
        $section = SiteSections::getData($slug)->first();

        if (!$section) return back()->with(['error' => [__('Section not found!')]]);

        $section_values = json_decode(json_encode($section->value), true);

        if (!isset($section_values['items'])) return back()->with(['error' => [__('Section item not found!')]]);

        if (!array_key_exists($request->target, $section_values['items'])) return back()->with(['error' => [__('Section item is invalid!')]]);

        try {
            unset($section_values['items'][$request->target]);
            $section->update([
                'value'     => $section_values,
            ]);
        } catch (Exception $e) {
            return back()->with(['error' => [__('Something went wrong! Please try again.')]]);
        }

        return back()->with(['success' => [__('Section item deleted successfully!')]]);
    }


    /**
     * Method for show services section page
     * @param string $slug
     * @return view
     */
    public function securityView($slug)
    {
        $page_title = __("Security Section");
        $section_slug = Str::slug(SiteSectionConst::SECURITY_SECTION);
        $data = SiteSections::getData($section_slug)->first();
        $languages = $this->languages;

        return view('admin.sections.setup-sections.security-section', compact(
            'page_title',
            'data',
            'languages',
            'slug',
        ));
    }

    /**
     * Method for update service section information
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function securityUpdate(Request $request, $slug)
    {
        $basic_field_name = [
            'section_title' => "required|string|max:100",
            'heading' => "required|string|max:100",
            'sub_heading' => "required|string|max:500",
        ];

        $slug = Str::slug(SiteSectionConst::SECURITY_SECTION);
        $section = SiteSections::where("key", $slug)->first();

        if ($section != null) {
            $section_data = json_decode(json_encode($section->value), true);
        } else {
            $section_data = [];
        }


        $section_data['language']  = $this->contentValidate($request, $basic_field_name);

        $section_data['language'];
        $update_data['key']    = $slug;
        $update_data['value']  = $section_data;

        try {
            SiteSections::updateOrCreate(['key' => $slug], $update_data);
        } catch (Exception $e) {
            return back()->with(['error' => [__('Something went wrong! Please try again.')]]);
        }

        return back()->with(['success' => [__('Section updated successfully!')]]);
    }

    /**
     * Method for store service item
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function securityItemStore(Request $request, $slug)
    {
        $basic_field_name = [
            'title'         => "required|string|max:255",
            'description'   => "required|string|max:500",
        ];

        $validator = Validator::make($request->all(), [
            'icon'      => "required|string|max:255",
        ]);
        if ($validator->fails()) return back()->withErrors($validator)->withInput()->with('modal', 'security-add');
        $validated = $validator->validate();

        $language_wise_data = $this->contentValidate($request, $basic_field_name, "security-add");

        if ($language_wise_data instanceof RedirectResponse) return $language_wise_data;

        $slug = Str::slug(SiteSectionConst::SECURITY_SECTION);
        $section = SiteSections::where("key", $slug)->first();

        if ($section != null) {
            $section_data = json_decode(json_encode($section->value), true);
        } else {
            $section_data = [];
        }
        $unique_id = uniqid();

        $section_data['items'][$unique_id]['language'] = $language_wise_data;
        $section_data['items'][$unique_id]['id'] = $unique_id;
        $section_data['items'][$unique_id]['icon'] = $validated['icon'];

        $update_data['key'] = $slug;
        $update_data['value']   = $section_data;

        try {
            SiteSections::updateOrCreate(['key' => $slug], $update_data);
        } catch (Exception $e) {
            return back()->with(['error' => [__('Something went wrong! Please try again')]]);
        }

        return back()->with(['success' => [__('Section item added successfully!')]]);
    }

    /**
     * Method for update service item
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function securityItemUpdate(Request $request, $slug)
    {
        $validator = Validator::make($request->all(), [
            'target'    => "required|string",
            'icon_edit'      => "required|string|max:255",
        ]);

        if ($validator->fails()) return back()->withErrors($validator)->withInput()->with('modal', 'security-edit');

        $basic_field_name = [
            'title_edit'     => "required|string|max:255",
            'description_edit'   => "required|string|max:500",
        ];

        $slug = Str::slug(SiteSectionConst::SECURITY_SECTION);
        $section = SiteSections::getData($slug)->first();

        if (!$section) return back()->with(['error' => [__('Section not found!')]]);

        $section_values = json_decode(json_encode($section->value), true);

        if (!isset($section_values['items'])) return back()->with(['error' => [__('Section item not found!')]]);

        if (!array_key_exists($request->target, $section_values['items'])) return back()->with(['error' => [__('Section item is invalid!')]]);

        $language_wise_data = $this->contentValidate($request, $basic_field_name, "security-edit");

        if ($language_wise_data instanceof RedirectResponse) return $language_wise_data;

        $language_wise_data = array_map(function ($language) {
            return replace_array_key($language, "_edit");
        }, $language_wise_data);

        $section_values['items'][$request->target]['language'] = $language_wise_data;

        $section_values['items'][$request->target]['icon']    = $request->icon_edit;

        try {
            $section->update([
                'value' => $section_values,
            ]);
        } catch (Exception $e) {
            return back()->with(['error' => [__('Something went wrong! Please try again')]]);
        }

        return back()->with(['success' => [__('Section Item updated successfully!')]]);
    }

    /**
     * Method for delete service item
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function securityItemDelete(Request $request, $slug)
    {
        $request->validate([
            'target'    => 'required|string',
        ]);
        $slug = Str::slug(SiteSectionConst::SECURITY_SECTION);
        $section = SiteSections::getData($slug)->first();

        if (!$section) return back()->with(['error' => [__('Section not found!')]]);

        $section_values = json_decode(json_encode($section->value), true);

        if (!isset($section_values['items'])) return back()->with(['error' => [__('Section item not found!')]]);

        if (!array_key_exists($request->target, $section_values['items'])) return back()->with(['error' => [__('Section item is invalid!')]]);

        try {
            unset($section_values['items'][$request->target]);
            $section->update([
                'value'     => $section_values,
            ]);
        } catch (Exception $e) {
            return back()->with(['error' => [__('Something went wrong! Please try again.')]]);
        }

        return back()->with(['success' => [__('Section item deleted successfully!')]]);
    }


    /**
     * Method for show services section page
     * @param string $slug
     * @return view
     */
    public function chooseUsView($slug)
    {
        $page_title = __("Choose Us Section");
        $section_slug = Str::slug(SiteSectionConst::CHOOSE_US_SECTION);
        $data = SiteSections::getData($section_slug)->first();
        $languages = $this->languages;

        return view('admin.sections.setup-sections.choose-us-section', compact(
            'page_title',
            'data',
            'languages',
            'slug',
        ));
    }

    /**
     * Method for update service section information
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function chooseUsUpdate(Request $request, $slug)
    {
        $basic_field_name = [
            'section_title' => "required|string|max:100",
            'heading' => "required|string|max:100",
            'sub_heading' => "required|string",
        ];

        $slug = Str::slug(SiteSectionConst::CHOOSE_US_SECTION);
        $section = SiteSections::where("key", $slug)->first();

        if ($section != null) {
            $section_data = json_decode(json_encode($section->value), true);
        } else {
            $section_data = [];
        }

        $section_data['language']  = $this->contentValidate($request, $basic_field_name);
        $update_data['key']    = $slug;
        $update_data['value']  = $section_data;

        try {
            SiteSections::updateOrCreate(['key' => $slug], $update_data);
        } catch (Exception $e) {
            return back()->with(['error' => [__('Something went wrong! Please try again.')]]);
        }

        return back()->with(['success' => [__('Section updated successfully!')]]);
    }

    /**
     * Method for store service item
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function chooseUsItemStore(Request $request, $slug)
    {
        $basic_field_name = [
            'description'   => "required|string|max:500",
        ];

        $language_wise_data = $this->contentValidate($request, $basic_field_name, "choose-add");

        if ($language_wise_data instanceof RedirectResponse) return $language_wise_data;

        $slug = Str::slug(SiteSectionConst::CHOOSE_US_SECTION);
        $section = SiteSections::where("key", $slug)->first();

        if ($section != null) {
            $section_data = json_decode(json_encode($section->value), true);
        } else {
            $section_data = [];
        }

        $unique_id = uniqid();
        $section_data['items'][$unique_id]['language'] = $language_wise_data;
        $section_data['items'][$unique_id]['id'] = $unique_id;
        $update_data['key'] = $slug;
        $update_data['value']   = $section_data;

        try {
            SiteSections::updateOrCreate(['key' => $slug], $update_data);
        } catch (Exception $e) {
            return back()->with(['error' => [__('Something went wrong! Please try again')]]);
        }

        return back()->with(['success' => [__('Section item added successfully!')]]);
    }

    /**
     * Method for update service item
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function chooseUsItemUpdate(Request $request, $slug)
    {
        $validator = Validator::make($request->all(), [
            'target'    => "required|string",
        ]);
        if ($validator->fails()) return back()->withErrors($validator)->withInput()->with('modal', 'choose-edit');

        $basic_field_name = [
            'title_edit'     => "required|string|max:255",
            'description_edit'   => "required|string|max:500",
        ];

        $slug = Str::slug(SiteSectionConst::CHOOSE_US_SECTION);
        $section = SiteSections::getData($slug)->first();

        if (!$section) return back()->with(['error' => [__('Section not found!')]]);

        $section_values = json_decode(json_encode($section->value), true);

        if (!isset($section_values['items'])) return back()->with(['error' => [__('Section item not found!')]]);

        if (!array_key_exists($request->target, $section_values['items'])) return back()->with(['error' => [__('Section item is invalid!')]]);

        $language_wise_data = $this->contentValidate($request, $basic_field_name, "choose-edit");

        if ($language_wise_data instanceof RedirectResponse) return $language_wise_data;

        $language_wise_data = array_map(function ($language) {
            return replace_array_key($language, "_edit");
        }, $language_wise_data);

        $section_values['items'][$request->target]['language'] = $language_wise_data;

        try {
            $section->update([
                'value' => $section_values,
            ]);
        } catch (Exception $e) {
            return back()->with(['error' => [__('Something went wrong! Please try again')]]);
        }

        return back()->with(['success' => [__('Section Item updated successfully!')]]);
    }

    /**
     * Method for delete service item
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function chooseUsItemDelete(Request $request, $slug)
    {
        $request->validate([
            'target'    => 'required|string',
        ]);
        $slug = Str::slug(SiteSectionConst::CHOOSE_US_SECTION);
        $section = SiteSections::getData($slug)->first();

        if (!$section) return back()->with(['error' => [__('Section not found!')]]);

        $section_values = json_decode(json_encode($section->value), true);

        if (!isset($section_values['items'])) return back()->with(['error' => [__('Section item not found!')]]);

        if (!array_key_exists($request->target, $section_values['items'])) return back()->with(['error' => [__('Section item is invalid!')]]);

        try {
            unset($section_values['items'][$request->target]);
            $section->update([
                'value'     => $section_values,
            ]);
        } catch (Exception $e) {
            return back()->with(['error' => [__('Something went wrong! Please try again.')]]);
        }

        return back()->with(['success' => [__('Section item deleted successfully!')]]);
    }


    /**
     * Method for show about us section page
     * @param string $slug
     * @return view
     */
    public function statisticsView($slug)
    {
        $page_title = __("Statistics Section");
        $section_slug = Str::slug(SiteSectionConst::STATISTICS_SECTION);
        $data = SiteSections::getData($section_slug)->first();
        $languages = $this->languages;

        return view('admin.sections.setup-sections.statistics-section', compact(
            'page_title',
            'data',
            'languages',
            'slug',
        ));
    }

    /**
     * Method for update about section information
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function statisticsUpdate(Request $request, $slug)
    {

        $basic_field_name = [
            'section_title'       => "required|string|max:100",
            'heading'       => "required|string|max:100",
            'sub_heading'   => "required|string|max:500",
        ];

        $slug = Str::slug(SiteSectionConst::STATISTICS_SECTION);
        $section = SiteSections::where("key", $slug)->first();

        if ($section != null) {
            $data = json_decode(json_encode($section->value), true);
        } else {
            $data = [];
        }

        $data['image'] = $section->value->image ?? null;

        if ($request->hasFile("image")) {
            $data['image']      = $this->imageValidate($request, "image", $section->value->image ?? null);
        }

        $data['language']  = $this->contentValidate($request, $basic_field_name);
        $update_data['value']  = $data;
        $update_data['key']    = $slug;

        try {
            SiteSections::updateOrCreate(['key' => $slug], $update_data);
        } catch (Exception $e) {
            return back()->with(['error' => [__('Something went wrong! Please try again.')]]);
        }

        return back()->with(['success' => [__('Section updated successfully!')]]);
    }

    /**
     * Method for store about us item
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function statisticsItemStore(Request $request, $slug)
    {

        $basic_field_name = [
            'title'         => "required|string|max:255",
        ];

        $validator = Validator::make($request->all(), [
            'icon'      => "required|string|max:255",
            'amount'        => "required|integer",
        ]);

        if ($validator->fails()) return back()->withErrors($validator)->withInput()->with('modal', 'stat-item-add');

        $validated = $validator->validate();

        $language_wise_data = $this->contentValidate($request, $basic_field_name, "stat-item-add");

        if ($language_wise_data instanceof RedirectResponse) return $language_wise_data;

        $slug = Str::slug(SiteSectionConst::STATISTICS_SECTION);
        $section = SiteSections::where("key", $slug)->first();

        if ($section != null) {
            $section_data = json_decode(json_encode($section->value), true);
        } else {
            $section_data = [];
        }
        $unique_id = uniqid();

        $section_data['items'][$unique_id]['language'] = $language_wise_data;
        $section_data['items'][$unique_id]['id'] = $unique_id;
        $section_data['items'][$unique_id]['icon'] = $validated['icon'];
        $section_data['items'][$unique_id]['amount'] = $validated['amount'];
        $update_data['key'] = $slug;
        $update_data['value']   = $section_data;

        try {
            SiteSections::updateOrCreate(['key' => $slug], $update_data);
        } catch (Exception $e) {
            return back()->with(['error' => [__('Something went wrong! Please try again.')]]);
        }

        return back()->with(['success' => [__('Section item added successfully!')]]);
    }

    /**
     * Method for update about us item
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function statisticsItemUpdate(Request $request, $slug)
    {

        $request->validate([
            'target'        => "required|string",
            'icon_edit'     => "required|string|max:255",
            'amount_edit'    => "required|integer",
        ]);
        $basic_field_name = [
            'title_edit'     => "required|string|max:255",
        ];

        $slug = Str::slug(SiteSectionConst::STATISTICS_SECTION);
        $section = SiteSections::getData($slug)->first();

        if (!$section) return back()->with(['error' => [__('Section not found!')]]);

        $section_values = json_decode(json_encode($section->value), true);

        if (!isset($section_values['items'])) return back()->with(['error' => [__('Section item not found!')]]);

        if (!array_key_exists($request->target, $section_values['items'])) return back()->with(['error' => [__('Section item is invalid!')]]);

        $language_wise_data = $this->contentValidate($request, $basic_field_name, "stat-item-edit");

        if ($language_wise_data instanceof RedirectResponse) return $language_wise_data;

        $language_wise_data = array_map(function ($language) {
            return replace_array_key($language, "_edit");
        }, $language_wise_data);



        $section_values['items'][$request->target]['language']  = $language_wise_data;
        $section_values['items'][$request->target]['icon']      = $request->icon_edit;
        $section_values['items'][$request->target]['amount']      = $request->amount_edit;

        try {
            $section->update([
                'value' => $section_values,
            ]);
        } catch (Exception $e) {
            return back()->with(['error' => [__('Something went wrong! Please try again.')]]);
        }

        return back()->with(['success' => [__('Section Item updated successfully!')]]);
    }

    /**
     * Method for delete about us item
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function statisticsItemDelete(Request $request, $slug)
    {
        $request->validate([
            'target'    => 'required|string',
        ]);
        $slug = Str::slug(SiteSectionConst::STATISTICS_SECTION);
        $section = SiteSections::getData($slug)->first();

        if (!$section) return back()->with(['error' => [__('Section not found!')]]);

        $section_values = json_decode(json_encode($section->value), true);

        if (!isset($section_values['items'])) return back()->with(['error' => [__('Section item not found!')]]);

        if (!array_key_exists($request->target, $section_values['items'])) return back()->with(['error' => [__('Section item is invalid!')]]);

        try {
            unset($section_values['items'][$request->target]);
            $section->update([
                'value'     => $section_values,
            ]);
        } catch (Exception $e) {
            return back()->with(['error' => [__('Something went wrong! Please try again.')]]);
        }

        return back()->with(['success' => [__('Section item deleted successfully!')]]);
    }


     /**
     * Method for show about us section page
     * @param string $slug
     * @return view
     */
    public function appView($slug)
    {
        $page_title = __("App Section");
        $section_slug = Str::slug(SiteSectionConst::APP_SECTION);
        $data = SiteSections::getData($section_slug)->first();
        $languages = $this->languages;

        return view('admin.sections.setup-sections.app-section', compact(
            'page_title',
            'data',
            'languages',
            'slug',
        ));
    }

    /**
     * Method for update about section information
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function appUpdate(Request $request, $slug)
    {

        $basic_field_name = [
            'heading'       => "required|string|max:100",
            'sub_heading'   => "required|string",
        ];

        $slug = Str::slug(SiteSectionConst::APP_SECTION);
        $section = SiteSections::where("key", $slug)->first();

        if ($section != null) {
            $data = json_decode(json_encode($section->value), true);
        } else {
            $data = [];
        }

        $data['image'] = $section->value->image ?? null;

        if ($request->hasFile("image")) {
            $data['image']      = $this->imageValidate($request, "image", $section->value->image ?? null);
        }

        $data['language']  = $this->contentValidate($request, $basic_field_name);
        $update_data['value']  = $data;
        $update_data['key']    = $slug;

        try {
            SiteSections::updateOrCreate(['key' => $slug], $update_data);
        } catch (Exception $e) {
            return back()->with(['error' => [__('Something went wrong! Please try again.')]]);
        }

        return back()->with(['success' => [__('Section updated successfully!')]]);
    }


    /**
     * Method for show about us section page
     * @param string $slug
     * @return view
     */
    public function vendorSafetyView($slug)
    {
        $page_title = __("Vendor Safety Section");
        $section_slug = Str::slug(SiteSectionConst::VENDOR_SAFETY_SECTION);
        $data = SiteSections::getData($section_slug)->first();
        $languages = $this->languages;

        return view('admin.sections.setup-sections.vendor-safety-section', compact(
            'page_title',
            'data',
            'languages',
            'slug',
        ));
    }

    /**
     * Method for update about section information
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function vendorSafetyUpdate(Request $request, $slug)
    {

        $basic_field_name = [
            'heading'       => "required|string|max:500",
        ];

        $slug = Str::slug(SiteSectionConst::VENDOR_SAFETY_SECTION);
        $section = SiteSections::where("key", $slug)->first();

        if ($section != null) {
            $data = json_decode(json_encode($section->value), true);
        } else {
            $data = [];
        }

        $data['image'] = $section->value->image ?? null;

        if ($request->hasFile("image")) {
            $data['image']      = $this->imageValidate($request, "image", $section->value->image ?? null);
        }

        $data['language']  = $this->contentValidate($request, $basic_field_name);
        $update_data['value']  = $data;
        $update_data['key']    = $slug;

        try {
            SiteSections::updateOrCreate(['key' => $slug], $update_data);
        } catch (Exception $e) {
            return back()->with(['error' => [__('Something went wrong! Please try again.')]]);
        }

        return back()->with(['success' => [__('Section updated successfully!')]]);
    }

    /**
     * Method for store about us item
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function vendorSafetyItemStore(Request $request, $slug)
    {

        $basic_field_name = [
            'title'         => "required|string|max:255",
            'description'   => "required|string|max:500",
        ];

        $validator = Validator::make($request->all(), [
            'icon'      => "required|string|max:255",
        ]);

        if ($validator->fails()) return back()->withErrors($validator)->withInput()->with('modal', 'vendor-safety-item-add');

        $validated = $validator->validate();
        $language_wise_data = $this->contentValidate($request, $basic_field_name, "vendor-safety-item-add");

        if ($language_wise_data instanceof RedirectResponse) return $language_wise_data;

        $slug = Str::slug(SiteSectionConst::VENDOR_SAFETY_SECTION);
        $section = SiteSections::where("key", $slug)->first();

        if ($section != null) {
            $section_data = json_decode(json_encode($section->value), true);
        } else {
            $section_data = [];
        }

        $unique_id = uniqid();

        $section_data['items'][$unique_id]['language'] = $language_wise_data;
        $section_data['items'][$unique_id]['id'] = $unique_id;
        $section_data['items'][$unique_id]['icon'] = $validated['icon'];
        $update_data['key'] = $slug;
        $update_data['value']   = $section_data;

        try {
            SiteSections::updateOrCreate(['key' => $slug], $update_data);
        } catch (Exception $e) {
            return back()->with(['error' => [__('Something went wrong! Please try again.')]]);
        }

        return back()->with(['success' => [__('Section item added successfully!')]]);
    }

    /**
     * Method for update about us item
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function vendorSafetyItemUpdate(Request $request, $slug)
    {

        $request->validate([
            'target'        => "required|string",
            'icon_edit'     => "required|string|max:255",
        ]);

        $basic_field_name = [
            'title_edit'     => "required|string|max:255",
            'description_edit'   => "required|string|max:500",
        ];

        $slug = Str::slug(SiteSectionConst::VENDOR_SAFETY_SECTION);
        $section = SiteSections::getData($slug)->first();

        if (!$section) return back()->with(['error' => [__('Section not found!')]]);

        $section_values = json_decode(json_encode($section->value), true);

        if (!isset($section_values['items'])) return back()->with(['error' => [__('Section item not found!')]]);

        if (!array_key_exists($request->target, $section_values['items'])) return back()->with(['error' => [__('Section item is invalid!')]]);

        $language_wise_data = $this->contentValidate($request, $basic_field_name, "vendor-safety-item-edit");

        if ($language_wise_data instanceof RedirectResponse) return $language_wise_data;

        $language_wise_data = array_map(function ($language) {
            return replace_array_key($language, "_edit");
        }, $language_wise_data);

        $section_values['items'][$request->target]['language'] = $language_wise_data;
        $section_values['items'][$request->target]['icon']    = $request->icon_edit;

        try {
            $section->update([
                'value' => $section_values,
            ]);
        } catch (Exception $e) {
            return back()->with(['error' => [__('Something went wrong! Please try again.')]]);
        }

        return back()->with(['success' => [__('Section Item updated successfully!')]]);
    }

    /**
     * Method for delete about us item
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function vendorSafetyItemDelete(Request $request, $slug)
    {
        $request->validate([
            'target'    => 'required|string',
        ]);
        $slug = Str::slug(SiteSectionConst::VENDOR_SAFETY_SECTION);
        $section = SiteSections::getData($slug)->first();

        if (!$section) return back()->with(['error' => [__('Section not found!')]]);

        $section_values = json_decode(json_encode($section->value), true);

        if (!isset($section_values['items'])) return back()->with(['error' => [__('Section item not found!')]]);

        if (!array_key_exists($request->target, $section_values['items'])) return back()->with(['error' => [__('Section item is invalid!')]]);

        try {
            unset($section_values['items'][$request->target]);
            $section->update([
                'value'     => $section_values,
            ]);
        } catch (Exception $e) {
            return back()->with(['error' => [__('Something went wrong! Please try again.')]]);
        }

        return back()->with(['success' => [__('Section item deleted successfully!')]]);
    }


    /**
     * Method for show vendorRequirements section page
     * @param string $slug
     * @return view
     */
    public function vendorRequirementsView($slug)
    {
        $page_title = __("Vendor Requirements Section");
        $section_slug = Str::slug(SiteSectionConst::VENDOR_REQUIREMENTS_SECTION);
        $data = SiteSections::getData($section_slug)->first();
        $languages = $this->languages;

        return view('admin.sections.setup-sections.vendor-requirements-section', compact(
            'page_title',
            'data',
            'languages',
            'slug',
        ));
    }

    /**
     * Method for update vendorRequirements section information
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function vendorRequirementsUpdate(Request $request, $slug)
    {
        $basic_field_name = [
            'title' => "required|string|max:100",
            'heading' => "required|string|max:100",
        ];

        $slug = Str::slug(SiteSectionConst::VENDOR_REQUIREMENTS_SECTION);
        $section = SiteSections::where("key", $slug)->first();

        if ($section != null) {
            $section_data = json_decode(json_encode($section->value), true);
        } else {
            $section_data = [];
        }

        $section_data['language']  = $this->contentValidate($request, $basic_field_name);
        $update_data['key']    = $slug;
        $update_data['value']  = $section_data;

        try {
            SiteSections::updateOrCreate(['key' => $slug], $update_data);
        } catch (Exception $e) {
            return back()->with(['error' => [__('Something went wrong! Please try again.')]]);
        }

        return back()->with(['success' => [__('Section updated successfully!')]]);
    }

    /**
     * Method for store vendorRequirements item
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function vendorRequirementsItemStore(Request $request, $slug)
    {
        $basic_field_name = [
            'title'         => "required|string|max:255",

        ];
        $validator  = Validator::make($request->all(), [
            'icon'            => "required|string|max:100",
        ]);

        if ($validator->fails()) return back()->withErrors($validator)->withInput()->with('modal', 'vendor-requirements-add');

        $validated = $validator->validate();
        $language_wise_data = $this->contentValidate($request, $basic_field_name, "vendor-requirements-add");

        if ($language_wise_data instanceof RedirectResponse) return $language_wise_data;

        $slug = Str::slug(SiteSectionConst::VENDOR_REQUIREMENTS_SECTION);
        $section = SiteSections::where("key", $slug)->first();

        if ($section != null) {
            $section_data = json_decode(json_encode($section->value), true);
        } else {
            $section_data = [];
        }

        $unique_id = uniqid();
        $section_data['items'][$unique_id]['language']   = $language_wise_data;
        $section_data['items'][$unique_id]['id']         = $unique_id;
        $section_data['items'][$unique_id]['icon']     = $validated['icon'];
        $update_data['key'] = $slug;
        $update_data['value']   = $section_data;

        try {
            SiteSections::updateOrCreate(['key' => $slug], $update_data);
        } catch (Exception $e) {
            return back()->with(['error' => [__('Something went wrong! Please try again')]]);
        }

        return back()->with(['success' => [__('Section item added successfully!')]]);
    }

    /**
     * Method for update vendorRequirements item
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function vendorRequirementsItemUpdate(Request $request)
    {
        $validator  = Validator::make($request->all(), [
            'target'    => "required|string",
            'icon_edit' => "required|string|max:100",
        ]);

        $basic_field_name = [
            'title_edit'     => "required|string|max:255",
        ];

        if ($validator->fails()) return back()->withErrors($validator)->withInput()->with('modal', 'vendor-requirements-edit');

        $slug = Str::slug(SiteSectionConst::VENDOR_REQUIREMENTS_SECTION);
        $section = SiteSections::getData($slug)->first();

        if (!$section) return back()->with(['error' => [__('Section not found!')]]);

        $section_values = json_decode(json_encode($section->value), true);

        if (!isset($section_values['items'])) return back()->with(['error' => [__('Section item not found!')]]);

        if (!array_key_exists($request->target, $section_values['items'])) return back()->with(['error' => [__('Section item is invalid!')]]);

        $request->merge(['old_image' => $section_values['items'][$request->target]['image'] ?? null]);

        $language_wise_data = $this->contentValidate($request, $basic_field_name, "vendor-requirements-edit");

        if ($language_wise_data instanceof RedirectResponse) return $language_wise_data;

        $language_wise_data = array_map(function ($language) {
            return replace_array_key($language, "_edit");
        }, $language_wise_data);

        $section_values['items'][$request->target]['language'] = $language_wise_data;

        if ($request->hasFile("image")) {
            $section_values['items'][$request->target]['image'] = $this->imageValidate($request, "image", $section_values['items'][$request->target]['image'] ?? null);
        }

        $section_values['items'][$request->target]['icon']    = $request->icon_edit;

        try {
            $section->update([
                'value' => $section_values,
            ]);
        } catch (Exception $e) {
            return back()->with(['error' => [__('Something went wrong! Please try again')]]);
        }

        return back()->with(['success' => [__('Information updated successfully!')]]);
    }

    /**
     * Method for delete vendorRequirements item
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function vendorRequirementsItemDelete(Request $request, $slug)
    {
        $request->validate([
            'target'    => 'required|string',
        ]);
        $slug = Str::slug(SiteSectionConst::VENDOR_REQUIREMENTS_SECTION);
        $section = SiteSections::getData($slug)->first();

        if (!$section) return back()->with(['error' => [__('Section not found!')]]);

        $section_values = json_decode(json_encode($section->value), true);

        if (!isset($section_values['items'])) return back()->with(['error' => [__('Section item not found!')]]);

        if (!array_key_exists($request->target, $section_values['items'])) return back()->with(['error' => [__('Section item is invalid!')]]);

        try {
            unset($section_values['items'][$request->target]);
            $section->update([
                'value'     => $section_values,
            ]);
        } catch (Exception $e) {
            return back()->with(['error' => [__('Something went wrong! Please try again.')]]);
        }

        return back()->with(['success' => [__('Section item delete successfully!')]]);
    }

    /**
     * Method for show brand section page
     * @param string $slug
     * @return view
     */
    public function requirementsDetailsView($id)
    {

        $languages      = $this->languages;
        $page_title     = __("Requirement Details Section");
        $result         = DB::table('site_sections')
            ->where('value->items->' . $id . '->id', $id)
            ->first();


        if ($result) {
            $value = json_decode($result->value, true);
            $key = $result->key;
            $item = $value['items'][$id] ?? null;
            if ($item) {
                return view('admin.sections.setup-sections.requirements-details-section', compact('page_title', 'item', 'languages'));
            }
        }
    }


    /**
     * Method for store brand item
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function requirementsDetailsItemStore(Request $request, $id)
    {
        try {

            $basic_field_name = ['details' => 'required|string|max:100'];
            $language_wise_data = $this->contentValidate($request, $basic_field_name, "requirements-details-add");

            if ($language_wise_data instanceof RedirectResponse) return $language_wise_data;

            $slug = Str::slug(SiteSectionConst::VENDOR_REQUIREMENTS_SECTION);
            $section = SiteSections::where("key", $slug)->first();
            $section_data = json_decode(json_encode($section->value), true);
            $unique_id = uniqid();
            $section_data['items'][$id]['detailsItem'][$unique_id]['language']  = $language_wise_data;
            $section_data['items'][$id]['detailsItem'][$unique_id]['id']        = $unique_id;

            $update_data = [
                'key' => $slug,
                'value' => $section_data
            ];

            SiteSections::updateOrCreate(['key' => $slug], $update_data);

            return back()->with(['success' => __('Section item added successfully!')]);

        } catch (Exception $e) {
            return back()->withErrors(['error' => __('Something went wrong! Please try again')]);
        }
    }


    /**
     * Method for update vendorRequirements item
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function requirementsDetailsItemUpdate(Request $request)
    {
        $request->validate([
            'main_id'    => 'required|string',
            'details_item_id' => 'required|string',
        ]);

        $main_id = $request->main_id;
        $details_item_id = $request->details_item_id;
        $result = DB::table('site_sections')
        ->where('value->items->' . $main_id . '->id', $main_id)
        ->first();
        $key = $result->key;
        $value = json_decode($result->value, true);
        $basic_field_name = ['details' => 'required|string|max:100'];
        $language_wise_data = $this->contentValidate($request, $basic_field_name, "requirements-details-edit");

        if ($language_wise_data instanceof RedirectResponse) return $language_wise_data;

            $basic_field_name = ['details' => 'required|string|max:100'];
            $language_wise_data = $this->contentValidate($request, $basic_field_name, "requirements-details-edit");

            if ($language_wise_data instanceof RedirectResponse) return $language_wise_data;


          if (isset($value['items'][$main_id]['detailsItem'][$details_item_id])) {

            try {
                $value['items'][$main_id]['detailsItem'][$details_item_id]['language'] = $language_wise_data;
                $section_data['items'][$main_id]['detailsItem'][$details_item_id]['id']= $details_item_id;
                DB::table('site_sections')
                    ->where('key', $key)
                    ->update(['value' => json_encode($value)]);

                return back()->with(['success' => [__('Section item deleted successfully!')]]);

            } catch (Exception $e) {
                return back()->with(['error' => [__('Something went wrong! Please try again.')]]);
            }
        } else {
            return back()->with(['error' => [__('Section not found!')]]);
        }
    }


    /**
     * Method for delete brand item
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function requirementsDetailsItemDelete(Request $request, $id, $parentId)
    {

        $request->validate([
            'target' => 'required|string',
        ]);

        $result = DB::table('site_sections')
            ->where('value->items->' . $id . '->id', $id)
            ->first();

        if ($result) {
            $value = json_decode($result->value, true);
            $key = $result->key;

            if (isset($value['items'][$id]['detailsItem'][$parentId])) {
                try {
                    unset($value['items'][$id]['detailsItem'][$parentId]);

                    DB::table('site_sections')
                        ->where('key', $key)
                        ->update(['value' => json_encode($value)]);

                    return back()->with(['success' => [__('Section item deleted successfully!')]]);

                } catch (Exception $e) {
                    return back()->with(['error' => [__('Something went wrong! Please try again.')]]);
                }
            } else {
                return back()->with(['error' => [__('Section not found!')]]);
            }
        } else {
            return back()->with(['error' => [__('Section not found!')]]);
        }
    }

    /**
     * Method for show about us section page
     * @param string $slug
     * @return view
     */
    public function aboutUsView($slug)
    {
        $page_title = __("About Us Section");
        $section_slug = Str::slug(SiteSectionConst::ABOUT_US_SECTION);
        $data = SiteSections::getData($section_slug)->first();
        $languages = $this->languages;

        return view('admin.sections.setup-sections.about-us-section', compact(
            'page_title',
            'data',
            'languages',
            'slug',
        ));
    }

    /**
     * Method for update about section information
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function aboutUsUpdate(Request $request, $slug)
    {

        $basic_field_name = [
            'section_title'       => "required|string|max:250",
            'heading'       => "required|string|max:250",
            'sub_heading'   => "required|string|max:700",
        ];

        $slug = Str::slug(SiteSectionConst::ABOUT_US_SECTION);
        $section = SiteSections::where("key", $slug)->first();

        if ($section != null) {
            $data = json_decode(json_encode($section->value), true);
        } else {
            $data = [];
        }

        $data['image'] = $section->value->image ?? null;

        if ($request->hasFile("image")) {
            $data['image']      = $this->imageValidate($request, "image", $section->value->image ?? null);
        }

        $data['language']  = $this->contentValidate($request, $basic_field_name);
        $update_data['value']  = $data;
        $update_data['key']    = $slug;

        try {
            SiteSections::updateOrCreate(['key' => $slug], $update_data);
        } catch (Exception $e) {
            return back()->with(['error' => [__('Something went wrong! Please try again.')]]);
        }

        return back()->with(['success' => [__('Section updated successfully!')]]);
    }

    /**
     * Method for show find car section page
     * @param string $slug
     * @return view
     */
    public function findCarView($slug)
    {
        $page_title = __("Find Car Section");
        $section_slug = Str::slug(SiteSectionConst::FIND_SECTION);
        $data = SiteSections::getData($section_slug)->first();
        $languages = $this->languages;

        return view('admin.sections.setup-sections.find-car-section', compact(
            'page_title',
            'data',
            'languages',
            'slug',
        ));
    }

    /**
     * Method for update find car section information
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function findCarUpdate(Request $request, $slug)
    {
        $basic_field_name = [
            'heading'       => "required|string|max:250",
            'sub_heading'   => "required|string|max:700",
            'button_name_one'   => "required|string|max:700",
        ];

        $validator = Validator::make($request->all(), [
            'button_link_one'      => "required|string|max:255",
        ]);

        if ($validator->fails()) return back()->withErrors($validator)->withInput();
        $validated = $validator->validate();

        $slug = Str::slug(SiteSectionConst::FIND_SECTION);
        $section = SiteSections::where("key", $slug)->first();

        if ($section != null) {
            $data = json_decode(json_encode($section->value), true);
        } else {
            $data = [];
        }

        $data['language']  = $this->contentValidate($request, $basic_field_name);
        $data['button_link_one'] = $validated['button_link_one'];
        $update_data['value']  = $data;
        $update_data['key']    = $slug;

        try {
            SiteSections::updateOrCreate(['key' => $slug], $update_data);
        } catch (Exception $e) {
            return back()->with(['error' => [__('Something went wrong! Please try again.')]]);
        }

        return back()->with(['success' => [__('Section updated successfully!')]]);
    }

    /**
     * Method for show services section page
     * @param string $slug
     * @return view
     */
    public function servicesView($slug)
    {
        $page_title = __("Services Section");
        $section_slug = Str::slug(SiteSectionConst::SERVICES_SECTION);
        $data = SiteSections::getData($section_slug)->first();
        $languages = $this->languages;

        return view('admin.sections.setup-sections.services-section', compact(
            'page_title',
            'data',
            'languages',
            'slug',
        ));
    }

    /**
     * Method for update service section information
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function servicesUpdate(Request $request, $slug)
    {
        $basic_field_name = [
            'section_title' => "required|string|max:250",
            'heading' => "required|string|max:250",
            'sub_heading' => "required|string|max:500",
        ];

        $slug = Str::slug(SiteSectionConst::SERVICES_SECTION);
        $section = SiteSections::where("key", $slug)->first();

        if ($section != null) {
            $section_data = json_decode(json_encode($section->value), true);
        } else {
            $section_data = [];
        }


        $section_data['language']  = $this->contentValidate($request, $basic_field_name);
        $update_data['key']    = $slug;
        $update_data['value']  = $section_data;

        try {
            SiteSections::updateOrCreate(['key' => $slug], $update_data);
        } catch (Exception $e) {
            return back()->with(['error' => [__('Something went wrong! Please try again.')]]);
        }

        return back()->with(['success' => [__('Section updated successfully!')]]);
    }

    /**
     * Method for store service item
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function servicesItemStore(Request $request, $slug)
    {
        $basic_field_name = [
            'title'         => "required|string|max:255",
            'description'   => "required|string|max:500",
        ];

        $validator = Validator::make($request->all(), [
            'icon'      => "required|string|max:255",
        ]);
        if ($validator->fails()) return back()->withErrors($validator)->withInput()->with('modal', 'service-add');

        $validated = $validator->validate();
        $language_wise_data = $this->contentValidate($request, $basic_field_name, "service-add");

        if ($language_wise_data instanceof RedirectResponse) return $language_wise_data;

        $slug = Str::slug(SiteSectionConst::SERVICES_SECTION);
        $section = SiteSections::where("key", $slug)->first();

        if ($section != null) {
            $section_data = json_decode(json_encode($section->value), true);
        } else {
            $section_data = [];
        }

        $unique_id = uniqid();
        $section_data['items'][$unique_id]['language'] = $language_wise_data;
        $section_data['items'][$unique_id]['id'] = $unique_id;
        $section_data['items'][$unique_id]['icon'] = $validated['icon'];
        $update_data['key'] = $slug;
        $update_data['value']   = $section_data;

        try {
            SiteSections::updateOrCreate(['key' => $slug], $update_data);
        } catch (Exception $e) {
            return back()->with(['error' => [__('Something went wrong! Please try again')]]);
        }

        return back()->with(['success' => [__('Section item added successfully!')]]);
    }

    /**
     * Method for update service item
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function servicesItemUpdate(Request $request, $slug)
    {
        $request->validate([
            'target'    => "required|string",
            'icon_edit'      => "required|string|max:255",
        ]);

        $basic_field_name = [
            'title_edit'     => "required|string|max:255",
            'description_edit'   => "required|string|max:500",
        ];

        $slug = Str::slug(SiteSectionConst::SERVICES_SECTION);
        $section = SiteSections::getData($slug)->first();

        if (!$section) return back()->with(['error' => [__('Section not found!')]]);

        $section_values = json_decode(json_encode($section->value), true);

        if (!isset($section_values['items'])) return back()->with(['error' => [__('Section item not found!')]]);

        if (!array_key_exists($request->target, $section_values['items'])) return back()->with(['error' => [__('Section item is invalid!')]]);

        $language_wise_data = $this->contentValidate($request, $basic_field_name, "service-edit");

        if ($language_wise_data instanceof RedirectResponse) return $language_wise_data;

        $language_wise_data = array_map(function ($language) {
            return replace_array_key($language, "_edit");
        }, $language_wise_data);

        $section_values['items'][$request->target]['language'] = $language_wise_data;

        $section_values['items'][$request->target]['icon']    = $request->icon_edit;

        try {
            $section->update([
                'value' => $section_values,
            ]);
        } catch (Exception $e) {
            return back()->with(['error' => [__('Something went wrong! Please try again')]]);
        }

        return back()->with(['success' => [__('Section Item updated successfully!')]]);
    }

    /**
     * Method for delete service item
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function servicesItemDelete(Request $request, $slug)
    {
        $request->validate([
            'target'    => 'required|string',
        ]);
        $slug = Str::slug(SiteSectionConst::SERVICES_SECTION);
        $section = SiteSections::getData($slug)->first();

        if (!$section) return back()->with(['error' => [__('Section not found!')]]);

        $section_values = json_decode(json_encode($section->value), true);

        if (!isset($section_values['items'])) return back()->with(['error' => [__('Section item not found!')]]);

        if (!array_key_exists($request->target, $section_values['items'])) return back()->with(['error' => [__('Section item is invalid!')]]);

        try {
            unset($section_values['items'][$request->target]);
            $section->update([
                'value'     => $section_values,
            ]);
        } catch (Exception $e) {
            return back()->with(['error' => [__('Something went wrong! Please try again.')]]);
        }

        return back()->with(['success' => [__('Section item deleted successfully!')]]);
    }


    /**
     * Method for show announcement section page
     * @param string $slug
     * @return view
     */
    public function announcementView($slug)
    {
        $page_title = __("Blog Section");
        $section_slug = Str::slug(SiteSectionConst::ANNOUNCEMENT_SECTION);
        $data = SiteSections::getData($section_slug)->first();
        $languages = $this->languages;
        $announcements = Announcement::get();
        $categories = AnnouncementCategory::get();
        $total_categories = $categories->count();
        $active_categories = $categories->where("status", GlobalConst::ACTIVE)->count();
        $total_announcements = $announcements->count();
        $active_announcements = $announcements->where("status", GlobalConst::ACTIVE)->count();


        return view('admin.sections.setup-sections.announcement-section', compact(
            'page_title',
            'data',
            'languages',
            'slug',
            'total_categories',
            'active_categories',
            'total_announcements',
            'active_announcements',
        ));
    }

    /**
     * Method for update announcement update section information
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function announcementUpdate(Request $request, $slug)
    {
        $basic_field_name = [
            'section_title' => "required|string|max:100",
            'heading' => "required|string|max:100",
        ];

        $slug = Str::slug(SiteSectionConst::ANNOUNCEMENT_SECTION);
        $section = SiteSections::where("key", $slug)->first();

        if ($section != null) {
            $section_data = json_decode(json_encode($section->value), true);
        } else {
            $section_data = [];
        }

        $section_data['language']  = $this->contentValidate($request, $basic_field_name);
        $update_data['key']    = $slug;
        $update_data['value']  = $section_data;

        try {
            SiteSections::updateOrCreate(['key' => $slug], $update_data);
        } catch (Exception $e) {
            return back()->with(['error' => [__('Something went wrong! Please try again.')]]);
        }

        return back()->with(['success' => [__('Section updated successfully!')]]);
    }

    /**
     * Method for show footer section page
     * @param string $slug
     * @return view
     */
    public function footerView($slug)
    {
        $page_title = __("Footer Section");
        $section_slug = Str::slug(SiteSectionConst::FOOTER_SECTION);
        $data = SiteSections::getData($section_slug)->first();
        $languages = $this->languages;

        return view('admin.sections.setup-sections.footer-section', compact(
            'page_title',
            'data',
            'languages',
            'slug',
        ));
    }

    /**
     * Method for update footer section information
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function footerUpdate(Request $request, $slug)
    {
        $basic_field_name = [
            'footer_desc'   => "required|string|max:500",
            'subscribe_desc'      => "required|string|max:1000",
            'footer_text' => "required|string|max:100",
        ];

        $slug = Str::slug(SiteSectionConst::FOOTER_SECTION);
        $section = SiteSections::where("key", $slug)->first();

        if ($section != null) {
            $section_data = json_decode(json_encode($section->value), true);
        } else {
            $section_data = [];
        }

        $section_data['language']  = $this->contentValidate($request, $basic_field_name);
        $update_data['value']  = $section_data;
        $update_data['key']    = $slug;

        try {
            SiteSections::updateOrCreate(['key' => $slug], $update_data);
        } catch (Exception $e) {
            return back()->with(['error' => [__('Something went wrong! Please try again')]]);
        }

        return back()->with(['success' => [__('Section updated successfully!')]]);
    }

    /**
     * Method for store footer item
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function footerItemStore(Request $request, $slug)
    {
        $validator = Validator::make($request->all(), [
            'item_name'        => 'required|string|max:100',
            'item_link'        => 'required|string|url|max:100',
            'item_social_icon' => 'required|max:100',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput()->with('modal', 'social-add');
        }

        $validated = $validator->validate();
        $slug = Str::slug(SiteSectionConst::FOOTER_SECTION);
        $section = SiteSections::where("key", $slug)->first();

        if ($section != null) {
            $section_data = json_decode(json_encode($section->value), true);
        } else {
            $section_data = [];
        }

        $unique_id = uniqid();
        $section_data['items'][$unique_id] = $validated;
        $section_data['items'][$unique_id]['id'] = $unique_id;
        $update_data['key'] = $slug;
        $update_data['value']   = $section_data;

        try {
            SiteSections::updateOrCreate(['key' => $slug], $update_data);
        } catch (Exception $e) {
            return back()->with(['error' => [__('Something went wrong! Please try again')]]);
        }

        return back()->with(['success' => [__('Social icon added successfully!')]]);
    }

    /**
     * Method for update social icon
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function footerItemUpdate(Request $request, $slug)
    {
        $validator = Validator::make($request->all(), [
            'item_name_edit'        => 'required|string|max:100',
            'item_link_edit'        => 'required|string|url|max:100',
            'item_social_icon_edit' => 'required|max:100',
        ]);
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput()->with('modal', 'social-edit');
        }

        $validated = $validator->validate();
        $validated = replace_array_key($validated, "_edit");

        $slug = Str::slug(SiteSectionConst::FOOTER_SECTION);
        $section = SiteSections::getData($slug)->first();

        if (!$section) return back()->with(['error' => [__('Section not found!')]]);

        $section_values = json_decode(json_encode($section->value), true);

        if (!isset($section_values['items'])) return back()->with(['error' => [__('Social Icon not found!')]]);

        if (!array_key_exists($request->target, $section_values['items'])) return back()->with(['error' => [__('Social Icon is invalid!')]]);

        $section_values['items'][$request->target] = $validated;
        $section_values['items'][$request->target]['id'] = $request->target;

        try {
            $section->update([
                'value' => $section_values,
            ]);
        } catch (Exception $e) {
            return back()->with(['error' => [__('Something went wrong! Please try again')]]);
        }

        return back()->with(['success' => [__('Information updated successfully!')]]);
    }

    /**
     * Method for delete social icon
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function footerItemDelete(Request $request, $slug)
    {
        $request->validate([
            'target'    => 'required|string',
        ]);
        $slug = Str::slug(SiteSectionConst::FOOTER_SECTION);
        $section = SiteSections::getData($slug)->first();

        if (!$section) return back()->with(['error' => [__('Section not found!')]]);

        $section_values = json_decode(json_encode($section->value), true);

        if (!isset($section_values['items'])) return back()->with(['error' => [__('Social Icon not found!')]]);

        if (!array_key_exists($request->target, $section_values['items'])) return back()->with(['error' => [__('Social Icon is invalid!')]]);

        try {
            unset($section_values['items'][$request->target]);
            $section->update([
                'value'     => $section_values,
            ]);
        } catch (Exception $e) {
            return back()->with(['error' => [__('Something went wrong! Please try again.')]]);
        }

        return back()->with(['success' => [__('Social Icon deleted successfully!')]]);
    }

    /**
     * Method for show contact us section page
     * @param string $slug
     * @return view
     */
    public function contactUsView($slug)
    {
        $page_title = __("Contact US Section");
        $section_slug = Str::slug(SiteSectionConst::CONTACT_US_SECTION);
        $data = SiteSections::getData($section_slug)->first();
        $languages = $this->languages;

        return view('admin.sections.setup-sections.contact-us-section', compact(
            'page_title',
            'data',
            'languages',
            'slug',
        ));
    }

    /**
     * Method for update contact section information
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function contactUsUpdate(Request $request, $slug)
    {
        $basic_field_name = ['section_title' => "required|string|max:100",'title' => "required|string|max:500",'description_title' => "required|string|max:100", 'description' => "required|string|max:2000", 'location_title' => "required|string|max:100", 'location' => "required|string|max:100", 'call_title' => "required|string|max:100", 'mobile' => "required|string|max:100",'email_address' => "required|string|email|max:100", 'email_title' => "required|string|max:100"];

        $slug = Str::slug(SiteSectionConst::CONTACT_US_SECTION);
        $section = SiteSections::where("key", $slug)->first();

        if ($section != null) {
            $section_data = json_decode(json_encode($section->value), true);
        } else {
            $section_data = [];
        }

        $section_data['language']  = $this->contentValidate($request, $basic_field_name);
        $update_data['value']  = $section_data;
        $update_data['key']    = $slug;

        try {
            SiteSections::updateOrCreate(['key' => $slug], $update_data);
        } catch (Exception $e) {
            return back()->with(['error' => [__('Something went wrong! Please try again.')]]);
        }

        return back()->with(['success' => [__('Section updated successfully!')]]);
    }


    /**
     * Method for show auth section page
     * @param string $slug
     * @return view
     */
    public function authView($slug)
    {
        $page_title = __("Auth Section");
        $section_slug = Str::slug(SiteSectionConst::AUTH_SECTION);
        $data = SiteSections::getData($section_slug)->first();
        $languages = $this->languages;

        return view('admin.sections.setup-sections.auth-section', compact(
            'page_title',
            'data',
            'languages',
            'slug'
        ));
    }

    /**
     * Method for update auth section information
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function authUpdate(Request $request, $slug)
    {
        $basic_field_name = ['login_heading' => "required|string|max:100", 'login_sub_heading' => "required|string|max:500", 'forgot_heading' => "required|string|max:100", 'forgot_sub_heading' => "required|string|max:500",];
        $slug = Str::slug(SiteSectionConst::AUTH_SECTION);
        $section = SiteSections::where("key", $slug)->first();

        if ($section != null) {
            $section_data = json_decode(json_encode($section->value), true);
        } else {
            $section_data = [];
        }

        $section_data['language']  = $this->contentValidate($request, $basic_field_name);
        $update_data['value']  = $section_data;
        $update_data['key']    = $slug;

        try {
            SiteSections::updateOrCreate(['key' => $slug], $update_data);
        } catch (Exception $e) {
            return back()->with(['error' => [__('Something went wrong! Please try again.')]]);
        }

        return back()->with(['success' => [__('Auth updated successfully!')]]);
    }

    /**
     * Method for show auth section page
     * @param string $slug
     * @return view
     */
    public function vendorAuthView($slug)
    {
        $page_title = __("Vendor Auth Section");
        $section_slug = Str::slug(SiteSectionConst::VENDOR_AUTH_SECTION);
        $data = SiteSections::getData($section_slug)->first();
        $languages = $this->languages;

        return view('admin.sections.setup-sections.vendor-auth-section', compact(
            'page_title',
            'data',
            'languages',
            'slug'
        ));
    }

    /**
     * Method for update auth section information
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function vendorAuthUpdate(Request $request, $slug)
    {
        $basic_field_name = ['login_heading' => "required|string|max:100",'register_heading' => "required|string|max:100",'forgot_heading' => "required|string|max:100", 'forgot_sub_heading' => "required|string|max:500",];

        $slug = Str::slug(SiteSectionConst::VENDOR_AUTH_SECTION);
        $section = SiteSections::where("key", $slug)->first();

        if ($section != null) {
            $section_data = json_decode(json_encode($section->value), true);
        } else {
            $section_data = [];
        }
        if ($request->hasFile("login_image")) {
            $section_data['login_image']      = $this->imageValidate($request, "login_image", $section->value->login_image ?? null);
        }

        $section_data['language']  = $this->contentValidate($request, $basic_field_name);
        $update_data['value']  = $section_data;
        $update_data['key']    = $slug;

        try {
            SiteSections::updateOrCreate(['key' => $slug], $update_data);
        } catch (Exception $e) {
            return back()->with(['error' => [__('Something went wrong! Please try again.')]]);
        }

        return back()->with(['success' => [__('Auth updated successfully!')]]);
    }


    /**
     * Method for get languages form record with little modification for using only this class
     * @return array $languages
     */
    public function languages()
    {
        $languages = Language::whereNot('code', LanguageConst::NOT_REMOVABLE)->select("code", "name")->get()->toArray();
        $languages[] = [
            'name'      => LanguageConst::NOT_REMOVABLE_CODE,
            'code'      => LanguageConst::NOT_REMOVABLE,
        ];
        return $languages;
    }

    /**
     * Method for validate request data and re-decorate language wise data
     * @param object $request
     * @param array $basic_field_name
     * @return array $language_wise_data
     */
    public function contentValidate($request, $basic_field_name, $modal = null)
    {
        $languages = $this->languages();

        $current_local = get_default_language_code();
        $validation_rules = [];
        $language_wise_data = [];
        foreach ($request->all() as $input_name => $input_value) {
            foreach ($languages as $language) {
                $input_name_check = explode("_", $input_name);
                $input_lang_code = array_shift($input_name_check);
                $input_name_check = implode("_", $input_name_check);
                if ($input_lang_code == $language['code']) {
                    if (array_key_exists($input_name_check, $basic_field_name)) {
                        $langCode = $language['code'];
                        if ($current_local == $langCode) {
                            $validation_rules[$input_name] = $basic_field_name[$input_name_check];
                        } else {
                            $validation_rules[$input_name] = str_replace("required", "nullable", $basic_field_name[$input_name_check]);
                        }
                        $language_wise_data[$langCode][$input_name_check] = $input_value;
                    }
                    break;
                }
            }
        }
        if ($modal == null) {
            $validated = Validator::make($request->all(), $validation_rules)->validate();
        } else {
            $validator = Validator::make($request->all(), $validation_rules);
            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput()->with("modal", $modal);
            }
            $validated = $validator->validate();
        }

        return $language_wise_data;
    }

    /**
     * Method for validate request image if have
     * @param object $request
     * @param string $input_name
     * @param string $old_image
     * @return boolean|string $upload
     */
    public function imageValidate($request, $input_name, $old_image)
    {
        if ($request->hasFile($input_name)) {
            $image_validated = Validator::make($request->only($input_name), [
                $input_name         => "image|mimes:png,jpg,webp,jpeg,svg",
            ])->validate();

            $image = get_files_from_fileholder($request, $input_name);
            $upload = upload_files_from_path_dynamic($image, 'site-section', $old_image);
            return $upload;
        }

        return false;
    }



    // *************************************************** end *********************************************//
}
