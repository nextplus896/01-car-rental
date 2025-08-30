@if ($basic_settings->vendor_kyc_verification == true && isset($kyc_data) && $kyc_data != null && $kyc_data->fields != null)
    @if (auth()->guard('vendor')->user()->kyc_verified == global_const()::PENDING)
        <div class="pending text--warning kyc-text">
            {{ __('Your KYC information is submitted. Please wait for admin confirmation. When you are KYC verified you will show your submitted information here.') }}
        </div>
    @elseif (auth()->guard('vendor')->user()->kyc_verified == global_const()::APPROVED)
        <div class="row justify-content-center mb-20-none">
            <div class="col-xl-8 col-lg-10 mb-20">
                <div class="kyc-preview mt-10">
                    <div class="kyc-title">
                        <i class="las la-exclamation-circle"></i>
                        <h3 class="title">{{ __('KYC-Status') }}</h3>
                    </div>
                    <div class="kyc-preview-area">
                        <p>{{ __('Status') }} : <span>{{ __('Success') }}</span></p>
                    </div>
                    <div class="submit-img">
                        <div class="row mb-20-none">
                            @foreach (auth()->guard('vendor')->user()->kyc->data ?? [] as $item)
                                @if ($item->type == 'file')
                                    @php
                                        $file_link = get_file_link('kyc-files', $item->value);
                                    @endphp
                                    @if (its_image($item->value))
                                        <div class="col-lg-6 col-md-6 col-sm-12 mb-20">
                                            <label>{{ $item->label }}:</label>
                                            <div class="kyc-image">
                                                <img src="{{ $file_link }}" alt="{{ $item->label }}">
                                            </div>
                                        </div>
                                    @else
                                        <span class="text--danger">
                                            @php
                                                $file_info = get_file_basename_ext_from_link($file_link);
                                            @endphp
                                            <a href="{{ setRoute('file.download', ['kyc-files', $item->value]) }}">
                                                {{ Str::substr($file_info->base_name ?? '', 0, 20) . '...' . $file_info->extension ?? '' }}
                                            </a>
                                        </span>
                                    @endif
                                @else
                                    <div class="kyc-preview-area">
                                        <p>{{ __('Submit Type') }}: <span> {{ $item->value }}</span></p>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @elseif (auth()->user()->kyc_verified == global_const()::REJECTED)
        <div class="row justify-content-center mb-20-none">
            <div class="col-xl-8 col-lg-10 mb-20">
                <div class="kyc-preview mt-10">
                    <div class="kyc-title">
                        <i class="las la-exclamation-circle"></i>
                        <h3 class="title">{{ __('KYC-Status') }}</h3>
                    </div>
                    <div class="kyc-preview-area">
                        <p>{{ __('Status') }}: <span>{{ __('Reject') }}</span></p>
                        <div class="rejected">
                            <div class="rejected-reason">{{ auth()->user()->kyc->reject_reason ?? '' }}</div>
                        </div>
                    </div>
                    <div class="submit-img">
                        <div class="row mb-20-none">
                            @foreach (auth()->guard('vendor')->user()->kyc->data ?? [] as $item)
                                @if ($item->type == 'file')
                                    @php
                                        $file_link = get_file_link('kyc-files', $item->value);
                                    @endphp
                                    @if (its_image($item->value))
                                        <div class="col-lg-6 col-md-6 col-sm-12 mb-20">
                                            <label>{{ $item->label }}:</label>
                                            <div class="kyc-image">
                                                <img src="{{ $file_link }}" alt="{{ $item->label }}">
                                            </div>
                                        </div>
                                    @else
                                        <span class="text--danger">
                                            @php
                                                $file_info = get_file_basename_ext_from_link($file_link);
                                            @endphp
                                            <a href="{{ setRoute('file.download', ['kyc-files', $item->value]) }}">
                                                {{ Str::substr($file_info->base_name ?? '', 0, 20) . '...' . $file_info->extension ?? '' }}
                                            </a>
                                        </span>
                                    @endif
                                @else
                                    <p>{{ $item->label }}: <span>{{ $item->type }}</span></p>
                                @endif
                            @endforeach
                        </div>
                    </div>
                    <div class="re-submit-btn pt-5 text-center">
                        <a href="{{ setRoute('vendor.kyc.re-submit') }}"
                            class="btn--base w-50">{{ __('Re Submit') }}</a>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="unverified kyc-text d-flex align-items-center justify-content-between">
            <div class="title">{{ __('Please verify KYC information') }}</div>
            <a href="{{ setRoute('vendor.authorize.kyc') }}" class="btn--base">{{ __('Verify KYC') }}</a>
        </div>
    @endif

@endif
