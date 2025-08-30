<section class="app-section ptb-80">
    <div class="container">
        <div class="app-section-title pb-40">
            <div class="row">
                <div class="col-lg-7">
                    <div class="app-title">
                        <h2 class="titl">{{ $app->value->language->$default->heading ?? '' }}</h2>
                    </div>
                    <p>{{ $app->value->language->$default->sub_heading ?? '' }}</p>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-5 pb-30">
                <div class="app-btn-wrapper">
                    <a href="{{ $app_link->android_url }}" class="app-btn">
                        <div class="app-icon">
                            <img src="{{ asset('frontend/images/icon/play-store.webp') }}" alt="icon">
                        </div>
                        <div class="content">
                            <span>{{ __('Get It On') }}</span>
                            <h5 class="title">{{ __('Google Play') }}</h5>
                        </div>
                        <div class="icon">
                            <img src="{{ asset('frontend/images/element/qr-icon.webp') }}" alt="element">
                        </div>
                        <div class="app-qr">
                            <img src="https://qrcode.tec-it.com/API/QRCode?data={{ $app_link->android_url }}" alt="element">
                        </div>
                    </a>
                    <a href="{{ $app_link->iso_url }}" class="app-btn">
                        <div class="app-icon">
                            <img src="{{ asset('frontend/images/icon/apple-store.webp') }}" alt="icon">
                        </div>
                        <div class="content">
                            <span>{{ __('Download On') }}</span>
                            <h5 class="title">{{ __('Apple Store') }}</h5>
                        </div>
                        <div class="icon">
                            <img src="{{ asset('frontend/images/element/qr-icon.webp') }}" alt="element">
                        </div>
                        <div class="app-qr">
                            <img src="https://qrcode.tec-it.com/API/QRCode?data={{ $app_link->iso_url }}" alt="element">
                        </div>
                    </a>
                </div>
            </div>
            <div class="col-lg-7">
                <div class="app-img">
                    <img src="{{ get_image($app->value->image, 'site-section') }}" alt="img">
                </div>
            </div>
        </div>
    </div>
</section>
