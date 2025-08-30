<section class="banner-section  bg_img" data-background="{{ asset('frontend/images/banner/banner-bg.webp') }}">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lx-6 col-lg-6">
                <div class="banner-content">
                    <h1 class="title">
                        <span class="text--base">{{ $site_name }} - </span>
                        {{ $banner->value->language->$default->heading ?? ''  }}
                    </h1>
                    <p>{{ $banner->value->language->$default->sub_heading ?? '' }}</p>
                    <div class="banner-btn mb-10-none">
                        <a href="{{ url($banner->value->button_link_one) }}" class="btn--base mb-10">{{ $banner->value->language->$default->button_name_one ?? "" }}</a>
                        <a href="{{ url($banner->value->button_link_two) }}" class="btn--base mb-10">{{ $banner->value->language->$default->button_name_two ?? "" }}</a>
                    </div>
                </div>
            </div>
            <div class="col-xl-6 col-lg-6">
                <div class="banner-img">
                    <img src="{{ asset('frontend/images/banner/banner-img.webp') }}" alt="img">
                    <div class="banner-inner-img">
                        <img id="myImg" alt="img">
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
