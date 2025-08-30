<section class="security-section pt-80">
    <div class="container">
        <div class="section-header-title">
            <h4 class="title text--base pb-20">
                {{ $security->value->language->$default->section_title ?? '' }}
            </h4>
            <div class="row">
                <div class="col-xl-10 col-lg-12">
                    <h2 class="title-head">
                        {{ $security->value->language->$default->heading ?? '' }}
                    </h2>
                    <p>{{ $security->value->language->$default->sub_heading ?? '' }}
                    </p>
                </div>
            </div>
        </div>
        <div class="security-area pt-30">
            <div class="row">
                @forelse ($security->value->items ?? [] as $value)
                    <div class="col-lg-4 col-md-6 mb-20">
                        <div class="security-item">
                            <div class="icon">
                                <i class="las {{ $value->icon }}"></i>
                            </div>
                            <div class="security-details">
                                <h3 class="title">{{ $value->language->$default->title ?? '' }}</h3>
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
