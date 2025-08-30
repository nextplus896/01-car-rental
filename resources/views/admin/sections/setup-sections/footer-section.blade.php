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

        .fileholder-files-view-wrp.accept-single-file .fileholder-single-file-view,
        .fileholder-files-view-wrp.fileholder-perview-single .fileholder-single-file-view {
            height: 330px !important;
        }
    </style>
@endpush

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
            <h6 class="title">{{ __('Contact Section') }}</h6>
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
                                            @include('admin.components.form.textarea', [
                                                'label' => __('Footer Description'),
                                                'label_after' => '*',
                                                'placeholder' => __('Write Here') . '...',
                                                'name' => $item->code . '_footer_desc',
                                                'value' => old(
                                                    $item->code . '_footer_desc',
                                                    $data->value->language->$lang_code->footer_desc ?? ''),
                                            ])
                                        </div>
                                        <div class="form-group">
                                            @include('admin.components.form.textarea', [
                                                'label' => __('Subscribe Description'),
                                                'label_after' => '*',
                                                'placeholder' => __('Write Here') . '...',
                                                'name' => $item->code . '_subscribe_desc',
                                                'value' => old(
                                                    $item->code . '_subscribe_desc',
                                                    $data->value->language->$lang_code->subscribe_desc ?? ''),
                                            ])
                                        </div>
                                        <div class="form-group">
                                            @include('admin.components.form.input', [
                                                'label' => __('Footer Text.'),
                                                'label_after' => '*',
                                                'placeholder' => __('Write Here') . '...',
                                                'name' => $item->code . '_footer_text',
                                                'value' => old(
                                                    $item->code . '_footer_text',
                                                    $data->value->language->$lang_code->footer_text ?? ''),
                                            ])
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-12 col-lg-12 form-group">
                        @include('admin.components.button.form-btn', [
                            'class' => 'w-100 btn-loading',
                            'text' => __('Update'),
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
                    <a href="#social-add" class="btn--base modal-btn"><i class="fas fa-plus me-1"></i>
                        {{ __('Add Social Icon') }}</a>
                </div>
            </div>
            <div class="table-responsive">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th>{{ __('Name') }}</th>
                            <th>{{ __('Icon') }}</th>
                            <th>{{ __('Link') }}</th>
                            <th>{{ __('Action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($data->value->items ?? [] as $key => $item)
                            <tr data-item="{{ json_encode($item) }}">
                                <td><a href="{{ $item->item_link ?? '' }}"
                                        target="_blank">{{ $item->item_name ?? '' }}</a></td>
                                <td><a href="{{ $item->item_link ?? '' }}" target="_blank">
                                        <i class="{{ $item->item_social_icon ?? '' }}"></i></a></td>
                                <td><a href="{{ $item->item_link ?? '' }}"
                                        target="_blank">{{ $item->item_link ?? '' }}</a></td>
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
    @include('admin.components.modals.site-section.add-social-icon')

    {{-- Social Item Edit Modal --}}
    <div id="social-edit" class="mfp-hide large">
        <div class="modal-data">
            <div class="modal-header px-0">
                <h5 class="modal-title">{{ __('Edit Social Icon') }}</h5>
            </div>
            <div class="modal-form-data">
                <form class="modal-form" method="POST"
                    action="{{ setRoute('admin.setup.sections.section.item.update', $slug) }}"
                    enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="target" value="">
                    <div class="row mb-10-none mt-3">
                        <div class="form-group">
                            @include('admin.components.form.input', [
                                'label' => __('Item Name') . '*',
                                'name' => 'item_name_edit',
                                'value' => old('item_name_edit', $data->value->items->item_name ?? ''),
                            ])
                        </div>
                        <div class="form-group">
                            @include('admin.components.form.input', [
                                'label' => __('Item Link') . '*',
                                'name' => 'item_link_edit',
                                'value' => old('item_link_edit', $data->value->items->item_link ?? ''),
                            ])
                        </div>
                        <div class="form-group">
                            @include('admin.components.form.input', [
                                'label' => __('Item Social Icon') . '*',
                                'name' => 'item_social_icon_edit',
                                'value' => old(
                                    'item_social_icon_edit',
                                    $data->value->items->item_social_icon ?? ''),
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

    @push('script')
        <script src="{{ asset('backend/js/fontawesome-iconpicker.js') }}"></script>
        <script>
            $(".input-field-generator .add-row-btn").click(function() {
                // alert();
                setTimeout(() => {
                    $('.icp-auto').iconpicker();
                }, 500);
            });
            // icon picker
            $('.icp-auto').iconpicker();
        </script>
        <script>
            openModalWhenError("social-add", "#social-add");
            openModalWhenError("social-edit", "#social-edit");

            var default_language = "{{ $default_lang_code }}";
            var system_default_language = "{{ $system_default_lang }}";
            var languages = "{{ $languages_for_js_use }}";
            languages = JSON.parse(languages.replace(/&quot;/g, '"'));

            $(".edit-modal-button").click(function() {
                var oldData = JSON.parse($(this).parents("tr").attr("data-item"));
                var editModal = $("#social-edit");

                // console.log(oldData);


                editModal.find("form").first().find("input[name=target]").val(oldData.id);
                console.log(oldData);
                $.each(languages, function(index, item) {
                    editModal.find("input[name=item_name_edit]").val(oldData.item_name);
                    editModal.find("input[name=item_link_edit]").val(oldData.item_link);
                    editModal.find("input[name=item_social_icon_edit]").val(oldData.item_social_icon);
                    editModal.find("input[name=item_social_icon_background_edit]").val(oldData
                        .item_social_icon_background);
                });

                openModalBySelector("#social-edit");

            });

            $(".delete-modal-button").click(function() {
                var oldData = JSON.parse($(this).parents("tr").attr("data-item"));

                var actionRoute = "{{ setRoute('admin.setup.sections.section.item.delete', $slug) }}";
                var target = oldData.id;
                console.log(target);
                var message = `Are you sure to delete item?`;

                openDeleteModal(actionRoute, target, message);
            });
        </script>
    @endpush
@endpush
