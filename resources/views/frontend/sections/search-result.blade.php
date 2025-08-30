<!-- serching data -->
<section class="car-list-area ptb-80">
    <div class="container">
        <div class="row justify-content-center mb-20-none">
            @if (@isset($cars))
                @forelse ($cars as $item)
                    <div class="col-lg-4 col-md-6 mb-20">
                        <div class="car-item">
                            <div class="car-img">
                                <img src="{{ get_image($item->image ?? '','site-section') ?? '' }}" alt="img">
                            </div>
                            <div class="car-details">
                                <h3 class="title">{{ $item->car_model ?? "" }}</h3>
                                <p>{{ __('Car Number') }} : <span>{{ $item->car_number ?? "" }}</span></p>
                                <p>{{ __('Total Seat') }} : <span>{{ $item->seat ?? "" }} {{ __('Seat') }}</span></p>
                                <p>{{ __('Km Charge') }} : <span>{{ get_amount($item->fees) }} {{ __(get_default_currency_code()) }}</span></p>
                                <p>{{ __('Experience') }} : <span>{{ $item->experience ?? "" }} {{ __('Year') }}</span></p>
                                <div class="booking-btn">
                                    <a href="{{ setRoute('user.car.booking.preview',[$token,$item->id ])}}" class="btn--base">{{ __('Book Now') }}</a>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-lg-12">
                        <h5 class="text-danger text-center">{{ __('Car not found!') }}</h5>
                    </div>
                @endforelse
            @endif
        </div>
    </div>
</section>
