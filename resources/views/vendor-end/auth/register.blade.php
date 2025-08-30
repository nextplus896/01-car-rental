<form id="register" class="d-none" action="{{ setRoute('vendor.register.submit') }}" method="POST">
    @csrf
    <div class="personal-account pt-30 select-account" data-select-target="1">
        <div class="row">
            <div class="col-lg-6 col-md-6 form-group">
                <label>{{ __('First Name') }}</label>
                <input type="text" class="form-control form--control" name="firstname"
                    placeholder="{{ __('First Name') }}">
            </div>
            <div class="col-lg-6 col-md-6 form-group">
                <label>{{ __('Last Name') }}</label>
                <input type="text" class="form-control form--control" name="lastname"
                    placeholder="{{ __('Last Name') }}">
            </div>
            <div class="col-lg-6 form-group">
                <label>{{ __('Email Address') }}</label>
                <input type="email" class="form-control form--control" name="email"
                    placeholder="{{ __('Email') }}">
            </div>
            <div class="col-lg-6 form-group show_hide_password-2">
                <label>{{ __('Password') }}</label>
                <input type="password" class="form-control form--control" name="password"
                    placeholder="{{ __('Password') }}">
                <a href="#0" class="show-pass-2"><i class="fa fa-eye-slash" aria-hidden="true"></i></a>
            </div>
            <div class="col-lg-12 form-group show_hide_password-2">
                <label>{{ __('Country') }}</label>
                <select class=" select2-auto-tokenize country-select" name="country" id="">
                    @foreach (get_all_countries() ?? [] as $item)
                        <option value="{{ $item->name }}">{{ $item->name }}</option>
                    @endforeach
                </select>
            </div>
            @if ($basic_settings->vendor_agree_policy)
                <div class="col-lg-12 form-group">
                    <div class="custom-check-group">
                        <input type="checkbox" name="agree" id="level-1">
                        <label for="level-1">{{ __('I have agreed with') }} <a href="#0" class="text--base">
                                {{ __('Terms Of Use') }} </a>{{ __(' & ') }}<a href="#0" class="text--base">
                                {{ __('Privacy Policy') }}</a></label>
                    </div>
                </div>
            @endif
            <div class="col-lg-12 form-group text-center">
                <button type="submit" class="btn--base w-100">{{ __('Register Now') }}</button>
            </div>
        </div>
    </div>
</form>
