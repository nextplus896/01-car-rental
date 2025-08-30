<nav class="navbar-wrapper">
    <div class="dashboard-title-part">
        <div class="left">
            <div class="icon">
                <button class="sidebar-menu-bar">
                    <i class="fas fa-exchange-alt"></i>
                </button>
            </div>
            <div class="dashboard-path">
                <span class="main-path"><a href="{{ setRoute('user.profile.index') }}">{{ __('Dashboard') }}</a></span>
                <i class="las la-angle-right"></i>
                <span class="active-path">{{ $breadcrumb ?? '' }}</span>
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
                        @forelse (get_user_notifications() ?? [] as $item)
                            @if ($item->type == notify_const()::PAYMENT_REJECT)
                                <li>
                                    <div class="thumb">
                                        <img src="{{ auth()->user()->userImage }}" alt="user">
                                    </div>
                                    <div class="content">
                                        <div class="title-area">
                                            <h6 class="title">{{ $item->message->title }}</h6>
                                            <span class="time">{{ $item->created_at->diffForHumans() }}</span>
                                        </div>
                                        <span class="sub-title">{{ $item->message->message ?? '' }}</span>
                                    </div>
                                </li>
                                <a class="btn btn-danger" href="{{ setRoute('user.car.booking.repayment.form',$item->message->trx_id) }}">{{ __('Re-payment') }}</a>
                            @else
                                <li>
                                    <div class="thumb">
                                        <img src="{{ auth()->user()->userImage }}" alt="user">
                                    </div>
                                    <div class="content">
                                        <div class="title-area">
                                            <h6 class="title">{{ $item->message->title }}</h6>
                                            <span class="time">{{ $item->created_at->diffForHumans() }}</span>
                                        </div>
                                        <span class="sub-title">{{ $item->message->message ?? '' }}</span>
                                    </div>
                                </li>
                            @endif
                            @empty
                            <h6>{{ __('No notification yet') }}</h6>
                        @endforelse
                    </ul>
                </div>
            </div>
            <div class="header-user-wrapper">
                <div class="header-user-thumb">
                    <a href="{{ setRoute('user.profile.index') }}"><img
                            src="{{ auth()->user()->userImage ?? asset('frontend/assets/images/client/client-3.webp') }}"
                            alt="client"></a>
                </div>
            </div>
        </div>
    </div>
</nav>
