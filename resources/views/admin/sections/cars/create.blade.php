@extends('admin.layouts.master')

@push('css')

    <style>
        .fileholder {
            min-height: 200px !important;
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
            'name'  => __("Cars"),
            'url'   => setRoute("admin.car.index")
        ]
    ], 'active' => __("Car Create")])
@endsection

@section('content')
<div class="custom-card">
    <div class="card-header">
        <h6 class="title">{{ __($page_title) }}</h6>
    </div>
    <div class="card-body">
        <form class="card-form" action="{{ setRoute('admin.car.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row justify-content-center">
                <div class="col-xl-4 col-lg-4 form-group mb-5">
                    @include('admin.components.form.input-file',[
                        'label'             => __("Image"),
                        'name'              => "image",
                        'class'             => "file-holder",
                        'old_files_path'    => files_asset_path("site-section"),
                        'old_files'         => old("old_image"),
                    ])
                </div>
            </div>
            <div class="row justify-content-center mb-10-none">
                <div class="col-xl-6 col-lg-6 form-group">
                    <label>{{ __("Select Area") }}*</label>
                    <select class="form--control select2-basic" name="area">
                        <option disabled selected>{{ __("Select Area") }}</option>
                        @foreach ($car_area as $area)
                            <option value="{{ $area->id }}">{{ $area->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-xl-6 col-lg-6 form-group">
                    <label>{{ __("Select Type") }}*</label>
                    <select class="form--control select2-basic" name="type">
                        <option disabled selected>{{ __("Select Type") }}</option>
                    </select>
                </div>
                <div class="col-xl-6 col-lg-6 form-group">
                    @include('admin.components.form.input',[
                        'label'             => __("Car Model")."*",
                        'name'              => "car_model",
                        'placeholder'       => __("Write Car Model")."...",
                        'value'             => old("car_model"),
                    ])
                </div>
                <div class="col-xl-6 col-lg-6 form-group">
                    @include('admin.components.form.input',[
                        'label'             => __("Car Number")."*",
                        'name'              => "car_number",
                        'placeholder'       => __("Write Car Number")."...",
                        'value'             => old("car_number"),
                    ])
                </div>
                <div class="col-xl-6 col-lg-6 form-group">
                    @include('admin.components.form.input',[
                        'label'             => __("Seat Number")."*",
                        'name'              => "seat",
                        'class'             => "number-input",
                        'placeholder'       => __("Write Seat Number")."...",
                        'value'             => old("seat"),
                    ])
                </div>
                <div class="col-xl-6 col-lg-6 form-group">
                    @include('admin.components.form.input',[
                        'label'             => __("Experience Year")."*",
                        'name'              => "experience",
                        'class'             => "number-input",
                        'placeholder'       => __("Write Experience Year")."...",
                        'value'             => old("experience"),
                    ])
                </div>
                <div class="col-xl-6 col-lg-6 mb-4">
                    <label>{{__("Car Fees")}}*</label>
                    <div class="input-group">
                        @include('admin.components.form.input',[
                        'name'              => "fees",
                        'class'             => "number-input",
                        'placeholder'       => __("Write Fees")."...",
                        'value'             => old('fees'),
                        ])
                        <span class="input-group-text">{{ get_default_currency_code($default_currency) }}</span>
                    </div>
                </div>
                <div class="col-xl-12 col-lg-12 form-group">
                    @include('admin.components.button.form-btn',[
                        'class'         => "w-100 btn-loading",
                        'text'          => __("Submit"),
                        'permission'    => "admin.car.area.store"
                    ])
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('script')
    <script>
        $(document).ready(function(){
            var getTypeURL = "{{ setRoute('admin.car.get.area.types') }}";

            $('select[name="area"]').on('change',function(){
                var area = $(this).val();

                if(area == "" || area == null) {
                    return false;
                }
                $.post(getTypeURL,{area:area,_token:"{{ csrf_token() }}"},function(response){

                    var option = '';
                    if(response.data.area.types.length > 0) {
                        $.each(response.data.area.types,function(index,item) {
                            option += `<option value="${item.car_type_id}">${item.type.name}</option>`
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
