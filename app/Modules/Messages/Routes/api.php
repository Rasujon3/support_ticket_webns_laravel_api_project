<?php

use App\Modules\Messages\Controllers\MessageController;
use Illuminate\Support\Facades\Route;


Route::prefix('messages')->name('messages.')->middleware('auth:sanctum')->group(function () {
    Route::post('/{ticket}', [MessageController::class, 'store'])->name('store');
});
