@if (admin_permission_by_name("admin.subscriber.send.mail"))
    <div id="send-mail-subscribers" class="mfp-hide large">
        <div class="modal-data">
            <div class="modal-header px-0">
                <h5 class="modal-title">{{ __("Send mail to all subscribers") }}</h5>
            </div>
            <div class="modal-form-data">
                <form class="card-form" action="{{ setRoute('admin.subscriber.send.mail') }}" method="POST">
                    @csrf
                    <div class="row mb-10-none">
                        <div class="col-xl-12 col-lg-12 form-group">
                            @include('admin.components.form.input',[
                                'label'         => __("Subject"),
                                'label_after'   => "*",
                                'name'          => "subject",
                                'data_limit'    => 150,
                                'placeholder'   => __("Write Here").'...',
                                'value'         => old('subject'),
                            ])
                        </div>
                        <div class="col-xl-12 col-lg-12 form-group">
                            @include('admin.components.form.input-text-rich',[
                                'label'         => __("Details"),
                                'label_after'   => "*",
                                'name'          => "message",
                                'placeholder'   => __("Write Here").'...',
                                'value'         => old('message'),
                            ])
                        </div>
                        <div class="col-xl-12 col-lg-12 form-group">
                            @include('admin.components.button.form-btn',[
                                'class'         => "w-100 btn-loading",
                                'permission'    => "admin.subscriber.send.mail",
                                'text'          => __("Send Email"),
                            ])
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push("script")

    @endpush
@endif
