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
    ], 'active' => __("Bookings")])
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
                            <th>{{ __("Name") }}</th>
                            <th>{{ __("Email") }}</th>
                            <th>{{ __("Phone No") }}</th>
                            <th>{{ __("Pick-up Date & Time") }}</th>
                            <th>{{ __("Pick-up Location") }}</th>
                            <th>{{ __("Destination") }}</th>
                            <th>{{ __("Round Trip") }}</th>
                            <th>{{ __("Booking Status") }}</th>
                            <th>{{ __("Rate") }}</th>
                            <th>{{ __("Total Distance") }}</th>
                            <th>{{ __("Payable Amount") }}</th>
                            <th>{{ __("Action") }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($car_bookings ?? [] as $key => $item)
                            <tr data-item="{{ json_encode($item->only(['id','status'])) }}">
                                <td>{{ $key + $car_bookings->firstItem() }}</td>
                                <td>{{ $item->user->fullname ?? __("Unauthorized") }}</td>
                                <td>{{ $item->email }}</td>
                                <td>{{ $item->user->full_mobile ?? __("N/A") }}</td>
                                <td>
                                    <div>
                                        {{
                                            $item->pickup_date ? \Carbon\Carbon::parse($item->pickup_date)->format('d-m-Y') : ''
                                        }}
                                    </div>
                                    <div>
                                        {{
                                            $item->pickup_time ? \Carbon\Carbon::parse($item->pickup_time)->format('h:i A') : ''
                                        }}
                                    </div>
                                </td>
                                <td>{{ $item->location }}</td>
                                <td>{{ $item->destination }}</td>
                                <td>
                                    <div>
                                        {{ $item->round_pickup_date ?  __("Yes")  : __("No") }}
                                    </div>
                                    @if($item->round_pickup_date)
                                        <div>
                                            {{
                                                $item->round_pickup_date ? \Carbon\Carbon::parse($item->round_pickup_date)->format('d-m-Y') : ''
                                            }}
                                        </div>
                                        <div>
                                            {{
                                                $item->round_pickup_time ? \Carbon\Carbon::parse($item->round_pickup_time)->format('h:i A') : ''
                                            }}
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    @if($item->status == 1) {{ __("Booked") }} @elseif ($item->status == 2) {{ __("OnGoing") }} @else {{ __("Completed") }} @endif <br>
                                    @include('admin.components.link.custom',[
                                        'href'          => "#status-change",
                                        'class'         => "btn btn--base status-button modal-btn",
                                        'text'          => __("Update Status"),
                                        'permission'    => "admin.booking.status",
                                    ])
                                </td>
                                <td>{{ get_amount($item->cars->fees) ?? " " }}{{ __("/KM") }} {{ $default_currency->code }}</td>
                                <td>{{ ($item->distance == 0) ? __("N/A") : get_amount($item->distance, __("KM"),2) }}</td>
                                <td>{{ ($item->amount== 0) ? __("N/A") : get_amount($item->amount,$default_currency->code) }}</td>
                                <td>
                                    @include('admin.components.link.custom',[
                                        'href'          => "#fare-add",
                                        'class'         => "btn btn--base fare-button modal-btn",
                                        'text'          => __("Add Fare"),
                                        'permission'    => "admin.booking.fare",
                                    ])
                                    @include('admin.components.link.custom',[
                                        'href'          => "#send-reply",
                                        'class'         => "btn btn--base reply-button modal-btn",
                                        'icon'          => "las la-envelope-open-text",
                                        'permission'    => "admin.booking.messages.reply",
                                    ])
                                    <a href="{{ setRoute('admin.booking.details',$item->slug)}}" class="btn btn--base btn--primary"><i class="las la-info-circle"></i></a>
                                </td>
                            </tr>
                        @empty
                            @include('admin.components.alerts.empty',['colspan' => 13])
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        {{ get_paginate($car_bookings) }}
    </div>
      {{-- Add Fare Modal --}}
      @if (admin_permission_by_name("admin.booking.fare"))
      <div id="fare-add" class="mfp-hide large">
          <div class="modal-data">
              <div class="modal-header px-0">
                  <h5 class="modal-title">{{ __("Add Fare") }}</h5>
              </div>
              <div class="modal-form-data">
                  <form class="card-form" action="{{ setRoute('admin.booking.fare') }}" method="POST">
                      @csrf
                      <input type="hidden" name="target" value="{{ old('target') }}">
                      <div class="row mb-10-none">
                          <div class="col-xl-12 col-lg-12 form-group">
                                @include('admin.components.form.input',[
                                    'label'         => __("Total Kilometer")."*",
                                    'name'          => "distance",
                                    'type'          => 'number',
                                    'placeholder'   => __("Write Here")."...",
                                    'value'         => old('distance'),
                                ])
                          </div>
                          <div class="col-xl-12 col-lg-12 form-group">
                              @include('admin.components.button.form-btn',[
                                  'class'         => "w-100 btn-loading",
                                  'text'          => __("Submit"),
                              ])
                          </div>
                      </div>
                  </form>
              </div>
          </div>
      </div>
    @endif
    {{-- Send Mail Modal --}}
    @if (admin_permission_by_name("admin.booking.messages.reply"))
        <div id="send-reply" class="mfp-hide large">
            <div class="modal-data">
                <div class="modal-header px-0">
                    <h5 class="modal-title">{{ __("Send Reply") }}</h5>
                </div>
                <div class="modal-form-data">
                    <form class="card-form" action="{{ setRoute('admin.booking.messages.reply') }}" method="POST">
                        @csrf
                        <input type="hidden" name="target" value="{{ old('target') }}">
                        <div class="row mb-10-none">
                            <div class="col-xl-12 col-lg-12 form-group">
                                @include('admin.components.form.input',[
                                    'label'         => __("Subject")."*",
                                    'name'          => "subject",
                                    'data_limit'    => 150,
                                    'placeholder'   => __("Write Subject")."...",
                                    'value'         => old('subject'),
                                ])
                            </div>
                            <div class="col-xl-12 col-lg-12 form-group">
                                @include('admin.components.form.input-text-rich',[
                                    'label'         => __("Details")."*",
                                    'name'          => "message",
                                    'value'         => old('message'),
                                ])
                            </div>
                            <div class="col-xl-12 col-lg-12 form-group">
                                @include('admin.components.button.form-btn',[
                                    'class'         => "w-100 btn-loading",
                                    'permission'    => "admin.subscriber.reply",
                                    'text'          => "Send Email",
                                ])
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
      {{-- State Change Modal --}}
    @if (admin_permission_by_name("admin.booking.state"))
      <div id="status-change" class="mfp-hide large">
          <div class="modal-data">
              <div class="modal-header px-0">
                  <h5 class="modal-title">{{ __("Update Status") }}</h5>
              </div>
              <div class="modal-form-data">
                  <form class="card-form" action="{{ setRoute('admin.booking.status') }}" method="POST">
                      @csrf
                      <input type="hidden" name="target" value="{{ old('target') }}">
                      <div class="row mb-10-none">
                          <div class="col-xl-12 col-lg-12 form-group">
                            <select class="form--control" name="status">
                                <option disabled selected value="">{{ __("Select Status") }}</option>
                                <option value="{{ global_const()::BOOKED }}">{{ __("Booked") }}</option>
                                <option value="{{ global_const()::ONGOING}}">{{ __("Ongoing") }}</option>
                                <option value="{{ global_const()::COMPLETED}}">{{ __("Completed") }}</option>
                            </select>
                          </div>
                          <div class="col-xl-12 col-lg-12 form-group">
                              @include('admin.components.button.form-btn',[
                                  'class'         => "w-100 btn-loading",
                                  'text'          => __("Submit"),
                              ])
                          </div>
                      </div>
                  </form>
              </div>
          </div>
      </div>
    @endif
@endsection
@push('script')
    <script>
        openModalWhenError("send-reply","#send-reply");
        $(".reply-button").click(function(){
            var oldData = JSON.parse($(this).parents("tr").attr("data-item"));
            $("#send-reply").find("input[name=target]").val(oldData.id);
        });
    </script>
    <script>
         openModalWhenError("#status-change","#status-change");
        $(".status-button").click(function(){
            var oldData = JSON.parse($(this).parents("tr").attr("data-item"));
            console.log(oldData);

            $("#status-change").find("input[name=target]").val(oldData.id);
            $("#status-change").find("select[name=status]").val(oldData.status);

            setTimeout(() => {
                $("#status-change").find("select[name=status]").select2();
            }, 300);
        });
    </script>
     <script>
        openModalWhenError("#fare-add","#fare-add");
       $(".fare-button").click(function(){
           var oldData = JSON.parse($(this).parents("tr").attr("data-item"));
           console.log(oldData);

           $("#fare-add").find("input[name=target]").val(oldData.id);
       });
   </script>
@endpush
