@extends('admin.layouts.master')

@push('css')
    <link rel="stylesheet" href="{{ asset('backend/css/fontawesome-iconpicker.min.css') }}">
    <style>
        .fileholder {
            min-height: 374px !important;
        }

        .fileholder-files-view-wrp.accept-single-file .fileholder-single-file-view,
        .fileholder-files-view-wrp.fileholder-perview-single .fileholder-single-file-view {
            height: 330px !important;
        }
    </style>
@endpush
@php
    $default_lang_code = language_const()::NOT_REMOVABLE;
    $system_default_lang = get_default_language_code();
    $languages_for_js_use = $languages->toJson();
@endphp

@section('page-title')
    @include('admin.components.page-title', ['title' => __($page_title)])
@endsection

@section('breadcrumb')
    @include('admin.components.breadcrumb', [
        'breadcrumbs' => [
            [
                'name' => __('Dashboard'),
                'url' => setRoute('admin.dashboard'),
            ],
        ],
        'active' => __('Setup Section'),
    ])
@endsection

@section('content')
    <div class="custom-card">
        <div class="card-header">
            <h6 class="title">{{ __($page_title) }}</h6>
        </div>
        <div class="card-body">
            <form class="card-form" action="{{ setRoute('admin.setup.sections.section.update', $slug) }}" method="POST"
                enctype="multipart/form-data">
                @csrf
                <div class="row justify-content-center mb-10-none">
                    <div class="col-xl-12 col-lg-12">
                        <div class="product-tab">
                            <nav>
                                <div class="nav nav-tabs" id="nav-tab" role="tablist">
                                    @foreach ($languages as $item)
                                        <button class="nav-link @if (get_default_language_code() == $item->code) active @endif"
                                            id="{{ $item->code }}-tab" data-bs-toggle="tab"
                                            data-bs-target="#{{ $item->code }}" type="button" role="tab"
                                            aria-controls="{{ $item->code }}"
                                            aria-selected="true">{{ $item->name }}</button>
                                    @endforeach
                                </div>
                            </nav>
                            <div class="tab-content" id="nav-tabContent">
                                @foreach ($languages as $item)
                                    @php
                                        $lang_code = $item->code;
                                    @endphp
                                    <div class="tab-pane @if (get_default_language_code() == $item->code) fade show active @endif"
                                        id="{{ $item->code }}" role="tabpanel" aria-labelledby="english-tab">
                                        <div class="form-group">
                                            @include('admin.components.form.input', [
                                                'label' => __('Heading'),
                                                'label_after' => '*',
                                                'placeholder' => __('Write Here') . '...',
                                                'name' => $item->code . '_heading',
                                                'value' => old(
                                                    $item->code . '_heading',
                                                    $data->value->language->$lang_code->heading ?? ''),
                                            ])
                                        </div>
                                        <div class="form-group">
                                            @include('admin.components.form.input', [
                                                'label' => __('Sub Heading'),
                                                'label_after' => '*',
                                                'placeholder' => __('Write Here') . '...',
                                                'name' => $item->code . '_sub_heading',
                                                'value' => old(
                                                    $item->code . '_sub_heading',
                                                    $data->value->language->$lang_code->sub_heading ?? ''),
                                            ])
                                        </div>
                                        <div class="form-group">
                                            @include('admin.components.form.input', [
                                                'label' => __('Button One Name'),
                                                'label_after' => '*',
                                                'placeholder' => __('Write Here') . '...',
                                                'name' => $item->code . '_button_name_one',
                                                'value' => old(
                                                    $item->code . '_button_name_one',
                                                    $data->value->language->$lang_code->button_name_one ?? ''),
                                            ])
                                        </div>
                                        <div class="form-group">
                                            @include('admin.components.form.input', [
                                                'label' => __('Button Two Name'),
                                                'label_after' => '*',
                                                'placeholder' => __('Write Here') . '...',
                                                'name' => $item->code . '_button_name_two',
                                                'value' => old(
                                                    $item->code . '_button_name_two',
                                                    $data->value->language->$lang_code->button_name_two ?? ''),
                                            ])
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="col-xl-12 col-lg-12 form-group">
                            <label for="">{{ __('Button One Link') }}*</label>
                            <div class="input-group mb-3">
                                <span class="input-group-text" id="basic-addon1">{{ url('/') }}/</span>
                                <input type="text" class="form--control" name="button_link_one"
                                    value="{{ old('button_link_one', $data->value->button_link_one ?? '') }}">
                            </div>
                        </div>
                        <div class="col-xl-12 col-lg-12 form-group">
                            <label for="">{{ __('Button Two Link') }}*</label>
                            <div class="input-group mb-3">
                                <span class="input-group-text" id="basic-addon1">{{ url('/') }}/</span>
                                <input type="text" class="form--control" name="button_link_two"
                                    value="{{ old('button_link_two', $data->value->button_link_two ?? '') }}">
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-12 col-lg-12 form-group">
                        @include('admin.components.button.form-btn', [
                            'class' => 'w-100 btn-loading',
                            'text' => __('Submit'),
                            'permission' => 'admin.setup.sections.section.update',
                        ])
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="custom-card mt-20">
        <div class="card-header">
            <h6 class="title">{{ __('Vendor Banner') }}</h6>
        </div>
        <div class="card-body">
            <form class="card-form" action="{{ setRoute('admin.setup.sections.section.update', 'vendor-banner') }}"
                method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row justify-content-center mb-10-none">
                    <div class="col-xl-4 col-lg-4 form-group">
                        @include('admin.components.form.input-file', [
                            'label' => __('Image') . ':',
                            'name' => 'vendor_image',
                            'class' => 'file-holder',
                            'old_files_path' => files_asset_path('site-section'),
                            'old_files' => $vendor_banner->value->image ?? '',
                        ])
                    </div>
                    <div class="col-xl-8 col-lg-8">
                        <div class="product-tab">
                            <nav>
                                <div class="nav nav-tabs" id="nav-tab" role="tablist">
                                    @foreach ($languages as $item)
                                        <button class="nav-link @if (get_default_language_code() == $item->code) active @endif"
                                            id="{{ $item->code }}-tab" data-bs-toggle="tab"
                                            data-bs-target="#{{ $item->code }}-one" type="button" role="tab"
                                            aria-controls="{{ $item->code }}"
                                            aria-selected="true">{{ $item->name }}</button>
                                    @endforeach
                                </div>
                            </nav>
                            <div class="tab-content" id="nav-tabContent">
                                @foreach ($languages as $item)
                                    @php
                                        $lang_code = $item->code;
                                    @endphp
                                    <div class="tab-pane @if (get_default_language_code() == $item->code) fade show active @endif"
                                        id="{{ $item->code }}-one" role="tabpanel" aria-labelledby="english-tab">
                                        <div class="form-group">
                                            @include('admin.components.form.input', [
                                                'label' => __('Heading'),
                                                'label_after' => '*',
                                                'placeholder' => __('Write Here') . '...',
                                                'name' => $item->code . '_heading',
                                                'value' => old(
                                                    $item->code . '_heading',
                                                    $vendor_banner->value->language->$lang_code->heading ?? ''),
                                            ])
                                        </div>
                                        <div class="form-group">
                                            @include('admin.components.form.input', [
                                                'label' => __('Sub Heading'),
                                                'label_after' => '*',
                                                'placeholder' => __('Write Here') . '...',
                                                'name' => $item->code . '_sub_heading',
                                                'value' => old(
                                                    $item->code . '_sub_heading',
                                                    $vendor_banner->value->language->$lang_code->sub_heading ?? ''),
                                            ])
                                        </div>
                                        <div class="form-group">
                                            @include('admin.components.form.input', [
                                                'label' => __('Button One Name'),
                                                'label_after' => '*',
                                                'placeholder' => __('Write Here') . '...',
                                                'name' => $item->code . '_button_name_one',
                                                'value' => old(
                                                    $item->code . '_button_name_one',
                                                    $vendor_banner->value->language->$lang_code->button_name_one ?? ''),
                                            ])
                                        </div>
                                        <div class="form-group">
                                            @include('admin.components.form.input', [
                                                'label' => __('Button Two Name'),
                                                'label_after' => '*',
                                                'placeholder' => __('Write Here') . '...',
                                                'name' => $item->code . '_button_name_two',
                                                'value' => old(
                                                    $item->code . '_button_name_two',
                                                    $vendor_banner->value->language->$lang_code->button_name_two ?? ''),
                                            ])
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="col-xl-12 col-lg-12 form-group">
                            @include('admin.components.form.input', [
                                'label' => __('Button One Icon'),
                                'label_after' => '*',
                                'placeholder' => __('Write Here') . '...',
                                'name' => 'button_one_icon',
                                'value' => old('button_one_icon', $vendor_banner->value->button_one_icon ?? ''),
                                'class' => 'form--control icp icp-auto iconpicker-element iconpicker-input',
                            ])
                        </div>
                        <div class="col-xl-12 col-lg-12 form-group">
                            <label for="">{{ __('Button One Link') }}*</label>
                            <div class="input-group mb-3">
                                <span class="input-group-text" id="basic-addon1">{{ url('/') }}/</span>
                                <input type="text" class="form--control" name="button_link_one"
                                    value="{{ old('button_link_one', $vendor_banner->value->button_link_one ?? '') }}">
                            </div>
                        </div>
                        <div class="col-xl-12 col-lg-12 form-group">
                            <label for="">{{ __('Button Two Link') }}*</label>
                            <div class="input-group mb-3">
                                <span class="input-group-text" id="basic-addon1">{{ url('/') }}/</span>
                                <input type="text" class="form--control" name="button_link_two"
                                    value="{{ old('button_link_two', $vendor_banner->value->button_link_two ?? '') }}">
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-12 col-lg-12 form-group">
                        @include('admin.components.button.form-btn', [
                            'class' => 'w-100 btn-loading',
                            'text' => __('Submit'),
                            'permission' => 'admin.setup.sections.section.update',
                        ])
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="table-area mt-15">
        <div class="table-wrapper">
            <div class="table-header justify-content-end">
                <div class="table-btn-area">
                    <a href="#vendor-item-add" class="btn--base modal-btn"><i class="fas fa-plus me-1"></i>
                        {{ __('Add Item') }}</a>
                </div>
            </div>
            <div class="table-responsive">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th></th>
                            <th>{{ __('Title') }}</th>
                            <th>{{ __('Description') }}</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($vendor_banner->value->items ?? [] as $key => $item)
                            <tr data-item="{{ json_encode($item) }}">
                                <td><span class="icon-circle"><i class="{{ $item->icon ?? '' }}"></i></span></td>
                                <td><span
                                        class="text--dark">{{ $item->language->$system_default_lang->title ?? '' }}</span>
                                </td>
                                <td><span
                                        class="text--dark">{{ $item->language->$system_default_lang->description ?? '' }}</span>
                                </td>
                                <td>
                                    <button class="btn btn--base edit-modal-button"><i
                                            class="las la-pencil-alt"></i></button>
                                    <button class="btn btn--base btn--danger delete-modal-button"><i
                                            class="las la-trash-alt"></i></button>
                                </td>
                            </tr>
                        @empty
                            @include('admin.components.alerts.empty', ['colspan' => 4])
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @include('admin.components.modals.site-section.add-vendor-item')

    {{-- edit modal --}}
    <div id="vendor-item-edit" class="mfp-hide large">
        <div class="modal-data">
            <div class="modal-header px-0">
                <h5 class="modal-title">{{ __('Edit Item') }}</h5>
            </div>
            <div class="modal-form-data">
                <form class="modal-form" method="POST"
                    action="{{ setRoute('admin.setup.sections.section.item.update', 'vendor-banner') }}"
                    enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="target" value="{{ old('target') }}">
                    <div class="row mb-10-none mt-3">
                        <div class="language-tab">
                            <nav>
                                <div class="nav nav-tabs" id="nav-tab" role="tablist">
                                    @foreach ($languages as $item)
                                        <button class="nav-link @if (get_default_language_code() == $item->code) active @endif"
                                            id="edit-modal-{{ $item->code }}-tab" data-bs-toggle="tab"
                                            data-bs-target="#edit-modal-{{ $item->code }}" type="button"
                                            role="tab" aria-controls="edit-modal-{{ $item->code }}"
                                            aria-selected="true">{{ $item->name }}</button>
                                    @endforeach
                                </div>
                            </nav>
                            <div class="tab-content" id="nav-tabContent">

                                @foreach ($languages as $item)
                                    @php
                                        $lang_code = $item->code;
                                    @endphp
                                    <div class="tab-pane @if (get_default_language_code() == $item->code) fade show active @endif"
                                        id="edit-modal-{{ $item->code }}" role="tabpanel"
                                        aria-labelledby="edit-modal-{{ $item->code }}-tab">
                                        <div class="form-group">
                                            @include('admin.components.form.input', [
                                                'label' => __('Title'),
                                                'label_after' => '*',
                                                'placeholder' => __('Write Here') . '...',
                                                'name' => $lang_code . '_title_edit',
                                                'value' => old(
                                                    $lang_code . '_title_edit',
                                                    $data->value->language->$lang_code->title ?? ''),
                                            ])
                                        </div>
                                        <div class="form-group">
                                            @include('admin.components.form.input', [
                                                'label' => __('Description'),
                                                'label_after' => '*',
                                                'placeholder' => __('Write Here') . '...',
                                                'name' => $lang_code . '_description_edit',
                                                'value' => old(
                                                    $lang_code . '_description_edit',
                                                    $data->value->language->$lang_code->description ?? ''),
                                            ])
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="col-xl-12 col-lg-12 form-group">
                            @include('admin.components.form.input', [
                                'label' => __('Icon'),
                                'label_after' => '*',
                                'placeholder' => __('Write Here') . '...',
                                'name' => 'icon_edit',
                                'value' => old('icon_edit'),
                                'class' => 'form--control icp icp-auto iconpicker-element iconpicker-input',
                            ])
                        </div>
                        <div class="col-xl-12 col-lg-12 form-group d-flex align-items-center justify-content-between mt-4">
                            <button type="button" class="btn btn--danger modal-close">{{ __('Cancel') }}</button>
                            <button type="submit" class="btn btn--base">{{ __('Update') }}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script src="{{ asset('backend/js/fontawesome-iconpicker.js') }}"></script>

    <script>
        // icon picker
        $('.icp-auto').iconpicker();
    </script>
    <script>
        openModalWhenError("vendor-item-add", "#vendor-item-add");
        openModalWhenError("vendor-item-edit", "#vendor-item-edit");

        var default_language = "{{ $default_lang_code }}";
        var system_default_language = "{{ $system_default_lang }}";
        var languages = "{{ $languages_for_js_use }}";
        languages = JSON.parse(languages.replace(/&quot;/g, '"'));

        $(".edit-modal-button").click(function() {
            var oldData = JSON.parse($(this).parents("tr").attr("data-item"));
            var editModal = $("#vendor-item-edit");

            editModal.find("form").first().find("input[name=target]").val(oldData.id);

            $.each(languages, function(index, item) {
                editModal.find("input[name=" + item.code + "_title_edit]").val(oldData.language[item.code]
                    ?.title);
                editModal.find("input[name=" + item.code + "_description_edit]").val(oldData.language[item.code]
                    ?.description);
                editModal.find("input[name=icon_edit]").val(oldData.icon);
            });
            openModalBySelector("#vendor-item-edit");

        });

        $(".delete-modal-button").click(function() {
            var oldData = JSON.parse($(this).parents("tr").attr("data-item"));

            var actionRoute = "{{ setRoute('admin.setup.sections.section.item.delete', 'vendor-banner') }}";
            var target = oldData.id;
            var message = `{{ __('Are you sure to') }} <strong>{{ __('delete') }}</strong> {{ __('item?') }}`;

            openDeleteModal(actionRoute, target, message);
        });
    </script>
@endpush
