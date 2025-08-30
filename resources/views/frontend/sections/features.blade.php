<section class="feature-section pt-80">
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                <div class="how-its-work-title">
                    <h4 class="titte text--base pb-20">
                        {{ $feature->value->language->$default->section_title ?? '' }}
                    </h4>
                    <div class="row">
                        <div class="col-xl-10 col-lg-12">
                            <h2 class="titte d-flex align-items-center">
                                {{ $feature->value->language->$default->heading ?? '' }}
                                <i class="las la-arrow-right"></i>
                            </h2>
                            <p> {{ $feature->value->language->$default->sub_heading ?? '' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="feature-details pt-40">
            <div class="row">
                <div class="col-lg-12">
                    <div class="featuare-content-area align-items-center">
                        <ul class="feature-list">
                            @forelse ($feature->value->items ?? [] as $value)
                                <li>
                                    <i class="las la-arrow-right"></i> {{ $value->language->$default->feature ?? '' }}
                                </li>
                            @empty
                            @endforelse
                        </ul>
                        <div class="key-deatils">
                            <h3 class="title">{{ $feature->value->language->$default->details_title ?? '' }}</h3>
                            <p>{{ $feature->value->language->$default->details ?? '' }}</p>
                            <div class="contact-btn">
                                <a href="{{ $feature->value->details_button_link ?? "#" }}" class="btn--base">
                                    {{ $feature->value->language->$default->details_button ?? '' }}
                                    <i class="las la-chevron-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
