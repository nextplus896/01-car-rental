    <section class="vendorlanding-section bg-overlay-vendor bg_img"
        data-background="{{ get_image($vendor_banner->value->image, 'site-section') }}">
        <div class="container">
            <div class="banner-content">
                <div class="row justify-content-center">
                    <div class="col-xl-7 col-lg-10 col-md-12">
                        <div class="banner-title">
                            <h1 class="title">
                                {{ $vendor_banner->value->language->$default->heading ?? '' }}
                            </h1>
                        </div>
                        <div class="title-paragraph">
                            <p>{{ $vendor_banner->value->language->$default->sub_heading ?? '' }}
                            </p>
                        </div>
                        @if ($basic_setting->vendor_registration)
                            <div class="banner-btn">
                                <a href="{{ url($vendor_banner->value->button_link_one) }}" class="btn--base">
                                    {{ $vendor_banner->value->language->$default->button_name_one ?? '' }}
                                    <i class="las {{ $vendor_banner->value->button_one_icon ?? '' }}"></i>
                                </a>
                            </div>
                        @endif
                        <div class="login-loction">
                            <span>{{ __('Already have an account?') }}
                                <a href="{{ url($vendor_banner->value->button_link_two) }}" class="text--base">
                                    {{ $vendor_banner->value->language->$default->button_name_two ?? '' }}
                                </a>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
        Why Choice CarBo
    ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
    <section class="why-choice-vendor">
        <div class="container">
            <div class="why-choice-vendor-wrapper">
                <div class="row justify-content-center mb-20-none">
                    @forelse ($vendor_banner->value->items ?? [] as $value)
                        <div class="col-xl-4 col-lg-6 col-md-6 mb-20">
                            <div class="driver-choice-card">
                                <div class="icon">
                                    <i class="las {{ $value->icon ?? '' }}"></i>
                                </div>
                                <div class="card-content">
                                    <h3 class="title">
                                        {{ $value->language->$default->title ?? '' }}
                                    </h3>
                                    <p>{{ $value->language->$default->description ?? '' }}</p>
                                </div>
                            </div>
                        </div>
                    @empty
                    @endforelse
                </div>
            </div>
        </div>
    </section>
