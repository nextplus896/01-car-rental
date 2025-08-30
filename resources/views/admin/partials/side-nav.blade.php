<div class="sidebar">
    <div class="sidebar-inner">
        <div class="sidebar-logo">
            <a href="{{ setRoute('admin.dashboard') }}" class="sidebar-main-logo">
                <img src="{{ get_logo($basic_settings) }}" data-white_img="{{ get_logo($basic_settings, 'white') }}"
                    data-dark_img="{{ get_logo($basic_settings, 'dark') }}" alt="logo">
            </a>
            <button class="sidebar-menu-bar">
                <i class="fas fa-exchange-alt"></i>
            </button>
        </div>
        <div class="sidebar-user-area">
            <div class="sidebar-user-thumb">
                <a href="{{ setRoute('admin.profile.index') }}"><img
                        src="{{ get_image(Auth::user()->image, 'admin-profile', 'profile') }}" alt="user"></a>
            </div>
            <div class="sidebar-user-content">
                <h6 class="title">{{ Auth::user()->fullname }}</h6>
                <span class="sub-title">{{ Auth::user()->getRolesString() }}</span>
            </div>
        </div>
        @php
            $current_route = Route::currentRouteName();
        @endphp
        <div class="sidebar-menu-wrapper">
            <ul class="sidebar-menu">

                @include('admin.components.side-nav.link', [
                    'route' => 'admin.dashboard',
                    'title' => __('Dashboard'),
                    'icon' => 'menu-icon las la-rocket',
                ])

                {{-- Section Default --}}
                @include('admin.components.side-nav.link-group', [
                    'group_title' => __('Default'),
                    'group_links' => [
                        [
                            'title' => __('Setup Currency'),
                            'route' => 'admin.currency.index',
                            'icon' => 'menu-icon las la-coins',
                        ],
                        [
                            'title' => __('Fees & Charges'),
                            'route' => 'admin.trx.settings.index',
                            'icon' => 'menu-icon las la-wallet',
                        ],
                        [
                            'title' => __('Subscribers'),
                            'route' => 'admin.subscriber.index',
                            'icon' => 'menu-icon las la-bell',
                        ],
                    ],
                ])

                {{-- Section Default --}}
                @include('admin.components.side-nav.link-group', [
                    'group_title' => __('CARS'),
                    'group_links' => [
                        [
                            'title' => __('Car Type'),
                            'route' => 'admin.car.types.index',
                            'icon' => 'menu-icon las la-coins',
                        ],
                        [
                            'title' => __('Car Area'),
                            'route' => 'admin.car.area.index',
                            'icon' => 'menu-icon las la-wallet',
                        ],
                        [
                            'title' => __('Car Approval'),
                            'route' => 'admin.car.index',
                            'icon' => 'menu-icon las la-wallet',
                        ],
                    ],
                ])

                {{-- Section Transaction & Logs --}}
                @include('admin.components.side-nav.link-group', [
                    'group_title' => __('Transactions & Logs'),
                    'group_links' => [
                        'dropdown' => [
                            [
                                'title' => __('Payment Logs'),
                                'icon' => 'menu-icon las la-calculator',
                                'links' => [
                                    [
                                        'title' => __('Pending Logs'),
                                        'route' => 'admin.add.money.pending',
                                    ],
                                    [
                                        'title' => __('Completed Logs'),
                                        'route' => 'admin.add.money.complete',
                                    ],
                                    [
                                        'title' => __('Canceled Logs'),
                                        'route' => 'admin.add.money.canceled',
                                    ],
                                    [
                                        'title' => __('Refund Logs'),
                                        'route' => 'admin.add.money.refund',
                                    ],
                                    [
                                        'title' => __('All Logs'),
                                        'route' => 'admin.add.money.index',
                                    ],
                                ],
                            ],
                            [
                                'title' => __('Money Out Logs'),
                                'icon' => 'menu-icon las la-sign-out-alt',
                                'links' => [
                                    [
                                        'title' => __('Pending Logs'),
                                        'route' => 'admin.money.out.pending',
                                    ],
                                    [
                                        'title' => __('Completed Logs'),
                                        'route' => 'admin.money.out.complete',
                                    ],
                                    [
                                        'title' => __('Canceled Logs'),
                                        'route' => 'admin.money.out.canceled',
                                    ],
                                    [
                                        'title' => __('All Logs'),
                                        'route' => 'admin.money.out.index',
                                    ],
                                ],
                            ],
                            [
                                'title' => __('Booking Logs'),
                                'icon' => 'menu-icon las la-sign-out-alt',
                                'links' => [
                                    [
                                        'title' => __('Pending Logs'),
                                        'route' => 'admin.booking.pending',
                                    ],
                                    [
                                        'title' => __('Completed Logs'),
                                        'route' => 'admin.booking.complete',
                                    ],
                                    [
                                        'title' => __('Canceled Logs'),
                                        'route' => 'admin.booking.canceled',
                                    ],
                                    [
                                        'title' => __('All Logs'),
                                        'route' => 'admin.booking.index',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ])
                {{-- Interface Panel --}}
                @include('admin.components.side-nav.link-group', [
                    'group_title' => __('Interface Panel'),
                    'group_links' => [
                        'dropdown' => [
                            [
                                'title' => __('User Care'),
                                'icon' => 'menu-icon las la-user-edit',
                                'links' => [
                                    [
                                        'title' => __('Active Users'),
                                        'route' => 'admin.users.active',
                                    ],
                                    [
                                        'title' => __('Email Unverified'),
                                        'route' => 'admin.users.email.unverified',
                                    ],
                                    [
                                        'title' => __('All Users'),
                                        'route' => 'admin.users.index',
                                    ],
                                    [
                                        'title' => __('Email To Users'),
                                        'route' => 'admin.users.email.users',
                                    ],
                                    [
                                        'title' => __('Banned Users'),
                                        'route' => 'admin.users.banned',
                                    ],
                                ],
                            ],
                            [
                                'title' => __('Vendor Care'),
                                'icon' => 'menu-icon las la-user-edit',
                                'links' => [
                                    [
                                        'title' => __('Active Vendor'),
                                        'route' => 'admin.vendor.active',
                                    ],
                                    [
                                        'title' => __('Email Unverified'),
                                        'route' => 'admin.vendor.email.unverified',
                                    ],
                                    [
                                        'title' => __('KYC Unverified'),
                                        'route' => 'admin.vendor.kyc.unverified',
                                    ],
                                    [
                                        'title' => __('All Vendor'),
                                        'route' => 'admin.vendor.index',
                                    ],
                                    [
                                        'title' => __('Email To Vendor'),
                                        'route' => 'admin.vendor.email.users',
                                    ],
                                    [
                                        'title' => __('Banned Vendor'),
                                        'route' => 'admin.vendor.banned',
                                    ],
                                ],
                            ],
                            [
                                'title' => __('Admin Care'),
                                'icon' => 'menu-icon las la-user-shield',
                                'links' => [
                                    [
                                        'title' => __('All Admin'),
                                        'route' => 'admin.admins.index',
                                    ],
                                    [
                                        'title' => __('Admin Role'),
                                        'route' => 'admin.admins.role.index',
                                    ],
                                    [
                                        'title' => __('Role Permission'),
                                        'route' => 'admin.admins.role.permission.index',
                                    ],
                                    [
                                        'title' => __('Email To Admin'),
                                        'route' => 'admin.admins.email.admins',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ])

                {{-- Section Settings --}}
                @include('admin.components.side-nav.link-group', [
                    'group_title' => __('Settings'),
                    'group_links' => [
                        'dropdown' => [
                            [
                                'title' => __('Web Settings'),
                                'icon' => 'menu-icon lab la-safari',
                                'links' => [
                                    [
                                        'title' => __('Basic Settings'),
                                        'route' => 'admin.web.settings.basic.settings',
                                    ],
                                    [
                                        'title' => __('Image Assets'),
                                        'route' => 'admin.web.settings.image.assets',
                                    ],
                                    [
                                        'title' => __('Setup SEO'),
                                        'route' => 'admin.web.settings.setup.seo',
                                    ],
                                ],
                            ],
                            [
                                'title' => __('App Settings'),
                                'icon' => 'menu-icon las la-mobile',
                                'links' => [
                                    [
                                        'title' => __('Splash Screen'),
                                        'route' => 'admin.app.settings.splash.screen',
                                    ],
                                    [
                                        'title' => __('Onboard Screen'),
                                        'route' => 'admin.app.settings.onboard.screens',
                                    ],
                                    [
                                        'title' => __('App URLs'),
                                        'route' => 'admin.app.settings.urls',
                                    ],
                                    [
                                        'title' => __('Vendor Splash Screen'),
                                        'route' => 'admin.app.settings.vendor.splash.screen',
                                    ],
                                    [
                                        'title' => __('Vendor Onboard Screen'),
                                        'route' => 'admin.app.settings.onboard.vendor.screens',
                                    ],
                                    [
                                        'title' => __('Vendor App URLs'),
                                        'route' => 'admin.app.settings.vendor.urls',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ])

                @include('admin.components.side-nav.link', [
                    'route' => 'admin.languages.index',
                    'title' => __('Languages'),
                    'icon' => 'menu-icon las la-language',
                ])

                @include('admin.components.side-nav.link', [
                    'route' => 'admin.system.maintenance.index',
                    'title' => 'System Maintenance',
                    'icon' => 'menu-icon las la-tools',
                ])

                {{-- Verification Center --}}
                @include('admin.components.side-nav.link-group', [
                    'group_title' => __('Verification Center'),
                    'group_links' => [
                        'dropdown' => [
                            [
                                'title' => __('Setup Email'),
                                'icon' => 'menu-icon las la-envelope-open-text',
                                'links' => [
                                    [
                                        'title' => __('Email Method'),
                                        'route' => 'admin.setup.email.config',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ])

                @include('admin.components.side-nav.link', [
                    'route' => 'admin.setup.kyc.index',
                    'title' => __('Setup KYC'),
                    'icon' => 'menu-icon las la-clipboard-list',
                ])

                @if (admin_permission_by_name('admin.setup.sections.section'))
                    <li class="sidebar-menu-header">{{ __('Setup Web Content') }}</li>
                    @php
                        $current_url = URL::current();

                        $setup_section_childs = [
                            setRoute('admin.setup.sections.section', 'banner'),
                            // setRoute('admin.setup.sections.section','brand'),
                            setRoute('admin.setup.sections.section', 'find-car'),
                            setRoute('admin.setup.sections.section', 'about-us'),
                            setRoute('admin.setup.sections.section', 'features'),
                            setRoute('admin.setup.sections.section', 'security'),
                            setRoute('admin.setup.sections.section', 'chooseUs'),
                            setRoute('admin.setup.sections.section', 'statistics'),
                            setRoute('admin.setup.sections.section', 'app'),
                            setRoute('admin.setup.sections.section', 'vendor-safety'),
                            setRoute('admin.setup.sections.section', 'vendor-require'),
                            setRoute('admin.setup.sections.section', 'faq'),
                            setRoute('admin.setup.sections.section', 'services'),
                            // setRoute('admin.setup.sections.section','feature'),
                            // setRoute('admin.setup.sections.section','clients-feedback'),
                            setRoute('admin.setup.sections.section', 'announcement'),
                            // setRoute('admin.setup.sections.section','how-it-work'),
                            setRoute('admin.setup.sections.section', 'contact-us'),
                            setRoute('admin.setup.sections.section', 'footer'),
                            setRoute('admin.setup.sections.section', 'auth'),
                            setRoute('admin.setup.sections.section', 'vendor-auth'),
                            // setRoute('admin.setup.sections.section','about-page')
                        ];
                    @endphp

                    <li class="sidebar-menu-item sidebar-dropdown @if (in_array($current_url, $setup_section_childs)) active @endif">
                        <a href="javascript:void(0)">
                            <i class="menu-icon las la-terminal"></i>
                            <span class="menu-title">{{ __('Setup Section') }}</span>
                        </a>
                        <ul class="sidebar-submenu">
                            <li class="sidebar-menu-item">
                                <a href="{{ setRoute('admin.setup.sections.section', 'banner') }}"
                                    class="nav-link @if ($current_url == setRoute('admin.setup.sections.section', 'banner')) active @endif">
                                    <i class="menu-icon las la-ellipsis-h"></i>
                                    <span class="menu-title">{{ __('Banner Section') }}</span>
                                </a>
                                {{-- <a href="{{ setRoute('admin.setup.sections.section','brand') }}" class="nav-link @if ($current_url == setRoute('admin.setup.sections.section', 'brand')) active @endif">
                                    <i class="menu-icon las la-ellipsis-h"></i>
                                    <span class="menu-title">{{ __("Brand Section") }}</span>
                                </a> --}}
                                <a href="{{ setRoute('admin.setup.sections.section', 'find-car') }}"
                                    class="nav-link @if ($current_url == setRoute('admin.setup.sections.section', 'find-car')) active @endif">
                                    <i class="menu-icon las la-ellipsis-h"></i>
                                    <span class="menu-title">{{ __('Find Car Section') }}</span>
                                </a>
                                <a href="{{ setRoute('admin.setup.sections.section', 'about-us') }}"
                                    class="nav-link @if ($current_url == setRoute('admin.setup.sections.section', 'about-us')) active @endif">
                                    <i class="menu-icon las la-ellipsis-h"></i>
                                    <span class="menu-title">{{ __('About Us Section') }}</span>
                                </a>
                                <a href="{{ setRoute('admin.setup.sections.section', 'features') }}"
                                    class="nav-link @if ($current_url == setRoute('admin.setup.sections.section', 'features')) active @endif">
                                    <i class="menu-icon las la-ellipsis-h"></i>
                                    <span class="menu-title">{{ __('Features Section') }}</span>
                                </a>
                                <a href="{{ setRoute('admin.setup.sections.section', 'security') }}"
                                    class="nav-link @if ($current_url == setRoute('admin.setup.sections.section', 'security')) active @endif">
                                    <i class="menu-icon las la-ellipsis-h"></i>
                                    <span class="menu-title">{{ __('Security Section') }}</span>
                                </a>
                                <a href="{{ setRoute('admin.setup.sections.section', 'statistics') }}"
                                    class="nav-link @if ($current_url == setRoute('admin.setup.sections.section', 'statistics')) active @endif">
                                    <i class="menu-icon las la-ellipsis-h"></i>
                                    <span class="menu-title">{{ __('Statistics Section') }}</span>
                                </a>
                                <a href="{{ setRoute('admin.setup.sections.section', 'chooseUs') }}"
                                    class="nav-link @if ($current_url == setRoute('admin.setup.sections.section', 'chooseUs')) active @endif">
                                    <i class="menu-icon las la-ellipsis-h"></i>
                                    <span class="menu-title">{{ __('Choose Us Section') }}</span>
                                </a>
                                <a href="{{ setRoute('admin.setup.sections.section', 'app') }}"
                                    class="nav-link @if ($current_url == setRoute('admin.setup.sections.section', 'app')) active @endif">
                                    <i class="menu-icon las la-ellipsis-h"></i>
                                    <span class="menu-title">{{ __('App Section') }}</span>
                                </a>
                                <a href="{{ setRoute('admin.setup.sections.section', 'vendor-safety') }}"
                                    class="nav-link @if ($current_url == setRoute('admin.setup.sections.section', 'vendor-safety')) active @endif">
                                    <i class="menu-icon las la-ellipsis-h"></i>
                                    <span class="menu-title">{{ __('Vendor Safety Section') }}</span>
                                </a>
                                <a href="{{ setRoute('admin.setup.sections.section', 'vendor-require') }}"
                                    class="nav-link @if ($current_url == setRoute('admin.setup.sections.section', 'vendor-require')) active @endif">
                                    <i class="menu-icon las la-ellipsis-h"></i>
                                    <span class="menu-title">{{ __('Vendor Requires Section') }}</span>
                                </a>
                                <a href="{{ setRoute('admin.setup.sections.section', 'faq') }}"
                                    class="nav-link @if ($current_url == setRoute('admin.setup.sections.section', 'faq')) active @endif">
                                    <i class="menu-icon las la-ellipsis-h"></i>
                                    <span class="menu-title">{{ __('FAQ Section') }}</span>
                                </a>
                                <a href="{{ setRoute('admin.setup.sections.section', 'services') }}"
                                    class="nav-link @if ($current_url == setRoute('admin.setup.sections.section', 'services')) active @endif">
                                    <i class="menu-icon las la-ellipsis-h"></i>
                                    <span class="menu-title">{{ __('Services Section') }}</span>
                                </a>
                                {{-- <a href="{{ setRoute('admin.setup.sections.section','feature') }}" class="nav-link @if ($current_url == setRoute('admin.setup.sections.section', 'feature')) active @endif">
                                    <i class="menu-icon las la-ellipsis-h"></i>
                                    <span class="menu-title">{{ __("Feature Section") }}</span>
                                </a> --}}
                                {{-- <a href="{{ setRoute('admin.setup.sections.section','clients-feedback') }}" class="nav-link @if ($current_url == setRoute('admin.setup.sections.section', 'clients-feedback')) active @endif">
                                    <i class="menu-icon las la-ellipsis-h"></i>
                                    <span class="menu-title">{{ __("Clients Feedback") }}</span>
                                </a> --}}
                                <a href="{{ setRoute('admin.setup.sections.section', 'announcement') }}"
                                    class="nav-link @if ($current_url == setRoute('admin.setup.sections.section', 'announcement')) active @endif">
                                    <i class="menu-icon las la-ellipsis-h"></i>
                                    <span class="menu-title">{{ __('Blogs') }}</span>
                                </a>
                                {{-- <a href="{{ setRoute('admin.setup.sections.section','how-it-work') }}" class="nav-link @if ($current_url == setRoute('admin.setup.sections.section', 'how-it-work')) active @endif">
                                    <i class="menu-icon las la-ellipsis-h"></i>
                                    <span class="menu-title">{{ __("How It Work Section") }}</span>
                                </a> --}}
                                {{-- <a href="{{ setRoute('admin.setup.sections.section','about-page') }}" class="nav-link @if ($current_url == setRoute('admin.setup.sections.section', 'about-page')) active @endif">
                                    <i class="menu-icon las la-ellipsis-h"></i>
                                    <span class="menu-title">{{ __("About Page Section") }}</span>
                                </a> --}}
                                <a href="{{ setRoute('admin.setup.sections.section', 'contact-us') }}"
                                    class="nav-link @if ($current_url == setRoute('admin.setup.sections.section', 'contact-us')) active @endif">
                                    <i class="menu-icon las la-ellipsis-h"></i>
                                    <span class="menu-title">{{ __('Contact US Section') }}</span>
                                </a>
                                <a href="{{ setRoute('admin.setup.sections.section', 'footer') }}"
                                    class="nav-link @if ($current_url == setRoute('admin.setup.sections.section', 'footer')) active @endif">
                                    <i class="menu-icon las la-ellipsis-h"></i>
                                    <span class="menu-title">{{ __('Footer Section') }}</span>
                                </a>
                                <a href="{{ setRoute('admin.setup.sections.section', 'auth') }}"
                                    class="nav-link @if ($current_url == setRoute('admin.setup.sections.section', 'auth')) active @endif">
                                    <i class="menu-icon las la-ellipsis-h"></i>
                                    <span class="menu-title">{{ __('Auth Section') }}</span>
                                </a>
                                <a href="{{ setRoute('admin.setup.sections.section', 'vendor-auth') }}"
                                    class="nav-link @if ($current_url == setRoute('admin.setup.sections.section', 'vendor-auth')) active @endif">
                                    <i class="menu-icon las la-ellipsis-h"></i>
                                    <span class="menu-title">{{ __('Vendor Auth Section') }}</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                @endif

                @include('admin.components.side-nav.link', [
                    'route' => 'admin.setup.pages.index',
                    'title' => __('Setup Pages'),
                    'icon' => 'menu-icon las la-file-alt',
                ])

                @include('admin.components.side-nav.link', [
                    'route' => 'admin.extensions.index',
                    'title' => __('Extensions'),
                    'icon' => 'menu-icon las la-puzzle-piece',
                ])

                @include('admin.components.side-nav.link', [
                    'route' => 'admin.useful.links.index',
                    'title' => __('Useful Links'),
                    'icon' => 'menu-icon las la-link',
                ])

                @if (admin_permission_by_name('admin.payment.gateway.view'))
                    <li class="sidebar-menu-header">{{ __('Payment Methods') }}</li>
                    @php
                        $payment_add_money_childs = [
                            setRoute('admin.payment.gateway.view', ['add-money', 'automatic']),
                            setRoute('admin.payment.gateway.view', ['add-money', 'manual']),
                        ];
                    @endphp
                    <li class="sidebar-menu-item sidebar-dropdown @if (in_array($current_url, $payment_add_money_childs)) active @endif">
                        <a href="javascript:void(0)">
                            <i class="menu-icon las la-funnel-dollar"></i>
                            <span class="menu-title">{{ __('Payment Methods') }}</span>
                        </a>
                        <ul class="sidebar-submenu">
                            <li class="sidebar-menu-item">
                                <a href="{{ setRoute('admin.payment.gateway.view', ['add-money', 'automatic']) }}"
                                    class="nav-link @if ($current_url == setRoute('admin.payment.gateway.view', ['add-money', 'automatic'])) active @endif">
                                    <i class="menu-icon las la-ellipsis-h"></i>
                                    <span class="menu-title">{{ __('Automatic') }}</span>
                                </a>
                                <a href="{{ setRoute('admin.payment.gateway.view', ['add-money', 'manual']) }}"
                                    class="nav-link @if ($current_url == setRoute('admin.payment.gateway.view', ['add-money', 'manual'])) active @endif">
                                    <i class="menu-icon las la-ellipsis-h"></i>
                                    <span class="menu-title">{{ __('Manual') }}</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="sidebar-menu-item @if ($current_url == setRoute('admin.payment.gateway.view', ['money-out', 'manual'])) active @endif">
                        <a href="{{ setRoute('admin.payment.gateway.view', ['money-out', 'manual']) }}">
                            <i class="menu-icon las la-print"></i>
                            <span class="menu-title">{{ __('Money Out') }}</span>
                        </a>
                    </li>
                @endif

                {{-- Notifications --}}
                @include('admin.components.side-nav.link-group', [
                    'group_title' => __('Notification'),
                    'group_links' => [
                        'dropdown' => [
                            [
                                'title' => __('Push Notification'),
                                'icon' => 'menu-icon las la-bell',
                                'links' => [
                                    [
                                        'title' => __('Setup Notification'),
                                        'route' => 'admin.push.notification.config',
                                    ],
                                    [
                                        'title' => __('Send Notification'),
                                        'route' => 'admin.push.notification.index',
                                    ],
                                ],
                            ],
                        ],
                        [
                            'title' => __('Contact Messages'),
                            'route' => 'admin.contact.messages.index',
                            'icon' => 'menu-icon las la-sms',
                        ],
                    ],
                ])

                @php
                    $bonus_routes = ['admin.cookie.index', 'admin.server.info.index', 'admin.cache.clear'];
                @endphp

                @if (admin_permission_by_name_array($bonus_routes))
                    <li class="sidebar-menu-header">{{ __('Bonus') }}</li>
                @endif

                @include('admin.components.side-nav.link', [
                    'route' => 'admin.cookie.index',
                    'title' => __('GDPR Cookie'),
                    'icon' => 'menu-icon las la-cookie-bite',
                ])

                @include('admin.components.side-nav.link', [
                    'route' => 'admin.server.info.index',
                    'title' => __('Server Info'),
                    'icon' => 'menu-icon las la-sitemap',
                ])

                @include('admin.components.side-nav.link', [
                    'route' => 'admin.cache.clear',
                    'title' => __('Clear Cache'),
                    'icon' => 'menu-icon las la-broom',
                ])
            </ul>
        </div>
    </div>
</div>
