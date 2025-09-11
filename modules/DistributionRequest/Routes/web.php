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

use Modules\DistributionRequest\Controllers\ApproveController;
use Modules\DistributionRequest\Controllers\ApprovedController;
use Modules\DistributionRequest\Controllers\HandoverApproveController;
use Modules\DistributionRequest\Controllers\HandoverReceiveController;
use Modules\DistributionRequest\Controllers\HandoverApprovedController;
use Modules\DistributionRequest\Controllers\DistributionRequestController;
use Modules\DistributionRequest\Controllers\DistributionHandoverController;
use Modules\DistributionRequest\Controllers\DistributionRequestItemController;
use Modules\DistributionRequest\Controllers\DistributionHandoverItemController;
use Modules\DistributionRequest\Controllers\HandoverReceivedController;

Route::middleware(['web', 'auth', 'logger'])->group(function () {
    Route::middleware('can:distribution-request')->group(function () {
        Route::get('distribution/requests', [DistributionRequestController::class, 'index'])->name('distribution.requests.index');
        Route::get('distribution/requests/create', [DistributionRequestController::class, 'create'])->name('distribution.requests.create');
        Route::post('distribution/requests', [DistributionRequestController::class, 'store'])->name('distribution.requests.store');
        Route::get('distribution/requests/{distribution}/show', [DistributionRequestController::class, 'show'])->name('distribution.requests.show');
        Route::get('distribution/requests/{distribution}/edit', [DistributionRequestController::class, 'edit'])->name('distribution.requests.edit');
        Route::put('distribution/requests/{distribution}', [DistributionRequestController::class, 'update'])->name('distribution.requests.update');
        Route::delete('distribution/requests/{distribution}/destroy', [DistributionRequestController::class, 'destroy'])->name('distribution.requests.destroy');

        Route::get('distribution/requests/{distribution}/items/create', [DistributionRequestItemController::class, 'create'])->name('distribution.requests.items.create');
        Route::post('distribution/requests/{distribution}/items', [DistributionRequestItemController::class, 'store'])->name('distribution.requests.items.store');
        Route::get('distribution/requests/{distribution}/items/{item}/edit', [DistributionRequestItemController::class, 'edit'])->name('distribution.requests.items.edit');
        Route::put('distribution/requests/{distribution}/items/{item}', [DistributionRequestItemController::class, 'update'])->name('distribution.requests.items.update');
        Route::delete('distribution/requests/{distribution}/items/{item}/destroy', [DistributionRequestItemController::class, 'destroy'])->name('distribution.requests.items.destroy');

        Route::get('distribution/requests/handovers', [DistributionHandoverController::class, 'index'])->name('distribution.requests.handovers.index');
        Route::post('distribution/requests/{distribution}/handover', [DistributionHandoverController::class, 'store'])->name('distribution.requests.handovers.store');
        Route::get('distribution/requests/handovers/{handover}/show', [DistributionHandoverController::class, 'show'])->name('distribution.requests.handovers.show');
        Route::get('distribution/requests/handovers/{handover}/edit', [DistributionHandoverController::class, 'edit'])->name('distribution.requests.handovers.edit');
        Route::put('distribution/requests/handovers/{handover}', [DistributionHandoverController::class, 'update'])->name('distribution.requests.handovers.update');
        Route::delete('distribution/requests/handovers/{handover}/destroy', [DistributionHandoverController::class, 'destroy'])->name('distribution.requests.handovers.destroy');
        Route::get('distribution/requests/handovers/{handover}/print', [DistributionHandoverController::class, 'printHandover'])->name('distribution.requests.handovers.print');
    });
    Route::get('distribution/requests/{distribution}/items', [DistributionRequestItemController::class, 'index'])->name('distribution.requests.items.index');
    Route::get('distribution/requests/handovers/{handover}/items', [DistributionHandoverItemController::class, 'index'])->name('distribution.requests.handovers.items.index');

    Route::middleware('can:approve-distribution-request')->group(function () {
        Route::get('approve/distribution/requests', [ApproveController::class, 'index'])->name('approve.distribution.requests.index');
        Route::get('approve/distribution/requests/{distribution}/create', [ApproveController::class, 'create'])->name('approve.distribution.requests.create');
        Route::post('approve/distribution/requests/{distribution}', [ApproveController::class, 'store'])->name('approve.distribution.requests.store');
    });

    Route::middleware('can:view-approved-distribution-request')->group(function () {
        Route::get('approved/distribution/requests', [ApprovedController::class, 'index'])->name('approved.distribution.requests.index');
        Route::get('approved/distribution/requests/{distribution}/show', [ApprovedController::class, 'show'])->name('approved.distribution.requests.show');
    });

    Route::middleware('can:approve-distribution-handover')->group(function () {
        Route::get('approve/distribution/requests/handovers', [HandoverApproveController::class, 'index'])->name('approve.distribution.requests.handovers.index');
        Route::get('approve/distribution/requests/handovers/{handover}/create', [HandoverApproveController::class, 'create'])->name('approve.distribution.requests.handovers.create');
        Route::post('approve/distribution/requests/handovers/{handover}', [HandoverApproveController::class, 'store'])->name('approve.distribution.requests.handovers.store');
    });

    Route::middleware('can:view-approved-distribution-request')->group(function () {
        Route::get('approved/distribution/requests/handovers', [HandoverApprovedController::class, 'index'])->name('approved.distribution.requests.handovers.index');
        Route::get('approved/distribution/requests/handovers/{handover}/show', [HandoverApprovedController::class, 'show'])->name('approved.distribution.requests.handovers.show');
    });

    Route::get('receive/distribution/requests/handovers', [HandoverReceiveController::class, 'index'])->name('receive.distribution.requests.handovers.index');
    Route::post('receive/distribution/requests/handovers/{handover}', [HandoverReceiveController::class, 'store'])->name('receive.distribution.requests.handovers.store');
    Route::get('receive/distribution/requests/handovers/{handover}/edit', [HandoverReceiveController::class, 'edit'])->name('receive.distribution.requests.handovers.edit');
    Route::put('receive/distribution/requests/handovers/{handover}', [HandoverReceiveController::class, 'update'])->name('receive.distribution.requests.handovers.update');

    // received
    Route::get('received/distribution/requests/handovers',[HandoverReceivedController::class, 'index'])->name('received.distribution.requests.handovers.index');
    Route::get('received/distribution/requests/handovers/{handover}/show',[HandoverReceivedController::class, 'show'])->name('received.distribution.requests.handovers.show');
});
