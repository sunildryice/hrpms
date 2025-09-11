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

use Modules\GoodRequest\Controllers\AssetController;
use Modules\GoodRequest\Controllers\AssignController;
use Modules\GoodRequest\Controllers\ReviewController;
use Modules\GoodRequest\Controllers\ApproveController;
use Modules\GoodRequest\Controllers\ApprovedController;
use Modules\GoodRequest\Controllers\HandoverController;
use Modules\GoodRequest\Controllers\GoodRequestController;
use Modules\GoodRequest\Controllers\DirectAssignController;
use Modules\GoodRequest\Controllers\DirectDispatchController;
use Modules\GoodRequest\Controllers\ApproveHandoverController;
use Modules\GoodRequest\Controllers\GoodRequestItemController;
use Modules\GoodRequest\Controllers\DirectAssignReceiveController;
use Modules\GoodRequest\Controllers\GoodRequestAssignItemController;

Route::middleware(['web', 'auth', 'logger'])->group(function () {
    Route::middleware('can:good-request')->group(function () {
        Route::get('good/requests', [GoodRequestController::class, 'index'])->name('good.requests.index');
        Route::get('good/requests/create', [GoodRequestController::class, 'create'])->name('good.requests.create');
        Route::post('good/requests', [GoodRequestController::class, 'store'])->name('good.requests.store');
        Route::get('good/requests/{goodRequest}/show', [GoodRequestController::class, 'show'])->name('good.requests.show');
        Route::get('good/requests/{goodRequest}/edit', [GoodRequestController::class, 'edit'])->name('good.requests.edit');
        Route::put('good/requests/{goodRequest}', [GoodRequestController::class, 'update'])->name('good.requests.update');
        Route::delete('good/requests/{goodRequest}/destroy', [GoodRequestController::class, 'destroy'])->name('good.requests.destroy');
        Route::put('good/request/{goodRequest}/receiver/note/update', [GoodRequestController::class, 'updateReceiverNote'])->name('good.requests.receiver.note.update');

        Route::get('good/requests/{good}/items/create', [GoodRequestItemController::class, 'create'])->name('good.requests.items.create');
        Route::post('good/requests/{good}/items', [GoodRequestItemController::class, 'store'])->name('good.requests.items.store');
        Route::get('good/requests/{good}/items/{item}/edit', [GoodRequestItemController::class, 'edit'])->name('good.requests.items.edit');
        Route::put('good/requests/{good}/items/{item}', [GoodRequestItemController::class, 'update'])->name('good.requests.items.update');
        Route::delete('good/requests/{good}/items/{item}/destroy', [GoodRequestItemController::class, 'destroy'])->name('good.requests.items.destroy');

    });
    Route::get('good/requests/{good}/items', [GoodRequestItemController::class, 'index'])->name('good.requests.items.index');


    Route::middleware('can:direct-dispatch-good-request')->group(function () {
        Route::get('good/requests/direct/dispatch/index', [DirectDispatchController::class, 'index'])->name('good.requests.direct.dispatch.index');
        Route::get('good/requests/direct/dispatch/{inventoryItem}/create', [DirectDispatchController::class, 'create'])->name('good.requests.direct.dispatch.create');
        Route::post('good/requests/direct/dispatch/{inventoryItem}/store', [DirectDispatchController::class, 'store'])->name('good.requests.direct.dispatch.store');
        Route::get('good/requests/direct/dispatch/{goodRequest}/show', [DirectDispatchController::class, 'show'])->name('good.requests.direct.dispatch.show');
        Route::get('good/request/direct/dispatch/bulk/create', [DirectDispatchController::class, 'createBulk'])->name('direct.dispatch.bulk.create');
        Route::post('good/request/direct/dispatch/bulk', [DirectDispatchController::class, 'storeBulk'])->name('direct.dispatch.bulk.store');
        Route::get('good/request/{goodRequest}/direct/dispatch/bulk/edit', [DirectDispatchController::class, 'editBulk'])->name('direct.dispatch.bulk.edit');
        Route::put('good/request/{goodRequest}/direct/dispatch/bulk', [DirectDispatchController::class, 'updateBulk'])->name('direct.dispatch.bulk.update');
    });

    Route::middleware('can:approve-direct-dispatch-good-request')->group(function () {
        Route::get('good/requests/direct/dispatch/approve', [DirectDispatchController::class, 'indexApprove'])->name('good.requests.direct.dispatch.approve.index');
        Route::post('good/requests/direct/dispatch/{goodRequest}/approve/store', [DirectDispatchController::class, 'storeApprove'])->name('good.requests.direct.dispatch.approve.store');
        Route::get('good/requests/direct/dispatch/{goodRequest}/approve/create', [DirectDispatchController::class, 'createApprove'])->name('good.requests.direct.dispatch.approve.create');

    });

    Route::middleware('can:review-good-request')->group(function () {
        Route::get('review/good/requests', [ReviewController::class, 'index'])->name('review.good.requests.index');
        Route::get('review/good/requests/{goodRequest}/create', [ReviewController::class, 'create'])->name('review.good.requests.create');
        Route::post('review/good/requests/{goodRequest}', [ReviewController::class, 'store'])->name('review.good.requests.store');
    });

    Route::middleware('can:approve-good-request')->group(function () {
        Route::get('approve/good/requests', [ApproveController::class, 'index'])->name('approve.good.requests.index');
        Route::get('approve/good/requests/{goodRequest}/create', [ApproveController::class, 'create'])->name('approve.good.requests.create');
        Route::post('approve/good/requests/{goodRequest}', [ApproveController::class, 'store'])->name('approve.good.requests.store');
    });

    Route::middleware('can:assign-good-request')->group(function () {
        Route::get('assign/good/requests', [AssignController::class, 'index'])->name('assign.good.requests.index');
        Route::get('assign/good/requests/{goodRequest}/create', [AssignController::class, 'create'])->name('assign.good.requests.create');
        Route::post('assign/good/requests/{goodRequest}', [AssignController::class, 'store'])->name('assign.good.requests.store');

        Route::get('good/requests/{goodRequest}/items/{item}/assign/create', [GoodRequestAssignItemController::class, 'create'])->name('assign.good.requests.items.create');
        Route::post('good/requests/{goodRequest}/items/{item}/assign', [GoodRequestAssignItemController::class, 'store'])->name('assign.good.requests.items.store');
    });

    Route::middleware('can:view-approved-good-request')->group(function () {
        Route::get('approved/good/requests', [ApprovedController::class, 'index'])->name('approved.good.requests.index');
        Route::get('approved/good/requests/{goodRequest}/show', [ApprovedController::class, 'show'])->name('approved.good.requests.show');
    });

    Route::get('profile/assets/{asset}/handover/create', [HandoverController::class, 'create'])->name('assets.handover.create');
    Route::post('profile/assets/{asset}/handover', [HandoverController::class, 'store'])->name('assets.handover.store');

    Route::middleware('can:approve-asset-handover')->group(function () {
        Route::get('approve/asset/handovers', [ApproveHandoverController::class, 'index'])->name('approve.asset.handovers.index');
        Route::get('approve/asset/handovers/{assetHandover}/create', [ApproveHandoverController::class, 'create'])->name('approve.asset.handovers.create');
        Route::post('approve/asset/handovers/{assetHandover}', [ApproveHandoverController::class, 'store'])->name('approve.asset.handovers.store');
    });

    Route::middleware('can:direct-assign-good-request')->group(function () {
        Route::get('good/requests/direct/assign/{asset}', [DirectAssignController::class, 'create'])->name('good.requests.direct.assign.create');
        Route::post('good/requests/direct/assign/{asset}', [DirectAssignController::class, 'store'])->name('good.requests.direct.assign.store');
    });

    // Approve Direct Assign
    Route::get('approve/good/requests/direct/assign', [DirectAssignController::class, 'indexApprove'])->name('approve.good.requests.direct.assign.index');
    Route::get('approve/good/requests/direct/assign/{goodRequest}/create', [DirectAssignController::class, 'createApprove'])->name('approve.good.requests.direct.assign.create');
    Route::post('approve/good/requests/direct/assign/{goodRequest}/store', [DirectAssignController::class, 'storeApprove'])->name('approve.good.requests.direct.assign.store');

    // Receive Direct Assign
    Route::get('receive/good/requests/direct/assign/', [DirectAssignReceiveController::class, 'index'])->name('receive.good.requests.direct.assign.index');
    Route::get('receive/good/requests/direct/assign/{goodRequest}/create', [DirectAssignReceiveController::class, 'create'])->name('receive.good.requests.direct.assign.create');
    Route::post('receive/good/requests/direct/assign/{goodRequest}/store', [DirectAssignReceiveController::class, 'store'])->name('receive.good.requests.direct.assign.store');

});
