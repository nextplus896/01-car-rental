@extends('user.layouts.master')

@section('breadcrumb')
    @include('user.components.breadcrumb', [
        'breadcrumbs' => [
            [
                'name' => __('Dashboard'),
                'url' => setRoute('user.dashboard'),
            ],
        ],
        'active' => __('Dashboard'),
    ])
@endsection

@section('content')
    <div class="dashboard-card-area pt-3">
        <div class="row mb-20-none">
            <div class="col-xxl-3 col-xl-4 col-lg-4 col-md-4 col-sm-6 mb-20">
                <div class="dashboard-card-item">
                    <div class="card-title">
                        <span class="title">{{ __('Total Booking Request') }}</span>
                        <h4 class="sub-title text--base">
                            {{ $total_booking }}
                        </h4>
                    </div>
                    <div class="card-icon">
                        <i class="las la-dollar-sign"></i>
                    </div>
                </div>
            </div>
            <div class="col-xxl-3 col-xl-4 col-lg-4 col-md-4 col-sm-6 mb-20">
                <div class="dashboard-card-item">
                    <div class="card-title">
                        <span class="title">{{ __('Total Complete Trip') }}</span>
                        <h4 class="sub-title text--base">{{ $ride_complete }}</h4>
                    </div>
                    <div class="card-icon">
                        <i class="menu-icon las la-history"></i>
                    </div>
                </div>
            </div>
            <div class="col-xxl-3 col-xl-4 col-lg-4 col-md-4 col-sm-6 mb-20">
                <div class="dashboard-card-item">
                    <div class="card-title">
                        <span class="title">{{ __('Total Round Trip') }}</span>
                        <h4 class="sub-title text--base">{{ $round_trips }}</h4>
                    </div>
                    <div class="card-icon">
                        <i class="las fa-redo"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="dashboard-chart pt-60">
        <div class="dashboard-header-wrapper">
            <h3 class="title">{{ __('Car Booking Overview') }}</h3>
        </div>
        <div class="chart-container">
            <div id="chart" class="chart" data-chart_one_data="{{ json_encode($chartData['chart_one_data']) }}"></div>
        </div>
    </div>


    <div class="booking-history pt-60">
        <div class="title-header pb-20">
            <h3 class="title">{{ __('Booking List') }}</h3>
        </div>
        <div class="dashboard-list-wrapper">
            @forelse ($bookings ?? [] as $value)
                <div class="dashboard-list-item-wrapper">
                    <div class="dashboard-list-item sent">
                        <div class="dashboard-list-left">
                            <div class="dashboard-list-user-wrapper">
                                <div class="dashboard-list-user-icon">
                                    <img src="{{ get_image($value->cars->image ?? '', 'site-section') ?? '' }}"
                                        alt="user">
                                </div>
                                <div class="dashboard-list-user-content">
                                    <h4 class="title">{{ $value->cars->car_model }}</h4>
                                    <span class="sub-title text--danger">
                                        @if ($value->status === 1)
                                            <span class="badge badge--warning ms-2">{{ __('Pending') }}</span>
                                        @elseif ($value->status === 2)
                                            <span class="badge badge--success ms-2">{{ __('On Going') }}</span>
                                        @elseif ($value->status === 3)
                                            <span class="badge badge--success ms-2">{{ __('Complete') }}</span>
                                        @elseif ($value->status === 4)
                                            <span class="badge badge--danger ms-2">{{ __('Reject') }}</span>
                                        @endif
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="dashboard-list-right">
                            <h4 class="main-money text--base">{{ $value->created_at->toDateString() }}</h4>
                        </div>
                    </div>
                    <div class="preview-list-wrapper">
                        <div class="preview-list-item">
                            <div class="preview-list-left">
                                <div class="preview-list-user-wrapper">
                                    <div class="preview-list-user-icon">
                                        <i class="las la-user"></i>
                                    </div>
                                    <div class="preview-list-user-content">
                                        <span>{{ __('Pick-up location') }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="preview-list-right">
                                <span>{{ $value->location }}</span>
                            </div>
                        </div>
                        <div class="preview-list-item">
                            <div class="preview-list-left">
                                <div class="preview-list-user-wrapper">
                                    <div class="preview-list-user-icon">
                                        <i class="las la-envelope"></i>
                                    </div>
                                    <div class="preview-list-user-content">
                                        <span>{{ __('Destination') }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="preview-list-right">
                                <span>{{ $value->destination }}</span>
                            </div>
                        </div>
                        <div class="preview-list-item">
                            <div class="preview-list-left">
                                <div class="preview-list-user-wrapper">
                                    <div class="preview-list-user-icon">
                                        <i class="las la-calendar"></i>
                                    </div>
                                    <div class="preview-list-user-content">
                                        <span>{{ __('Pick-up date') }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="preview-list-right">
                                <span>{{ $value->pickup_date }}</span>
                            </div>
                        </div>
                        <div class="preview-list-item">
                            <div class="preview-list-left">
                                <div class="preview-list-user-wrapper">
                                    <div class="preview-list-user-icon">
                                        <i class="las la-history"></i>
                                    </div>
                                    <div class="preview-list-user-content">
                                        <span>{{ __('Pick-up time') }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="preview-list-right">
                                <span>{{ $value->pickup_time }}</span>
                            </div>
                        </div>
                        @if ($value->round_pickup_date)
                            <div class="preview-list-item">
                                <div class="preview-list-left">
                                    <div class="preview-list-user-wrapper">
                                        <div class="preview-list-user-icon">
                                            <i class="las la-calendar"></i>
                                        </div>
                                        <div class="preview-list-user-content">
                                            <span>{{ __('Round Pick-up date') }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="preview-list-right">
                                    <span>{{ $value->round_pickup_date }}</span>
                                </div>
                            </div>
                        @endif
                        @if ($value->round_pickup_time)
                            <div class="preview-list-item">
                                <div class="preview-list-left">
                                    <div class="preview-list-user-wrapper">
                                        <div class="preview-list-user-icon">
                                            <i class="las la-history"></i>
                                        </div>
                                        <div class="preview-list-user-content">
                                            <span>{{ __('Round Pick-up time') }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="preview-list-right">
                                    <span>{{ $value->round_pickup_time }}</span>
                                </div>
                            </div>
                        @endif
                        <div class="preview-list-item">
                            <div class="preview-list-left">
                                <div class="preview-list-user-wrapper">
                                    <div class="preview-list-user-icon">
                                        <i class="las la-car-side"></i>
                                    </div>
                                    <div class="preview-list-user-content">
                                        <span>{{ __('Car Model') }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="preview-list-right">
                                <span">{{ $value->cars->car_model }}</span>
                            </div>
                        </div>
                        <div class="preview-list-item">
                            <div class="preview-list-left">
                                <div class="preview-list-user-wrapper">
                                    <div class="preview-list-user-icon">
                                        <i class="las la-car"></i>
                                    </div>
                                    <div class="preview-list-user-content">
                                        <span>{{ __('Car Number') }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="preview-list-right">
                                <span">{{ $value->cars->car_number }}</span>
                            </div>
                        </div>
                        <div class="preview-list-item">
                            <div class="preview-list-left">
                                <div class="preview-list-user-wrapper">
                                    <div class="preview-list-user-icon">
                                        <i class="las la-battery-half"></i>
                                    </div>
                                    <div class="preview-list-user-content">
                                        <span>{{ __('Fees') }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="preview-list-right">
                                <span">{{ get_amount($value->cars->fees) }} {{ get_default_currency_code() }}</span>
                            </div>
                        </div>
                        <div class="preview-list-item">
                            <div class="preview-list-left">
                                <div class="preview-list-user-wrapper">
                                    <div class="preview-list-user-icon">
                                        <i class="las la-battery-full"></i>
                                    </div>
                                    <div class="preview-list-user-content">
                                        <span>{{ __('Rate') }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="preview-list-right">
                                <span">{{ get_amount($value->cars->fees) }} {{ get_default_currency_code() }} / 1
                                    {{ __('km') }}</span>
                            </div>
                        </div>
                        <div class="preview-list-item">
                            <div class="preview-list-left">
                                <div class="preview-list-user-wrapper">
                                    <div class="preview-list-user-icon">
                                        <i class="las la-edit"></i>
                                    </div>
                                    <div class="preview-list-user-content">
                                        <span>{{ __('Total Amount') }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="preview-list-right">
                                <span">{{ get_amount($value->amount) }} {{ get_default_currency_code() }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
            <div class="dashboard-list-item-wrapper text-center">
                <div class="dashboard-list-item sent">
                </div>
                <h4>{{ __('No Bookings Found!') }}</h4>
            </div>
            @endforelse
        </div>
    </div>
@endsection

@push('script')
    <script>
        var chart = $('#chart');
        var chart_one_data = chart.data('chart_one_data');
        var month = chart.data('chart_one_data');
        var year = chart.data('chart_one_data')
        console.log(month.month);
        console.log(chart_one_data.booking_data);
        var options = {
            series: [{
                name: 'Inflation',
                color: "#fff",
                data: chart_one_data.booking_data,
            }],
            chart: {
                height: 350,
                type: 'bar',
            },
            plotOptions: {
                bar: {
                    borderRadius: 10,
                    dataLabels: {
                        position: 'top', // top, center, bottom
                    },
                }
            },
            dataLabels: {
                enabled: true,
                formatter: function(val) {
                    return val;
                },
                offsetY: -20,
                style: {
                    fontSize: '12px',
                    colors: ["#fff"]
                }
            },

            xaxis: {
                categories: month.month,
                position: 'top',
                axisBorder: {
                    show: false
                },
                axisTicks: {
                    show: false
                },
                crosshairs: {
                    fill: {
                        type: 'gradient',
                        gradient: {
                            colorFrom: '#15b887',
                            colorTo: '#15b887',
                            stops: [0, 100],
                            opacityFrom: 0.4,
                            opacityTo: 0.5,
                        }
                    }
                },
                tooltip: {
                    enabled: true,
                }
            },
            yaxis: {
                axisBorder: {
                    show: false
                },
                axisTicks: {
                    show: false,
                },
                labels: {
                    show: false,
                    formatter: function(val) {
                        return val;
                    }
                }

            },
            title: {
                text: year.year,
                floating: true,
                offsetY: 330,
                align: 'center',
                style: {
                    color: '#fff'
                }
            }
        };

        var chart = new ApexCharts(document.querySelector("#chart"), options);
        chart.render();
    </script>
@endpush
