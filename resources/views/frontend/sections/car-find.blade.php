<div class="car-find-action">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-7 col-md-8 col-sm-10">
                <div class="find-action">
                    <div class="animated-border">
                        <!-- Card Content -->
                        <div class="find-content">
                            <h2 class="title">{{ $find_car->value->language->$default->heading ?? ''  }}</h2>
                            <p>{{ $find_car->value->language->$default->sub_heading ?? '' }}
                            <div class="booking-btn pt-4">
                                <a href="{{ url($banner->value->button_link_one) }}" class="btn--base">{{ $find_car->value->language->$default->button_name_one ?? "" }}</a>
                            </div>
                        </div>
                        <!-- Car Animation -->
                        <div class="car-animation">
                            <img src="{{ asset('frontend/images/element/loder.gif') }}">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
