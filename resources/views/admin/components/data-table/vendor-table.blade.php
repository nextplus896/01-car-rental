<table class="custom-table user-search-table">
    <thead>
        <tr>
            <th></th>
            <th>{{ __("Username") }}</th>
            <th>{{ __("Email") }}</th>
            <th>{{ __("Phone") }}</th>
            <th>{{ __("Status") }}</th>
            <th>{{ __("Action") }}</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($users ?? [] as $key => $item)
            <tr>
                <td>
                    <ul class="user-list">
                        <li><img src="{{ $item->userImage }}" alt="user"></li>
                    </ul>
                </td>
                <td><span>{{ $item->username }}</span></td>
                <td>{{ $item->email }}</td>
                <td>{{ $item->full_mobile ?? "N/A" }}</td>
                <td>
                    @if (Route::currentRouteName() == "admin.vendor.kyc.unverified")
                        <span class="{{ $item->kycStringStatus->class }}">{{ __($item->kycStringStatus->value) }}</span>
                    @else
                        <span class="{{ $item->stringStatus->class }}">{{ __($item->stringStatus->value) }}</span>
                    @endif
                </td>
                <td>
                    @if (Route::currentRouteName() == "admin.vendor.kyc.unverified")
                        @include('admin.components.link.info-default',[
                            'href'          => setRoute('admin.vendor.kyc.details', $item->username),
                            'permission'    => "admin.vendor.kyc.details",
                        ])
                    @else
                        @include('admin.components.link.info-default',[
                            'href'          => setRoute('admin.vendor.details', $item->username),
                            'permission'    => "admin.vendor.details",
                        ])
                    @endif
                </td>
            </tr>
        @empty
            @include('admin.components.alerts.empty',['colspan' => 7])
        @endforelse
    </tbody>
</table>
