@extends('frontend.layouts.master')

@push('css')
@endpush

<?php
$default = get_default_language_code() ?? 'en';
$default_lng = 'en';
?>

@section('content')
    <section class="forgot-password  ptb-80">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xl-6 col-lg-7 col-md-10">
                    <div class="forgot-password-area">
                        <div class="account-wrapper">
                            <div class="account-logo text-center">
                                <a href="{{ setRoute('frontend.index') }}" class="site-logo">
                                    <img src="{{ get_logo_vendor($basic_settings) }}"
                                        data-white_img="{{ get_logo_vendor($basic_settings, 'dark') }}"
                                        data-dark_img="{{ get_logo_vendor($basic_settings) }}" alt="logo">
                                </a>
                            </div>
                            <div class="forgot-password-content">
                                <h3 class="title">{{ $auth->value->language->$default->forgot_heading ?? $auth->value->language->$default_lng->forgot_heading }}</h3>
                                <p>{{ $auth->value->language->$default->forgot_sub_heading ?? $auth->value->language->$default_lng->forgot_sub_heading }}</p>
                            </div>
                            <form class="account-form pt-30" action="{{ setRoute('vendor.password.forgot.send.code') }}" method="POST">
                                @csrf
                                <div class="row ml-b-20">
                                    <div class="col-lg-12 form-group pb-20">
                                        <input type="text" required class="form-control form--control" name="credentials"
                                            placeholder="{{ __('Email') }}" spellcheck="false" data-ms-editor="true">
                                    </div>
                                    <div class="col-lg-12 form-group text-center">
                                        <button type="submit" class="btn--base btn w-100">{{ __("Send OTP") }}</button>
                                    </div>
                                    <div class="col-lg-12 text-center">
                                        <div class="account-item">
                                            <label>
                                                {{ __('Already Have An Account?') }}
                                                <a href="{{ setRoute('vendor.register') }}" class="text--base">{{ __('Login Now') }}</a>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('script')
@endpush
