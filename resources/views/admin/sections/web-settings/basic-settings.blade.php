@extends('admin.layouts.master')

@push('css')

@endpush

@section('page-title')
    @include('admin.components.page-title',['title' => __($page_title)])
@endsection

@section('breadcrumb')
    @include('admin.components.breadcrumb',['breadcrumbs' => [
        [
            'name'  => __("Dashboard"),
            'url'   => setRoute("admin.dashboard"),
        ]
    ], 'active' => __("Web Settings")])
@endsection

@section('content')
    <div class="custom-card">
        <div class="card-header">
            <h6 class="title">{{ __("Basic Settings") }}</h6>
        </div>
        <div class="card-body">
            <form class="card-form" method="POST" action="{{ setRoute('admin.web.settings.basic.settings.update') }}">
                @csrf
                @method("PUT")
                <div class="row">
                    <div class="col-xl-3 col-lg-3 form-group">
                        <label>{{ __("Site Base Color") }}*</label>
                        <div class="picker">
                            <input type="color" value="{{ old('base_color',$basic_settings->base_color) }}" class="color color-picker">
                            <input type="text" autocomplete="off" spellcheck="false" class="color-input" value="{{ old('base_color',$basic_settings->base_color) }}" name="base_color">
                        </div>
                    </div>
                    <div class="col-xl-3 col-lg-3 form-group">
                        <label>{{ __("Site Secondary Color") }}*</label>
                        <div class="picker">
                            <input type="color" value="{{ old('secondary_color',$basic_settings->secondary_color) }}" class="color color-picker">
                            <input type="text" autocomplete="off" spellcheck="false" class="color-input" value="{{ old('secondary_color',$basic_settings->secondary_color) }}" name="secondary_color">
                        </div>
                    </div>

                    <div class="col-xl-3 col-lg-3 form-group">
                        <label>{{ __("Timezone") }}*</label>
                        <select name="timezone" class="form--control select2-auto-tokenize timezone-select" data-old="{{ old('timezone',$basic_settings->timezone) }}">
                            <option selected disabled>{{ __("Select Timezone") }}</option>
                        </select>
                    </div>

                    <div class="col-xl-3 col-lg-3 form-group">
                        @include('admin.components.form.input',[
                            'label'         => __("Site Name"),
                            'label_after'   => "*",
                            'class'         => "form--control",
                            'placeholder'   => __("Write Here").'...',
                            'name'          => "site_name",
                            'value'         => old('site_name',$basic_settings->site_name),
                        ])
                    </div>
                    <div class="col-xl-3 col-lg-3 form-group">
                        @include('admin.components.form.input',[
                            'label'         => __("Site Title"),
                            'label_after'   => "*",
                            'type'          => "text",
                            'class'         => "form--control",
                            'placeholder'   => __("Write Here").'...',
                            'name'          => "site_title",
                            'value'         => old('site_title',$basic_settings->site_title),
                        ])
                    </div>
                    <div class="col-xl-3 col-lg-3 form-group">
                        <label>{{ __("OTP Expiration") }}*</label>
                        <div class="input-group">
                            <input type="number" class="form--control" value="{{ old('otp_exp_seconds',$basic_settings->otp_exp_seconds) }}" name="otp_exp_seconds">
                            <span class="input-group-text">{{ __("Seconds") }}</span>
                        </div>
                    </div>
                    <div class="col-xl-3 col-lg-3 form-group">
                        @include('admin.components.form.input',[
                            'label'         => __("Web Version"),
                            'type'          => "text",
                            'class'         => "form--control",
                            'placeholder'   => __("Write Here").'...',
                            'name'          => "web_version",
                            'value'         => old('web_version',$basic_settings->web_version),
                        ])
                    </div>
                    <div class="col-xl-3 col-lg-3 form-group">

                        @php
                            $old_precision = old('precision',$basic_settings->precision);
                        @endphp

                        <label>{{ __("Precision") }}</label>
                        <select name="precision" class="form--control select2-auto-tokenize">
                            <option selected disabled>{{ __("Select Amount Precision") }}</option>
                            <option value="0" @if ($old_precision == 0) selected @endif>{{ __("Integer") }}</option>
                            <option value="1" @if ($old_precision == 1) selected @endif>{{ __("1 decimal") }}</option>
                            <option value="2" @if ($old_precision == 2) selected @endif>{{ __("2 decimals") }}</option>
                            <option value="3" @if ($old_precision == 3) selected @endif>{{ __("3 decimals") }}</option>
                            <option value="4" @if ($old_precision == 4) selected @endif>{{ __("4 decimals") }}</option>
                            <option value="5" @if ($old_precision == 5) selected @endif>{{ __("5 decimals") }}</option>
                            <option value="6" @if ($old_precision == 6) selected @endif>{{ __("6 decimals") }}</option>
                            <option value="7" @if ($old_precision == 7) selected @endif>{{ __("7 decimals") }}</option>
                            <option value="8" @if ($old_precision == 8) selected @endif>{{ __("8 decimals") }}</option>
                            <option value="9" @if ($old_precision == 9) selected @endif>{{ __("9 decimals") }}</option>
                            <option value="10" @if ($old_precision == 10) selected @endif>{{ __("10 decimals") }}</option>
                            <option value="11" @if ($old_precision == 11) selected @endif>{{ __("11 decimals") }}</option>
                            <option value="12" @if ($old_precision == 12) selected @endif>{{ __("12 decimals") }}</option>
                            <option value="13" @if ($old_precision == 13) selected @endif>{{ __("13 decimals") }}</option>
                            <option value="14" @if ($old_precision == 14) selected @endif>{{ __("14 decimals") }}</option>
                            <option value="15" @if ($old_precision == 15) selected @endif>{{ __("15 decimals") }}</option>
                        </select>
                    </div>
                </div>
                <div class="col-xl-12 col-lg-12">
                    @include('admin.components.button.form-btn',[
                        'class'         => "w-100 btn-loading",
                        'text'          => __("Update"),
                        'permission'    => "admin.web.settings.basic.settings.update",
                    ])
                </div>
            </form>
        </div>
    </div>
    <div class="custom-card">
        <div class="card-header">
            <h6 class="title">{{ __("Basic Settings") }} ({{ __('Vendor') }})</h6>
        </div>
        <div class="card-body">
            <form class="card-form" method="POST" action="{{ setRoute('admin.web.settings.vendor.basic.settings.update') }}">
                @csrf
                @method("PUT")
                <div class="row">
                    <div class="col-xl-6 col-lg-6 form-group">
                        <label>{{ __("Site Base Color") }}*</label>
                        <div class="picker">
                            <input type="color" value="{{ old('base_color',$basic_settings->vendor_base_color) }}" class="color color-picker">
                            <input type="text" autocomplete="off" spellcheck="false" class="color-input" value="{{ old('base_color',$basic_settings->vendor_base_color) }}" name="vendor_base_color">
                        </div>
                    </div>
                    <div class="col-xl-6 col-lg-6 form-group">
                        <label>{{ __("Site Secondary Color") }}*</label>
                        <div class="picker">
                            <input type="color" value="{{ old('secondary_color',$basic_settings->vendor_secondary_color) }}" class="color color-picker">
                            <input type="text" autocomplete="off" spellcheck="false" class="color-input" value="{{ old('secondary_color',$basic_settings->vendor_secondary_color) }}" name="vendor_secondary_color">
                        </div>
                    </div>
                </div>
                <div class="col-xl-12 col-lg-12">
                    @include('admin.components.button.form-btn',[
                        'class'         => "w-100 btn-loading",
                        'text'          => __("Update"),
                        'permission'    => "admin.web.settings.basic.settings.update",
                    ])
                </div>
            </form>
        </div>
    </div>
    <div class="custom-card mt-15">
        <div class="card-header">
            <h6 class="title">{{ __("Activation Settings") }}</h6>
        </div>
        <div class="card-body">
            <div class="custom-inner-card mt-10 mb-10">
                <div class="card-inner-body">
                    <div class="row mb-10-none">
                        <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6 form-group">
                            @include('admin.components.form.switcher',[
                                'label'         => __('User Registration'),
                                'name'          => 'user_registration',
                                'value'         => old('user_registration',$basic_settings->user_registration),
                                'options'       =>  [__('Activated') => 1,__('Deactivated') => 0],
                                'onload'        => true,
                                'permission'    => "admin.web.settings.basic.settings.activation.update",
                            ])
                        </div>
                        <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6 form-group">
                            @include('admin.components.form.switcher',[
                                'label'         => __('Secure Password'),
                                'name'          => 'secure_password',
                                'value'         => old('secure_password',$basic_settings->secure_password),
                                'options'       =>[__('Activated') => 1,__('Deactivated') => 0],
                                'onload'        => true,
                                'permission'    => "admin.web.settings.basic.settings.activation.update",
                            ])
                        </div>
                        <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6 form-group">
                            @include('admin.components.form.switcher',[
                                'label'         => __('Agree Policy'),
                                'name'          => 'agree_policy',
                                'value'         => old('agree_policy',$basic_settings->agree_policy),
                                'options'       => [__('Activated') => 1,__('Deactivated') => 0],
                                'onload'        => true,
                                'permission'    => "admin.web.settings.basic.settings.activation.update",
                            ])
                        </div>
                        <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6 form-group">
                            @include('admin.components.form.switcher',[
                                'label'         => __('Force SSL'),
                                'name'          => 'force_ssl',
                                'value'         => old('force_ssl',$basic_settings->force_ssl),
                                'options'       => [__('Activated') => 1,__('Deactivated') => 0],
                                'onload'        => true,
                                'permission'    => "admin.web.settings.basic.settings.activation.update",
                            ])
                        </div>
                        <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6 form-group">
                            @include('admin.components.form.switcher',[
                                'label'         =>  __('Email Verification'),
                                'name'          => 'email_verification',
                                'value'         => old('email_verification',$basic_settings->email_verification),
                                'options'       =>[__('Activated') => 1,__('Deactivated') => 0],
                                'onload'        => true,
                                'permission'    => "admin.web.settings.basic.settings.activation.update",
                            ])
                        </div>
                        <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6 form-group">
                            @include('admin.components.form.switcher',[
                                'label'         =>__('Email Notification'),
                                'name'          => 'email_notification',
                                'value'         => old('email_notification',$basic_settings->email_notification),
                                'options'       => [__('Activated') => 1,__('Deactivated') => 0],
                                'onload'        => true,
                                'permission'    => "admin.web.settings.basic.settings.activation.update",
                            ])
                        </div>
                        <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6 form-group">
                            @include('admin.components.form.switcher',[
                                'label'         => __('Push Notification'),
                                'name'          => 'push_notification',
                                'value'         => old('push_notification',$basic_settings->push_notification),
                                'options'       =>[__('Activated') => 1,__('Deactivated') => 0],
                                'onload'        => true,
                                'permission'    => "admin.web.settings.basic.settings.activation.update",
                            ])
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="custom-card mt-15">
        <div class="card-header">
            <h6 class="title">{{ __("Activation Settings") }} ({{ __('Vendor') }})</h6>
        </div>
        <div class="card-body">
            <div class="custom-inner-card mt-10 mb-10">
                <div class="card-inner-body">
                    <div class="row mb-10-none">
                        <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6 form-group">
                            @include('admin.components.form.switcher',[
                                'label'         => __('Vendor Registration'),
                                'name'          => 'vendor_registration',
                                'value'         => old('vendor_registration',$basic_settings->vendor_registration),
                                'options'       =>  [__('Activated') => 1,__('Deactivated') => 0],
                                'onload'        => true,
                                'permission'    => "admin.web.settings.basic.settings.activation.update",
                            ])
                        </div>
                        <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6 form-group">
                            @include('admin.components.form.switcher',[
                                'label'         => __('Vendor Secure Password'),
                                'name'          => 'vendor_secure_password',
                                'value'         => old('vendor_secure_password',$basic_settings->vendor_secure_password),
                                'options'       =>[__('Activated') => 1,__('Deactivated') => 0],
                                'onload'        => true,
                                'permission'    => "admin.web.settings.basic.settings.activation.update",
                            ])
                        </div>
                        <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6 form-group">
                            @include('admin.components.form.switcher',[
                                'label'         => __('Vendor Agree Policy'),
                                'name'          => 'vendor_agree_policy',
                                'value'         => old('vendor_agree_policy',$basic_settings->vendor_agree_policy),
                                'options'       => [__('Activated') => 1,__('Deactivated') => 0],
                                'onload'        => true,
                                'permission'    => "admin.web.settings.basic.settings.activation.update",
                            ])
                        </div>
                        <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6 form-group">
                            @include('admin.components.form.switcher',[
                                'label'         =>  __('Vendor Email Verification'),
                                'name'          => 'vendor_email_verification',
                                'value'         => old('vendor_email_verification',$basic_settings->vendor_email_verification),
                                'options'       =>[__('Activated') => 1,__('Deactivated') => 0],
                                'onload'        => true,
                                'permission'    => "admin.web.settings.basic.settings.activation.update",
                            ])
                        </div>
                        <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6 form-group">
                            @include('admin.components.form.switcher',[
                                'label'         =>__('Vendor Email Notification'),
                                'name'          => 'vendor_email_notification',
                                'value'         => old('vendor_email_notification',$basic_settings->vendor_email_notification),
                                'options'       => [__('Activated') => 1,__('Deactivated') => 0],
                                'onload'        => true,
                                'permission'    => "admin.web.settings.basic.settings.activation.update",
                            ])
                        </div>
                        <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6 form-group">
                            @include('admin.components.form.switcher',[
                                'label'         => __('Vendor Push Notification'),
                                'name'          => 'vendor_push_notification',
                                'value'         => old('vendor_push_notification',$basic_settings->vendor_push_notification),
                                'options'       =>[__('Activated') => 1,__('Deactivated') => 0],
                                'onload'        => true,
                                'permission'    => "admin.web.settings.basic.settings.activation.update",
                            ])
                        </div>
                        <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6 form-group">
                            @include('admin.components.form.switcher',[
                                'label'         => __('Vendor KYC Verification'),
                                'name'          => 'vendor_kyc_verification',
                                'value'         => old('vendor_kyc_verification',$basic_settings->vendor_kyc_verification),
                                'options'       => [__('Activated') => 1,__('Deactivated') => 0],
                                'onload'        => true,
                                'permission'    => "admin.web.settings.basic.settings.activation.update",
                            ])
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        $(document).ready(function() {
            $(".color-picker").on("input",function() {
                $(this).siblings("input").val($(this).val());
            });

            // Get Timezone
            getTimeZones("{{ setRoute('global.timezones') }}");

            switcherAjax("{{ setRoute('admin.web.settings.basic.settings.activation.update') }}");
        });
    </script>
@endpush
