@extends('vendor-end.layouts.master')

@push('css')
    <style>
        .fileholder {
            min-height: 200px !important;
        }

        .fileholder-files-view-wrp.accept-single-file .fileholder-single-file-view,
        .fileholder-files-view-wrp.fileholder-perview-single .fileholder-single-file-view {
            height: 330px !important;
        }
    </style>
@endpush

@section('content')
    <div class="row mb-20-none">
        <div class="col-xl-12 col-lg-12 mb-20">
            <div class="custom-card mt-10">
                <div class="dashboard-header-wrapper">
                    <h4 class="title">{{ __('Edit Car') }}</h4>
                </div>
                <div class="card-body">
                    <form class="card-form" action="{{ setRoute('vendor.car.update',$cars->id) }}" method="POST"
                        enctype="multipart/form-data">
                        @method("PUT")
                        @csrf
                        <div class="row mb-10-none">
                            <div class="col-xl-12 col-lg-12 col-md-6 mb-10 form-group">
                                <nav>
                                    <div class="nav nav-tabs car-tab-button" id="nav-tab" role="tablist">
                                        @foreach ($languages as $item)
                                            <button class="nav-link @if (get_default_language_code() == $item->code) active @endif" id="{{$item->code}}-tab" data-bs-toggle="tab" data-bs-target="#{{$item->code}}" type="button" role="tab" aria-controls="{{ $item->code }}" aria-selected="true">{{ $item->name }}</button>
                                        @endforeach

                                    </div>
                                </nav>
                                <div class="tab-content" id="nav-tabContent">
                                    @foreach ($languages as $item)
                                        @php
                                            $lang_code = $item->code;
                                        @endphp
                                        <div class="tab-pane @if (get_default_language_code() == $item->code) fade show active @endif" id="{{ $item->code }}" role="tabpanel" aria-labelledby="english-tab">
                                            <div class="form-group">
                                                @include('admin.components.form.input',[
                                                    'label'         => __("Car Title"),
                                                    'label_after'   => "*",
                                                    'placeholder'   => __("Write Here").'...',
                                                    'name'      => $lang_code . "_car_title",
                                                    'value'     => old($lang_code . "_car_title",$cars->car_title->$lang_code->car_title ?? "")

                                                ])
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-6 mb-10 form-group">
                                <div class="vehicle-type">
                                    <label>{{ __('Vehicle Type') }}<span>*</span></label>
                                    <select class="select2 select2-basic" name="type" value="{{ old('type') }}">
                                        <option disabled selected>{{ __('Select Vehicle') }}</option>
                                        @foreach ($car_type as $type)
                                            <option value="{{ $type->id }}"
                                                {{ $type->id == $cars->car_type_id ? 'selected' : '' }}>{{ $type->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-6 mb-10 form-group">
                                <div class="vehicle-type">
                                    <label>{{ __('Area') }}<span>*</span></label>
                                    <select class="select2 select2-basic" name="area" value="{{ old('area') }}">
                                        <option disabled selected>{{ __('Select Area') }}</option>
                                        @foreach ($car_area as $area)
                                            <option value="{{ $area->id }}"
                                                {{ $area->id == $cars->car_area_id ? 'selected' : '' }}>
                                                {{ $area->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-6 mb-10 form-group">
                                <label>{{ __('Vehicle Model') }}<span>*</span></label>
                                <input type="text" class="form--control" name="car_model" placeholder="Enter Model"
                                    value="{{ old('car_model',$cars->car_model) }}">
                            </div>
                            <div class="col-xl-3 col-lg-3 col-md-6 mb-10 form-group">
                                <label>{{ __('Vehicle Number') }}<span>*</span></label>
                                <input type="text" class="form--control" name="car_number" placeholder="Enter Number"
                                    value="{{ old("car_number",$cars->car_number) }}" readonly>
                            </div>
                            <div class="col-xl-3 col-lg-3 col-md-6 mb-10 form-group">
                                <label>{{ __('Total Seat') }}<span>*</span></label>
                                <input type="number" class="form--control" name="seat" placeholder="Enter Number"
                                    value="{{ old('seat',$cars->seat) }}">
                            </div>
                            <div class="col-xl-3 col-lg-3 col-md-6 mb-10 form-group">
                                <label>{{ __('Per klm Charge') }}<span>*</span></label>
                                <input type="text" class="form--control klm-charge" name="fees" placeholder="Enter Charge"
                                    value="{{ old('fees',get_amount($cars->fees))}}">
                                <span class="charge-currency">{{ get_default_currency_code($default_currency) }}</span>
                            </div>
                            <div class="col-xl-3 col-lg-3 col-md-6 mb-10 form-group">
                                <label>{{ __('Experience') }} <span class="text--base">*</span></label>
                                <input type="text" class="form--control" name="experience" placeholder="Enter Experience"
                                    value="{{ old('experience',$cars->experience) }}">
                            </div>
                            <div class="col-xl-12 col-lg-12 mb-10 form-group">
                                <label>{{ __('Car Photo') }}<span>*</span></label>
                                <div class="file-holder-wrapper">
                                    @include('admin.components.form.input-file', [
                                        'name' => 'image',
                                        'class' => 'file-holder',
                                        'old_files_path' => files_asset_path('site-section'),
                                        'old_files' => $cars->image ?? "",
                                    ])
                                </div>
                            </div>
                        </div>
                        <div class="button pt-3">
                            <button type="submit" class="btn btn--base w-100">{{ __('Update') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        $(document).ready(function() {
            var getTypeURL = "{{ setRoute('vendor.car.get.area.types') }}";

            $('select[name="area"]').on('change', function() {
                var area = $(this).val();

                if (area == "" || area == null) {
                    return false;
                }
                $.post(getTypeURL, {
                    area: area,
                    _token: "{{ csrf_token() }}"
                }, function(response) {

                    var option = '';
                    if (response.data.area.types.length > 0) {
                        $.each(response.data.area.types, function(index, item) {
                            option +=
                                `<option value="${item.car_type_id}">${item.type.name}</option>`
                        });

                        $("select[name=type]").html(option);
                        $("select[name=type]").select2();
                    }
                }).fail(function(response) {
                    var errorText = response.responseJSON;
                });
            });
        });
    </script>
@endpush
