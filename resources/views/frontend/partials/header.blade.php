@php
    $current_url = URL::current();
    $pages = App\Models\Admin\SetupPage::where(['status' => true])
        ->orWhere('slug', 'home')
        ->get();
@endphp
<!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    Start Header
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
<header class="header-section two">
    <div class="header">
        <div class="header-bottom-area">
            <div class="container custom-container">
                <div class="header-menu-content">
                    <nav class="navbar navbar-expand-xl p-0">
                        <a class="site-logo site-title" href="{{ setRoute('frontend.index') }}">
                            <img src="{{ get_logo($basic_settings) }}"
                                data-white_img="{{ get_logo($basic_settings, 'white') }}"
                                data-dark_img="{{ get_logo($basic_settings, 'dark') }}" alt="site-logo">
                        </a>
                        <button class="navbar-toggler ms-auto" type="button" data-bs-toggle="collapse"
                            data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                            aria-expanded="false" aria-label="Toggle navigation">
                            <span class="fas fa-bars"></span>
                        </button>
                        <div class="collapse navbar-collapse" id="navbarSupportedContent">
                            <ul class="navbar-nav main-menu ms-auto">
                                @php
                                    $current_url = URL::current();
                                @endphp
                                @foreach ($pages as $item)
                                    @php
                                        $title = json_decode($item->title);
                                    @endphp
                                    <li>
                                        <a href="{{ url($item->url) }}"
                                            class="@if ($current_url == url($item->url)) active @endif">
                                            <span>{{ __($item->title) }}</span>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                            <div class="language-select">
                                <select class="nice-select" name="lang_switcher" id="">
                                    @foreach ($__languages as $item)
                                        <option value="{{ $item->code }}"
                                            @if (get_default_language_code() == $item->code) selected @endif>{{ $item->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            @if (auth()->user() || auth()->guard('vendor')->user())
                                <div class="header-action">
                                    @if (auth()->user())
                                        <a href="{{ setRoute('user.dashboard') }}" class="btn--base">
                                            {{ __('Dashboard') }}
                                        </a>
                                    @else
                                        <a href="{{ setRoute('vendor.dashboard.index') }}" class="btn--base">
                                            {{ __('Dashboard') }}
                                        </a>
                                    @endif
                                </div>
                            @else
                                <div class="header-action">
                                    <a href="{{ setRoute('user.login') }}" class="btn--base">{{ __('Login Now') }}</a>
                                </div>
                            @endif

                        </div>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</header>
<!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
                End Header
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
@push('script')
    <script>
        $("select[name=lang_switcher]").change(function() {
            var selected_value = $(this).val();
            var submitForm =
                `<form action="{{ setRoute('frontend.languages.switch') }}" id="local_submit" method="POST"> @csrf <input type="hidden" name="target" value="${$(this).val()}" ></form>`;
            $("body").append(submitForm);
            $("#local_submit").submit();
        });
    </script>
@endpush
