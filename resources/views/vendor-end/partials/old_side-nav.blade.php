<div class="sidebar">
    <div class="sidebar-inner">
        <div class="sidebar-logo">
            <a href="index.html" class="sidebar-main-logo">
                <img src="{{ get_logo($basic_settings) }}" data-white_img="{{ get_logo($basic_settings,"dark") }}"
                data-dark_img="{{ get_logo($basic_settings) }}" alt="logo">
            </a>
            <button class="sidebar-menu-bar">
                <i class="fas fa-exchange-alt"></i>
            </button>
        </div>
        <div class="sidebar-menu-wrapper">
            <ul class="sidebar-menu">
                <li class="sidebar-menu-item">
                    <a href="{{ setRoute('user.dashboard') }}">
                        <i class="menu-icon las la-palette"></i>
                        <span class="menu-title">{{ __("Dashboard") }}</span>
                    </a>
                </li>
                <li class="sidebar-menu-item">
                    <a href="transfer-money.html">
                        <i class="menu-icon las la-fax"></i>
                        <span class="menu-title">Transfer Money</span>
                        <div class="sidebar-item-badge">
                            <span class="badge">2</span>
                        </div>
                    </a>
                </li>
                <li class="sidebar-menu-item">
                    <a href="add-money.html">
                        <i class="menu-icon las la-sign"></i>
                        <span class="menu-title">Add Money</span>
                    </a>
                </li>
                <li class="sidebar-menu-item">
                    <a href="money-out.html">
                        <i class="menu-icon las la-sign-out-alt"></i>
                        <span class="menu-title">Money Out</span>
                    </a>
                </li>
                <li class="sidebar-menu-item">
                    <a href="money-exchange.html">
                        <i class="menu-icon lab la-stack-exchange"></i>
                        <span class="menu-title">Money Exchange</span>
                    </a>
                </li>
                <li class="sidebar-menu-item">
                    <a href="request-money.html">
                        <i class="menu-icon las la-fill-drip"></i>
                        <span class="menu-title">Request Money</span>
                    </a>
                </li>
                <li class="sidebar-menu-item">
                    <a href="invoice.html">
                        <i class="menu-icon las la-receipt"></i>
                        <span class="menu-title">My Invoice</span>
                    </a>
                </li>
                <li class="sidebar-menu-item">
                    <a href="voucher.html">
                        <i class="menu-icon las la-scroll"></i>
                        <span class="menu-title">My Voucher</span>
                    </a>
                </li>
                <li class="sidebar-menu-item">
                    <a href="api-key.html">
                        <i class="menu-icon las la-key"></i>
                        <span class="menu-title">API Key</span>
                    </a>
                </li>
                <li class="sidebar-menu-item">
                    <a href="2fa.html">
                        <i class="menu-icon las la-qrcode"></i>
                        <span class="menu-title">2FA Security</span>
                    </a>
                </li>
                <li class="sidebar-menu-item">
                    <a href="javascript:void(0)" class="logout-btn">
                        <i class="menu-icon las la-sign-out-alt"></i>
                        <span class="menu-title">{{ __("Logout") }}</span>
                    </a>
                </li>
            </ul>
        </div>
        <div class="sidebar-doc-box bg_img" data-background="{{ asset("frontend/images/element/side-bg.webp") }}">
            <div class="sidebar-doc-icon">
                <i class="fas fa-question-circle"></i>
            </div>
            <div class="sidebar-doc-content">
                <h4 class="title">Need Help?</h4>
                <p>Please check our docs</p>
                <div class="sidebar-doc-btn">
                    <a href="{{ setRoute('user.support.ticket.index') }}" class="btn--base w-100">{{ __("Get Support") }}</a>
                </div>
            </div>
        </div>
    </div>
</div>

@push('script')
    <script>
        $(".logout-btn").click(function(){
            var actionRoute =  "{{ setRoute('user.logout') }}";
            var target      = 1;
            var message     = `Are you sure to <strong>Logout</strong>?`;

            openAlertModal(actionRoute,target,message,"Logout","POST");
        });
    </script>
@endpush
