@extends('vendor-end.layouts.master')

@push('css')
@endpush

@section('content')
    <div class="row mb-20-none">
        <div class="col-xl-12 col-lg-12 mb-20">
            <div class="custom-card mt-10">
                <div class="dashboard-header-wrapper">
                    <h4 class="title">{{ __('KYC-Verification') }}</h4>
                </div>
                <div class="kyc-area">
                    <div class="send-add-form row">
                        <div class="card-body">
                            @include('vendor-end.components.profile.kyc', compact('kyc_data'))
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
@endpush
