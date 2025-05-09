<?php

use App\Modules\Messages\Controllers\MessageController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth'])->prefix('messages')->name('messages.')->group(function () {

    // Store a new message (reply to a ticket)
    Route::post('/{ticket}', [MessageController::class, 'store'])->name('store');

});
