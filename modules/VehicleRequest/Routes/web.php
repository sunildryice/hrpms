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

use Modules\VehicleRequest\Controllers\CloseController;
use Modules\VehicleRequest\Controllers\ClosedController;
use Modules\VehicleRequest\Controllers\VehicleRequestController;
use Modules\VehicleRequest\Controllers\ApproveController;
use Modules\VehicleRequest\Controllers\ApprovedController;
use Modules\VehicleRequest\Controllers\AssignController;

Route::middleware(['web', 'auth', 'logger'])->group(function () {
    Route::middleware('can:vehicle-request')->group(function () {
        Route::get('vehicle/requests', [VehicleRequestController::class, 'index'])->name('vehicle.requests.index');
        Route::get('vehicle/requests/create', [VehicleRequestController::class, 'create'])->name('vehicle.requests.create');
        Route::post('vehicle/requests', [VehicleRequestController::class, 'store'])->name('vehicle.requests.store');
        Route::get('vehicle/requests/{vehicleRequest}/edit', [VehicleRequestController::class, 'edit'])->name('vehicle.requests.edit');
        Route::put('vehicle/requests/{vehicleRequest}', [VehicleRequestController::class, 'update'])->name('vehicle.requests.update');
        Route::delete('vehicle/requests/{vehicleRequest}/destroy', [VehicleRequestController::class, 'destroy'])->name('vehicle.requests.destroy');
        Route::post('vehicle/requests/{vehicleRequest}/amend', [VehicleRequestController::class, 'amend'])->name('vehicle.requests.amend.store');
    });
    Route::get('vehicle/requests/{vehicleRequest}/show', [VehicleRequestController::class, 'show'])->name('vehicle.requests.show');
    Route::get('vehicle/requests/{vehicleRequest}/print', [VehicleRequestController::class, 'print'])->name('vehicle.requests.print');

    Route::middleware('can:approve-hire-vehicle-request')->group(function () {
        Route::get('approve/vehicle/requests', [ApproveController::class, 'index'])->name('approve.vehicle.requests.index');
        Route::get('approve/vehicle/requests/{vehicleRequest}/create', [ApproveController::class, 'create'])->name('approve.vehicle.requests.create');
        Route::post('approve/vehicle/requests/{vehicleRequest}', [ApproveController::class, 'store'])->name('approve.vehicle.requests.store');
    });
    Route::middleware('can:assign-office-vehicle')->group(function () {
        Route::get('assign/vehicle/requests', [AssignController::class, 'index'])->name('assign.vehicle.requests.index');
        Route::get('assign/vehicle/requests/{vehicleRequest}/show', [AssignController::class, 'show'])->name('assign.vehicle.requests.show');
        Route::get('assign/vehicle/requests/{vehicleRequest}/create', [AssignController::class, 'create'])->name('assign.vehicle.requests.create');
        Route::post('assign/vehicle/requests/{vehicleRequest}', [AssignController::class, 'store'])->name('assign.vehicle.requests.store');
    });

    Route::middleware('can:view-approved-vehicle-request')->group(function () {
        Route::get('approved/vehicle/requests', [ApprovedController::class, 'index'])->name('approved.vehicle.requests.index');
        Route::get('approved/vehicle/requests/{vehicleRequest}/show', [ApprovedController::class, 'show'])->name('approved.vehicle.requests.show');
        Route::get('approved/vehicle/requests/{vehicleRequest}/print', [ApprovedController::class, 'print'])->name('approved.vehicle.requests.print');

        Route::get('close/vehicle/requests/{vehicleRequest}/create',[CloseController::class, 'create'])->name('close.vehicle.requests.create');
        Route::post('close/vehicle/requests/{vehicleRequest}',[CloseController::class, 'store'])->name('close.vehicle.requests.store');

        Route::get('closed/vehicle/requests',[ClosedController::class, 'index'])->name('closed.vehicle.requests.index');
        Route::get('closed/vehicle/requests/{vehicleRequest}/show',[ClosedController::class, 'show'])->name('closed.vehicle.requests.show');
    });
});
