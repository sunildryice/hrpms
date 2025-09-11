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

use Modules\Master\Controllers\Api\ProvinceController;
use Modules\Master\Controllers\Api\DistrictController;

Route::middleware(['api', 'logger'])->prefix('api/contract')->group(function () {
});
