<table class="custom-table transaction-search-table">
    <thead>
        <tr>
            <th></th>
            <th>{{ __('TRX ID') }}</th>
            <th>{{ __('Full Name') }}</th>
            <th>{{ __('Email') }}</th>
            <th>{{ __('Username') }}</th>
            <th>{{ __('Amount') }}</th>
            <th>{{ __('Gateway') }}</th>
            <th>{{ __('Status') }}</th>
            <th>{{ __('Time') }}</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        @forelse ($transactions ?? [] as $key => $item)
            <tr>
                @php
                    $currency = App\Models\Admin\Currency::where('code',$item->request_currency)->first();
                    $request_precision = $basic_settings->fiat_precision_value ?? 2;
                    if ($currency && $currency->type == 'CRYPTO') {
                        $request_precision = $basic_settings->crypto_precision_value ?? 8;
                    }
                @endphp
                <td>
                    <ul class="user-list">
                        <li><img src="{{ get_image($item->creator->image ?? '', 'user-profile') }}"
                                alt="user"></li>
                    </ul>
                </td>
                <td>{{ $item->trx_id ?? 'N/A' }}</td>
                <td>{{ $item->vendor->fullname ?? 'N/A' }}</td>
                <td>{{ $item->vendor->email ?? 'N/A' }}</td>
                <td>{{ $item->vendor->username ?? 'N/A' }}</td>
                <td>{{ get_amount($item->request_amount ?? 0, $item->request_currency ?? '', $request_precision) }}
                </td>
                <td><span class="text--info">{{ $item->gateway_currency->gateway->name ?? 'N/A' }}</span>
                </td>
                <td>
                    <span
                        class="{{ $item->stringStatus->class ?? '' }}">{{ __($item->stringStatus->value) ?? 'N/A' }}</span>
                </td>
                <td>{{ $item->created_at ? $item->created_at->format('d-m-y h:i:s A') : 'N/A' }}</td>
                <td>
                    @if ($item->status == 1)
                        <button type="button" class="btn btn--base bg--success"><i
                                class="las la-check-circle"></i></button>
                    @elseif($item->status == 4)
                        <button type="button" class="btn btn--base bg--danger"><i
                                class="las la-times-circle"></i></button>
                    @endif
                    @include('admin.components.link.custom', [
                        'href' => setRoute('admin.money.out.details', $item->id ?? ''),
                        'class' => 'btn btn--base modal-btn',
                        'icon' => 'las la-expand',
                        'permission' => 'admin.money.out.details',
                    ])
                </td>
            </tr>
        @empty
            @include('admin.components.alerts.empty', ['colspan' => 11])
        @endforelse
    </tbody>
</table>
