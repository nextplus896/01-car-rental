<!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
        Start Footer
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
<footer class="footer-section">
    <div class="footer-top-area">
        <div class="container">
            <div class="social-area">
                <ul class="footer-social">
                    @forelse (@$footer->value->items ?? [] as $value)
                    <li>
                        <a href="{{ $value->item_link }}">
                            <i class="{{ $value->item_social_icon }}"></i> {{ $value->item_name }}
                        </a>
                    </li>
                    @empty
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
    <canvas class="moon"></canvas>
    <div class="container mx-auto">
        <div class="footer-content pt-60">
            <div class="row mb-30-none">
                <div class="col-xl-4 col-lg-4 mb-30">
                    <div class="footer-widget">
                        <div class="footer-text">
                                <img src="{{ get_logo($basic_settings, 'dark') }}" alt="site-logo">
                            <p>
                                {{ @$footer->value->language->$default->footer_desc ?? @$footer->value->language->$default_lng->footer_desc }}
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4 col-lg-4 col-md-6 mb-30">
                    <div class="footer-widget">
                        <div class="footer-widget-heading">
                            <h3>{{ __('Useful Links') }}</h3>
                        </div>
                        <ul>
                            @forelse (getLinks() ?? [] as $item)
                                <li>
                                    <a href="{{ setRoute('global.useful.page', $item->slug) }}">
                                        {{ $item->title->language->$default->title ?? $item->title->language->$default_lng->title }}
                                    </a>
                                </li>
                            @empty
                            @endforelse
                        </ul>
                    </div>
                </div>
                <div class="col-xl-4 col-lg-4 col-md-6 mb-50">
                    <div class="footer-widget">
                        <div class="footer-widget-heading">
                            <h3>{{ __('Subscribe') }}</h3>
                        </div>
                        <div class="footer-text mb-25">
                            <p>{{ @$footer->value->language->$default->subscribe_desc ?? @$footer->value->language->$default_lng->subscribe_desc }}
                            </p>
                        </div>
                        <div class="subscribe-form">
                            <form action="{{ setRoute('frontend.subscribers.store') }}" method="POST">
                                @csrf
                                <input type="text" class="form--control" placeholder="Email Address" name="email">
                                <button><i class="fab fa-telegram-plane"></i></button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="svg-container">
        <div class="back"></div>
        <div class="mid">
        </div>
        <div class="fore">
            <div class="figure"></div>
        </div>
    </div>
    <div class="copyright-area">
        <div class="container">
            <div class="row">
                <div class="col-12 text-center text-lg-left">
                    <div class="copyright-text">
                        <p>
                            {{ __('Copyright') }}
                            &copy;
                            {{ Carbon\Carbon::now()->year }}
                            {{ $footer->value->language->$default->footer_text ?? '' }}
                            <a href="{{ setRoute('frontend.index') }}">{{ $site_name }}</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>
<!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    End Footer
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->

<!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    Start cookie
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
<div class="cookie-main-wrapper">
    <div class="cookie-content">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M21.598 11.064a1.006 1.006 0 0 0-.854-.172A2.938 2.938 0 0 1 20 11c-1.654 0-3-1.346-3.003-2.937c.005-.034.016-.136.017-.17a.998.998 0 0 0-1.254-1.006A2.963 2.963 0 0 1 15 7c-1.654 0-3-1.346-3-3c0-.217.031-.444.099-.716a1 1 0 0 0-1.067-1.236A9.956 9.956 0 0 0 2 12c0 5.514 4.486 10 10 10s10-4.486 10-10c0-.049-.003-.097-.007-.16a1.004 1.004 0 0 0-.395-.776zM12 20c-4.411 0-8-3.589-8-8a7.962 7.962 0 0 1 6.006-7.75A5.006 5.006 0 0 0 15 9l.101-.001a5.007 5.007 0 0 0 4.837 4C19.444 16.941 16.073 20 12 20z"/><circle cx="12.5" cy="11.5" r="1.5"/><circle cx="8.5" cy="8.5" r="1.5"/><circle cx="7.5" cy="12.5" r="1.5"/><circle cx="15.5" cy="15.5" r="1.5"/><circle cx="10.5" cy="16.5" r="1.5"/></svg>
        <p class="text-white">{{ __(strip_tags(@$cookie->value->desc)) }} <a href="{{ secure_url('/').'/'.@$cookie->value->link }}">{{ __("Privacy Policy") }}</a></p>
    </div>
    <div class="cookie-btn-area">
        <button class="cookie-btn">{{__("Allow")}}</button>
        <button class="cookie-btn-cross">{{__("Decline")}}</button>
    </div>
</div>
<!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    End cookie
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
