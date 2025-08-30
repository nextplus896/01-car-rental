<!-- Find Car -->
<div class="car-searching-area ptb-80">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10 col-md-10">
                <div class="car-booking-area">
                    <form class="booking-form" action="{{ setRoute('user.car.booking.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="country_code" class="form--control place-input"
                            value="{{ $basic_settings->country_code }}">
                        <div class="form-header-title pb-20">
                            <h2 class="title text--base text-center">{{ __('Find Your Car') }}</h2>
                        </div>
                        @php
                            $old_area = session()->get('form_data')['area'] ?? request()->get('area');
                            $old_type = session()->get('form_data')['type'] ?? request()->get('type');
                        @endphp
                        <div class="row">
                            <div class="col-lg-6 col-md-6 pb-10">
                                <div class="select-area">
                                    <label>{{ __('Select Area') }}</label>
                                    <select class="select2-basic" name="area" spellcheck="false"
                                        data-ms-editor="true">
                                        <option disabled selected>{{ __('Select Area') }}</option>
                                        @foreach ($areas as $area)
                                            <option {{ $old_area == $area->id ? 'selected' : '' }}
                                                value="{{ $area->id }}">
                                                {{ $area->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6 pb-10">
                                <div class="vehicle-type">
                                    <label>{{ __('Vehicle Type') }}</label>
                                    <select class="select2-basic" name="type" spellcheck="false"
                                        data-ms-editor="true">
                                        <option disabled selected>{{ __('Select Type') }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6 pb-10">
                                <label>{{ __('Email') }}*</label>
                                @php
                                    $email = auth()->user()->email ?? '';
                                @endphp
                                <input type="email" name="credentials" required class="form--control"
                                    value="{{ $email }}" @if ($email) readonly @endif placeholder="{{ __('Write Here') }}...">
                            </div>
                            <div class="col-lg-6 col-md-6 pb-10">
                                <label>{{ __('Phone No') }}.</label>
                                <input type="tel" name="mobile" class="form--control"
                                    value="{{ auth()->user()->mobile ?? '' }}" placeholder="{{ __('Write Here') }}...">
                            </div>
                            <div class="col-lg-6 col-md-6 pb-10">
                                <div class="select-area">
                                    <label>{{ __('Pick-up Location') }}*</label>
                                    <input type="text" name="location" value="{{ old('location', session()->get('form_data')['location'] ?? null)}}" required class="form--control place-input" placeholder="{{ __('Write Here') }}...">
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6 pb-10">
                                <div class="select-area">
                                    <label>{{ __('Destination') }}*</label>
                                    <input type="text" name="destination" value="{{ old('destination', session()->get('form_data')['destination'] ?? null)}}" required class="form--control place-input" placeholder="{{ __('Write Here') }}...">
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6 pb-10">
                                <label>{{ __('Distance') }}*</label>
                                <div class="input-group">
                                    <input type="number" name="distance" value="{{ old('distance', session()->get('form_data')['distance'] ?? null)}}" id="distance-input" required
                                        class="form--control place-input" placeholder="{{ __('Write Here') }}...">
                                    <div class="input-group-text">K/M</div>
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6 pb-10">
                                <div class="select-date">
                                    <label>{{ __('Pick-up Date') }}*</label>
                                    <input type="date" id="date" name="pickup_date" value="{{ old('pickup_date', session()->get('form_data')['pickup_date'] ?? null)}}" required
                                        class="form--control">
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6 pb-10">
                                <div class="select-date">
                                    <label>{{ __('Pick-up Time') }}*</label>
                                    <input type="time" id="pickTime" name="pickup_time" value="{{ old('pickup_time', session()->get('form_data')['pickup_time'] ?? null)}}" required
                                        class="form--control">
                                    <div id="time-input"></div>
                                </div>
                            </div>
                            <div class="return-trep-checkbox pt-2">
                                <div class="custom-check-group">
                                    <input type="checkbox" id="level-2" @checked(session()->get('form_data')['round_pickup_date'] ?? null) class="dependency-checkbox"
                                        data-target="book-check-form">
                                    <label for="level-2">{{ __('Round Trip') }}?</label>
                                </div>
                            </div>
                            <div class="book-check-form" style="display: none;">
                                <div class="row">
                                    <div class="col-lg-6 pb-10">
                                        <div class="select-date">
                                            <label>{{ __('Pick-up Date') }}*</label>
                                            <input type="date" id="round-date" name="round_pickup_date" value="{{ old('round_pickup_date', session()->get('form_data')['round_pickup_date'] ?? null)}}" class="form--control">
                                        </div>
                                    </div>
                                    <div class="col-lg-6 pb-10">
                                        <div class="select-date">
                                            <label>{{ __('Pick-up Time') }}*</label>
                                            <input type="time" name="round_pickup_time" value="{{ old('round_pickup_time', session()->get('form_data')['round_pickup_time'] ?? null)}}" class="form--control">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-12 pb-10">
                                <div class="select-date">
                                    <label>{{ __('Note') }} <span>( {{ __('Optional') }} )</span></label>
                                    <textarea class="form--control" name="message" placeholder="{{ __('Write Here') }}..."></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="searching-btn pt-3">
                            <button type="submit" class="btn--base w-100">{{ __('Find Now') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@push('script')
    <script>
        var getTypeURL = "{{ setRoute('frontend.get.area.types') }}";
        var old_type = "{{ $old_type }}";
        $(document).ready(function() {
            getAreaItems();
        });
        $('select[name="area"]').on('change', function() {
            getAreaItems();
        });

        function getAreaItems() {
            var area = $('select[name="area"]').val();

            if (area == "" || area == null) {
                return false;
            }

            $.post(getTypeURL, {
                area: area,
                _token: "{{ csrf_token() }}"
            }, function(response) {
                console.log(response);
                var option = '';
                if (response.data.area.types.length > 0) {
                    $.each(response.data.area.types, function(index, item) {
                        if (item.type != null) {
                            var selected_item = old_type == item.car_type_id ? "selected" : "";
                            option +=
                                `<option ${ selected_item } value="${item.car_type_id}">${item.type.name}</option>`
                        }

                    });

                    $("select[name=type]").html(option);
                    $("select[name=type]").select2();

                }
            }).fail(function(response) {
                var errorText = response.responseJSON;
                throwMessage('failed', ['An error occurred.']);
            });

        }
    </script>

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

    <script>
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('date').setAttribute('min', today);
        document.getElementById('round-date').setAttribute('min', today);
    </script>

    <script>
        $(document).ready(function() {
            $("#date, #pickTime").on("change", function() {
                let currentDate = new Date();
                let selectedDate = new Date($("#date").val());
                let selectedTime = $("#pickTime").val();

                if (selectedDate.toDateString() === currentDate.toDateString()) {
                    let nowHours = currentDate.getHours();
                    let nowMinutes = currentDate.getMinutes();
                    let [selectedHours, selectedMinutes] = selectedTime.split(":");

                    if (
                        parseInt(selectedHours) < nowHours ||
                        (parseInt(selectedHours) === nowHours && parseInt(selectedMinutes) < nowMinutes)
                    ) {
                        $("#pickTime").val("");
                        $('#time-input').html("")
                        $('#time-input').append(`<p class='text-danger'>{{ __('Please select future time') }}</p>`)

                    }
                }
            });
        });
    </script>
@endpush
