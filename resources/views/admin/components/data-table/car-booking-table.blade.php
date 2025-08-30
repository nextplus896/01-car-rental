<table class="custom-table booking-search-table">
    <thead>
        <tr>
            <th>{{ __('Trip ID') }}</th>
            <th>{{ __('Vendor') }}</th>
            <th>{{ __('User') }}</th>
            <th>{{ __('Car Type') }}</th>
            <th>{{ __('Car Number') }}</th>
            <th>{{ __('Amount') }}</th>
            <th>{{ __('Distance') }}</th>
            <th>{{ __('Status') }}</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        @forelse ($bookings ?? [] as $booking)
            <tr>
                <td>{{ $booking->trip_id }}</td>
                <td><span>{{ $booking->cars->vendor->firstname }}
                        {{ $booking->cars->vendor->lastname }}</span></td>
                <td>{{ $booking->user->firstname }} {{ $booking->user->lastname }}</td>
                <td>{{ $booking->cars->type->name }}</td>
                <td>{{ $booking->cars->car_number }}</td>
                <td>{{ get_amount($booking->amount) }} {{ get_default_currency_code() }}</td>
                <td>{{ get_amount($booking->distance) }} {{ __('Km') }}</td>
                <td>
                    @if ($booking->status === 1)
                        <span class="badge badge--warning">{{ __('Pending') }}</span>
                    @elseif($booking->status === 2)
                        <span class="badge badge--info">{{ __('On Going') }}</span>
                    @elseif($booking->status === 3)
                        <span class="badge badge--success">{{ __('Complete') }}</span>
                    @elseif($booking->status === 4)
                        <span class="badge badge--danger">{{ __('Reject') }}</span>
                    @endif
                </td>
                <td>
                    <a href="{{ setRoute('admin.booking.details',$booking->trip_id) }}" class="btn btn--base reply-button modal-btn"><i class="las la-info-circle"></i></a>
                </td>
                <td></td>
            </tr>
        @empty
            @include('admin.components.alerts.empty', ['colspan' => 9])
        @endforelse
    </tbody>
</table>
