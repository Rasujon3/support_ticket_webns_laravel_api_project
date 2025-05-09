<?php

use App\Modules\Product\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

// Products
Route::prefix('products')->group(function () {
    Route::get('/', [ProductController::class, 'index'])->name('products.index');
    Route::get('/create', [ProductController::class, 'create'])->name('products.create');
    Route::get('/{product}/view', [ProductController::class, 'view'])->name('products.view');
    Route::get('/edit/{product}', [ProductController::class, 'edit'])->name('products.edit');
    Route::post('/', [ProductController::class, 'store'])->name('products.store');
    Route::put('/{product}', [ProductController::class, 'update'])->name('products.update');
    Route::delete('/{product}', [ProductController::class, 'destroy'])->name('products.destroy');
});
