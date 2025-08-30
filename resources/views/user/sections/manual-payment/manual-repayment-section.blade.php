<?php
$default = get_default_language_code() ?? 'en';
$default_lng = 'en';
?>
@extends('frontend.layouts.master')
@section('content')
    <section class="manual-payment-section ptb-80">
        <div class="container">
                <div class="row justify-content-center">
                    <div class="col-xl-7 col-lg-8 col-md-10">
                    <div class="payment-section-area">
                        <h2 class="title text-center"></h2>
                        <div class="manual-payment-form pt-3">
                            <h3 class="title">{{ __('Reject reason') }}</h3>
                            <p>
                                {{ $reject_reason }}
                            </p>
                        </div>
                    </div>
                    <div class="payment-section-area">
                        <h2 class="title text-center"></h2>
                        <div class="manual-payment-form pt-3">
                            <h3 class="title">{{ __('Please follow the instructions below') }}</h3>
                            <p>
                                @php
                                    echo @$gateway->desc;
                                @endphp
                            </p>
                            <div class="form-fild">
                                <form action="{{ setRoute('user.car.booking.repayment.submit', $trx_id) }}" method="POST"
                                    enctype="multipart/form-data">
                                    @csrf
                                    <div class="row mb-10-none">
                                        @foreach ($gateway->input_fields as $item)
                                            @if ($item->type == 'select')
                                                <div class="col-lg-12 mb-10">
                                                    <label for="{{ $item->name }}">{{ $item->label }}
                                                        @if ($item->required == true)
                                                            <span class="text-white">*</span>
                                                        @else
                                                            <span class="">( {{ __('Optional') }} )</span>
                                                        @endif
                                                    </label>
                                                    <select name="{{ $item->name }}" id="{{ $item->name }}"
                                                        class="form--control nice-select">
                                                        <option selected disabled>{{ __('Choose One') }}</option>
                                                        @foreach ($item->validation->options as $innerItem)
                                                            <option value="{{ $innerItem }}">{{ $innerItem }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            @elseif ($item->type == 'file')
                                                <div class="col-lg-12 form-group">
                                                    <label for="{{ $item->name }}">{{ $item->label }}
                                                        @if ($item->required == true)
                                                            <span class="text-white">*</span>
                                                        @else
                                                            <span class="">( {{ __('Optional') }} )</span>
                                                        @endif
                                                    </label>
                                                    <div class="file-holder-wrapper">
                                                        <input type="{{ $item->type }}" class="form--control"
                                                            name="{{ $item->name }}" value="{{ old($item->name) }}">
                                                    </div>
                                                </div>
                                            @elseif ($item->type == 'text')
                                                <div class="col-lg-12 form-group">
                                                    <label for="{{ $item->name }}">{{ $item->label }}
                                                        @if ($item->required == true)
                                                            <span class="text-white">*</span>
                                                        @else
                                                            <span class="">( {{ __('Optional') }} )</span>
                                                        @endif
                                                    </label>
                                                    <input type="{{ $item->type }}" class="form--control"
                                                        placeholder="{{ ucwords(str_replace('_', ' ', $item->name)) }}"
                                                        name="{{ $item->name }}" value="{{ old($item->name) }}">
                                                </div>
                                            @elseif ($item->type == 'textarea')
                                                <div class="col-lg-12 form-group">
                                                    @include('admin.components.form.textarea', [
                                                        'label' => $item->label,
                                                        'name' => $item->name,
                                                        'value' => old($item->name),
                                                    ])
                                                </div>
                                            @endif
                                        @endforeach
                                        <div class="planbuy-btn pt-20">
                                            <button type="submit" class="btn--base w-100">{{ __('Submit') }}</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
