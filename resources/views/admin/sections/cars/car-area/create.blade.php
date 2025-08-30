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
            'name'  => __("Car Area"),
            'url'   => setRoute("admin.car.area.index"),
        ]
    ], 'active' => __("Car Area Create")])
@endsection

@section('content')
<div class="custom-card">
    <div class="card-header">
        <h6 class="title">{{ __($page_title) }}</h6>
    </div>
    <div class="card-body">
        <form class="card-form" action="{{ setRoute('admin.car.area.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row mb-10-none">
                <div class="col-xl-12 col-lg-12 form-group">
                    @include('admin.components.form.input',[
                        'label'             => __("Area Name")."*",
                        'name'              => "name",
                        'placeholder'       => __("Write Name")."...",
                        'value'             => old('name'),
                    ])
                </div>
                <div class="col-xl-12 col-lg-12 form-group">
                    <label for="type">{{ __("Type") }}*</label>
                    <div class="custom-check-list">
                        @foreach ($car_types as $item)
                        <div class="custom-check-group">
                            <input type="checkbox" class="payment-gateway-currency" name="types[]" value="{{ $item->id }}" id="dp-{{$item->id}}">
                            <label for="dp-{{$item->id}}">{{$item->name}}</label>
                        </div>
                    @endforeach
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


    </script>
@endpush
