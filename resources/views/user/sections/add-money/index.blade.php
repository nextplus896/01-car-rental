@extends('user.layouts.master')
    
@push('css')
    
@endpush

@section('breadcrumb')
    @include('user.components.breadcrumb',['breadcrumbs' => [
        [
            'name'  => __("Dashboard"),
            'url'   => setRoute("user.dashboard"),
        ]
    ], 'active' => __("Add Money")])
@endsection

@section('content')

@endsection

@push('script')
    
@endpush