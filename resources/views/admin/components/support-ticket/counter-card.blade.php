@if (isset($support_tickets))
    <div class="dashboard-area">
        <div class="dashboard-item-area">
            <div class="row">
                <div class="col-xxl-3 col-xl-3 col-lg-6 col-md-6 col-sm-12 mb-15">
                    <div class="dashbord-item">
                        <div class="dashboard-content">
                            <div class="left">
                                <h6 class="title">{{ __("Pending Tickets") }}</h6>
                                <div class="user-info">
                                    <h2 class="user-count">{{ $pending_ticket = $support_tickets->where("status",support_ticket_const()::PENDING)->count() }}</h2>
                                </div>
                                <div class="user-badge">
                                    <a href="{{ setRoute('admin.support.ticket.pending') }}" class="view-btn bg--warning">{{ __("View All") }}</a>
                                </div>
                            </div>
                            <div class="right">
                                @php
                                    $percent_count = get_percentage_from_two_number($support_tickets->count(),$pending_ticket);
                                @endphp
                                <div class="chart" id="chart6" data-percent="{{ $percent_count }}"><span>{{ $percent_count }}%</span></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xxl-3 col-xl-3 col-lg-6 col-md-6 col-sm-12 mb-15">
                    <div class="dashbord-item">
                        <div class="dashboard-content">
                            <div class="left">
                                <h6 class="title">{{ __("Active Tickets") }}</h6>
                                <div class="user-info">
                                    <h2 class="user-count">{{ $active_ticket = $support_tickets->where("status",support_ticket_const()::ACTIVE)->count() }}</h2>
                                </div>
                                <div class="user-badge">
                                    <a href="{{ setRoute('admin.support.ticket.active') }}" class="view-btn bg--info">{{ __("View All") }}</a>
                                </div>
                            </div>
                            <div class="right">
                                @php
                                    $percent_count = get_percentage_from_two_number($support_tickets->count(),$active_ticket);
                                @endphp
                                <div class="chart" id="chart7" data-percent="{{ $percent_count }}"><span>{{ $percent_count }}%</span></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xxl-3 col-xl-3 col-lg-6 col-md-6 col-sm-12 mb-15">
                    <div class="dashbord-item">
                        <div class="dashboard-content">
                            <div class="left">
                                <h6 class="title">{{ __("Solved Tickets") }}</h6>
                                <div class="user-info">
                                    <h2 class="user-count">{{ $solved_ticket = $support_tickets->where("status",support_ticket_const()::SOLVED)->count() }}</h2>
                                </div>
                                <div class="user-badge">
                                    <a href="{{ setRoute('admin.support.ticket.solved') }}" class="view-btn bg--success">{{ __("View All") }}</a>
                                </div>
                            </div>
                            <div class="right">
                                @php
                                    $percent_count = get_percentage_from_two_number($support_tickets->count(),$solved_ticket);
                                @endphp
                                <div class="chart" id="chart8" data-percent="{{ $percent_count }}"><span>{{ $percent_count }}%</span></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xxl-3 col-xl-3 col-lg-6 col-md-6 col-sm-12 mb-15">
                    <div class="dashbord-item">
                        <div class="dashboard-content">
                            <div class="left">
                                <h6 class="title">{{ __("All Tickets") }}</h6>
                                <div class="user-info">
                                    <h2 class="user-count">{{ $all_ticket = $support_tickets->count() }}</h2>
                                </div>
                                <div class="user-badge">
                                    <a href="{{ setRoute('admin.support.ticket.index') }}" class="view-btn bg--base">{{ __("View All") }}</a>
                                </div>
                            </div>
                            <div class="right">
                                @php
                                    $percent_count = get_percentage_from_two_number($support_tickets->count(),$all_ticket);
                                @endphp
                                <div class="chart" id="chart9" data-percent="{{ $percent_count }}"><span>{{ $percent_count }}%</span></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif

@push('script')
<script>
    // pie-chart
    $(function() {
        $('#chart6').easyPieChart({
            size: 80,
            barColor: '#f05050',
            scaleColor: false,
            lineWidth: 5,
            trackColor: '#f050505a',
            lineCap: 'circle',
            animate: 3000
        });
    });

    $(function() {
        $('#chart7').easyPieChart({
            size: 80,
            barColor: '#10c469',
            scaleColor: false,
            lineWidth: 5,
            trackColor: '#10c4695a',
            lineCap: 'circle',
            animate: 3000
        });
    });

    $(function() {
        $('#chart8').easyPieChart({
            size: 80,
            barColor: '#ffbd4a',
            scaleColor: false,
            lineWidth: 5,
            trackColor: '#ffbd4a5a',
            lineCap: 'circle',
            animate: 3000
        });
    });

    $(function() {
        $('#chart9').easyPieChart({
            size: 80,
            barColor: '#ff8acc',
            scaleColor: false,
            lineWidth: 5,
            trackColor: '#ff8acc5a',
            lineCap: 'circle',
            animate: 3000
        });
    });
</script>
@endpush
