@extends('user.layouts.master')

@push('css')
@endpush

@section('breadcrumb')
    @include('user.components.breadcrumb', [
        'breadcrumbs' => [
            [
                'name' => __('Dashboard'),
                'url' => setRoute('user.dashboard'),
            ],
        ],
        'active' => __('Support Tickets'),
    ])
@endsection

@section('content')
    <div class="table-area mt-10">
        <div class="table-wrapper">
            <div class="dashboard-header-wrapper">
                <h4 class="title">{{ __('Support Tickets') }}</h4>
                <div class="dashboard-btn-wrapper">
                    <div class="dashboard-btn">
                        <a href="{{ route('user.support.ticket.create') }}" class="btn--base"><i class="las la-plus me-1"></i>
                            {{ __('Add New') }}</a>
                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th>{{ __('Ticket ID') }}</th>
                            <th>{{ __('Subject') }}</th>
                            <th>{{ __('Message') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th>{{ __('Last Reply') }}</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($support_tickets ?? [] as $item)
                            <tr>
                                <td>#{{ $item->token }}</td>
                                <td>
                                    <span
                                        class="@if ($item->stringStatus->value === 'Pending') text--warning
                                @elseif($item->stringStatus->value === 'Active')
                                    text--info @endif">
                                        {{ $item->subject }}
                                    </span>
                                </td>
                                <td>
                                    @if ($item->message)
                                        {{ Str::words($item->message, 10, '...') }}
                                    @else
                                        {{ __('Not given') }}
                                    @endif
                                </td>
                                <td>
                                    <span class="{{ $item->stringStatus->class }}">{{ $item->stringStatus->value }}</span>
                                </td>
                                <td>
                                    @if (getReply($item->id) != null)
                                        {{ getReply($item->id)->created_at->diffForHumans() }}
                                    @else
                                        {{ __('Not replied yet') }}
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('user.support.ticket.conversation', encrypt($item->id)) }}"
                                        class="btn btn--base"><i class="las la-comment"></i></a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="text-center text-warning" colspan="7">{{ __('Nothing to show yet') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <nav>
            {{ get_paginate($support_tickets) }}
        </nav>
    </div>
@endsection

@push('script')
    <script></script>
@endpush
