@extends('frontend.layouts.master')

@push('css')
@endpush

<?php
$default = get_default_language_code() ?? 'en';
$default_lng = 'en';
?>

@section('content')
    <!-- Blog section  -->
    @include('frontend.sections.blogs')

    <!-- app section -->
    @include('frontend.sections.app')

@endsection


@push('script')
@endpush
