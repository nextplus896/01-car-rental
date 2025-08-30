<?php
$default = get_default_language_code() ?? 'en';
$default_lng = 'en';
?>
@extends('frontend.layouts.master')

@section('content')
 <div class="container ptb-60">
    <div class="row mb-30-none">
        <div class="col-lg-12 mb-30">
            <div class="dash-payment-item-wrapper">
                <div class="dash-payment-item active">
                    <div class="dash-payment-title-area">
                        <span class="dash-payment-badge">!</span>
                        <h5 class="title">{{ $page_title }}</h5>
                    </div>
                    <div class="dash-payment-body">
                        <form class="card-form" method="POST" action="{{ setRoute('vendor.withdraw.money.instruction.submit',$token) }}" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-lg-12 form-group">
                                    @csrf
                                    <div class="withdraw-instructions mb-4">
                                        {!! $gateway->desc ?? "" !!}
                                    </div>
                                </div>
                                <div class="col-lg-12 form-group">
                                    @include('vendor-end.components.money-out.generate-dy-input',['input_fields' => array_reverse($gateway->input_fields)])
                                </div>
                                <div class="col-xl-12 col-lg-12">
                                    <button type="submit" class="btn--base w-100 text-center">{{ __("Submit") }}</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
 </div>

@endsection

@push('script')

@endpush
