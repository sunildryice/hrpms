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

use Illuminate\Support\Facades\Route;
use Modules\Inventory\Controllers\AssetAssignmentLogController;
use Modules\Inventory\Controllers\AssetConditionLogController;
use Modules\Inventory\Controllers\AssetController;
use Modules\Inventory\Controllers\AssetRecoverController;
use Modules\Inventory\Controllers\StoredAssetController;
use Modules\Inventory\Controllers\AssignedAssetController;
use Modules\Inventory\Controllers\InventoryItemController;
use Modules\Inventory\Controllers\InventoryAssetController;
use Modules\Inventory\Controllers\InventoryImportController;

//Route::middleware(['web', 'auth', 'logger', 'can:manage-employee'])->group(function () {
Route::middleware(['web', 'auth', 'logger'])->group(function () {
    Route::get('inventories', [InventoryItemController::class, 'index'])->name('inventories.index');
    Route::any('inventories/office', [InventoryItemController::class, 'officeUseIndex'])->name('inventories.office.use.index');
    Route::any('inventories/distribution', [InventoryItemController::class, 'distributionIndex'])->name('inventories.distribution.index');
    Route::get('inventories/create', [InventoryItemController::class, 'create'])->name('inventories.create');
    Route::post('inventories', [InventoryItemController::class, 'store'])->name('inventories.store');
    Route::get('inventories/{inventory}/show', [InventoryItemController::class, 'show'])->name('inventories.show');
   Route::get('inventories/{inventory}/edit', [InventoryItemController::class, 'edit'])->name('inventories.edit');
   Route::put('inventories/{inventory}', [InventoryItemController::class, 'update'])->name('inventories.update');
    Route::delete('inventories/{inventory}/destroy', [InventoryItemController::class, 'destroy'])->name('inventories.destroy');

    Route::get('inventories/{inventory}/assets', [InventoryAssetController::class, 'index'])->name('inventories.assets.index');
    Route::get('inventories/assets/{asset}/edit', [InventoryAssetController::class, 'edit'])->name('inventories.assets.edit');
    Route::put('inventories/assets/{asset}', [InventoryAssetController::class, 'update'])->name('inventories.assets.update');

    Route::get('inventories/import', [InventoryImportController::class, 'create'])->name('inventories.import.create');
    Route::post('inventories/import', [InventoryImportController::class, 'store'])->name('inventories.import.store');


    Route::get('assets/index', [AssetController::class, 'index'])->name('assets.index');
    Route::get('inventories/assets/{asset}/show', [AssetController::class, 'show'])->name('assets.show');

    Route::get('/assets/store/index', [StoredAssetController::class, 'index'])->name('assets.store.index');
    Route::get('inventories/assets/{asset}/store/show', [StoredAssetController::class, 'show'])->name('assets.store.show');

    Route::get('assets/assigned/index', [AssignedAssetController::class, 'index'])->name('assets.assigned.index');
    Route::get('inventories/assets/{asset}/assigned/show', [AssignedAssetController::class, 'show'])->name('assets.assigned.show');

    Route::get('asset/{asset}/recover/create', [AssetRecoverController::class, 'create'])->name('assets.recover.create');
    Route::post('asset/{asset}/recover', [AssetRecoverController::class, 'store'])->name('assets.recover.store');

    Route::get('asset/{asset}/condition/logs', [AssetConditionLogController::class, 'index'])->name('asset.condition.logs.index');
    Route::get('asset/{asset}/condition/logs/create', [AssetConditionLogController::class, 'create'])->name('asset.condition.logs.create');
    Route::post('asset/condition/logs', [AssetConditionLogController::class, 'store'])->name('asset.condition.logs.store');
    Route::get('asset/condition/logs/{assetConditionLog}/show', [AssetConditionLogController::class, 'show'])->name('asset.condition.logs.show');
    Route::get('asset/condition/logs/{assetConditionLog}/edit', [AssetConditionLogController::class, 'edit'])->name('asset.condition.logs.edit');
    Route::put('asset/condition/logs/{assetConditionLog}/update', [AssetConditionLogController::class, 'update'])->name('asset.condition.logs.update');
    Route::delete('asset/condition/logs/{assetConditionLog}/destroy', [AssetConditionLogController::class, 'destroy'])->name('asset.condition.logs.destroy');

    Route::get('asset/{asset}/assignment/logs', [AssetAssignmentLogController::class, 'index'])->name('asset.assignment.logs.index');

});
