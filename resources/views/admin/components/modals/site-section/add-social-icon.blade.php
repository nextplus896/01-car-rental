<div id="social-add" class="mfp-hide large">
    <div class="modal-data">
        <div class="modal-header px-0">
            <h5 class="modal-title">{{ __("Add Social Icon") }}</h5>
        </div>
        <div class="modal-form-data">
            <form class="modal-form" method="POST" action="{{ setRoute('admin.setup.sections.section.item.store',$slug) }}" enctype="multipart/form-data">
                @csrf
                <div class="row mb-10-none mt-3">
                    <div class="form-group">
                        @include('admin.components.form.input',[
                            'label'     => __("Item Name").'*',
                            'name'      => "item_name",
                            'value'     => old("item_name")
                        ])
                    </div>
                    <div class="form-group">
                        @include('admin.components.form.input',[
                            'label'     => __("Item Link").'*',
                            'name'      => "item_link",
                            'value'     => old("item_link")
                        ])
                    </div>
                    <div class="form-group">
                        @include('admin.components.form.input',[
                            'label'     => __("Item Social Icon").'*',
                            'name'      => "item_social_icon",
                            'value'     => old("item_social_icon"),
                            'class'     => "form--control icp icp-auto iconpicker-element iconpicker-input",
                        ])
                    </div>
                    <div class="col-xl-12 col-lg-12 form-group d-flex align-items-center justify-content-between mt-4">
                        <button type="button" class="btn btn--danger modal-close">{{ __("Cancel") }}</button>
                        <button type="submit" class="btn btn--base">{{ __("Add") }}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
