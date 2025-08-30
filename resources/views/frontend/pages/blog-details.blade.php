@extends('frontend.layouts.master')

@push('css')
@endpush

<?php
$default = get_default_language_code() ?? 'en';
$default_lng = 'en';
?>

@section('content')
    @include('frontend.sections.blog-detail')
@endsection


@push('script')
@endpush
