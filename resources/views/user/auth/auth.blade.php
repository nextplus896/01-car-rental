@extends('frontend.layouts.master')

@push('css')
@endpush
<?php
$default = get_default_language_code() ?? 'en';
$default_lng = 'en';
?>

@section('content')
    <!-- User Account -->
    <section class="register-section ptb-80">
        <div class="container">
            <div class="row mb-40-none align-items-center justify-content-center">
                <div class="col-lg-6 col-md-12 mb-40">
                    <div class="register-content">
                        <h2 class="title">
                            {{ $auth->value->language->$default->login_heading ?? $auth->value->language->$default_lng->login_heading }}
                        </h2>
                        <p>{{ $auth->value->language->$default->login_sub_heading ?? $auth->value->language->$default_lng->login_sub_heading }}
                        </p>
                    </div>
                </div>
                <div class="col-lg-6 col-md-9 mb-40">
                    <div class="register-form">
                        <div class="login-form">
                            <div class="form-hader d-flex justify-content-between">
                                <div class="login-button">
                                    <button class="btn login-btn active">{{ __('Login') }}</button>
                                </div>
                                @if ($basic_setting->user_registration)
                                    <div class="register-button">
                                        <button class="btn register-btn">{{ __('Registration') }}</button>
                                    </div>
                                @endif
                            </div>
                            @include('user.auth.login')
                            @include('user.auth.register')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
