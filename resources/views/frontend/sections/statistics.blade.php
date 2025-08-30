<section class="statistics-section ptb-80">
    <div class="container">
        <div class="section-header-title">
            <h4 class="title text--base pb-20">
                {{ $stat->value->language->$default->section_title ?? '' }}
            </h4>
            <div class="row">
                <div class="col-xl-10 col-lg-12">
                    <h2 class="title-head">
                        {{ $stat->value->language->$default->heading ?? '' }}
                    </h2>
                    <p>
                        {{ $stat->value->language->$default->sub_heading ?? '' }}
                    </p>
                </div>
            </div>
        </div>
        <div class="row align-items-center pt-40">
            <div class="col-xl-7 col-lg-12 mb-20">
                <div class="counter-img" data-aos="fade-right" data-aos-duration="1200">
                    <img src="{{ get_image($stat->value->image, 'site-section') }}" alt="car">
                </div>
            </div>
            <div class="col-xl-5 col-lg-12 mb-20">
                <div class="row text-center">
                    @forelse ($stat->value->items as $value)
                        <div class="col-xl-12 col-lg-4 col-md-6 pb-20">
                            <div class="counter">
                                <div class="icon">
                                    <i class="las {{ $value->icon }}"></i>
                                </div>
                                <div class="odo-area">
                                    <h2 class="odo-title odometer" data-odometer-final="{{ $value->amount }}"></h2>
                                </div>
                                <h4 class="title">{{ $value->language->$default->title ?? '' }}</h4>
                            </div>
                        </div>
                    @empty
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</section>
