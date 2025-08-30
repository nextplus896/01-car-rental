@php
    $lang = selectedLang();
@endphp
<!DOCTYPE html>
<html lang="{{ get_default_language_code() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ (isset($page_title) ? __($page_title) : __("Public")) }}</title>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,700;0,9..40,800;0,9..40,900;0,9..40,1000;1,9..40,400;1,9..40,500;1,9..40,600;1,9..40,700;1,9..40,800;1,9..40,900&family=Josefin+Sans:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;1,100;1,200;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet">
    @include('partials.header-asset')
    @stack('css')
</head>
<body class="{{ get_default_language_dir() }}">
    @include('frontend.partials.preloader')
    @include('frontend.partials.body-overlay')

<section class="verification-otp ptb-80">
    <div class="container">
        <div class="row justify-content-center">
            <div class=" col-xl-6 col-lg-8 col-md-10 col-sm-12">
                <div class="verification-otp-area">
                    <div class="account-wrapper otp-verification">
                        <div class="account-logo text-center">
                            <a href="{{setRoute('frontend.index')}}" class="site-logo">
                                <img src="{{ get_logo($basic_settings)}}" alt="logo">
                            </a>
                        </div>
                        <div class="verification-otp-content">
                            <h4 class="title text-center">{{ __("Booking Verification") }}</h4>
                        <p class="d-block text-center">{{ __("Need to verify your account. Please check your mail inbox to get the authorization code") }}</p>
                        </div>
                        <form class="account-form pt-20" action="{{ setRoute('frontend.car.booking.mail.verify',$token) }}" method="POST">
                            @csrf
                            <div class="row ml-b-20">
                                <div class="col-lg-12 form-group text-center">
                                    <input class="otp" type="text" name="code[]"  oninput='digitValidate(this)' onkeyup='tabChange(1)'
                                        maxlength=1 required>
                                    <input class="otp" type="text" name="code[]"  oninput='digitValidate(this)' onkeyup='tabChange(2)'
                                        maxlength=1 required>
                                    <input class="otp" type="text" name="code[]"  oninput='digitValidate(this)' onkeyup='tabChange(3)'
                                        maxlength=1 required>
                                    <input class="otp" type="text" name="code[]"  oninput='digitValidate(this)' onkeyup='tabChange(4)'
                                        maxlength=1 required>
                                    <input class="otp" type="text" name="code[]"  oninput='digitValidate(this)' onkeyup='tabChange(5)'
                                        maxlength=1 required>
                                        <input class="otp" type="text" name="code[]"  oninput='digitValidate(this)' onkeyup='tabChange(6)'
                                        maxlength=1 required>
                                </div>
                                <div class="col-lg-12 form-group ">
                                    <div class="time-area">{{ __("You can resend the code after") }} <span id="time"></span></div>
                                </div>
                                <div class="col-lg-12 form-group text-center">
                                    <button type="submit" class="btn--base w-100">{{ __("Submit") }}</button>
                                </div>
                                <div class="col-lg-12 text-center">
                                    <div class="account-item">
                                        <label>{{ __("Back to") }} <a href="{{ setRoute('frontend.index') }}" class="text--base" data-block="login">{{__("Home")}}</a></label>
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


@include('partials.footer-asset')
@include('admin.partials.notify')

    <script>
        function scrollToTop() {
      window.scrollTo({
        top: 0,
        behavior: 'smooth'
      });
    }
    </script>

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
         var convertAsSecond = "{{ global_const()::USER_PASS_RESEND_TIME_MINUTE }}" * 60;
            function resetTime (second = convertAsSecond) {
                var coundDownSec = second;
                var countDownDate = new Date();
                countDownDate.setMinutes(countDownDate.getMinutes() + 120);
                var x = setInterval(function () {
                    var now = new Date().getTime();
                    var distance = countDownDate - now;
                    var minutes = Math.floor((distance % (1000 * coundDownSec)) / (1000 * coundDownSec));
                    var seconds = Math.floor((distance % (1000 * coundDownSec)) / 1000);
                    var resendCodeLink = "{{ setRoute('frontend.car.booking.mail.resend',$token) }}";
                    document.getElementById("time").innerHTML =seconds + "s ";

                    if (distance < 0 || second < 2 ) {

                        clearInterval(x);

                        document.querySelector(".time-area").innerHTML = `Didn't get the code? <a class='text--danger' href='${resendCodeLink}'>Resend</a>`;
                    }

                    second--
                }, 1000);
            }

    resetTime();
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

</body>
</html>

