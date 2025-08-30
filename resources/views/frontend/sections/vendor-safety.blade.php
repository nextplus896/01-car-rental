<section class="safety-section ptb-80">
    <div class="container">
        <div class="safety-section-title pb-30">
            <h2 class="title">
                {{ $vendor_safety->value->language->$default->heading ?? '' }}
            </h2>
        </div>
        <div class="row mb-20-none">
            @forelse ($vendor_safety->value->items as $value)
                <div class="col-xl-4 col-lg-6 col-md-6 mb-20">
                    <div class="safty-content">
                        <div class="icon">
                            <i class="las {{ $value->icon }}"></i>
                        </div>
                        <div class="safety-title">
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
</section>
