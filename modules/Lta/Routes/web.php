<?php

use Modules\Lta\Controllers\LtaController;
use Modules\Lta\Controllers\LtaItemController;

Route::middleware(['web', 'auth', 'logger'])->group(function () {
    Route::middleware('can:manage-lta')->group(function () {
        Route::get('lta', [LtaController::class, 'index'])->name('lta.index');
        Route::get('lta/create', [LtaController::class, 'create'])->name('lta.create');
        Route::post('lta', [LtaController::class, 'store'])->name('lta.store');
        Route::get('lta/{lta}', [LtaController::class, 'show'])->name('lta.show');
        Route::get('lta/{lta}/edit', [LtaController::class, 'edit'])->name('lta.edit');
        Route::put('lta/{lta}', [LtaController::class, 'update'])->name('lta.update');
        Route::delete('lta/{lta}', [LtaController::class, 'destroy'])->name('lta.destroy');
        Route::get('lta/{lta}/detail', [LtaController::class, 'detail'])->name('lta.detail');

        Route::get('lta/{lta}/items', [LtaItemController::class, 'index'])->name('lta.items.index');
        Route::get('lta/{lta}/items/create', [LtaItemController::class, 'create'])->name('lta.items.create');
        Route::post('lta/{lta}/items', [LtaItemController::class, 'store'])->name('lta.items.store');
        Route::get('lta/{lta}/items/{item}/edit', [LtaItemController::class, 'edit'])->name('lta.items.edit');
        Route::put('lta/{lta}/items/{item}', [LtaItemController::class, 'update'])->name('lta.items.update');
        Route::delete('lta/{lta}/items/{item}', [LtaItemController::class, 'destroy'])->name('lta.items.destroy');
    });
});
