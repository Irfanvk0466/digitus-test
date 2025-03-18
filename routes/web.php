<?php

use App\Http\Controllers\BrandController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProductController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('brands', BrandController::class);
    Route::resource('products', controller: ProductController::class);
    Route::post('/import', [ProductController::class, 'import'])->name('products.import');
    Route::get('/export', [ProductController::class, 'export'])->name('products.export');
    Route::get('/payment/{productId}', [PaymentController::class, 'create'])->name('payment.create');
    Route::get('/payment/order/{orderId}', [PaymentController::class, 'show'])->name('payment.show');
    Route::post('/payment/verify', [PaymentController::class, 'verify'])->name('payment.verify');
    Route::get('/orders', [PaymentController::class, 'index'])->name('orders.index');
    Route::post('/orders/{orderId}/cancel', [PaymentController::class, 'cancelOrder'])->name('orders.cancel');
    Route::delete('/orders/{orderId}', [PaymentController::class, 'destroy'])->name('orders.delete');
});

