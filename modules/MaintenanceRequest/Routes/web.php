<?php

/*
|--------------------------------------------------------------------------
| Application Routes for Maintenance Request Module
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

use Modules\MaintenanceRequest\Controllers\ApprovedController;
use Modules\MaintenanceRequest\Controllers\MaintenanceRequestController;
use Modules\MaintenanceRequest\Controllers\MaintenanceRequestItemController;
use Modules\MaintenanceRequest\Controllers\ApproveController;
use Modules\MaintenanceRequest\Controllers\ReviewController;


Route::middleware(['web', 'auth', 'logger'])->group(function () {
    Route::middleware('can:maintenance-request')->group(function () {
        Route::get('maintenance/requests/', [MaintenanceRequestController::class, 'index'])
            ->name('maintenance.requests.index');
        Route::get('maintenance/requests/create', [MaintenanceRequestController::class, 'create'])
            ->name('maintenance.requests.create');
        Route::post('maintenance/requests/', [MaintenanceRequestController::class, 'store'])
            ->name('maintenance.requests.store');
        Route::get('maintenance/requests/{request}/edit', [MaintenanceRequestController::class, 'edit'])
            ->name('maintenance.requests.edit');
        Route::put('maintenance/requests/{request}', [MaintenanceRequestController::class, 'update'])
            ->name('maintenance.requests.update');
        Route::delete('maintenance/requests/{request}', [MaintenanceRequestController::class, 'destroy'])
            ->name('maintenance.requests.destroy');
        Route::post('maintenance/request/{maintenance}/amend', [MaintenanceRequestController::class, 'amend'])
            ->name('maintenance.requests.amend');

        Route::get('maintenance/requests/{maintenance}/items/create', [MaintenanceRequestItemController::class, 'create'])->name('maintenance.requests.items.create');
        Route::post('maintenance/requests/{maintenance}/items', [MaintenanceRequestItemController::class, 'store'])->name('maintenance.requests.items.store');
        Route::get('maintenance/requests/{maintenance}/items/{item}/edit', [MaintenanceRequestItemController::class, 'edit'])->name('maintenance.requests.items.edit');
        Route::put('maintenance/requests/{maintenance}/items/{item}', [MaintenanceRequestItemController::class, 'update'])->name('maintenance.requests.items.update');
        Route::delete('maintenance/requests/{maintenance}/items/{item}/destroy', [MaintenanceRequestItemController::class, 'destroy'])->name('maintenance.requests.items.destroy');
    });
    Route::get('maintenance/requests/{request}/view', [MaintenanceRequestController::class, 'view'])
        ->name('maintenance.requests.view');
    Route::get('maintenance/requests/{maintenance}/items', [MaintenanceRequestItemController::class, 'index'])->name('maintenance.requests.items.index');

    Route::middleware('can:review-maintenance-request')->group(function () {
        Route::get('review/maintenance/requests/', [ReviewController::class, 'index'])
            ->name('review.maintenance.requests.index');
        Route::get('review/maintenance/requests/{request}/create', [ReviewController::class, 'create'])
            ->name('review.maintenance.requests.create');
        Route::post('review/maintenance/requests/{request}', [ReviewController::class, 'store'])
            ->name('review.maintenance.requests.store');
    });

    Route::middleware('can:approve-maintenance-request')->group(function () {
        Route::get('approve/maintenance/requests/', [ApproveController::class, 'index'])
            ->name('approve.maintenance.requests.index');
        Route::get('approve/maintenance/requests/{request}/create', [ApproveController::class, 'create'])
            ->name('approve.maintenance.requests.create');
        Route::post('approve/maintenance/requests/{request}', [ApproveController::class, 'store'])
            ->name('approve.maintenance.requests.store');
    });

    Route::get('approved/maintenance/requests', [ApprovedController::class, 'index'])
        ->name('approved.maintenance.requests.index');
    Route::get('approved/maintenance/requests/{request}/show', [ApprovedController::class, 'show'])
        ->name('approved.maintenance.requests.show');
    Route::get('approved/maintenance/requests/{request}/print', [ApprovedController::class, 'print'])
        ->name('approved.maintenance.requests.print');
});
