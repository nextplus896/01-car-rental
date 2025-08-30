@extends('frontend.layouts.master')
@section('content')
    <!-- Find Car -->
    <div class="car-searching-area pt-80">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10 col-md-10">
                    <div class="car-booking-area">
                        <form class="booking-form" action="{{ setRoute('user.car.booking.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="car" value="{{ $car->slug }}">
                            <input type="hidden" name="country_code" class="form--control place-input"
                                value="{{ $basic_settings->country_code }}">
                            <div class="form-header-title pb-20">
                                <h2 class="title text--base text-center">{{ __('Book Your Car') }}</h2>
                            </div>
                            <div class="row">
                                <div class="col-lg-6 col-md-6 pb-10">
                                    <label>{{ __('Email') }}*</label>
                                    @php
                                        $email = auth()->user()->email ?? '';
                                    @endphp
                                    <input type="email" name="credentials" required class="form--control"
                                        value="{{ $email }}" @if ($email) readonly @endif>
                                </div>
                                <div class="col-lg-6 col-md-6 pb-10">
                                    <label>{{ __('Phone No') }}.</label>
                                    <input type="tel" name="mobile" class="form--control"
                                        value="{{ auth()->user()->mobile ?? '' }}">
                                </div>
                                <div class="col-lg-6 col-md-6 pb-10">
                                    <div class="select-area">
                                        <label>{{ __('Pick-up Location') }}*</label>
                                        <input type="text" name="location" required class="form--control place-input">
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6 pb-10">
                                    <div class="select-area">
                                        <label>{{ __('Destination') }}*</label>
                                        <input type="text" name="destination" required class="form--control place-input">
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6 pb-10">
                                    <div class="select-area">
                                        <label>{{ __('Distance') }}*</label>
                                        <input type="number" name="distance" id="distance-input" required class="form--control place-input">
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6 pb-10">
                                    <div class="select-date">
                                        <label>{{ __('Pick-up Date') }}*</label>
                                        <input type="date" name="pickup_date" required class="form--control">
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6 pb-10">
                                    <div class="select-date">
                                        <label>{{ __('Pick-up Time') }}*</label>
                                        <input type="time" name="pickup_time" required class="form--control">
                                    </div>
                                </div>
                                <div class="return-trep-checkbox pt-2">
                                    <div class="custom-check-group">
                                        <input type="checkbox" id="level-2" class="dependency-checkbox"
                                            data-target="book-check-form">
                                        <label for="level-2">{{ __('Round Trip?') }}</label>
                                    </div>
                                </div>
                                <div class="book-check-form" style="display: none;">
                                    <div class="row">
                                        <div class="col-lg-6 pb-10">
                                            <div class="select-date">
                                                <label>{{ __('Pick-up Date') }}*</label>
                                                <input type="date" name="round_pickup_date" class="form--control">
                                            </div>
                                        </div>
                                        <div class="col-lg-6 pb-10">
                                            <div class="select-date">
                                                <label>{{ __('Pick-up Time') }}*</label>
                                                <input type="time" name="round_pickup_time" class="form--control">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-12 pb-10">
                                    <div class="select-date">
                                        <label>{{ __('Note') }} <span>( {{ __('Optional') }} )</span></label>
                                        <textarea class="form--control" name="message" placeholder="Write Here..."></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="searching-btn pt-3">
                                <button type="submit" class="btn--base w-100">{{ __('Book Now') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const quantityInput = document.getElementById('distance-input');
            quantityInput.addEventListener('input', function(event) {
                let inputValue = event.target.value.trim();
                if (inputValue === '' || inputValue <= '0') {
                    inputValue = '1';
                }
                event.target.value = inputValue;
            });
        });
    </script>
@endpush
