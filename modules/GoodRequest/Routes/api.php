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

use Modules\GoodRequest\Controllers\Api\AssignValidateController;

Route::middleware(['api', 'logger'])->group(function () {
    Route::any('validate/inventory/item/assign', AssignValidateController::class)->name('validate.inventory.item.assign');
});
