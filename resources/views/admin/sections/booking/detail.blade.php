@extends('admin.layouts.master')

@push('css')

    <style>
        .fileholder {
            min-height: 374px !important;
        }

        .fileholder-files-view-wrp.accept-single-file .fileholder-single-file-view,.fileholder-files-view-wrp.fileholder-perview-single .fileholder-single-file-view{
            height: 330px !important;
        }
    </style>
@endpush

@section('page-title')
    @include('admin.components.page-title',['title' => __($page_title)])
@endsection

@section('breadcrumb')
    @include('admin.components.breadcrumb',['breadcrumbs' => [
        [
            'name'  => __("Dashboard"),
            'url'   => setRoute("admin.dashboard"),
        ],
        [
            'name'  => __("Bookings"),
            'url'   => setRoute("admin.booking.index"),
        ]
    ], 'active' => __("Booking Details")])
@endsection
@section('content')
<div class="custom-card">
    <div class="card-header">
        <h6 class="title">{{ __($page_title) }}</h6>
    </div>
    <div class="card-body">
        <div class="row mb-30-none">
            <div class="col-lg-4 mb-30">
                <div class="booking-area">
                    <h4 class="title"><i class="fas fa-car text--base me-2"></i>{{ __("Car Information") }}</h4>
                    <div class="thumb">
                        <img src="{{ get_image($booking->cars->image ?? '','site-section') ?? '' }}" alt="profile">
                    </div>
                    <div class="content">
                        <div class="list-wrapper">
                            <ul class="list">
                                <li>{{ __("Model") }}:<span>{{ $booking->cars->car_model ?? "" }}</span></li>
                                <li>{{ __("Number") }}:<span>{{ $booking->cars->car_number ?? "" }}</span></li>
                                <li>{{ __("Seat Number") }}:<span>{{ $booking->cars->seat ?? "" }}</span></li>
                                <li>{{ __("Rate") }}:<span>{{ get_amount($booking->cars->fees) ?? "" }}{{ __("/KM") }} {{ $default_currency->code }}</span></li>
                                <li>{{ __("Experience Year") }}:<span>{{ $booking->cars->experience ?? "" }}</span></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 mb-30">
                <div class="booking-area">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="title mb-0"><i class="fas fa-book text--base me-2"></i>{{ __("Booking Information") }}</h4>
                    </div>

                    <div class="content pt-0">
                        <div class="list-wrapper">
                            <ul class="list">
                                <li>{{ __("Pick-up Location") }}:<span>{{ $booking->location ?? "" }}</span></li>
                                <li>{{ __("Destination") }}:<span>{{ $booking->destination ?? "" }}</span></li>
                                <li>{{ __("Pick-up Date") }}:<span>{{ $booking->pickup_date ? \Carbon\Carbon::parse($booking->pickup_date)->format('d-m-Y') : '' }}</span></li>
                                <li>{{ __("Pick-up Time") }}:<span>{{ $booking->pickup_time ? \Carbon\Carbon::parse($booking->pickup_time)->format('h:i A') : '' }}</span></li>
                                <li>{{ __("Round Trip Date") }}:<span>{{ $booking->round_pickup_date ? \Carbon\Carbon::parse($booking->round_pickup_date)->format('d-m-Y') : __('N/A') }}</span></li>
                                <li>{{ __("Round Trip Time") }}:<span>{{ $booking->round_pickup_time ? \Carbon\Carbon::parse($booking->round_pickup_time)->format('h:i A') : __('N/A') }}</span></li>
                                <li>{{ __("Message") }}:<span>{{ ($booking->message == null) ? __("N/A") : $booking->message }}</span></li>
                                <li>{{ __("Total Distance") }}: <span>{{ ($booking->distance== 0) ? __("N/A") : get_amount($booking->distance,__("KM"),2) }} </span></li>
                                <li>{{ __("Payable Amount") }}:<span>{{ ($booking->amount== 0) ? __("N/A") : get_amount($booking->amount,$default_currency->code) }}</span></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 mb-30">
                <div class="booking-area">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="title mb-0"><i class="fas fa-user text--base me-2"></i>{{ __("User Information") }}</h4>
                    </div>

                    <div class="content pt-0">
                        <div class="list-wrapper">
                            <ul class="list">
                                <li>{{ __("Full Name") }}:<span>{{ $booking->user->fullname ?? __("Unauthorized") }}</span></li>
                                <li>{{ __("Email") }}:<span>{{ $booking->email ?? "" }}</span></li>
                                <li>{{ __("Phone") }}:<span>{{ $booking->phone ?? "" }}</span></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


@endsection
@push('script')
    <script>

    </script>
@endpush
