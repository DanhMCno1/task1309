<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ProductController;

Route::post('/cart', [CartController::class, 'addToCart']);
Route::delete('/cart/{productId}', [CartController::class, 'removeFromCart']);
Route::put('/cart/{productId}', [CartController::class, 'updateCartItemQuantity']);

Route::get('/products', [ProductController::class, 'getProducts']);
Route::get('/cart-items', [CartController::class, 'index']);

Route::put('/cart/{productId}', [CartController::class, 'updateCartItemQuantity']);




