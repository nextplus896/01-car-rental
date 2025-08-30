<nav class="navbar-wrapper">
    <div class="dashboard-title-part">
        <div class="left">
            <div class="icon">
                <button class="sidebar-menu-bar">
                    <i class="fas fa-exchange-alt"></i>
                </button>
            </div>
            <div class="dashboard-path">
                <span class="main-path"><a href="{{ setRoute('vendor.profile.index') }}">{{ __('Dashboard') }}</a></span>
                <i class="las la-angle-right"></i>
                <span class="active-path">{{ $page_title ?? ""}}</span>
            </div>
        </div>
        <div class="right">
            <div class="header-notification-wrapper">
                <button class="notification-icon">
                    <i class="las la-bell"></i>
                </button>
                <div class="notification-wrapper">
                    <div class="notification-header">
                        <h5 class="title">{{ __('Notification') }}</h5>
                    </div>
                    <ul class="notification-list">
                        @forelse (get_vendor_notifications() ?? [] as $item)
                            <li>
                                <div class="thumb">
                                    <img src="{{ auth()->guard('vendor')->user()->userImage }}" alt="user">
                                </div>
                                <div class="content">
                                    <div class="title-area">
                                        <h6 class="title">{{ $item->message->title }}</h6>
                                        <span class="time">{{ $item->created_at->diffForHumans() }}</span>
                                    </div>
                                    <span class="sub-title">{{ $item->message->message ?? '' }}</span>
                                </div>
                            </li>
                            @empty
                            <h6>{{ __('No notification yet') }}</h6>
                        @endforelse
                    </ul>
                </div>
            </div>
            <div class="header-user-wrapper">
                <div class="header-user-thumb">
                    <a href="{{ setRoute('vendor.profile.index') }}"><img src="{{ auth()->user()->userImage ?? asset('frontend/assets/images/client/client-3.webp') }}" alt="client"></a>
                </div>
            </div>
        </div>
    </div>
</nav>
