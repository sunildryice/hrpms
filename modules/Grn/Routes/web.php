<?php

/*
|--------------------------------------------------------------------------
| Application Routes for User Module
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is ordered.
|
*/

use Modules\Grn\Controllers\ApproveController;
use Modules\Grn\Controllers\ApprovedController;
use Modules\Grn\Controllers\GrnController;
use Modules\Grn\Controllers\GrnItemController;
use Modules\Grn\Controllers\GrnItemInventoryController;

Route::middleware(['web', 'auth', 'logger'])->group(function () {
    Route::middleware('can:grn')->group(function () {
        Route::get('grns', [GrnController::class, 'index'])->name('grns.index');
        Route::get('grns/create', [GrnController::class, 'create'])->name('grns.create');
        Route::post('grns', [GrnController::class, 'store'])->name('grns.store');
        Route::get('grns/{grn}/show', [GrnController::class, 'show'])->name('grns.show');
        Route::get('grns/{grn}/edit', [GrnController::class, 'edit'])->name('grns.edit');
        Route::put('grns/{grn}', [GrnController::class, 'update'])->name('grns.update');
        Route::delete('grns/{grn}/destroy', [GrnController::class, 'destroy'])->name('grns.destroy');
        Route::get('grns/{grn}/print', [GrnController::class, 'printGrn'])->name('grns.print');
        Route::post('grns/{grn}/unreceive', [GrnController::class, 'unreceive'])->name('grns.unreceive.store');

        Route::get('grns/{grn}/items', [GrnItemController::class, 'index'])->name('grns.items.index');
        Route::get('grns/{grn}/items/create', [GrnItemController::class, 'create'])->name('grns.items.create');
        Route::post('grns/{grn}/items', [GrnItemController::class, 'store'])->name('grns.items.store');
        Route::get('grns/{grn}/items/{item}/edit', [GrnItemController::class, 'edit'])->name('grns.items.edit');
        Route::put('grns/{grn}/items/{item}/order', [GrnItemController::class, 'fromOrderUpdate'])->name('grns.items.from.order.update');
        Route::put('grns/{grn}/items/{item}', [GrnItemController::class, 'update'])->name('grns.items.update');
        Route::delete('grns/{grn}/items/{item}/destroy', [GrnItemController::class, 'destroy'])->name('grns.items.destroy');

        Route::get('grns/{grn}/items/add', [GrnController::class, 'addItem'])->name('grns.items.add.create');
        Route::post('grns/{grn}/items/add', [GrnController::class, 'updateItem'])->name('grns.items.add.store');

    });

//    Route::get('approve/grns', [ApproveController::class, 'index'])->name('approve.grns.index');
//    Route::get('approve/grns/{grn}/create', [ApproveController::class, 'create'])->name('approve.grns.create');
//    Route::post('approve/grns/{grn}', [ApproveController::class, 'store'])->name('approve.grns.store');

    Route::middleware('can:view-received-grn')->group(function () {
        Route::get('approved/grns', [ApprovedController::class, 'index'])->name('approved.grns.index');
        Route::get('approved/grns/{grn}/show', [ApprovedController::class, 'show'])->name('approved.grns.show');
    });

    Route::middleware('can:manage-inventory')->group(function () {
        Route::get('approved/grns/{grn}/items/bulk/inventory/create', [GrnItemInventoryController::class, 'createBulk'])->name('approved.grns.items.inventory.bulk.create');
        Route::post('approved/grns/{grn}/items/bulk/inventory', [GrnItemInventoryController::class, 'storeBulk'])->name('approved.grns.items.inventory.bulk.store');
        
        Route::get('approved/grns/{grn}/items/{item}/inventory/create', [GrnItemInventoryController::class, 'create'])->name('approved.grns.items.inventory.create');
        Route::post('approved/grns/{grn}/items/{item}/inventory', [GrnItemInventoryController::class, 'store'])->name('approved.grns.items.inventory.store');

    });
});
