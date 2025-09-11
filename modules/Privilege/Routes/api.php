<?php

use Illuminate\Http\Request;
use Modules\Privilege\Controllers\Api\UserController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix('api/v1/privilege')->namespace('Modules\Privilege\Controllers\Api')->group(function(){
    Route::get('users/office/{office}', [UserController::class, 'officeUsers'])->name('api.office.users.index');

    Route::post('validate/email', 'UserController@validateEmail');
    Route::post('check/exist/email', 'UserController@checkExist');
    Route::post('search/user', 'UserController@search');
    Route::get('user/{user}', 'UserController@show');
});
