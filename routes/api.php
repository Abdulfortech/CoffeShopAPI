<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\v1\AuthController;
use App\Http\Controllers\v1\CartController;
use App\Http\Controllers\v1\OrderController;
use App\Http\Controllers\v1\CoffeeController;
use App\Http\Controllers\v1\PaymentController;

Route::get('/', function (Request $request) {
    return response()->json('Welcome to Coffee Shop API');
});


// Authentication
Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'signin'])->name('login');
    Route::post('/register', [AuthController::class, 'signup'])->name('register');
    Route::post('/logout', [AuthController::class, 'signout'])->name('logout')->middleware('auth:api');
});

// Coffee
Route::middleware('auth:api')->prefix('coffees')->group(function () {
    Route::get('/', [CoffeeController::class, 'index'])->name('coffees.all');
    Route::get('/{id}', [CoffeeController::class, 'single'])->name('coffees.single');
    Route::post('/admin/add', [CoffeeController::class, 'store'])->name('coffees.store');
    Route::put('/admin/edit/{id}', [CoffeeController::class, 'update'])->name('coffees.update');
    Route::delete('/admin/delete/{id}', [CoffeeController::class, 'delete'])->name('coffees.delete');
});

// Cart
Route::middleware('auth:api')->prefix('cart')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('cart.all');
    Route::post('/add', [CartController::class, 'store'])->name('cart.store');
    Route::post('/remove/{id}', [CartController::class, 'remove'])->name('cart.remove');
    Route::post('/checkout', [CartController::class, 'checkout'])->name('cart.checkout');
});


// Order
Route::middleware('auth:api')->prefix('orders')->group(function () {
    Route::get('/', [OrderController::class, 'index'])->name('orders.all');
    Route::get('/{id}', [OrderController::class, 'single'])->name('orders.single');
    Route::get('/admin/orders/', [OrderController::class, 'all'])->name('orders.orders');
    Route::put('/admin/orders/{id}', [OrderController::class, 'update'])->name('orders.status');
});

// Payment
Route::middleware('auth:api')->prefix('payments')->group(function () {
    Route::post('/initiate', [PaymentController::class, 'initiate'])->name('payments.initiate');
    Route::post('/verify', [PaymentController::class, 'verify'])->name('payments.verify');
});