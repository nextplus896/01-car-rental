<!-- fontawesome css link -->
<link rel="stylesheet" href="{{ asset('frontend/css/fontawesome-all.css') }}">
<!-- bootstrap css link -->
<link rel="stylesheet" href="{{ asset('frontend/css/bootstrap.css') }}">
<!-- favicon -->
<link rel="shortcut icon" href="{{ get_fav_vendor($basic_settings) }}" type="image/x-icon">
<!-- swipper css link -->
<link rel="stylesheet" href="{{ asset('frontend/css/swiper.css ') }}">
<!-- lightcase css links -->
<link rel="stylesheet" href="{{ asset('frontend/css/lightcase.css') }}">
 <!-- AOS css link -->
 <link rel="stylesheet" href="{{ asset('frontend/css/aos.css') }}">
<!-- odometer css link -->
<link rel="stylesheet" href="{{ asset('frontend/css/odometer.css') }}">
<!-- animate.css -->
<link rel="stylesheet" href="{{ asset('frontend/css/animate.css') }}">
<!-- line-awesome-icon css -->
<link rel="stylesheet" href="{{ asset('frontend/css/line-awesome.css') }}">
<!-- nice-select -->
<link rel="stylesheet" href="{{ asset('frontend/css/nice-select.css') }}">
<!-- select2 css -->
<link rel="stylesheet" href="{{ asset('frontend/css/select2.css') }}">
<!-- main style css link -->
<link rel="stylesheet" href="{{ asset('frontend/css/style.css') }}">
<!-- Popup  -->
<link rel="stylesheet" href="{{ asset('backend/library/popup/magnific-popup.css') }}">
<!-- file holder css -->
{{-- <link rel="stylesheet" href="https://rokon.appdevs.net/fileholder-laravel/public/fileholder/css/fileholder-style.css" type="text/css"> --}}
<!-- Fileholder CSS CDN -->
<link rel="stylesheet" href="https://appdevs.cloud/cdn/fileholder/v1.0/css/fileholder-style.css" type="text/css">


<style>
    :root {
        --primary-color: {{ $basic_settings->vendor_base_color }};
        --secondary-color: {{ $basic_settings->vendor_secondary_color }};
    }
</style>
