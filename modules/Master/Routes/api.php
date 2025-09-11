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

use Modules\Master\Controllers\Api\ActivityCodeController;
use Modules\Master\Controllers\Api\DistrictController;
use Modules\Master\Controllers\Api\DsaCategoryController;
use Modules\Master\Controllers\Api\ItemController;
use Modules\Master\Controllers\Api\ProvinceController;

Route::middleware(['api', 'logger'])->prefix('api/master')->group(function () {
    Route::get('activity-codes/{activityCode}', [ActivityCodeController::class, 'show'])->name('api.master.activity.codes.show');
    Route::get('districts/{district}', [DistrictController::class, 'show'])->name('api.master.districts.show');
    Route::get('dsa-categories/{dsaCategory}', [DsaCategoryController::class, 'show'])->name('api.master.dsa.categories.show');
    Route::get('items/{item}', [ItemController::class, 'show'])->name('api.master.items.show');
    Route::get('provinces/{province}', [ProvinceController::class, 'show'])->name('api.master.provinces.show');
});
