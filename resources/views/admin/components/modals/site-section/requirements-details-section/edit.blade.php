<div id="requirements-details-edit" class="mfp-hide large">
    <div class="modal-data">
        <div class="modal-header px-0">
            <h5 class="modal-title">{{ __('Edit Requirement') }}</h5>
        </div>
        <div class="modal-form-data">
            <form class="modal-form" method="POST" action="{{ setRoute('admin.setup.sections.requirements.update') }}">
                @csrf
                @method('PUT')
                <div class="row mb-10-none mt-3">
                    <!-- Main ID -->
                    <input type="hidden" name="main_id" value="{{ $data->id }}">

                    <!-- Details Item ID -->
                    <input type="hidden" name="details_item_id" value="{{ $details_id }}">
                    <div class="language-tab">
                        <nav>
                            <div class="nav nav-tabs" id="nav-tab" role="tablist">
                                @foreach ($languages as $item)
                                    <button class="nav-link @if (get_default_language_code() == $item->code) active @endif"
                                        id="modal-{{ $item->code }}-tab" data-bs-toggle="tab"
                                        data-bs-target="#modal-{{ $item->code }}" type="button" role="tab"
                                        aria-controls="modal-{{ $item->code }}"
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
                                    id="modal-{{ $item->code }}" role="tabpanel"
                                    aria-labelledby="modal-{{ $item->code }}-tab">
                                    <div class="form-group">
                                        @include('admin.components.form.input', [
                                            'label' => __('Details') . '*',
                                            'name' => $lang_code . '_details',
                                            'value' => old(
                                                $lang_code . '_details',
                                                $data->value->$lang_code->details ?? ''),
                                            'class' =>
                                                'form--control icp icp-auto iconpicker-element iconpicker-input',
                                        ])
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                </div>
                <div class="col-xl-12 col-lg-12 form-group d-flex align-items-center justify-content-between mt-4">
                    <button type="button" class="btn btn--danger modal-close">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn--base">{{ __('Add') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
