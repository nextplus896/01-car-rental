@extends('frontend.layouts.master')
@section('content')
    <!-- Car booking preview -->

    <section class="appointment-preview ptb-60">
        <div class="container">
            <div class="row justify-content-center mb-30-none">
                <div class="col-xl-8 col-lg-8 col-md-12 mb-30">
                    <div class="booking-area">
                        <div class="content pt-0">
                            <h3 class="title"><i class="fas fa-info-circle text--base mb-20"></i> {{ __('Booking Preview') }}
                            </h3>
                            <div class="list-wrapper">
                                <ul class="list">
                                    <li>{{ __('Pick-up location') }} :<span>{{ $customer->data->location }}</span></li>
                                    <li>{{ __('Destination') }} :<span>{{ $customer->data->destination }}</span></li>
                                    <li>{{ __('Pick-up date') }}
                                        :<span>{{ $customer->data->pickup_date ? \Carbon\Carbon::parse($customer->data->pickup_date)->format('d-m-Y') : '' }}</span>
                                    </li>
                                    <li>{{ __('Pick-up time') }}
                                        :<span>{{ $customer->data->pickup_time ? \Carbon\Carbon::parse($customer->data->pickup_time)->format('h:i A') : '' }}</span>
                                    </li>
                                    <li>{{ __('Round Trip Date') }}
                                        :<span>{{ $customer->data->round_pickup_date ? \Carbon\Carbon::parse($customer->data->round_pickup_date)->format('d-m-Y') : 'N/A' }}</span>
                                    </li>
                                    <li>{{ __('Round Trip Time') }}
                                        :<span>{{ $customer->data->round_pickup_time ? \Carbon\Carbon::parse($customer->data->round_pickup_time)->format('h:i A') : 'N/A' }}</span>
                                    </li>
                                    <li>{{ __('Car Model') }} :<span>{{ $car->car_model }}</span></li>
                                    <li>{{ __('Car Number') }} :<span>{{ $car->car_number }}</span></li>
                                    <li>{{ __('Distance') }} :<span>{{ $customer->data->distance }}
                                            {{ __('KM') }}</span></li>
                                    <li>{{ __('Rate') }} :<span>{{ get_amount($car->fees) }}/{{ __('KM') }}
                                            {{ $default_currency->code }}</li>
                                    <li>{{ __('Total Rent') }} :<span>
                                            {{ $total_rent }} {{ $default_currency->code }}
                                        </span>
                                    </li>
                                    <li id="ttl-pay">{{ __('Total Payable') }} :<span
                                            class="pay-in-total"> -- </span></li>
                                </ul>
                            </div>
                            <form action="{{ setRoute('user.car.booking.confirm', $customer->identifier) }}"
                                method="POST">
                                @csrf
                                <div class="payment-type">
                                    <div class="payment-type-select">
                                        <label class="title">{{ __('Pay With') }} :</label>
                                        <div class="select-payment-option">
                                            <div class="form-check">
                                                <input class="d-none" name="car_id" value="{{ $car->id }}">
                                                <input class="d-none" name="total_rent" value="{{ $total_rent }}">
                                                <input class="form-check-input cash-radio" type="radio" name="payment"
                                                    id="flexRadioDefault1" value="cash" checked>
                                                <label class="form-check-label" for="flexRadioDefault1">
                                                    {{ __('Cash Balance') }}
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input pay-radio" type="radio" name="payment"
                                                    id="flexRadioDefault2" value="online-payment">
                                                <label class="form-check-label" for="flexRadioDefault2">
                                                    {{ __('Other Payment Method') }}
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="payment-page-section" style="display: block;">
                                        <div class="payment-page-area">
                                            <div class="payment-type pt-20">
                                                <div class="select-payment-area">
                                                    <label class="title">{{ __('Select Payment Method') }}</label>
                                                    <div class="radio-wrapper pt-2" id="pg-view">
                                                        @forelse ($payment_gateways ?? [] as $key => $gateway)
                                                            <div class="radio-item">
                                                                <input type="radio" id="level-{{ $key }}"
                                                                    class="hide-input select-gateway" name="gateway"
                                                                    data-key='{{ $key }}'
                                                                    data-crypto="{{ $gateway->crypto }}"
                                                                    data-supported-currency='{{ $gateway->currencies }}'>
                                                                <label for="level-{{ $key }}">
                                                                    <img src="{{ get_image($gateway->image, 'payment-gateways') }}"
                                                                        alt="icon">
                                                                    {{ __($gateway->name) }}
                                                                </label>
                                                            </div>
                                                        @empty
                                                        @endforelse
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="currency-select">
                                            <select class="form--control select-2" name="gateway_currency">
                                                <option value="" selected>{{ __('please select gateway') }}</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="btn-area mt-20">
                                        <button class="btn--base w-100" type="submit">
                                            {{ __('Confirm Booking') }}
                                            <i class="fas fa-check-circle ms-1"></i>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
    </section>
@endsection

@push('script')
    <script>
        // Function to update the display based on the selected payment option
        function updatePaymentSection() {
            const walletRadio = document.getElementById('flexRadioDefault1');
            const paymentSection = document.querySelector('.payment-page-section');

            if (walletRadio.checked) {
                paymentSection.style.display = 'none';
            } else {
                paymentSection.style.display = 'block';
            }
        }

        // Add event listeners to the radio buttons
        document.getElementById('flexRadioDefault1').addEventListener('change', updatePaymentSection);
        document.getElementById('flexRadioDefault2').addEventListener('change', updatePaymentSection);

        // Initial call to set the correct display on page load
        updatePaymentSection();
    </script>

    <script>
        $(document).on("change", ".select-gateway", function() {
            var selectedGateway = $(this);
            var dataKey = selectedGateway.attr("data-key");
            var crypto = selectedGateway.attr("data-crypto");
            var supportedCurrencies = JSON.parse(selectedGateway.attr("data-supported-currency"));
            var $select = $(
                '<select class="form--control currency-option select-2" name="gateway_currency" required></select>'
                );
            $select.append(`<option value="" selected>Select Currency</option>`);
            $.each(supportedCurrencies, function(index, currency) {
                $select.append(
                    `<option class="currency-option" data-currency="${currency['currency_code']}" data-rate="${currency['rate']}" data-percent="${currency['percent_charge']}" data-fixed="${currency['fixed_charge']}" value="${currency['alias']}">${currency['currency_code']}</option>`
                    );
            });
            $('.currency-select').empty().append($select);

            var defualCurrency = "{{ get_default_currency_code() }}";
            var presion = 4;
            $('select[name=gateway_currency]').on('change', function() {
                isCrypto(),
                    getExchangeRate();
                getFees();
                activeItems();
            });
            $(document).ready(function() {
                isCrypto(),
                    getExchangeRate();
                getFees();
            });

            function isCrypto() {
                if (crypto == 1) {
                    presion = 4;
                } else {
                    presion = 2;
                }
                return presion;
            }

            function getExchangeRate() {
                if (acceptVar().selectedVal.val() === "null") {
                    return false;
                }
                var currencyCode = acceptVar().currencyCode;
                var currencyRate = acceptVar().currencyRate;
                var currencyMinAmount = acceptVar().currencyMinAmount;
                var currencyMaxAmount = acceptVar().currencyMaxAmount;
                $('.rate-show').html("1 " + defualCurrency + " = " + parseFloat(currencyRate).toFixed(presion) +
                    " " + currencyCode);
            }

            function getLimit() {
                var sender_currency = acceptVar().currencyCode;
                var sender_currency_rate = acceptVar().currencyRate;
                var min_limit = acceptVar().currencyMinAmount;
                var max_limit = acceptVar().currencyMaxAmount;
                if ($.isNumeric(min_limit) || $.isNumeric(max_limit)) {
                    var min_limit_calc = parseFloat(min_limit / sender_currency_rate).toFixed(presion);
                    var max_limit_clac = parseFloat(max_limit / sender_currency_rate).toFixed(presion);
                    $('.limit-show').html("{{ __('limit') }} " + min_limit_calc + " " + defualCurrency + " - " +
                        max_limit_clac + " " + defualCurrency);
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

            function acceptVar() {
                var selectedVal = $("select[name=gateway_currency] :selected");
                var currencyCode = $("select[name=gateway_currency] :selected").attr("data-currency");
                var currencyRate = $("select[name=gateway_currency] :selected").attr("data-rate");
                var cryptoType = $("select[name=currency-select] :selected").attr("data-crypto");
                var currencyFixedCharge = $("select[name=gateway_currency] :selected").attr("data-fixed");
                var currencyPercentCharge = $("select[name=gateway_currency] :selected").attr("data-percent");

                return {
                    currencyCode: currencyCode,
                    currencyRate: currencyRate,
                    cryptoType: cryptoType,
                    currencyFixedCharge: currencyFixedCharge,
                    currencyPercentCharge: currencyPercentCharge,
                    selectedVal: selectedVal,

                };
            }

            function feesCalculation() {
                var sender_currency = acceptVar().currencyCode;
                var sender_currency_rate = acceptVar().currencyRate;
                var sender_amount = "{{ $total_rent }}";
                sender_amount == "" ? (sender_amount = 0) : (sender_amount = sender_amount);

                var fixed_charge = acceptVar().currencyFixedCharge;
                var percent_charge = acceptVar().currencyPercentCharge;
                if ($.isNumeric(percent_charge) && $.isNumeric(fixed_charge) && $.isNumeric(sender_amount)) {
                    // Process Calculation
                    var fixed_charge_calc = parseFloat(fixed_charge);
                    var percent_charge_calc = ((parseFloat(sender_amount) / 100) * parseFloat(percent_charge)) * parseFloat(sender_currency_rate);
                    var total_charge = parseFloat(fixed_charge_calc) + parseFloat(percent_charge_calc);
                    total_charge = parseFloat(total_charge).toFixed(4);
                    // return total_charge;
                    return {
                        total: total_charge,
                        fixed: fixed_charge_calc,
                        percent: percent_charge,
                    };
                } else {
                    // return "--";
                    return false;
                }
            }

            function getFees() {
                var sender_currency = acceptVar().currencyCode;
                var percent = acceptVar().currencyPercentCharge;
                var charges = feesCalculation();
                if (charges == false) {
                    return false;
                }
                $(".fees-show").html("{{ __('charge') }}: " + parseFloat(charges.fixed).toFixed(presion) + " " +
                    sender_currency + " + " + parseFloat(charges.percent).toFixed(presion) + "% = " +
                    parseFloat(charges.total).toFixed(presion) + " " + sender_currency);
            }

            function activeItems() {
                var selectedVal = acceptVar().selectedVal.val();
                if (selectedVal === undefined || selectedVal === '' || selectedVal === null) {
                    return false;
                } else {
                    return getPreview();
                }
            }

            function getPreview() {
                var senderAmount = JSON.parse("{{ $total_rent }}");
                var sender_currency = acceptVar().currencyCode;
                var sender_currency_rate = acceptVar().currencyRate;
                // var receiver_currency = acceptVar().rCurrency;
                senderAmount == "" ? senderAmount = 0 : senderAmount = senderAmount;

                // Sending Amount
                $('.request-amount').text(senderAmount + " " + defualCurrency);

                // Fees
                var charges = feesCalculation();
                // console.log(total_charge + "--");
                $('.fees').text(charges.total + " " + sender_currency);

                // will get amount
                // var willGet = parseFloat(senderAmount) - parseFloat(charges.total);
                var willGet = parseFloat(senderAmount).toFixed(presion);
                $('.will-get').text(willGet + " " + defualCurrency);

                // Pay In Total
                var totalPay = parseFloat(senderAmount) * parseFloat(sender_currency_rate)
                var pay_in_total = parseFloat(charges.total) + parseFloat(totalPay);
                $('.pay-in-total').text(parseFloat(pay_in_total).toFixed(presion) + " " + sender_currency);
            }
        });
    </script>
    <script>
        $(document).ready(function() {
            $('#ttl-pay').hide();
            $('input[name="payment"]').on('change', function() {
                console.log('done');
                if ($('#flexRadioDefault2').is(':checked')) {
                    $('#ttl-pay').show();
                } else {
                    $('#ttl-pay').hide();
                }
            });
        });
    </script>
@endpush
