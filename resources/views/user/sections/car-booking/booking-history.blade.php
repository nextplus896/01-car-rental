@extends('user.layouts.master')
@section('content')
    <div class="table-area mt-10">
        <div class="table-wrapper">
            <div class="dashboard-header-wrapper">
                <h4 class="title">{{ __('History List') }}</h4>
            </div>
            <div class="table-responsive">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th>{{ __('Pick-up location') }}</th>
                            <th>{{ __('Destination') }}</th>
                            <th>{{ __('Pick-up date') }}</th>
                            <th>{{ __('Pick-up time') }}</th>
                            <th>{{ __('Car Model') }}</th>
                            <th>{{ __('Car Number') }}</th>
                            <th>{{ __('Total Amount') }}</th>
                            <th>{{ __('Status') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($car_bookings ?? [] as $history)
                            <tr>
                                <td>{{ $history->location }}</td>
                                <td>{{ $history->destination }}</td>
                                <td>{{ $history->pickup_date }}</td>
                                <td>{{ $history->pickup_time }}</td>
                                <td>{{ $history->cars->car_model }}</td>
                                <td>{{ $history->cars->car_number }}</td>
                                <td>{{ get_amount($history->amount) }} {{ get_default_currency_code() }}</td>
                                <td>
                                    @if ($history->status === 3)
                                        <span class="badge badge--success">{{ __('Complete') }}</span>
                                    @elseif($history->status === 4)
                                        <span class="badge badge--danger">{{ __('Rejected') }}</span>
                                    @elseif($history->status === 2)
                                        <span class="badge badge--info">{{ __('On Going') }}</span>
                                    @elseif($history->status === 1)
                                        <span class="badge badge--warning">{{ __('Pending') }}</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="text-center text-warning" colspan="8">{{ __('Nothing to show yet') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        {{ $car_bookings->links() }}
    </div>
@endsection
