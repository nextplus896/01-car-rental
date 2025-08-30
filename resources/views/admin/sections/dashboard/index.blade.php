@extends('admin.layouts.master')

@push('css')
@endpush

@section('page-title')
    @include('admin.components.page-title', ['title' => __($page_title)])
@endsection

@section('breadcrumb')
    @include('admin.components.breadcrumb', [
        'breadcrumbs' => [
            [
                'name' => __('Dashboard'),
                'url' => setRoute('admin.dashboard'),
            ],
        ],
        'active' => __('Dashboard'),
    ])
@endsection

@section('content')
    <div class="dashboard-area">
        <div class="dashboard-item-area">
            <div class="row">
                <div class="col-xxxl-4 col-xxl-3 col-xl-3 col-lg-6 col-md-6 col-sm-12 mb-15">
                    <div class="dashbord-item">
                        <div class="dashboard-content">
                            <div class="left">
                                <h6 class="title">{{ __('Total Users') }}</h6>
                                <div class="user-info">
                                    <h2 class="user-count">{{ $users }}</h2>
                                </div>
                                <div class="user-badge">
                                    <span class="badge badge--success">{{ __('Active') }} {{ $active_users }}</span>
                                    <span class="badge badge--info">{{ __('Banned') }} {{ $banned_users }}</span>
                                </div>
                            </div>
                            <div class="right">
                                <div class="chart" id="chart6"
                                    data-percent="{{ $users != 0 ? intval(($active_users / $users) * 100) : '0' }}">
                                    <span>
                                        @if ($users != 0)
                                            {{ intval(($active_users / $users) * 100) }}%
                                        @else
                                            0%
                                        @endif
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xxxl-4 col-xxl-3 col-xl-3 col-lg-6 col-md-6 col-sm-12 mb-15">
                    <div class="dashbord-item">
                        <div class="dashboard-content">
                            <div class="left">
                                <h6 class="title">{{ __('Total Payments') }}</h6>
                                <div class="user-info">
                                    <h2 class="user-count">{{ $payment_request }}</h2>
                                </div>
                                <div class="user-badge">
                                    <span class="badge badge--info">{{ __('Success') }}
                                        {{ $payment_success_request }}</span>
                                    <span class="badge badge--warning">{{ __('Pending') }}
                                        {{ $payment_pending_request }}</span>
                                </div>
                            </div>
                            <div class="right">
                                <div class="chart" id="chart7"
                                    data-percent="{{ $payment_request != 0 ? intval(($payment_success_request / $payment_request) * 100) : '0' }}">
                                    <span>
                                        @if ($payment_request != 0)
                                            {{ intval(($payment_success_request / $payment_request) * 100) }}%
                                        @else
                                            0%
                                        @endif
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xxxl-4 col-xxl-3 col-xl-3 col-lg-6 col-md-6 col-sm-12 mb-15">
                    <div class="dashbord-item">
                        <div class="dashboard-content">
                            <div class="left">
                                <h6 class="title">{{ __('Total Bookings') }}</h6>
                                <div class="user-info">
                                    <h2 class="user-count">{{ $total_booking }}</h2>
                                </div>
                                <div class="user-badge">
                                    <span class="badge badge--info">{{ __('Complete') }} {{ $complete_booking }}</span>
                                    <span class="badge badge--danger">{{ __('Reject') }} {{ $reject_booking }}</span>
                                    <span class="badge badge--warning">{{ __('Pending') }} {{ $pending_booking }}</span>
                                </div>
                            </div>
                            <div class="right">
                                <div class="chart" id="chart8"
                                    data-percent="{{ $total_booking != 0 ? intval(($complete_booking / $total_booking) * 100) : '0' }}">
                                    <span>
                                        @if ($total_booking != 0)
                                            {{ intval(($complete_booking / $total_booking) * 100) }}%
                                        @else
                                            0%
                                        @endif
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xxxl-4 col-xxl-3 col-xl-3 col-lg-6 col-md-6 col-sm-12 mb-15">
                    <div class="dashbord-item">
                        <div class="dashboard-content">
                            <div class="left">
                                <h6 class="title">{{ __('Total Support Ticket') }}</h6>
                                <div class="user-info">
                                    <h2 class="user-count">{{ $solved_tickets + $active_tickets + $pending_tickets }}</h2>
                                </div>
                                <div class="user-badge">
                                    <span class="badge badge--info">{{ __('Active Ticket') }} {{ $solved_tickets }}</span>
                                    <span class="badge badge--warning">{{ __('Pending Ticket') }}
                                        {{ $active_tickets }}</span>
                                    <span class="badge badge--warning">{{ __('Complete Ticket') }}
                                        {{ $pending_tickets }}</span>
                                </div>
                            </div>
                            <div class="right">
                                <div class="chart" id="chart9"
                                    data-percent="{{ $total_ticket != 0 ? intval(($pending_tickets / $total_ticket) * 100) : '0' }}">
                                    <span>
                                        @if ($total_ticket != 0)
                                            {{ intval(($pending_tickets / $total_ticket) * 100) }}%
                                        @else
                                            0%
                                        @endif
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xxxl-4 col-xxl-3 col-xl-3 col-lg-6 col-md-6 col-sm-12 mb-15">
                    <div class="dashbord-item">
                        <div class="dashboard-content">
                            <div class="left">
                                <h6 class="title">{{ __('Total Vendor') }}</h6>
                                <div class="user-info">
                                    <h2 class="user-count">{{ $vendors }}</h2>
                                </div>
                                <div class="user-badge">
                                    <span class="badge badge--info">{{ __('Active') }} {{ $active_vendors }}</span>
                                    <span class="badge badge--danger">{{ __('Banned') }} {{ $banned_vendors }}</span>
                                    <span class="badge badge--warning">{{ __('Pending') }}
                                        {{ $unverified_vendors }}</span>
                                </div>
                            </div>
                            <div class="right">
                                <div class="chart" id="chart10"
                                    data-percent="{{ $vendors != 0 ? intval(($active_vendors / $vendors) * 100) : '0' }}">
                                    <span>
                                        @if ($vendors != 0)
                                            {{ intval(($active_vendors / $vendors) * 100) }}%
                                        @else
                                            0%
                                        @endif
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xxxl-4 col-xxl-3 col-xl-3 col-lg-6 col-md-6 col-sm-12 mb-15">
                    <div class="dashbord-item">
                        <div class="dashboard-content">
                            <div class="left">
                                <h6 class="title">{{ __('Admin Profit') }}</h6>
                                <div class="user-info">
                                    <h2 class="user-count">
                                        {{ get_default_currency_symbol() }}{{ get_amount($admin_profit) }}</h2>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="chart-area mt-15">
        <div class="row mb-15-none">
            <div class="col-xxl-6 col-xl-6 col-lg-6 mb-15">
                <div class="chart-wrapper">
                    <div class="chart-area-header">
                        <h5 class="title">{{ __('Monthly Payment Chart') }}</h5>
                    </div>
                    <div class="chart-container">
                        <div id="chart1" data-chart_one_data="{{ json_encode($chartData['chart_one_data']) }}"
                            data-month_day="{{ json_encode($chartData['month_day']) }}" class="sales-chart"></div>
                    </div>
                </div>
            </div>
            <div class="col-xxl-6 col-xl-6 col-lg-6 mb-15">
                <div class="chart-wrapper">
                    <div class="chart-area-header">
                        <h5 class="title">{{ __('Monthly Money Out Chart') }}</h5>
                    </div>
                    <div class="chart-container">
                        <div id="chart3" data-chart_three_data="{{ json_encode($chartData['chart_three_data']) }}"
                            data-month_day="{{ json_encode($chartData['month_day']) }}" class="order-chart"></div>
                    </div>
                </div>
            </div>
            <div class="col-xxl-6 col-xxl-6 col-xl-6 col-lg-6 mb-15">
                <div class="chart-wrapper">
                    <div class="chart-area-header">
                        <h5 class="title">{{ __('User Analytics') }}</h5>
                    </div>
                    <div class="chart-container">
                        <div id="chart4" data-chart_four_data="{{ json_encode($chartData['chart_four_data']) }}"
                            class="balance-chart"></div>
                    </div>
                    <div class="chart-area-footer">
                        <div class="chart-btn">
                            <a href="{{ setRoute('admin.users.index') }}"
                                class="btn--base w-100">{{ __('View Users') }}</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xxl-6 col-xxl-6 col-xl-6 col-lg-6 mb-15">
                <div class="chart-wrapper">
                    <div class="chart-area-header">
                        <h5 class="title">{{ __('Vendor Analytics') }}</h5>
                    </div>
                    <div class="chart-container">
                        <div id="chart5" data-chart_five_data="{{ json_encode($chartData['chart_five_data']) }}"
                            class="balance-chart"></div>
                    </div>
                    <div class="chart-area-footer">
                        <div class="chart-btn">
                            <a href="{{ setRoute('admin.vendor.index') }}"
                                class="btn--base w-100">{{ __('View Vendor') }}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="table-area mt-15">
        <div class="table-wrapper">
            <div class="table-header">
                <h5 class="title">{{ __('Latest Money Withdraw') }}</h5>
            </div>
            <div class="table-responsive">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th></th>
                            <th>{{ __('Full Name') }}</th>
                            <th>{{ __('Email') }}</th>
                            <th>{{ __('Username') }}</th>
                            <th>{{ __('Phone') }}</th>
                            <th>{{ __('Amount') }}</th>
                            <th>{{ __('Gateway') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th>{{ __('Time') }}</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($transactions  as $key => $item)
                            <tr>
                                <td>
                                    <ul class="user-list">
                                        <li><img src="{{ get_image($item->user->image ?? "","user-profile") }}" alt="user"></li>
                                    </ul>
                                </td>
                                <td>{{ $item->vendor->firstname }} {{ $item->vendor->lastname }}</td>
                                <td>{{ $item->vendor->email }}</td>
                                <td>{{ $item->vendor->username }}</td>
                                <td>{{ $item->vendor->full_mobile ?? '' }}</td>
                                <td>{{ get_amount($item->receive_amount,$item->creator_wallet->currency->code) }}</td>
                                <td><span class="text--info">{{ $item->gateway_currency->gateway->name }}</span></td>
                                <td>
                                    <span class="{{ $item->stringStatus->class }}">{{ $item->stringStatus->value }}</span>
                                </td>
                                <td>{{ $item->created_at->format('d-m-y h:i:s A') }}</td>
                                <td>
                                    @if ($item->status == 1)
                                        <button type="button" class="btn btn--base bg--success"><i
                                                class="las la-check-circle"></i></button>
                                    @elseif($item->status == 4)
                                    <button type="button" class="btn btn--base bg--danger"><i
                                        class="las la-times-circle"></i></button>
                                    @endif
                                    @include('admin.components.link.custom',[
                                        'href'          => setRoute('admin.money.out.details', $item->id),
                                        'class'         => "btn btn--base modal-btn",
                                        'icon'          => "las la-expand",
                                        'permission'    => "admin.money.out.details",
                                    ])
                                </td>
                            </tr>
                        @empty
                        @include('admin.components.alerts.empty',['colspan' => 10])
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        // apex-chart
        var chart1 = $('#chart1');
        var chart_one_data = chart1.data('chart_one_data');
        console.log(chart_one_data);
        var month_day = chart1.data('month_day');
        var options = {
            series: [{
                name: '{{ __('Pending') }}',
                color: "#5A5278",
                data: chart_one_data.pending_data
            }, {
                name: '{{ __('Completed') }}',
                color: "#6F6593",
                data: chart_one_data.success_data
            }, {
                name: '{{ __('Canceled') }}',
                color: "#8075AA",
                data: chart_one_data.canceled_data
            }, {
                name: '{{ __('Hold') }}',
                color: "#A192D9",
                data: chart_one_data.hold_data
            }],
            chart: {
                type: 'bar',
                height: 350,
                stacked: true,
                toolbar: {
                    show: false
                },
                zoom: {
                    enabled: true
                }
            },
            responsive: [{
                breakpoint: 480,
                options: {
                    legend: {
                        position: 'bottom',
                        offsetX: -10,
                        offsetY: 0
                    }
                }
            }],
            plotOptions: {
                bar: {
                    horizontal: false,
                    borderRadius: 10
                },
            },
            xaxis: {
                type: 'datetime',
                categories: month_day,
            },
            legend: {
                position: 'bottom',
                offsetX: 40
            },
            fill: {
                opacity: 1
            }
        };

        var chart = new ApexCharts(document.querySelector("#chart1"), options);
        chart.render();

        var chart3 = $('#chart3');
        var chart_three_data = chart3.data('chart_three_data');
        var month_day = chart3.data('month_day');
        var options = {
            series: [{
                name: '{{ __('Pending') }}',
                color: "#5A5278",
                data: chart_three_data.pending_data
            }, {
                name: '{{ __('Completed') }}',
                color: "#6F6593",
                data: chart_three_data.success_data
            }, {
                name: '{{ __('Canceled') }}',
                color: "#8075AA",
                data: chart_three_data.canceled_data
            }, {
                name: '{{ __('Hold') }}',
                color: "#A192D9",
                data: chart_three_data.hold_data
            }],
            chart: {
                type: 'bar',
                height: 350,
                stacked: true,
                toolbar: {
                    show: false
                },
                zoom: {
                    enabled: true
                }
            },
            responsive: [{
                breakpoint: 480,
                options: {
                    legend: {
                        position: 'bottom',
                        offsetX: -10,
                        offsetY: 0
                    }
                }
            }],
            plotOptions: {
                bar: {
                    horizontal: false,
                    borderRadius: 10
                },
            },
            xaxis: {
                type: 'datetime',
                categories: month_day,
            },
            legend: {
                position: 'bottom',
                offsetX: 40
            },
            fill: {
                opacity: 1
            }
        };

        var chart = new ApexCharts(document.querySelector("#chart3"), options);
        chart.render();

        var chart4 = $('#chart4');
        var chart_four_data = chart4.data('chart_four_data');
        var options = {
            series: chart_four_data,
            chart: {
                width: 350,
                type: 'pie'
            },
            colors: ['#5A5278', '#6F6593', '#8075AA', '#A192D9'],
            labels: ['{{ __('Active') }}', '{{ __('Unverified') }}', '{{ __('Banned') }}', '{{ __('All') }}'],
            responsive: [{
                breakpoint: 1480,
                options: {
                    chart: {
                        width: 280
                    },
                    legend: {
                        position: 'bottom'
                    }
                },
                breakpoint: 1199,
                options: {
                    chart: {
                        width: 380
                    },
                    legend: {
                        position: 'bottom'
                    }
                },
                breakpoint: 575,
                options: {
                    chart: {
                        width: 280
                    },
                    legend: {
                        position: 'bottom'
                    }
                }
            }],
            legend: {
                position: 'bottom'
            },
        };

        var chart = new ApexCharts(document.querySelector("#chart4"), options);
        chart.render();

        var chart5 = $('#chart5');
        var chart_five_data = chart5.data('chart_five_data');
        var options = {
            series: chart_five_data,
            chart: {
                width: 350,
                type: 'pie'
            },
            colors: ['#5A5278', '#6F6593', '#8075AA', '#A192D9'],
            labels: ['{{ __('Active') }}', '{{ __('Unverified') }}', '{{ __('Banned') }}', '{{ __('All') }}'],
            responsive: [{
                breakpoint: 1480,
                options: {
                    chart: {
                        width: 280
                    },
                    legend: {
                        position: 'bottom'
                    }
                },
                breakpoint: 1199,
                options: {
                    chart: {
                        width: 380
                    },
                    legend: {
                        position: 'bottom'
                    }
                },
                breakpoint: 575,
                options: {
                    chart: {
                        width: 280
                    },
                    legend: {
                        position: 'bottom'
                    }
                }
            }],
            legend: {
                position: 'bottom'
            },
        };

        var chart = new ApexCharts(document.querySelector("#chart5"), options);
        chart.render();

        // pie-chart
        $(function() {
            $('#chart6').easyPieChart({
                size: 80,
                barColor: '#f05050',
                scaleColor: false,
                lineWidth: 5,
                trackColor: '#f050505a',
                lineCap: 'circle',
                animate: 3000
            });
        });

        $(function() {
            $('#chart7').easyPieChart({
                size: 80,
                barColor: '#10c469',
                scaleColor: false,
                lineWidth: 5,
                trackColor: '#10c4695a',
                lineCap: 'circle',
                animate: 3000
            });
        });

        $(function() {
            $('#chart8').easyPieChart({
                size: 80,
                barColor: '#ffbd4a',
                scaleColor: false,
                lineWidth: 5,
                trackColor: '#ffbd4a5a',
                lineCap: 'circle',
                animate: 3000
            });
        });

        $(function() {
            $('#chart9').easyPieChart({
                size: 80,
                barColor: '#ff8acc',
                scaleColor: false,
                lineWidth: 5,
                trackColor: '#ff8acc5a',
                lineCap: 'circle',
                animate: 3000
            });
        });

        $(function() {
            $('#chart10').easyPieChart({
                size: 80,
                barColor: '#7367f0',
                scaleColor: false,
                lineWidth: 5,
                trackColor: '#7367f05a',
                lineCap: 'circle',
                animate: 3000
            });
        });
    </script>
@endpush
