<table class="custom-table transaction-search-table">
    <thead>
        <tr>
            <th>{{ __('TRX ID') }}</th>
            <th>{{ __('Full Name') }}</th>
            <th>{{ __('Email') }}</th>
            <th>{{ __('Username') }}</th>
            <th>{{ __('Phone') }}</th>
            <th>{{ __('Amount') }}</th>
            <th>{{ __('Gateway') }}</th>
            <th>{{ __('Status') }}</th>
            @if ($page_slug == Str::slug(App\Constants\CarBookingConst::CAR_REFUND))
                <th>{{ __('Refundable') }}</th>
            @endif
            <th>{{ __('Time') }}</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        @forelse ($transactions  as $key => $item)
            <tr data-item="{{ json_encode($item->only(['trx_id', 'refundable'])) }}">
                <td>{{ $item->trx_id }}</td>
                <td>{{ $item->user->fullname }}</td>
                <td>{{ $item->user->email }}</td>
                <td>{{ $item->user->username }}</td>
                <td>{{ $item->user->mobile ?? '' }}</td>
                <td>{{ $item->request_amount }} {{ get_default_currency_code() }}</td>
                <td><span class="text--info">{{ $item['gateway_currency']->name }}</span></td>
                <td>
                    <span class="{{ $item->stringStatus->class }}">{{ __($item->stringStatus->value) }}</span>
                </td>
                @if ($page_slug == Str::slug(App\Constants\CarBookingConst::CAR_REFUND))
                    <td><span class="{{ $item->stringRefund->class }}">{{ __($item->stringRefund->value) }}</span></td>
                    <td>
                        @if ($item->refundable == 2)
                            {{ __('Pending') }}
                        @elseif ($item->refundable == 1)
                            {{ __('Complete') }}
                        @endif <br>
                        @include('admin.components.link.custom', [
                            'href' => '#status-change',
                            'class' => 'btn btn--base status-button modal-btn',
                            'text' => __('Update Status'),
                            'permission' => 'admin.add.money.status',
                        ])
                    </td>
                @endif
                <td>{{ $item->created_at->format('d-m-y h:i:s A') }}</td>
                <td>
                    @include('admin.components.link.info-default', [
                        'href' => setRoute('admin.add.money.details', $item->id),
                        'permission' => 'admin.add.money.details',
                    ])
                </td>
            </tr>
        @empty
            @include('admin.components.alerts.empty', ['colspan' => 11])
        @endforelse
    </tbody>
</table>
@if (admin_permission_by_name('admin.booking.state'))
    <div id="status-change" class="mfp-hide large">
        <div class="modal-data">
            <div class="modal-header px-0">
                <h5 class="modal-title">{{ __('Update Status') }}</h5>
            </div>
            <div class="modal-form-data">
                <form class="card-form" action="{{ setRoute('admin.add.money.refund.status') }}" method="POST">
                    @csrf
                    <input type="hidden" name="target" value="{{ old('target') }}">
                    <div class="row mb-10-none">
                        <div class="col-xl-12 col-lg-12 form-group">
                            <select class="form--control" name="status">
                                <option disabled selected value="">{{ __('Select Status') }}</option>
                                <option value="{{ App\Constants\PaymentGatewayConst::STATUSPENDING }}">
                                    {{ __('Pending') }}
                                </option>
                                <option value="{{ App\Constants\PaymentGatewayConst::STATUSSUCCESS }}">
                                    {{ __('Completed') }}
                                </option>
                            </select>
                        </div>
                        <div class="col-xl-12 col-lg-12 form-group">
                            @include('admin.components.button.form-btn', [
                                'class' => 'w-100 btn-loading',
                                'text' => __('Submit'),
                            ])
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif

@push('script')
    <script>
        openModalWhenError("#status-change", "#status-change");
        $(".status-button").click(function() {
            var oldData = JSON.parse($(this).parents("tr").attr("data-item"));
            $("#status-change").find("input[name=target]").val(oldData.trx_id);
            $("#status-change").find("select[name=status]").val(oldData.refundable);
            setTimeout(() => {
                $("#status-change").find("select[name=status]").select2();
            }, 300);
        });
    </script>
@endpush
