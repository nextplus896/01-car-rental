<section class="driver-requirements-area ptb-80">
    <div class="container">
        <div class="required-title pb-30">
            <h2 class="title">
                {{ $vendor_require->value->language->$default->heading ?? '' }}
            </h2>
        </div>
        <div class="row mb-20-none">
            @forelse ($vendor_require->value->items ?? [] as $value)
                <div class="col-xl-4 col-lg-6 col-md-6 mb-20">
                    <div class="driver-requirements">
                        <div class="requirements-title">
                            <h4 class="title"><i class="las {{ $value->icon ?? '' }}"></i>
                                {{ $value->language->$default->title ?? '' }}</h3>
                        </div>
                        <ul class="requirements-list">
                            @forelse ($value->detailsItem as $req)
                                <li>{{ $req->language->$default->details ?? '' }}</li>
                            @empty
                            @endforelse
                        </ul>
                    </div>
                </div>
            @empty
            @endforelse
        </div>
    </div>
</section>
