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
                            @if ($user->kyc_verified != global_const()::VERIFIED && $user->kyc_verified != global_const()::PENDING)
                                <div class="personal-details pb-4">
                                    <div class="row justify-content-center mb-20-none">
                                        <form class="row g-4" method="POST" action="{{ setRoute('vendor.kyc.submit') }}"
                                            enctype="multipart/form-data">
                                            @csrf
                                            @include('vendor-end.components.generate-kyc-fields', [
                                                'fields' => $kyc_fields,
                                            ])
                                            <div class="col-xl-12 col-lg-12 pt-5">
                                                <button type="submit" class="btn--base w-100">{{ __('Verify') }}</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
@endpush
