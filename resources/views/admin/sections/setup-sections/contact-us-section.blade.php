@php
    $default_lang_code = language_const()::NOT_REMOVABLE;
    $system_default_lang = get_default_language_code();
    $languages_for_js_use = $languages->toJson();
@endphp

@extends('admin.layouts.master')

@push('css')
    <link rel="stylesheet" href="{{ asset('backend/css/fontawesome-iconpicker.min.css') }}">
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
        ]
    ], 'active' => __("Setup Section")])
@endsection

@section('content')
    <div class="custom-card">
        <div class="card-header">
            <h6 class="title">{{ __($page_title) }}</h6>
        </div>
        <div class="card-body">
            <form class="card-form" action="{{ setRoute('admin.setup.sections.section.update',$slug) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row justify-content-center mb-10-none">
                    <div class="col-xl-12 col-lg-12">
                        <div class="product-tab">
                            <nav>
                                <div class="nav nav-tabs" id="nav-tab" role="tablist">
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
                                                'label'         => __("Section Title"),
                                                'label_after'   => "*",
                                                'placeholder'   => __("Write Here").'...',
                                                'name'      => $lang_code . "_section_title",
                                                'value'     => old($lang_code . "_section_title",$data->value->language->$lang_code->section_title ?? "")

                                            ])
                                        </div>
                                        <div class="form-group">
                                            @include('admin.components.form.input',[
                                                'label'         =>  __("Title"),
                                                'label_after'   => "*",
                                                'placeholder'   => __("Write Here").'...',
                                                'name'      => $lang_code . "_title",
                                                'value'     => old($lang_code . "_title",$data->value->language->$lang_code->title ?? "")
                                            ])
                                        </div>
                                        <div class="form-group">
                                            @include('admin.components.form.input',[
                                                'label'         => __("Description Title"),
                                                'label_after'   => "*",
                                                'placeholder'   => __("Write Here").'...',
                                                'name'      => $lang_code . "_description_title",
                                                'value'     => old($lang_code . "_description_title",$data->value->language->$lang_code->description_title ?? "")
                                            ])
                                        </div>
                                        <div class="form-group">
                                            @include('admin.components.form.input',[
                                                'label'         => __("Description"),
                                                'label_after'   => "*",
                                                'placeholder'   => __("Write Here").'...',
                                                'name'      => $lang_code . "_description",
                                                'value'     => old($lang_code . "_description",$data->value->language->$lang_code->description ?? "")
                                            ])
                                        </div>
                                        <div class="form-group">
                                            @include('admin.components.form.input',[
                                                'label'         => __("Location Title"),
                                                'label_after'   => "*",
                                                'placeholder'   => __("Write Here").'...',
                                                'name'      => $lang_code . "_location_title",
                                                'value'     => old($lang_code . "_location_title",$data->value->language->$lang_code->location_title ?? "")
                                            ])
                                        </div>
                                        <div class="form-group">
                                            @include('admin.components.form.input',[
                                                'label'         =>__("Location"),
                                                'label_after'   => "*",
                                                'placeholder'   => __("Write Here").'...',
                                                'name'      => $lang_code . "_location",
                                                'value'     => old($lang_code . "_location",
                                                $data->value->language->$lang_code->location ?? "")
                                            ])
                                        </div>
                                        <div class="form-group">
                                            @include('admin.components.form.input',[
                                                'label'         => __("Call Title"),
                                                'label_after'   => "*",
                                                'placeholder'   => __("Write Here").'...',
                                                'name'      => $lang_code . "_call_title",
                                                'value'     => old($lang_code . "_call_title",
                                                $data->value->language->$lang_code->call_title ?? "")
                                            ])
                                        </div>
                                        <div class="form-group">
                                            @include('admin.components.form.input',[
                                                'label'         =>__("Mobile"),
                                                'label_after'   => "*",
                                                'placeholder'   => __("Write Here").'...',
                                                'name'      => $lang_code . "_mobile",
                                                'value'     => old($lang_code . "_mobile",
                                                $data->value->language->$lang_code->mobile ?? "")
                                            ])
                                        </div>
                                        <div class="form-group">
                                            @include('admin.components.form.input',[
                                                'label'     => __("Email Title"),
                                                'label_after'   => "*",
                                                'placeholder'   => __("Write Here").'...',
                                                'name'      => $lang_code . "_email_title",
                                                'value'     => old($lang_code . "_email_title",$data->value->language->$lang_code->email_title ?? "")
                                            ])
                                        </div>
                                        <div class="form-group">
                                            @include('admin.components.form.input',[
                                                'label'     => __("Email Address"),
                                                'label_after'   => "*",
                                                'placeholder'   => __("Write Here").'...',
                                                'name'      => $lang_code . "_email_address",
                                                'value'     => old($lang_code . "_email_address",$data->value->language->$lang_code->email_address ?? "")
                                            ])
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-12 col-lg-12 form-group">
                        @include('admin.components.button.form-btn',[
                            'class'         => "w-100 btn-loading",
                            'text'          => __("Update"),
                            'permission'    => "admin.setup.sections.section.update"
                        ])
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('script')

@endpush
