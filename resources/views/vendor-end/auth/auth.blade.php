@extends('frontend.layouts.master')

@push('css')
@endpush
<?php
$default = get_default_language_code() ?? 'en';
$default_lng = 'en';
?>

@section('content')
    <!-- User Account -->
    <section class="register-section vendor-account bg-overlay-vendor bg_img"
        data-background="{{ get_image($auth->value->login_image ?? '', 'site-section') ?? '' }}">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xl-6 col-lg-7 col-md-9">
                    <div class="register-form">
                        <div class="login-form">
                            <div class="login-header-top">
                                <h3 class="title">
                                    {{ $auth->value->language->$default->login_heading ?? $auth->value->language->$default_lng->login_heading }}
                                </h3>
                            </div>
                            <div class="register-header-top d-none">
                                <h3 class="title">
                                    {{ $auth->value->language->$default->register_heading ?? $auth->value->language->$default_lng->register_heading }}
                                </h3>
                            </div>
                            <div class="form-hader d-flex justify-content-between">
                                <div class="login-button">
                                    <button class="btn login-btn active">{{ __('Login') }}</button>
                                </div>
                                @if ($basic_setting->vendor_registration)
                                    <div class="register-button">
                                        <button class="btn register-btn">{{ __('Registration') }}</button>
                                    </div>
                                @endif
                            </div>
                            @include('vendor-end.auth.login')
                            @include('vendor-end.auth.register')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
