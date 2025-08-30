
@extends('frontend.layouts.master')

@php
    $default = get_default_language_code() ?? 'en';
    $default_lng = 'en';
@endphp

@section('content')

<!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    Start Privacy
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->

<section class="blog-section style-01 ptb-120">
    <div class="container">
        <div class="row justify-content-center mb-30-none">
            <div class="col-xl-12 col-lg-12 mb-30">
                <div class="row justify-content-center mb-30-none">
                    <div class="col-xl-12 mb-30">
                        <div class="blog-item">
                            <div class="blog-content">
                                <h2 class="title mb-30 text-center"><a href="javascript:void(0)">{{ $page->title->language->$default->title ?? $page->title->language->$default_lng->title }}</a></h2>
                                @php
                                echo $page->content->language->$default->content ?? $page->content->language->$default_lng->content
                            @endphp
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    End Privacy
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
@endsection

