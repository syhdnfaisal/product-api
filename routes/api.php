<?php

use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentsController;
use App\Http\Controllers\ProductCategories;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::apiResource('products', ProductController::class);
Route::apiResource('product-categories', ProductCategories::class);

Route::prefix('orders')->group(function () {
    Route::get('/', [OrderController::class, 'index']);
    Route::post('/', [OrderController::class, 'store']);
    Route::get('/{id}', [OrderController::class, 'show']);
    Route::delete('/{id}', [OrderController::class, 'destroy']);
});

Route::prefix('payments')->group(function () {
    Route::post('/create', [PaymentsController::class, 'create']);
    Route::post('/xendit/webhook', [PaymentsController::class, 'webhook']);
    Route::get('/{order_id}', [PaymentsController::class, 'show']);
});
