<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DiscountDemoController;

Route::get('/', function () {
    return view('welcome');
});



Route::get('/discount-demo', [DiscountDemoController::class, 'index'])->name('discount.demo');
Route::post('/discount-demo', [DiscountDemoController::class, 'apply'])->name('discount.demo.apply');
Route::post('/discount-demo/create', [DiscountDemoController::class, 'store'])->name('discount.demo.create');
