@extends('admin.layouts.master')

@push('css')

    <style>
        .fileholder {
            min-height: 374px !important;
        }

        .fileholder-files-view-wrp.accept-single-file .fileholder-single-file-view,.fileholder-files-view-wrp.fileholder-perview-single .fileholder-single-file-view{
            height: 330px !important;
        }
    </style>
@endpush

@section('page-title')
    @include('admin.components.page-title',['title' => __($page_title)])
@endsection

@section('breadcrumb')
    @include('admin.components.breadcrumb',['breadcrumbs' => [
        [
            'name'  => __("Dashboard"),
            'url'   => setRoute("admin.dashboard"),
        ]
    ], 'active' => __("Car Approval")])
@endsection

@section('content')
    <div class="table-area">
        <div class="table-wrapper">
            <div class="table-header">
                <h5 class="title">{{ __($page_title) }}</h5>
            </div>
            <div class="table-responsive">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th></th>
                            <th>{{ __("Vendor") }}</th>
                            <th>{{ __("Car Model") }}</th>
                            <th>{{ __("Car Number") }}</th>
                            <th>{{ __("Seat") }}</th>
                            <th>{{ __("Experience") }}</th>
                            <th>{{ __("Fees") }}</th>
                            <th>{{ __("Car Area") }}</th>
                            <th>{{ __("Car Type") }}</th>
                            <th>{{ __("Status") }}</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($cars ?? [] as $key => $car)
                            <tr data-item="{{ $car }}">
                                <td>
                                    <ul class="user-list">
                                        <li><img src="{{ get_image($car->image ?? null,'site-section') }}" alt="image"></li>
                                    </ul>
                                </td>
                                <td>{{ ($car->vendor->firstname ?? '') ." ".  ($car->vendor->lastname ?? '')}}</td>
                                <td>{{ ($car->car_model ?? '') }}</td>
                                <td>{{ ($car->car_number ?? '') }}</td>
                                <td>{{ ($car->seat ?? '') }}</td>
                                <td>{{ ($car->experience ?? '') }}</td>
                                <td>{{ (get_amount($car->fees ?? '')) }}</td>
                                <td>{{ ($car->area->name ?? '') }}</td>
                                <td>{{ ($car->type->name ?? '') }}</td>
                                <td>
                                    @include('admin.components.form.switcher',[
                                        'name'        => 'status',
                                        'value'       => $car->approval,
                                        'options'     => [__("Approve") => 1, __("Not Approval") => 0],
                                        'onload'      => true,
                                        'data_target' => $car->id,
                                    ])
                                </td>
                                <td></td>
                            </tr>
                        @empty
                            @include('admin.components.alerts.empty',['colspan' => 5])
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        {{-- {{ get_paginate($car_area) }} --}}
    </div>


@endsection

@push('script')
    <script>
        $(document).ready(function(){
            // Switcher
            switcherAjax("{{ setRoute('admin.car.approval.update') }}");
        })
    </script>
@endpush
