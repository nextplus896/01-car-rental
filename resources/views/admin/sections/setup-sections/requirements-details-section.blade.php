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
    <div class="table-area mt-15">
        <div class="table-wrapper">
            <div class="table-header justify-content-between">
                <h6 class="title bg-none">{{ __($page_title) }}</h6>
                <div class="table-btn-area">
                    <a href="#requirements-details-add" class="btn--base modal-btn"><i class="fas fa-plus me-1"></i>
                        {{ __('Add Requirement Details') }}</a>
                </div>
            </div>
            <div class="table-responsive">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th></th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $details = json_decode(json_encode($item));

                        @endphp
                        @forelse ($details->detailsItem ?? [] as $key => $value)
                            <tr data-item="{{ json_encode($value) }}" data-id="{{ $key }}"
                                data-parent-id="{{ $details->id }}">
                                <td>{{ $value->language->$system_default_lang->details ?? 'No details available' }}</td>
                                <td>
                                    <button class="btn btn--base btn--danger delete-modal-button"><i
                                            class="las la-trash-alt"></i></button>
                                            <button class="btn btn--base edit-modal-button"><i class="las la-pencil-alt"></i></button>
                                </td>
                                @include('admin.components.modals.site-section.requirements-details-section.edit',
                                ['data' => $details, 'details_id' => $value->id ])
                            </tr>
                        @empty
                            @include('admin.components.alerts.empty', ['colspan' => 4])
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @include('admin.components.modals.site-section.requirements-details-section.add', [
        'data' => $details->id,
    ])
@endsection

@push('script')
    <script>
        openModalWhenError("requirements-details-add", "#requirements-details-add");
        openModalWhenError("requirements-details-edit", "#requirements-details-edit");

        var default_language = "{{ $default_lang_code }}";
        var system_default_language = "{{ $system_default_lang }}";
        var languages = "{{ $languages_for_js_use }}";
        languages = JSON.parse(languages.replace(/&quot;/g,'"'));
        console.log(languages);


        $(".edit-modal-button").click(function() {
            var oldData = JSON.parse($(this).parents("tr").attr("data-item"));



            var editModal = $("#requirements-details-edit");

            $.each(languages,function(index,item) {
                editModal.find("input[name="+item.code+"_details]").val(oldData.language[item.code]?.details);
            });
            editModal.find("input[name=details_item_id]").val(oldData.id);
            openModalBySelector("#requirements-details-edit");

        });

        $(".delete-modal-button").click(function() {
            var oldData = JSON.parse($(this).parents("tr").attr("data-item"));
            var target = $(this).parents("tr").attr("data-id"); // Get the specific ID
            var parentId = $(this).parents("tr").attr("data-parent-id"); // Get the parent ID
            var actionRoute =
                "{{ route('admin.setup.sections.requirements.delete', ['parentId' => '__parentId__', 'id' => '__id__']) }}"
                .replace('__parentId__', parentId)
                .replace('__id__', target);

            var message = `Are you sure to <strong>delete</strong> item?`;

            openDeleteModal(actionRoute, target, message);
        });
    </script>
@endpush
