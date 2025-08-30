<?php
$default = get_default_language_code() ?? 'en';
$default_lng = 'en';
?>
@extends('vendor-end.layouts.master')
@section('content')
    <div class="selon-add-btn ptb-20 text-end">
        <a href="{{ setRoute('vendor.car.create') }}" class="btn--base"><i class="las la-plus"></i> {{ __('Add car') }}</a>
    </div>
    <div class="my-car-details">
        <div class="row justify-content-center mb-20-none">
            @forelse ($cars ?? [] as $car)
                <div class="col-xl-4 col-lg-4 col-md-6 col-sm-12 mb-20">
                    <div class="car-item">
                        <div class="car-img">
                            <img src="{{ get_image($car->image ?? '', 'site-section') ?? '' }}" alt="img">
                            @if ($car->approval === 0)
                                <span class="booking-status bg-danger">{{ __('Not Approved') }}</span>
                            @else
                                @if (App\Models\CarBooking::where('car_id', $car->id)->where('status', 2)->count() > 0)
                                    <span class="booking-status">{{ __('Booked') }}</span>
                                @endif
                            @endif
                            <div class="car-status">
                                <div class="switch-field">
                                    @include('vendor-end.components.form.switcher', [
                                        'name' => 'currency_status',
                                        'value' => $car->status,
                                        'options' => [__('Enable') => 1, __('Disable') => 0],
                                        'onload' => true,
                                        'data_target' => $car->id,
                                        'permission' => 'vendor.car.status.update',
                                    ])
                                </div>
                            </div>
                        </div>
                        <div class="car-details" style="{{ $car->status ? 'pointerEvents: auto; opacity: 1;' : 'pointerEvents: none; opacity:0.3' }}">
                            <h3 class="title">{{ $car->car_model }}</h3>
                            <p>{{ $car->car_number }}</p>
                            <p>{{ __('Per km') }} {{ get_amount($car->fees) }} {{ get_default_currency_code() }}</p>
                            <p>{{ $car->experience }} {{ __('Year Experience') }}</p>
                            <p>{{ $car->car_title->$default->car_title }}</p>
                            <div class="control-btn pt-2 mb-10-none justify-content-sm-between d-flex">
                                <button type="button" class="delate-btn delete-modal-button mb-10" data-bs-toggle="modal"
                                    data-bs-target="#delateModal"
                                    data-item="{{ $car->id }}">{{ __('Delete') }}</button>
                                <a href="{{ setRoute('vendor.car.edit', $car->id) }}"
                                    class="edit-btn mb-10">{{ __('Edit') }}</a>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="dashboard-list-item-wrapper text-center">
                    <h4>{{ __('No car added yet') }}!</h4>
                </div>
            @endforelse
        </div>
    </div>
    {{ $cars->links() }}
@endsection

@push('script')
    <script>
        $('.delete-modal-button').click(function() {
            var oldData = JSON.parse($(this).attr("data-item"));
            var actionRoute = "{{ setRoute('vendor.car.delete') }}";
            var target = oldData;
            var message = `Are you sure to <strong>delete</strong> this car?`;
            console.log(oldData);
            openDeleteModal(actionRoute, target, message);
        });
    </script>
    <script>
        $(document).ready(function() {
            // Switcher
            switcherAjax("{{ setRoute('vendor.car.status.update') }}");
        })
    </script>
@endpush
