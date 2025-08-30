<form id="login" action="{{ setRoute('user.login.submit') }}" method="POST">
    @csrf
    <div class="login-information pt-30">
        <div class="row mb-10-none">
            <div class="col-lg-12 form-group mb-10">
                <label>{{ __('Enter Email') }}</label>
                <input type="email" class="form--control" name="credentials" placeholder="{{ __('Enter Email') }}">
            </div>
            <div class="col-lg-12 form-group show_hide_password mb-10">
                <label>{{ __('Enter Password') }}</label>
                <input type="password" class="form-control form--control" name="password"
                    placeholder="{{ __('Enter Password') }}">
                <a href="#0" class="show-pass"><i class="fa fa-eye-slash" aria-hidden="true"></i></a>
            </div>
            <div class="col-lg-12 form-group">
                <div class="forgot-item text-end">
                    <label><a href="{{ setRoute('user.password.forgot') }}" class="text--base">{{ __("Forgot Password?") }}</a></label>
                </div>
            </div>
            <div class="col-lg-12 form-group text-center">
                <button type="submit" class="btn--base w-100">{{ __('Login Now') }}</button>
            </div>
        </div>
    </div>
</form>
