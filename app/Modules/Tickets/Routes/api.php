<?php

use App\Modules\Tickets\Controllers\TicketController;
use Illuminate\Support\Facades\Route;


Route::prefix('tickets')->middleware('auth:sanctum')->group(function () {
    // List all tickets (user)
    Route::get('/', [TicketController::class, 'index'])->name('index');

    // Show create ticket form
    Route::get('/create', [TicketController::class, 'create'])->name('create');

    // Store new ticket
    Route::post('/', [TicketController::class, 'store'])->name('store');

    // Show ticket detail (only creator or assigned admin)
    Route::get('/{ticket}', [TicketController::class, 'show'])->name('show');

    // Admin: update ticket status or assignment
//    Route::middleware('isAdmin')->group(function () {
        Route::get('/{ticket}/assign', [TicketController::class, 'assignForm'])->name('assign.form');
        Route::get('/{ticket}/status', [TicketController::class, 'statusForm'])->name('status.form');

        Route::put('/{ticket}/assign', [TicketController::class, 'assign'])->name('assign');
        Route::put('/{ticket}/status', [TicketController::class, 'updateStatus'])->name('status.update');
//    });
});
