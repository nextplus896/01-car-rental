@extends('user.layouts.master')

@push('css')
@endpush

@section('breadcrumb')
    @include('user.components.breadcrumb', [
        'breadcrumbs' => [
            [
                'name' => __('Dashboard'),
                'url' => setRoute('user.dashboard'),
            ],
        ],
        'active' => __('Profile'),
    ])
@endsection

@section('content')
    <!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
                            Start Dashboard
                        ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->

    <div class="row mb-20-none">
        <div class="col-xl-6 col-lg-6 mb-20">
            <div class="custom-card mt-10">
                <div class="dashboard-header-wrapper">
                    <div class="header-title">
                        <h4 class="title">{{ __('Profile Settings') }}</h4>
                    </div>
                    <div class="account-delate">
                        <button class="btn--danger btn" data-bs-toggle="modal" data-bs-target="#delateModal">
                            {{ __('Delete Account') }}
                        </button>
                    </div>
                </div>
                <div class="card-body profile-body-wrapper">
                    <form class="card-form" method="POST" action="{{ setRoute('user.profile.update') }}"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="profile-settings-wrapper">
                            <div class="preview-thumb profile-wallpaper">
                                <div class="avatar-preview">
                                    <div class="profilePicPreview bg_img"
                                        data-background="{{ asset('frontend/images/element/profile-thumb.webp') }}">
                                    </div>
                                </div>
                            </div>
                            <div class="profile-thumb-content">
                                <div class="preview-thumb profile-thumb">
                                    <div class="avatar-preview">
                                        <div class="profilePicPreview bg_img"
                                            data-background="{{ auth()->user()->userImage }}"></div>
                                    </div>
                                    <div class="avatar-edit">
                                        <input type='file' class="profilePicUpload" name="image" id="profilePicUpload2"
                                            accept=".png, .jpg, .jpeg">
                                        <label for="profilePicUpload2"><i class="las la-upload"></i></label>
                                    </div>
                                </div>
                                <div class="profile-content">
                                    <h6 class="username text--base">{{ auth()->user()->username }}</h6>
                                    <ul class="user-info-list">
                                        <li><i class="las la-envelope"></i>{{ auth()->user()->email }}</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="profile-form-area">
                            <div class="row">
                                <div class="col-xl-6 col-lg-6 form-group">
                                    <label>{{ __('First Name') }}<span>*</span></label>
                                    <input type="text" name="firstname" class="form--control"
                                        placeholder="{{ __('Enter First Name') }}"
                                        value="{{ old('firstname', auth()->user()->firstname) }}">
                                </div>
                                <div class="col-xl-6 col-lg-6 form-group">
                                    <label>{{ __('Last Name') }}<span>*</span></label>
                                    <input type="text" name="lastname" class="form--control"
                                        placeholder="{{ __('Enter Last Name') }}"
                                        value="{{ old('lastname', auth()->user()->lastname) }}">
                                </div>
                                <div class="col-xl-6 col-lg-6 form-group">
                                    <label>{{ __('Country') }}</label>
                                    <select class="form--control select2-auto-tokenize country-select"
                                        data-placeholder="{{ __('Select Country') }}"
                                        data-old="{{ old('country', auth()->user()->address->country ?? '') }}"
                                        name="country"></select>
                                </div>
                                <div class="col-xl-6 col-lg-6 form-group">
                                    <label>{{ __('Phone') }} <span>*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-text phone-code">+{{ auth()->user()->mobile_code }}</div>
                                        <input class="phone-code" type="hidden" name="phone_code"
                                            value="{{ auth()->user()->mobile_code }}" />
                                        <input type="text" class="form--control" placeholder="{{ __('Enter Phone') }}"
                                            name="phone" value="{{ old('phone', auth()->user()->mobile) }}">
                                    </div>
                                </div>
                                <div class="col-xl-6 col-lg-6 form-group">
                                    <label>{{ __('Address') }}</label>
                                    <input type="text" name="address" class="form--control"
                                        placeholder="{{ __('Enter Address') }}"
                                        value="{{ old('address', auth()->user()->address->address ?? '') }}">
                                </div>
                                <div class="col-xl-6 col-lg-6 form-group">
                                    @php
                                        $old_city = old('city', auth()->user()->address->city ?? '');
                                    @endphp
                                    <label>{{ __('City') }}</label>
                                    <select name="city" class="form--control select2-auto-tokenize city-select"
                                        data-placeholder="{{ __('Select City') }}" data-old="{{ $old_city }}">
                                        @if ($old_city)
                                            <option value="{{ $old_city }}" selected>{{ $old_city }}</option>
                                        @endif
                                    </select>
                                </div>
                                <div class="col-xl-6 col-lg-6 form-group">
                                    @php
                                        $old_state = old('state', auth()->user()->address->state ?? '');
                                    @endphp
                                    <label>{{ __('State') }}</label>
                                    <select name="state" class="form--control select2-auto-tokenize state-select"
                                        data-placeholder="{{ __('Select State') }}" data-old="{{ $old_state }}">
                                        @if ($old_state)
                                            <option value="{{ $old_state }}" selected>{{ $old_state }}</option>
                                        @endif
                                    </select>
                                </div>
                                <div class="col-xl-6 col-lg-6 form-group">
                                    <label>{{ __('Zip Code') }}</label>
                                    <input type="text" name="zip_code" class="form--control"
                                        placeholder="{{ __('Enter Zip') }}"
                                        value="{{ old('zip_code', auth()->user()->address->zip ?? '') }}">
                                </div>
                            </div>
                            <div class="col-xl-12 col-lg-12">
                                <button type="submit" class="btn--base w-100">{{ __('Update') }}</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-xl-6 col-lg-6 mb-20">
            <div class="custom-card mt-10">
                <div class="dashboard-header-wrapper">
                    <h4 class="title">{{ __('Change Password') }}</h4>
                </div>
                <div class="card-body">
                    <form class="card-form" action="{{ setRoute('user.profile.password.update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-xl-12 col-lg-12 form-group show_hide_password">
                                <label>{{ __('Current Password') }}<span>*</span></label>
                                <input type="password" name="current_password" class="form--control"
                                    placeholder="{{ __('Enter Password') }}">
                                <a href="javascript:void(0)" class="show-pass field-icon"><i class="fa fa-eye-slash"
                                        aria-hidden="true"></i></a>
                            </div>

                            <div class="col-xl-12 col-lg-12 form-group show_hide_password-2">
                                <label>{{ __('New Password') }}<span>*</span></label>
                                <input type="password" name="password" class="form--control"
                                    placeholder="{{ __('Enter Password') }}">
                                <a href="javascript:void(0)" class="show-pass field-icon"><i class="fa fa-eye-slash"
                                        aria-hidden="true"></i></a>
                            </div>
                            <div class="col-xl-12 col-lg-12 form-group show_hide_password-3">
                                <label>{{ __('Confirm Password') }}<span>*</span></label>
                                <input type="password" name="password_confirmation" class="form--control"
                                    placeholder="{{ __('Enter Password') }}">
                                <a href="javascript:void(0)" class="show-pass field-icon"><i class="fa fa-eye-slash"
                                        aria-hidden="true"></i></a>
                            </div>
                        </div>
                        <div class="col-xl-12 col-lg-12">
                            <button type="submit" class="btn--base w-100">{{ __('Change') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
                            End Dashboard
        ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->

    <!-- Modal -->
    <div class="modal fade" id="delateModal" tabindex="-1" aria-labelledby="delateModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body">
                    <h4 class="title">{{ __('Are you sure to delete your account?') }}</h4>
                    <p>{{ __('If you do not think you will use') }} <span
                            class="text--base">{{ $basic_settings->site_name }}</span>
                        {{ __('again and like your account
                                                deleted. Keep in mind you will not be able to reactivate your account or retrieve any of the content
                                                or information you have added. If you would still like your account deleted, click “Delete
                                                Account”') }}?
                    </p>

                </div>
                <div class="modal-footer justify-content-between border-0">
                    <button type="button" class="bg-danger" data-bs-dismiss="modal">{{ __('Close') }}</button>
                    <form action="{{ setRoute('user.profile.delete', auth()->user()->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn--base">{{ __('Confirm') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        getAllCountries("{{ setRoute('global.countries') }}");
        $(document).ready(function() {
            $("select[name=country]").change(function() {
                var phoneCode = $("select[name=country] :selected").attr("data-mobile-code");
                placePhoneCode(phoneCode);
            });
            countrySelect(".country-select", $(".country-select").siblings(".select2"));
            stateSelect(".state-select", $(".state-select").siblings(".select2"));

        });
    </script>
@endpush
