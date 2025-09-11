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
use Modules\Privilege\Controllers\PermissionController;
use Modules\Privilege\Controllers\RoleController;
use Modules\Privilege\Controllers\UserController;

Route::middleware(['web','auth'])->prefix('privilege')->namespace('Modules\Privilege\Controllers')->group(function(){

        Route::get('permissions', [PermissionController::class, 'index'])->name('privilege.permissions.index');
        Route::get('permissions/create', [PermissionController::class, 'create'])->name('privilege.permissions.create');
        Route::post('permissions', [PermissionController::class, 'store'])->name('privilege.permissions.store');
        Route::get('permissions/{permission}/edit', [PermissionController::class, 'edit'])->name('privilege.permissions.edit');
        Route::put('permissions/{permission}', [PermissionController::class, 'update'])->name('privilege.permissions.update');
        Route::delete('permissions/{permission}', [PermissionController::class, 'destroy'])->name('privilege.permissions.destroy');
        Route::get('permissions/{permission}/view', [PermissionController::class, 'view'])->name('privilege.permissions.view');

        Route::get('roles', [RoleController::class, 'index'])->name('privilege.roles.index');
        Route::get('roles/create', [RoleController::class, 'create'])->name('privilege.roles.create');
        Route::post('roles', [RoleController::class, 'store'])->name('privilege.roles.store');
        Route::get('roles/{role}/edit', [RoleController::class, 'edit'])->name('privilege.roles.edit');
        Route::put('roles/{role}', [RoleController::class, 'update'])->name('privilege.roles.update');
        Route::delete('roles/{role}', [RoleController::class, 'destroy'])->name('privilege.roles.destroy');
        Route::get('roles/{role}/view', [RoleController::class, 'view'])->name('privilege.roles.view');

        Route::get('users', [UserController::class, 'index'])->name('privilege.users.index');
        Route::get('users/{user}/show', [UserController::class, 'show'])->name('privilege.users.show');
//        Route::get('users/export/excel', 'UserExportController@index')->name('users.export');
//        Route::put('user/change/status/{user}', 'UserController@changeStatus')->name('user.change.status');
//        Route::get('user/{user}/change/password', 'UserPasswordChangeController@getChangePassword')->name('user.get.change.password');
//        Route::post('user/change/password', 'UserPasswordChangeController@changePassword')->name('user.change.password');
});

Route::middleware(['web','auth'])->namespace('Modules\Privilege\Controllers')->group(function(){
    Route::get('profile', 'UserProfileController@index')->middleware(['guideline.checker'])->name('user.profile.index');
    Route::post('profile/update', 'UserProfileController@update')->name('user.profile.update');

    Route::get('profile/print/leavesummary', 'UserPrintController@printLeaveSummary')->middleware(['guideline.checker'])->name('user.leavesummary.print');

    Route::get('profile/print/inventories', 'UserPrintController@printInventories')->middleware(['guideline.checker'])->name('user.inventories.print');

    Route::get('user/{user}/department/edit', 'UserDepartmentController@edit')->name('user.department.edit');
    Route::put('user/department/{department}', 'UserDepartmentController@update')->name('user.department.update');

    Route::get('authority/delegation', 'UserDelegationController@index')->name('authority.delegation.index');
    Route::get('authority/delegation/create', 'UserDelegationController@create')->name('authority.delegation.create');
    Route::post('authority/delegation', 'UserDelegationController@store')->name('authority.delegation.store');
    Route::get('authority/delegation/{delegation}/edit', 'UserDelegationController@edit')->name('authority.delegation.edit');
    Route::put('authority/delegation/{delegation}', 'UserDelegationController@update')->name('authority.delegation.update');
    Route::put('authority/delegation/deactivate/{delegation}', 'UserDelegationController@deactivate')->name('authority.delegation.change.status');

    Route::get('profile/inventories', 'UserInventoryController@index')->name('user.profile.inventory.index');

    Route::get('guidelines', 'UserGuidelineController@index')->name('guideline.index');
    Route::get('guidelines/{guideline}', 'UserGuidelineController@show')->name('guideline.show');
});
