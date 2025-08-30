<section class="blog-section blog-details-section ptb-80">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-8 col-lg-7">
                <div class="blog-item">
                    <div class="blog-thumb">
                        <img src="{{ get_image($blog->data->image, 'site-section') }}" alt="blog">
                    </div>
                    <div class="blog-content pt-3=4">
                        <h3 class="title">
                            {{ $blog->data->language->$default->title ?? '' }}
                        </h3>
                        <p>
                            <?php echo $blog->data->language->$default->description ?? '' ?>
                        </p>
                        <div class="blog-tag-wrapper">
                            <span>{{ __('Tags') }}:</span>
                            <ul class="blog-footer-tag">
                                @forelse ($blog->data->language->$default->tags ?? [] as $value)
                                    <li><a href="#0">{{ __($value ?? '') }}</a></li>
                                @empty
                                @endforelse
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-4 col-lg-5 mb-30">
                <div class="blog-sidebar">
                    <div class="widget-box mb-30">
                        <h4 class="widget-title">{{ __('Categories') }}</h4>
                        <div class="category-widget-box">
                            <ul class="category-list">
                                @forelse ($blog_categories ?? [] as $value)
                                    <li><a href="{{ setRoute('frontend.blog.category',$value->id) }}">{{ $value->name->language->$default->name ?? '' }}<span>{{ blogCount($value->id) }}</span></a></li>
                                @empty
                                @endforelse
                            </ul>
                        </div>
                    </div>
                    <div class="widget-box mb-30">
                        <h4 class="widget-title">{{ __('Recent Posts') }}</h4>
                        <div class="popular-widget-box">
                            @forelse ($recent_blogs ?? [] as $post)
                                <div class="single-popular-item d-flex flex-wrap align-items-center">
                                    <div class="popular-item-thumb">
                                        <a href="{{ setRoute('frontend.blog.detail',$post->id) }}"><img src="{{ get_image($post->data->image, 'site-section') }}" alt="blog"></a>
                                    </div>
                                    <div class="popular-item-content">
                                        <span class="date">{{ $post->created_at->format('d-M-Y') ?? '' }}</span>
                                        <h6 class="title">
                                            <a href="{{ setRoute('frontend.blog.detail',$post->id) }}">
                                                {{ $post->data->language->$default->title ?? '' }}
                                            </a>
                                        </h6>
                                    </div>
                                </div>
                            @empty
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
