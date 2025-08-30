@extends('vendor-end.layouts.master')

@section('breadcrumb')
    @include('user.components.breadcrumb', [
        'breadcrumbs' => [
            [
                'name' => __('Dashboard'),
                'url' => setRoute('user.dashboard'),
            ],
        ],
        'active' => __('Money Withdraw'),
    ])
@endsection

@section('content')
    <div class="dashboard-list-area mt-60 mb-30">
        <div class="dashboard-header-wrapper">
            <h4 class="title">{{ __('Withdraw Logs') }}</h4>
        </div>
    </div>
    <div class="dashboard-list-wrapper">
        @forelse ($transactions ?? [] as $value)
            <div class="dashboard-list-item-wrapper">
                <div class="dashboard-list-item sent">
                    <div class="dashboard-list-left">
                        <div class="dashboard-list-user-wrapper">
                            <div class="dashboard-list-user-icon">
                                <i class="las la-arrow-up"></i>
                            </div>
                            <div class="dashboard-list-user-content">
                                <h4 class="title">{{ __('Withdraw Money') }} <span
                                        class="text-info">{{ $value->gateway_currency->gateway->name }}</span></h4>
                                @if ($value->status === payment_gateway_const()::STATUSSUCCESS)
                                    <span class="badge badge--success ms-2">{{ __('Success') }}</span>
                                @elseif ($value->status === payment_gateway_const()::STATUSPENDING)
                                    <span class="badge badge--warning ms-2">{{ __('Pending') }}</span>
                                @elseif ($value->status === payment_gateway_const()::STATUSREJECTED)
                                    <span class="badge badge--danger ms-2">{{ __('Reject') }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="dashboard-list-right">
                        <h4 class="main-money text--base">
                            {{ get_amount($value->request_amount) }} {{ $value->payment_currency }}
                        </h4>
                        <h5 class="exchange-money">{{ $value->created_at->format('d-m-Y') }}</h5>
                    </div>
                </div>
                <div class="preview-list-wrapper">
                    <div class="preview-list-item">
                        <div class="preview-list-left">
                            <div class="preview-list-user-wrapper">
                                <div class="preview-list-user-icon">
                                    <i class="las la-exchange-alt"></i>
                                </div>
                                <div class="preview-list-user-content">
                                    <span>{{ __('TRX ID') }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="preview-list-right">
                            <span>{{ $value->trx_id }}</span>
                        </div>
                    </div>
                    <div class="preview-list-item">
                        <div class="preview-list-left">
                            <div class="preview-list-user-wrapper">
                                <div class="preview-list-user-icon">
                                    <i class="las la-share-square"></i>
                                </div>
                                <div class="preview-list-user-content">
                                    <span>{{ __('Amount') }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="preview-list-right">
                            <span>{{ get_amount($value->request_amount) }} {{ $value->payment_currency }}</span>
                        </div>
                    </div>
                    <div class="preview-list-item">
                        <div class="preview-list-left">
                            <div class="preview-list-user-wrapper">
                                <div class="preview-list-user-icon">
                                    <i class="las la-coins"></i>
                                </div>
                                <div class="preview-list-user-content">
                                    <span>{{ __('Gateway') }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="preview-list-right">
                            <span>{{ $value->gateway_currency->gateway->name }}</span>
                        </div>
                    </div>
                    <div class="preview-list-item">
                        <div class="preview-list-left">
                            <div class="preview-list-user-wrapper">
                                <div class="preview-list-user-icon">
                                    <i class="las la-battery-half"></i>
                                </div>
                                <div class="preview-list-user-content">
                                    <span>{{ __('Fees & Charges') }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="preview-list-right">
                            <span>{{ get_amount($value->total_charge) }} {{ get_default_currency_code() }}</span>
                        </div>
                    </div>
                    <div class="preview-list-item">
                        <div class="preview-list-left">
                            <div class="preview-list-user-wrapper">
                                <div class="preview-list-user-icon">
                                    <i class="las la-receipt"></i>
                                </div>
                                <div class="preview-list-user-content">
                                    <span>{{ __('Payable Amount') }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="preview-list-right">
                            <span>{{ get_amount($value->total_payable) }} {{ $value->request_currency }}</span>
                        </div>
                    </div>
                    <div class="preview-list-item">
                        <div class="preview-list-left">
                            <div class="preview-list-user-wrapper">
                                <div class="preview-list-user-icon">
                                    <i class="lab la-artstation"></i>
                                </div>
                                <div class="preview-list-user-content">
                                    <span>{{ __('Status') }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="preview-list-right">
                            @if ($value->status === payment_gateway_const()::STATUSSUCCESS)
                                <span class="badge badge--success ms-2">{{ __('Success') }}</span>
                            @elseif ($value->status === payment_gateway_const()::STATUSPENDING)
                                <span class="badge badge--warning ms-2">{{ __('Pending') }}</span>
                            @elseif ($value->status === payment_gateway_const()::STATUSREJECTED)
                                <span class="badge badge--danger ms-2">{{ __('Reject') }}</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="alert alert-primary text-center">{{ __('No Transactions Found!') }}</div>
        @endforelse
    </div>
    {{ $transactions->links() }}
@endsection

