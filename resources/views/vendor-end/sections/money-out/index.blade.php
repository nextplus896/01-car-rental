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
    <div class="row mb-20-none">
        <div class="col-xl-7 col-lg-7 mb-20">
            <div class="custom-card mt-10">
                <div class="dashboard-header-wrapper">
                    <h4 class="title">{{ __('Withdraw Money') }}</h4>
                </div>
                <div class="card-body">
                    <div class="exchange-area text-center mb-20">
                        <code class="text-center"><span>{{ __('Exchange Rate') }}</span><span class="rate-show"></span></code>
                    </div>
                    <form class="withdraw-form" action="{{ setRoute('vendor.withdraw.money.submit') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label>{{ __('Withdraw Amount') }}<span>*</span></label>
                            <div class="input-form">
                                <input type="float" name="amount" id="amount-input" class="form--control"
                                    placeholder="{{ __('Enter Amount') }}">
                                    <span class="charge-currency">{{ get_default_currency_code() }}</span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>{{ __('Receiving Gateway') }}<span>*</span></label>
                            <select class="nice-select" name="gateway_currency">
                                <option selected disabled value="0">{{ __('Select Gateway Currency') }}</option>
                                @forelse ($payment_gateways ?? [] as $currency)
                                    <option value="{{ $currency->alias }}"
                                        data-item="{{ json_encode($currency->only(['currency_code', 'rate', 'min_limit', 'max_limit', 'percent_charge', 'fixed_charge', 'crypto'])) }}">
                                        {{ $currency->name }}
                                    </option>
                                @empty
                                @endforelse
                            </select>
                            <code class="d-block mt-10 text-end balance-show">--</code>
                        </div>
                        <div class="col-xl-12 col-lg-12 form-group">
                            <div class="note-area">
                                <code class="d-block limit-show">--</code>
                                <code class="d-block charge-show">--</code>
                            </div>
                        </div>
                        <div class="sending-btn pt-3">
                            <button type="submit" class="btn--base w-100">{{ __('Withdraw Money') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-xl-5 col-lg-5 mb-20">
            <div class="custom-card mt-10">
                <div class="dashboard-header-wrapper">
                    <h4 class="title">{{ __('Summary') }}</h4>
                </div>
                <div class="card-body">
                    <div class="preview-list-wrapper">
                        <div class="preview-list-item">
                            <div class="preview-list-left">
                                <div class="preview-list-user-wrapper">
                                    <div class="preview-list-user-icon">
                                        <i class="las la-receipt"></i>
                                    </div>
                                    <div class="preview-list-user-content">
                                        <span>{{ __('Entered Amount') }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="preview-list-right">
                                <span class="text--success enter-amount">--</span>
                            </div>
                        </div>
                        <div class="preview-list-item">
                            <div class="preview-list-left">
                                <div class="preview-list-user-wrapper">
                                    <div class="preview-list-user-icon">
                                        <i class="las la-battery-half"></i>
                                    </div>
                                    <div class="preview-list-user-content">
                                        <span>{{ __('Total Fees & Charges') }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="preview-list-right">
                                <span class="text--warning fees">--</span>
                            </div>
                        </div>
                        <div class="preview-list-item">
                            <div class="preview-list-left">
                                <div class="preview-list-user-wrapper">
                                    <div class="preview-list-user-icon">
                                        <i class="lab la-get-pocket"></i>
                                    </div>
                                    <div class="preview-list-user-content">
                                        <span>{{ __('Will Get') }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="preview-list-right">
                                <span class="text--danger will-get">--</span>
                            </div>
                        </div>
                        <div class="preview-list-item">
                            <div class="preview-list-left">
                                <div class="preview-list-user-wrapper">
                                    <div class="preview-list-user-icon">
                                        <i class="las la-money-check-alt"></i>
                                    </div>
                                    <div class="preview-list-user-content">
                                        <span class="last">{{ __('Total Payable Amount') }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="preview-list-right">
                                <span class="text--info last payable">--</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="dashboard-list-area mt-60 mb-30">
        <div class="dashboard-header-wrapper">
            <h4 class="title">{{ __('Latest Withdraw Money') }}</h4>
            <div class="dashboard-btn-wrapper">
                <div class="dashboard-btn">
                    <a href="{{ setRoute('vendor.withdraw.money.logs') }}" class="btn--base">{{ __('View More') }}</a>
                </div>
            </div>
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
@endsection
@push('script')
    <script>
        let default_currency_code = "{{ get_default_currency_code() }}";
        let userBalanceRoute = "{{ setRoute('vendor.wallets.balance') }}";
        let criptoPrecision = {{ $basic_settings->crypto_precision_value ?? 8 }};
        let fiatPrecision = {{ $basic_settings->precision ?? 2 }};

        $(document).ready(function() {
            let gatewayCurrency = getSelectedCurrency($("select[name=gateway_currency]"));
            if (gatewayCurrency != false) {
                run(gatewayCurrency, false);
            }
        });

        $("select[name=gateway_currency]").change(function() {

            let gatewayCurrency = getSelectedCurrency($("select[name=gateway_currency]"));
            if (gatewayCurrency != false) {
                run(gatewayCurrency);
            }

        });
        $("input[name=amount]").keyup(function() {
            let gatewayCurrency = getSelectedCurrency($("select[name=gateway_currency]"));
            if (gatewayCurrency != false) {
                run(gatewayCurrency, false);
            }
        });

        function getSelectedCurrency(selectElement) {
            var selectedItem = selectElement.find(":selected");
            if (selectedItem != null, selectedItem != undefined) {
                return JSON.parse(selectedItem.attr("data-item"));
            }

            return false;
        }

        function run(gatewayCurrency, userBalance = true) {

            if (gatewayCurrency == false) {
                return false;
            }
            if (gatewayCurrency.length == 0) {
                return false;
            }

            let gatewayPrecision = gatewayCurrency.crypto == true ? criptoPrecision : fiatPrecision;

            function acceptVar() {
                return {
                    gatewayCurrencyCode: gatewayCurrency.currency_code ?? "",
                    gatewayCurrencyRate: gatewayCurrency.rate ?? 0,
                    gatewayCurrencyMinLimit: gatewayCurrency.min_limit ?? 0,
                    gatewayCurrencyMaxLimit: gatewayCurrency.max_limit ?? 0,
                    gatewayCurrencyPercentCharge: gatewayCurrency.percent_charge ?? 0,
                    gatewayCurrencyFixedCharge: gatewayCurrency.fixed_charge ?? 0,
                };
            }

            function getExchangeRate() {
                let gatewayCurrencyCode = acceptVar().gatewayCurrencyCode;
                let gatewayCurrencyRate = acceptVar().gatewayCurrencyRate;

                let rate = parseFloat(gatewayCurrencyRate);
                $('.rate-show').html("1 USD" + " = " + parseFloat(rate).toFixed(gatewayPrecision) + " " +
                    gatewayCurrencyCode);
                return rate;

            }
            getExchangeRate();
            let exchangeRate = getExchangeRate();

            function getLimit() {
                let gatewayCurrencyCode = acceptVar().gatewayCurrencyCode;
                let gatewayCurrencyRate = acceptVar().gatewayCurrencyRate;
                let min_limit = acceptVar().gatewayCurrencyMinLimit;
                let max_limit = acceptVar().gatewayCurrencyMaxLimit
                if ($.isNumeric(min_limit) && $.isNumeric(max_limit)) {
                    let min_limit_calc = parseFloat(min_limit) / parseFloat(exchangeRate);
                    let max_limit_clac = parseFloat(max_limit) / parseFloat(exchangeRate);

                    $('.limit-show').html("{{ __('Limit') }} " + parseFloat(min_limit_calc) + " " + "USD" + " - " +
                        parseFloat(max_limit_clac) + " " + "USD");
                    return {
                        minLimit: min_limit_calc,
                        maxLimit: max_limit_clac,
                    };
                } else {
                    $('.limit-show').html("--");
                    return {
                        minLimit: 0,
                        maxLimit: 0,
                    };
                }
            }
            getLimit();

            function feesCalculation() {
                let gatewayCurrencyCode = acceptVar().gatewayCurrencyCode;
                let gatewayCurrencyRate = acceptVar().gatewayCurrencyRate;
                let amount = $("input[name=amount]").val();
                amount = $.isNumeric(amount) ? parseFloat(amount) : 0;
                let fixed_charge = acceptVar().gatewayCurrencyFixedCharge;
                let percent_charge = acceptVar().gatewayCurrencyPercentCharge;
                if ($.isNumeric(percent_charge) && $.isNumeric(fixed_charge) && $.isNumeric(amount)) {
                    // Process Calculation
                    let fixed_charge_calc = parseFloat(fixed_charge) * parseFloat(1 / exchangeRate);
                    let percent_charge_calc = (parseFloat(amount) * parseFloat(percent_charge) / 100);
                    let total_charge = parseFloat(fixed_charge_calc) + parseFloat(percent_charge_calc);
                    total_charge = parseFloat(total_charge).toFixed(4);
                    // return total_charge;
                    return {
                        total: total_charge,
                        fixed: fixed_charge_calc,
                        percent: percent_charge_calc,
                    };
                } else {
                    // return "--";
                    return false;
                }
            }

            function getFees() {
                let gatewayCurrencyCode = acceptVar().gatewayCurrencyCode;
                let percent = acceptVar().gatewayCurrencyPercentCharge;
                let charges = feesCalculation();
                if (charges == false) {
                    return false;
                }
                $('.charge-show').html("{{ __('Charge:') }} " + parseFloat(charges.fixed).toFixed(4) + " " + "USD" +
                    " + " + parseFloat(percent).toFixed(gatewayPrecision) + "%");
            }
            getFees();

            function getPreview() {
                let amount = $("input[name=amount]").val();
                let gatewayCurrencyCode = acceptVar().gatewayCurrencyCode;
                amount == "" ? amount = 0 : amount = amount;

                // Sending Amount
                $('.enter-amount').text(parseFloat(amount).toFixed(gatewayPrecision) + " " + "USD");

                // Fees
                let charges = feesCalculation();
                $('.fees').text(parseFloat(charges.total).toFixed(gatewayPrecision) + " " + "USD");

                // will get amount
                let willGet = parseFloat(amount) * exchangeRate;
                $('.will-get').text(willGet.toFixed(gatewayPrecision) + " " + gatewayCurrencyCode);

                // Pay In Total
                let pay_in_total = parseFloat(charges.total) + parseFloat(amount);
                $('.payable').text(parseFloat(pay_in_total).toFixed(gatewayPrecision) + " " + "USD");
            }
            getPreview();

            function getUserBalance() {

                let CSRF = $("meta[name=csrf-token]").attr("content");
                let data = {
                    _token: CSRF,
                    target: 'USD',
                };
                // Make AJAX request for getting user balance
                $.post(userBalanceRoute, data, function() {
                    // success
                }).done(function(response) {
                    let balance = response.data;
                    balance = parseFloat(balance).toFixed(2);
                    $(".balance-show").html("{{ __('Available Balance') }} " + balance + " " + 'USD');
                }).fail(function(response) {
                    var response = JSON.parse(response.responseText);
                    throwMessage(response.type, response.message.error);
                });
            }
            getUserBalance();
        }

        document.addEventListener("DOMContentLoaded", function() {
            const quantityInput = document.getElementById('amount-input');
            quantityInput.addEventListener('input', function(event) {
                let inputValue = event.target.value.trim();
                if (inputValue === '' || inputValue <= '0') {
                    inputValue = '';
                }
                event.target.value = inputValue;
            });
        });
    </script>
@endpush
