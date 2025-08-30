<section class="service-section ptb-80">
    <div class="container">
        <div class="row">
            <div class="col-xl-8 col-lg-10">
                <div class="service-content">
                    <div class="section-title pb-20">
                        <h4 class="sub-title text--base">
                            {{ $service->value->language->$default->section_title ?? '' }}
                        </h4>
                    </div>
                    <div class="service-title pb-20">
                        <h2 class="title">
                            {{ $service->value->language->$default->heading ?? '' }}
                        </h2>
                    </div>
                    <div class="service-paragraph pb-40">
                        <p>{{ $service->value->language->$default->sub_heading ?? '' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mb-20-none">
            @forelse ($service->value->items ?? [] as $value)
                <div class="col-lg-4 mb-20">
                    <div class="service-area">
                        <div class="icon">
                            <i class="las {{ $value->icon ??  '' }}"></i>
                        </div>
                        <div class="area-content">
                            <h4 class="title">{{ $value->language->$default->title ?? '' }}</h4>
                            <p>{{ $value->language->$default->description ?? '' }}</p>
                        </div>
                    </div>
                </div>
            @empty
            @endforelse
        </div>
    </div>
</section>
