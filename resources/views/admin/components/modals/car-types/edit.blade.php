@if (admin_permission_by_name("admin.car.types.update"))
    <div id="car-types-edit" class="mfp-hide large">
        <div class="modal-data">
            <div class="modal-header px-0">
                <h5 class="modal-title">{{ __("Edit Car Type") }}</h5>
            </div>
            <div class="modal-form-data">
                <form class="modal-form" method="POST" action="{{ setRoute('admin.car.types.update') }}">
                    @csrf
                    @method("PUT")
                    <input type="hidden" name="target" value="{{ old('target') }}">
                    <div class="row mb-10-none mt-2">
                        <div class="col-xl-12 col-lg-12 form-group">
                            @include('admin.components.form.input',[
                                'label'         => __("Name")."*",
                                'name'          => 'edit_name',
                                'placeholder'   => __("Enter Name")."...",
                                'value'         => old('edit_name')
                            ])
                        </div>

                        <div class="col-xl-12 col-lg-12 form-group d-flex align-items-center justify-content-between mt-4">
                            <button type="button" class="btn btn--danger modal-close">{{ __("Cancel") }}</button>
                            <button type="submit" class="btn btn--base">{{ __("Update") }}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push("script")
        <script>
            openModalWhenError("car-types-edit","#car-types-edit");
            $(".edit-modal-button").click(function(){
                var oldData = JSON.parse($(this).parents("tr").attr("data-item"));
                var editModal = $("#car-types-edit");

                editModal.find("form").first().find("input[name=target]").val(oldData.id);
                editModal.find("input[name=edit_name]").val(oldData.name);

                openModalBySelector("#car-types-edit");
            });
        </script>
    @endpush
@endif
