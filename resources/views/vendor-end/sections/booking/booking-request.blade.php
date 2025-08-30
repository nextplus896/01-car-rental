@extends('vendor-end.layouts.master')
@section('content')
    <div class="booking-request pt-40">
        <div class="title-header pb-20">
            <h3 class="title">{{ __('Booking Request') }}</h3>
        </div>
        <div class="dashboard-list-wrapper">
            @forelse ($car_bookings ?? [] as $value)
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
                        @if (Str::slug(car_booking_const()::CASH) == $value->payment_type)
                        <div class="preview-list-item">
                            <div class="preview-list-left">
                                <div class="preview-list-user-wrapper">
                                    <div class="preview-list-user-icon">
                                        <i class="las la-edit"></i>
                                    </div>
                                    <div class="preview-list-user-content">
                                        <span>{{ __('Charge') }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="preview-list-right">
                                <span">{{ get_amount($value->charges) }} {{ get_default_currency_code() }}</span>
                            </div>
                        </div>
                        @endif
                        <div class="preview-list-item">
                            <div class="preview-list-left">
                                <div class="preview-list-user-wrapper">
                                    <div class="preview-list-user-icon">
                                        <i class="las la-edit"></i>
                                    </div>
                                    <div class="preview-list-user-content">
                                        <span>{{ __('Payment Type') }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="preview-list-right">
                                <span">{{ $value->payment_type == Str::slug(car_booking_const()::ONLINE_PAYMENT) ? __('Online Payment') : __('Cash') }}</span>
                            </div>
                        </div>
                        <div class="preview-list-item">
                            <div class="preview-list-left">
                                <div class="preview-list-user-wrapper">
                                    <div class="preview-list-user-icon">
                                        <i class="las la-smoking"></i>
                                    </div>
                                    <div class="preview-list-user-content">
                                        <span>{{ __('Status') }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="preview-list-right text-center">
                                @if ($value->status === 1)
                                    <span class="base--text text--warning">{{ __('Pending') }}</span>
                                    @else
                                    <span class="base--text text--success">{{ __('On Going') }}</span>
                                @endif
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
                                @else
                                    <a href="{{ setRoute('vendor.booking.complete', $value->id) }}"
                                        class="service-btn">{{ __('Complete') }}</a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="dashboard-list-item-wrapper">
                    <div class="dashboard-list-item sent justify-content-center">
                        <div class="dashboard-list-left">
                            <div class="dashboard-list-user-content">
                                <h4 class="title text-primary">{{ __('Nothing to show yet') }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
    {{ $car_bookings->links() }}
@endsection
