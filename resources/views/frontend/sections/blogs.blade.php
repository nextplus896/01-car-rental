<section class="blog-section ptb-80">
    <div class="container">
        <div class="section-header-title">
            <div class="section-title pb-20">
                <h4 class="sub-title text--base">
                    {{ $blog_sec->value->language->$default->section_title ?? '' }}
                </h4>
            </div>
            <div class="blog-title">
                <h2 class="title">{{ $blog_sec->value->language->$default->heading ?? '' }}</h2>
            </div>
        </div>
        <div class="blog-area pt-40">
            @forelse ($blogs ?? [] as $value)
            <div class="blog-item mb-30">
                <div class="row">
                    <div class="col-lg-4 col-md-4">
                        <div class="blog-img">
                            <img src="{{ get_image($value->data->image, 'site-section') }}" alt="img">
                        </div>
                    </div>
                    <div class="col-lg-8 col-md-8">
                        <div class="blog-content">
                            <h3 class="title">{{ $value->data->language->$default->title ?? '' }}</h3>
                            <p>{{textLength(strip_tags($value->data->language->$default->description ?? '',120))}}</p>
                            <div class="blog-btn">
                                <a href="{{ setRoute('frontend.blog.detail',$value->id) }}" class="btn--base btn">{{ __('Blog Details') }}</a>
                                <div class="blog-date">
                                    <i class="las la-history"></i>
                                    <p>{{ $value->created_at->format('d-M-Y') ?? '' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            @endforelse
        </div>
    </div>
</section>
