<?php

/*
|--------------------------------------------------------------------------
| Application Routes for User Module
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

use Modules\Inventory\Controllers\Api\InventoryItemController;

Route::middleware(['api', 'logger'])->prefix('api/inventory')->group(function () {
    Route::get('items/{item}', [InventoryItemController::class, 'show'])->name('api.inventory.items.show');
    Route::get('items/{office}/disposable',[InventoryItemController::class, 'disposable'])->name('api.inventory.items.disposable');
});
