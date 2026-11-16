<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrderItemController;
Route::get('/orders', [OrderController::class, 'index']);
Route::get('/orders/{id}', [OrderController::class, 'show']);
Route::post('/orders', [OrderController::class, 'store']);
Route::put('/orders/{id}', [OrderController::class, 'update']);
Route::delete('/orders/{id}', [OrderController::class, 'destroy']);

Route::get('/order_items', [OrderItemController::class, 'index']);
Route::get('/order_items/{id}', [OrderItemController::class, 'show']);
Route::post('/order_items', [OrderItemController::class, 'store']);
Route::put('/order_items/{id}', [OrderItemController::class, 'update']);
Route::delete('/order_items/{id}', [OrderItemController::class, 'destroy']);