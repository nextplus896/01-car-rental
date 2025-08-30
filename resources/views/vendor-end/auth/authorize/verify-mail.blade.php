@extends('frontend.layouts.master')

@push('css')
@endpush

<?php
$default = get_default_language_code() ?? 'en';
$default_lng = 'en';
?>

@section('content')
<section class="verification-otp ptb-80">
    <div class="container">
        <div class="row justify-content-center">
            <div class=" col-xl-6 col-lg-8 col-md-10 col-sm-12">
                <div class="verification-otp-area">
                    <div class="account-wrapper otp-verification">
                        <div class="account-logo text-center">
                            <a href="{{ setRoute('frontend.index') }}" class="site-logo">
                                <img src="{{ get_logo_vendor($basic_settings) }}"
                                    data-white_img="{{ get_logo_vendor($basic_settings, 'dark') }}"
                                    data-dark_img="{{ get_logo_vendor($basic_settings) }}" alt="logo">
                            </a>
                        </div>
                        <div class="verification-otp-content">
                            <h4 class="title text-center">{{ __("Account Authorization") }}</h4>
                        <p class="d-block text-center">{{ __("Need to verify your account. Please check your mail inbox to get the authorization code") }}</p>
                        </div>
                        <form class="account-form pt-20" action="{{ setRoute('vendor.authorize.mail.verify',$token) }}" method="POST">
                            @csrf
                            <div class="row ml-b-20">
                                <div class="col-lg-12 form-group text-center">
                                    <input class="otp" type="text" name="code[]" oninput='digitValidate(this)' onkeyup='tabChange(1)'
                                        maxlength=1 required>
                                    <input class="otp" type="text" name="code[]" oninput='digitValidate(this)' onkeyup='tabChange(2)'
                                        maxlength=1 required>
                                    <input class="otp" type="text" name="code[]" oninput='digitValidate(this)' onkeyup='tabChange(3)'
                                        maxlength=1 required>
                                    <input class="otp" type="text" name="code[]" oninput='digitValidate(this)' onkeyup='tabChange(4)'
                                        maxlength=1 required>
                                    <input class="otp" type="text" name="code[]" oninput='digitValidate(this)' onkeyup='tabChange(5)'
                                        maxlength=1 required>
                                        <input class="otp" type="text" name="code[]" oninput='digitValidate(this)' onkeyup='tabChange(6)'
                                        maxlength=1 required>
                                </div>
                                <div class="col-lg-12 form-group text-center">
                                    <button type="submit" class="btn--base btn w-100">{{ __("Authorize") }}</button>
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
<script>
    let digitValidate = function (ele) {
        console.log(ele.value);
        ele.value = ele.value.replace(/[^0-9]/g, '');
    }

    let tabChange = function (val) {
        let ele = document.querySelectorAll('.otp');
        if (ele[val - 1].value != '') {
            ele[val].focus()
        } else if (ele[val - 1].value == '') {
            ele[val - 2].focus()
        }
    }
</script>

<script>
    $(".otp").parents("form").find("input[type=submit],button[type=submit]").click(function(e){
        // e.preventDefault();
        var otps = $(this).parents("form").find(".otp");
        var result = true;
        $.each(otps,function(index,item){
            if($(item).val() == "" || $(item).val() == null) {
                result = false;
            }
        });

        if(result == false) {
            $(this).parents("form").find(".otp").addClass("required");
        }else {
            $(this).parents("form").find(".otp").removeClass("required");
            $(this).parents("form").submit();
        }
    });
</script>
@endpush
