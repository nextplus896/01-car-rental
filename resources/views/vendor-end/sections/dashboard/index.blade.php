@extends('vendor-end.layouts.master')

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
                        <span class="title">{{ __('Wallet Balance') }}</span>
                        <h4 class="sub-title text--base">{{ get_amount(auth()->guard('vendor')->user()->wallets->balance) }}
                            <span>{{ get_default_currency_code() }}</span>
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
                        <span class="title">{{ __('Due Payment') }}</span>
                        <h4 class="sub-title text--base">
                            {{ get_amount(auth()->guard('vendor')->user()->wallets->due_payment) }}
                            <span>{{ get_default_currency_code() }}</span>
                        </h4>
                    </div>
                    <div class="card-icon">
                        <i class="las la-dollar-sign"></i>
                    </div>
                    <a class="btn-sm btn-danger due-balance-update-modal">{{ __('Due Payment') }}</a>

                </div>
            </div>
            <div class="col-xxl-3 col-xl-4 col-lg-4 col-md-4 col-sm-6 mb-20">
                <div class="dashboard-card-item">
                    <div class="card-title">
                        <span class="title">{{ __('Complete Ride') }}</span>
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
                        <span class="title">{{ __('Round Trip') }}</span>
                        <h4 class="sub-title text--base">{{ $round_trips }}</h4>
                    </div>
                    <div class="card-icon">
                        <i class="las fa-redo"></i>
                    </div>
                </div>
            </div>
            <div class="col-xxl-3 col-xl-4 col-lg-4 col-md-4 col-sm-6 mb-20">
                <div class="dashboard-card-item bg-overlay  bg_img" data-background="assets/images/element/card-bg.webp">
                    <div class="card-title">
                        <span class="title">{{ __('Booking Reject') }}</span>
                        <h4 class="sub-title text--base">{{ $booking_rejects }}</h4>
                    </div>
                    <div class="card-icon">
                        <i class="las la-spinner"></i>
                    </div>
                </div>
            </div>
            <div class="col-xxl-3 col-xl-4 col-lg-4 col-md-4 col-sm-6 mb-20">
                <div class="dashboard-card-item bg-overlay  bg_img" data-background="assets/images/element/card-bg.webp">
                    <div class="card-title">
                        <span class="title">{{ __('Total Income') }}</span>
                        <h4 class="sub-title text--base">{{ get_amount($total_income,get_default_currency_code()) }}</h4>
                    </div>
                    <div class="card-icon">
                        <i class="las la-dollar-sign"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="dashboard-chart pt-60">
        <div class="dashboard-header-wrapper">
            <h3 class="title">{{ __('Withdraw Overview') }}</h3>
        </div>
        <div class="chart-container">
            <div id="chart" class="chart" data-chart_one_data="{{ json_encode($chartData['chart_one_data']) }}"></div>
        </div>
    </div>

    <div class="dashboard-chart pt-60">
        <div class="dashboard-header-wrapper">
            <h3 class="title">{{ __('Booking Overview') }}</h3>
        </div>
        <div class="chart-container">
            <div class="row pe-0">
                <div class="col-12">
                    <div class="chart">
                        <div class="chart-bg">
                            <div id="chart-two"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="booking-history pt-60">
        <div class="title-header pb-20">
            <h3 class="title">{{ __('Recent Booking Logs') }}</h3>
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
                            <h4 class="main-money text--base">{{ $value->pickup_date }}</h4>
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
                        <div class="preview-list-item">
                            <div class="preview-list-left">
                                <div class="preview-list-user-wrapper">
                                    <div class="preview-list-user-icon">
                                        <i class="las fa-edit"></i>
                                    </div>
                                    <div class="preview-list-user-content">
                                        <span>{{ __('Action') }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="preview-list-right text-center">
                                @if ($value->status === 1)
                                    <a href="{{ setRoute('vendor.booking.reject', $value->id) }}"
                                        class="service-btn-1">{{ __('Reject') }}</a>
                                    <a href="{{ setRoute('vendor.booking.accept', $value->id) }}"
                                        class="service-btn">{{ __('Accept') }}</a>
                                @elseif($value->status === 2)
                                    <a href="{{ setRoute('vendor.booking.complete', $value->id) }}"
                                        class="service-btn">{{ __('Complete') }}</a>
                                @elseif($value->status === 4)
                                    <span class="text-danger">{{ __('Reject') }}</span>
                                @else
                                    <span class="text-success">{{ __('Complete') }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="dashboard-list-item-wrapper text-center">
                    <h4>{{ __('No Bookings Found!') }}</h4>
                </div>
            @endforelse
        </div>
    </div>

    {{-- Modal --}}

    <div id="due-balance-update-modal" class="mfp-hide large">
        <div class="modal-data">
            <div class="modal-header px-0">
                <h5 class="modal-title">{{ __('Pay Due Payment') }}</h5>
            </div>
            <div class="modal-form-data">
                <form class="modal-form" method="POST" action="{{ setRoute('vendor.due.pay') }}"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="row mb-10-none">
                        <div class="col-xl-12 col-lg-12 form-group">
                            @include('admin.components.form.input', [
                                'label' => __('Amount'),
                                'label_after' => '<span>*</span>',
                                'type' => 'text',
                                'name' => 'amount',
                                'attribute' => 'step="any"',
                                'value' => old('amount'),
                                'placeholder' => __('Write Here..'),
                                'class' => 'number-input',
                            ])
                        </div>
                        <div class="col-xl-12 col-lg-12 form-group d-flex align-items-center justify-content-between mt-4">
                            <button type="button" class="btn btn--danger modal-close">{{ __('Close') }}</button>
                            <button type="button" class="btn btn--danger" id="set-amount">{{ __('Max Amount') }}</button>
                            <button type="submit" class="btn btn--base">{{ __('Action') }}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- End --}}
@endsection
@push('script')
    <script>
        var chart = $('#chart');
        var chart_one_data = chart.data('chart_one_data');
        var month = chart.data('chart_one_data');
        var year = chart.data('chart_one_data');
        var options = {
            series: [{
                name: 'Inflation',
                color: "#efefef",
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
                    colors: ["#ffffff"]
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
                labels: {
                    style: {
                        colors: Array(month.month.length).fill('#ffffff'), // Set color for each category
                        fontSize: '12px', // Adjust font size
                    }
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
                    color: '#ffffff'
                }
            }
        };

        var chart = new ApexCharts(document.querySelector("#chart"), options);
        chart.render();
    </script>
    <script>
        $(".due-balance-update-modal").click(function() {
            openModalBySelector("#due-balance-update-modal");
        });

        openModalWhenError("due-balance-update-modal", "#due-balance-update-modal");
    </script>
    <script>
        $(document).ready(function() {
            $('input[name="amount"]').on('input', function() {
                let value = $(this).val();
                if (value < 0) {
                    $(this).val('');
                }
            });
        });
    </script>
    <script>
        let times = '@json($month_day)';
        let booking_accept = '@json($booking_accept)';
        let booking_complete = '@json($booking_complete)';
        let booking_reject = '@json($booking_reject)';

        var options = {
            series: [{
                name: '{{ __("Accept") }}',
                color: "#4eade5",
                data: JSON.parse(booking_accept)
            }, {
                name: '{{ __("Complete") }}',
                color: "#5ea2c9",
                data: JSON.parse(booking_complete),
            },
            {
                name: '{{ __("Reject") }}',
                color: "#2781B9",
                data: JSON.parse(booking_reject),
            }
        ],
            chart: {
                height: 350,
                type: 'area'
            },
            dataLabels: {
                enabled: true,
                style: {
                    colors: ["#4eade5", "#5ea2c9", "#2781B9"] // Set custom colors for data labels
                }
            },
            stroke: {
                curve: 'smooth'
            },
            xaxis: {
                type: 'datetime',
                categories: JSON.parse(times)
            },
            tooltip: {
                x: {
                    format: 'dd/MM/yy HH:mm'
                },
            },
        };

        var chart = new ApexCharts(document.querySelector("#chart-two"), options);
        chart.render();
    </script>
    <script>
        $(document).ready(function(){
            let due = "{{ auth()->user()->wallets->due_payment }}";
            $("#set-amount").click(function(){
                $("input[name='amount']").val();
                $("input[name='amount']").val(due); // Change 100 to any desired value
            });
        });
    </script>
@endpush
