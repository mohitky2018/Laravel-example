<?php

declare(strict_types=1);

use App\Http\Controllers\Web\OrderController;
use App\Http\Controllers\Web\ProductController;
use App\Http\Controllers\Web\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::resource('users', UserController::class);
Route::resource('products', ProductController::class);
Route::resource('orders', OrderController::class);

