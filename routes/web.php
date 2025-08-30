<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('frontend.index');
})->name('index');


// Route::get('/public/{path}', function ($path) {
//     $fullPath = public_path($path);

//     if (!File::exists($fullPath)) {
//         abort(404);
//     }

//     $file = File::get($fullPath);
//     $type = File::mimeType($fullPath);

//     return Response::make($file, 200)->header("Content-Type", $type);
// })->where('path', '.*');
