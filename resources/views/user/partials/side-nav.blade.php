<div class="sidebar">
    <div class="sidebar-inner">
        <div class="sidebar-inner-wrapper">
            <div class="sidebar-logo">
                <a href="{{ setRoute('frontend.index') }}" class="sidebar-main-logo">
                    <img src="{{ get_logo($basic_settings) }}" data-white_img="{{ get_logo($basic_settings, 'dark') }}"
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
                            <span class="menu-title">{{ __('Dashboard') }}</span>
                        </a>
                    </li>
                    <li class="sidebar-menu-item">
                        <a href="{{ setRoute('frontend.find.car') }}">
                            <i class="menu-icon las la-user"></i>
                            <span class="menu-title">{{ __('Find Car') }}</span>
                        </a>
                    </li>
                    <li class="sidebar-menu-item">
                        <a href="{{ setRoute('user.history.index') }}">
                            <i class="menu-icon las la-cart-plus"></i>
                            <span class="menu-title">{{ __('History') }}</span>
                        </a>
                    </li>
                    <li class="sidebar-menu-item">
                        <a href="javascript:void(0)" class="logout-btn">
                            <i class="menu-icon las la-sign-out-alt"></i>
                            <span class="menu-title">{{ __('Logout') }}</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="sidebar-doc-box bg-overlay-base bg_img" data-background="{{ asset('frontend/images/element/sidebar-img.webp') }}">
            <div class="sidebar-doc-icon">
                <i class="las la-headphones-alt"></i>
            </div>
            <div class="sidebar-doc-content">
                <h4 class="title">{{ __('Help Center') }}</h4>
                <p>{{ __('Please contact our support') }}.</p>
                <div class="sidebar-doc-btn">
                    <a href="{{ setRoute('user.support.ticket.index') }}" class="btn--base w-100">{{ __('Get Support') }}</a>
                </div>
            </div>
        </div>
    </div>
</div>
@push('script')
    <script>
        $(".logout-btn").click(function() {
            var actionRoute = "{{ setRoute('user.logout') }}";
            var target = 1;
            var message = `{{ __("Are you sure to") }} <strong>{{ __("Logout") }}</strong>?`;

            openAlertModal(actionRoute, target, message, "{{ __('Logout') }}", "POST");
        });

        function openAlertModal(URL, target, message, actionBtnText = "{{ __('Remove') }}", method = "DELETE") {
            if (URL == "" || target == "") {
                return false;
            }

            if (message == "") {
                message = "{{ __('Are you sure to delete ?') }}";
            }
            var method = `<input type="hidden" name="_method" value="${method}">`;
            openModalByContent({
                    content: `<div class="card modal-alert border-0">
                        <div class="card-body">
                            <form method="POST" action="${URL}">
                                <input type="hidden" name="_token" value="${laravelCsrf()}">
                                ${method}
                                <div class="head mb-3">
                                    ${message}
                                    <input type="hidden" name="target" value="${target}">
                                </div>
                                <div class="foot d-flex align-items-center justify-content-between">
                                    <button type="button" class="modal-close btn--base btn-for-modal">{{ __('Close') }}</button>
                                    <button type="submit" class="alert-submit-btn btn--danger btn-loading btn-for-modal">${actionBtnText}</button>
                                </div>
                            </form>
                        </div>
                    </div>`,
                },

            );
        }

        function openModalByContent(data = {
            content: "",
            animation: "mfp-move-horizontal",
            size: "medium",
        }) {
            $.magnificPopup.open({
                removalDelay: 500,
                items: {
                    src: `<div class="white-popup mfp-with-anim ${data.size ?? "medium"}">${data.content}</div>`, // can be a HTML string, jQuery object, or CSS selector
                },
                callbacks: {
                    beforeOpen: function() {
                        this.st.mainClass = data.animation ?? "mfp-move-horizontal";
                    },
                    open: function() {
                        var modalCloseBtn = this.contentContainer.find(".modal-close");
                        $(modalCloseBtn).click(function() {
                            $.magnificPopup.close();
                        });
                    },
                },
                midClick: true,
            });
        }

        function laravelCsrf() {
            return $("head meta[name=csrf-token]").attr("content");
        }
    </script>
@endpush
