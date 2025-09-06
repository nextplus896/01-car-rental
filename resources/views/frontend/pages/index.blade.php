@extends('frontend.layouts.master')

@push('css')
@endpush

<?php
$default = get_default_language_code() ?? 'en';
$default_lng = 'en';
?>

@section('content')
    <!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
            Start Banner Section
        ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->

    @include('frontend.sections.banner')
    <!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
            End Banner Section
        ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
    <!-- car-find -->
    @include('frontend.sections.car-find')

    @include('frontend.sections.features')

    <!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
                Start Security System
         ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
    @include('frontend.sections.security')
    <!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
                End Security System
         ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->

    <!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
            Why Choice US Section
        ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
    @include('frontend.sections.choose-us')
    <!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
            statistics-section
        ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
    @include('frontend.sections.statistics')
    <!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
            app-section
        ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
    @include('frontend.sections.app')
@endsection


@push('script')
    <script>
        const img = document.getElementById('myImg');
        let currentImageIndex = 1; // Start with the 4th image
        const totalImages = 19; // Adjust the total number of images
        const animationInterval = 200; // Adjust the interval between images in milliseconds
        let isForward = true; // Flag to indicate the direction of animation

        function changeImage() {
            if (isForward) {
                currentImageIndex = (currentImageIndex % totalImages) + 1;
            } else {
                currentImageIndex = (currentImageIndex === 1) ? totalImages : currentImageIndex - 1;
            }
            img.src = `frontend/images/car/car-${currentImageIndex}.webp`;
        }

        function toggleDirection() {
            isForward = !isForward;
        }

        function startAnimation() {
            setInterval(function() {
                changeImage();
                if (currentImageIndex === 1 || currentImageIndex === totalImages) {
                    toggleDirection();
                }
            }, animationInterval);
        }

        // Start the animation
        startAnimation();
    </script>
@endpush
