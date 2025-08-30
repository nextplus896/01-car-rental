<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Frontend\IndexController;

Route::name('frontend.')->group(function() {
    Route::controller(IndexController::class)->group(function() {
        Route::get('/','index')->name('index');
        Route::get('search/car','searchCar')->name('car.search');
        Route::get('/find/car','findCar')->name('find.car')->middleware('page.check:find-car');
        Route::get('/searched/cars','cars')->name('cars');
        Route::post('get/area/types','getAreaTypes')->name('get.area.types');
        Route::get('vendor-info','vendor')->middleware('page.check:vendor')->name('vendor');
        Route::get('about/us','aboutUs')->middleware('page.check:about-us')->name('aboutUs');
        Route::get('services','services')->middleware('page.check:services')->name('services');
        Route::get('blog','blog')->middleware('page.check:blog')->name('blog');
        Route::get('blog/detail/{id}','blogDetail')->name('blog.detail');
        Route::get('blog/category/{id}','categoryBlog')->name('blog.category');
        Route::get('contact','contact')->middleware('page.check:contact')->name('contact');
        Route::post("subscribe","subscribe")->name("subscribe");
        Route::post("contact/message/send","contactMessageSend")->name("contact.message.send");
        Route::get('link/{slug}','usefulLink')->name('useful.links');
        Route::post('languages/switch','languageSwitch')->name('languages.switch');
        Route::post('subscribers/store', 'subscribersStore')->name('subscribers.store');
    });
});
