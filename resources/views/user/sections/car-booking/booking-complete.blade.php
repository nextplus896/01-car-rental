<?php
    $basic_settings = App\Models\Admin\BasicSettings::first();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title><?= $basic_settings->site_name?></title>
    <link
        href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,700;0,9..40,800;0,9..40,900;0,9..40,1000;1,9..40,400;1,9..40,500;1,9..40,600;1,9..40,700;1,9..40,800;1,9..40,900&family=Josefin+Sans:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;1,100;1,200;1,300;1,400;1,500;1,600;1,700&display=swap"
        rel="stylesheet">
    @include('partials.header-asset')
</head>

<body>

    <section class="confirmation-page">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xl-6 col-lg-7 col-md-10">
                    <div class="payment-area text-center pt-40">
                        <div class="loading-animation">
                            <div class="car-loader pb-40">
                                <img src="{{ asset('frontend/images/element/loder.gif') }}" class="car-gif">
                            </div>
                            <div class="tickmark-container pb-40" style="display: none;">
                                <svg class="checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52">
                                    <circle class="checkmark__circle" cx="26" cy="26" r="25"
                                        fill="none" />
                                    <path class="checkmark__check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8" />
                                </svg>
                            </div>
                            <h4 class="title">{{ __('Booking') }}...</h4>
                            <div class="conformation-footer pt-4" style="display: none;">
                                <div class="payment-conformation-footer">
                                    <a href="{{ setRoute('frontend.index') }}" class="btn--base w-100">{{ __('Go To Home') }}</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @include('partials.footer-asset')

    <script>
        // Function to handle booking confirmation animation flow
        document.addEventListener('DOMContentLoaded', function() {
            const carLoader = document.querySelector('.car-loader');
            const tickmarkContainer = document.querySelector('.tickmark-container');
            const title = document.querySelector('.title');
            const footerButton = document.querySelector('.conformation-footer');
            setTimeout(function() {
                carLoader.style.display = 'none';
                tickmarkContainer.style.display = 'block';

                // Update the title to "Your Booking Is Successfully Completed"
                title.textContent = 'Your Booking Is Successfully Completed';

                // After tickmark shows, display the button after 1 second
                setTimeout(function() {
                    footerButton.style.display = 'block';
                }, 1000);
            }, 3000); // Adjust the time for how long the car is visible
        });
    </script>

</body>

</html>
