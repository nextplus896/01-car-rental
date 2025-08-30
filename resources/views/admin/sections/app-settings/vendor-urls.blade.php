@extends('admin.layouts.master')

@push('css')

@endpush

@section('page-title')
    @include('admin.components.page-title',['title' => __($page_title)])
@endsection

@section('breadcrumb')
    @include('admin.components.breadcrumb',['breadcrumbs' => [
        [
            'name'  => __("Dashboard"),
            'url'   => setRoute("admin.dashboard"),
        ]
    ], 'active' => __("Vendor App Settings")])
@endsection

@section('content')
    <div class="custom-card">
        <div class="card-header">
            <h6 class="title">{{ __("Vendor App URLs") }}</h6>
        </div>
        <div class="card-body">
            <form class="card-form" method="POST" action="{{ setRoute('admin.app.settings.vendor.urls.update') }}">
                @csrf
                @method("PUT")
                <div class="row align-items-center mb-10-none">
                    <div class="col-xl-12 col-lg-12">
                        <div class="form-group">
                            @include('admin.components.form.input',[
                                'label'             => __("Android App URL"),
                                'label_after'       => "*",
                                'name'              => "vendor_android_url",
                                'placeholder'       => __("Write Here").'...',
                                'value'             => old('android_url',$app_settings->vendor_android_url),
                                'attribute'         => "data-limit=255",
                            ])
                        </div>
                        <div class="form-group">
                            @include('admin.components.form.input',[
                                'label'             => __("iOS App URL"),
                                'name'              => "vendor_iso_url",
                                'placeholder'       => __("Write Here").'...',
                                'value'             => old('iso_url',$app_settings->vendor_iso_url),
                                'attribute'         => "data-limit=255",
                            ])
                        </div>
                    </div>
                    <div class="col-xl-12 col-lg-12 form-group">
                        <button type="submit" class="btn--base w-100 btn-loading">{{ __("Update") }}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('script')

@endpush
