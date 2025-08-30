<div class="about-section pt-80">
    <div class="container">
        <div class="row mb-30-none align-items-center">
            <div class="col-xl-6 col-lg-12 mb-30">
                <div class="about-img">
                    <img src="{{ get_image($about->value->image, 'site-section') }}" alt="img">
                </div>
            </div>
            <div class="col-xl-6 col-lg-12 mb-30">
                <div class="about-content-area">
                    <div class="section-title pb-20">
                        <h4 class="sub-title text--base">
                            {{ $about->value->language->$default->section_title ?? '' }}
                        </h4>
                    </div>
                    <div class="about-title pb-20">
                        <h2 class="title">{{ $about->value->language->$default->heading ?? '' }}</h2>
                    </div>
                    <div class="about-paragraph">
                        <p>{{ $about->value->language->$default->sub_heading ?? '' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
