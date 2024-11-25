<?php

use App\Http\Controllers\BookController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('/api/1/nyt')->group(function () {
        Route::get('best-sellers', [BookController::class, 'getBestSellers']);
});
