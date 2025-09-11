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

use Modules\PurchaseRequest\Controllers\GrnController;
use Modules\PurchaseRequest\Controllers\CloseController;
use Modules\PurchaseRequest\Controllers\ClosedController;
use Modules\PurchaseRequest\Controllers\ReviewController;
use Modules\PurchaseRequest\Controllers\ApproveController;
use Modules\PurchaseRequest\Controllers\PackageController;
use Modules\PurchaseRequest\Controllers\ApprovedController;
use Modules\PurchaseRequest\Controllers\PurchaseOrderController;
use Modules\PurchaseRequest\Controllers\PurchaseRequestController;
use Modules\PurchaseRequest\Controllers\ReviewRecommendedController;
use Modules\PurchaseRequest\Controllers\ApproveRecommendedController;
use Modules\PurchaseRequest\Controllers\PurchaseRequestItemController;
use Modules\PurchaseRequest\Controllers\PurchaseOrderCombineController;
use Modules\PurchaseRequest\Controllers\VerifyPurchaseRequestController;
use Modules\PurchaseRequest\Controllers\PurchaseRequestForwardController;

Route::middleware(['web', 'auth', 'logger'])->group(function () {
    Route::get('purchase/requests/{order}/print', [PurchaseRequestController::class, 'printRequest'])->name('purchase.requests.print');
    Route::middleware('can:purchase-request')->group(function () {
        Route::get('purchase/requests', [PurchaseRequestController::class, 'index'])->name('purchase.requests.index');
        Route::get('purchase/requests/create', [PurchaseRequestController::class, 'create'])->name('purchase.requests.create');
        Route::post('purchase/requests', [PurchaseRequestController::class, 'store'])->name('purchase.requests.store');
        Route::get('purchase/requests/{purchase}/show', [PurchaseRequestController::class, 'show'])->name('purchase.requests.show');
        Route::get('purchase/requests/{purchase}/edit', [PurchaseRequestController::class, 'edit'])->name('purchase.requests.edit');
        Route::put('purchase/requests/{purchase}', [PurchaseRequestController::class, 'update'])->name('purchase.requests.update');
        Route::delete('purchase/requests/{purchase}/destroy', [PurchaseRequestController::class, 'destroy'])->name('purchase.requests.destroy');

        Route::post('purchase/requests/{purchase}/amend/store', [PurchaseRequestController::class, 'amend'])->name('purchase.requests.amend.store');
        Route::post('purchase/requests/{purchase}/replicate/store', [PurchaseRequestController::class, 'replicate'])->name('purchase.requests.replicate.store');

        Route::get('purchase/requests/{purchase}/forward/create', [PurchaseRequestForwardController::class, 'create'])->name('purchase.requests.forward.create');
        Route::post('purchase/requests/{purchase}/forward', [PurchaseRequestForwardController::class, 'store'])->name('purchase.requests.forward.store');
        Route::get('purchase/requests/{purchase}/items/create', [PurchaseRequestItemController::class, 'create'])->name('purchase.requests.items.create');
        Route::post('purchase/requests/{purchase}/items', [PurchaseRequestItemController::class, 'store'])->name('purchase.requests.items.store');
        Route::get('purchase/requests/{purchase}/items/{item}/edit', [PurchaseRequestItemController::class, 'edit'])->name('purchase.requests.items.edit');
        Route::put('purchase/requests/{purchase}/items/{item}', [PurchaseRequestItemController::class, 'update'])->name('purchase.requests.items.update');
        Route::delete('purchase/requests/{purchase}/items/destroy', [PurchaseRequestItemController::class, 'destroyAll'])->name('purchase.requests.items.destroy.all');
        Route::delete('purchase/requests/{purchase}/items/{item}/destroy', [PurchaseRequestItemController::class, 'destroy'])->name('purchase.requests.items.destroy');
        Route::get('purchase/requests/{purchase}/items/view', [PurchaseRequestItemController::class, 'show'])->name('purchase.requests.items.show');

        Route::get('purchase/requests/{purchase}/package/add', [PackageController::class, 'add'])->name('purchase.requests.package.add');
        Route::post('purchase/requests/{purchase}/package', [PackageController::class, 'store'])->name('purchase.requests.package.store');
    });

    Route::get('purchase/requests/{purchase}/items', [PurchaseRequestItemController::class, 'index'])->name('purchase.requests.items.index');

    Route::get('purchase/requests/special/index', [PurchaseRequestController::class, 'specialIndex'])->name('purchase.requests.special.index');
    Route::get('purchase/requests/{purchase}/edit/special', [PurchaseRequestController::class, 'specialEdit'])->name('purchase.requests.special.edit');
    Route::put('purchase/requests/{purchase}/special', [PurchaseRequestController::class, 'specialUpdate'])->name('purchase.requests.special.update');

    Route::get('purchase/requests/{purchase}/items/special', [PurchaseRequestItemController::class, 'specialIndex'])->name('purchase.requests.items.special.index');
    Route::get('purchase/requests/{purchase}/items/{item}/edit/special', [PurchaseRequestItemController::class, 'specialEdit'])->name('purchase.requests.items.special.edit');
    Route::put('purchase/requests/{purchase}/items/{item}/special', [PurchaseRequestItemController::class, 'specialUpdate'])->name('purchase.requests.items.special.update');

    Route::middleware('can:budget-verify-purchase-request')->group(function() {
        Route::get('budget/verify/purchase/requests', [VerifyPurchaseRequestController::class, 'index'])->name('verify.purchase.requests.index');
        Route::get('budget/verify/purchase/requests/{purchase}/create', [VerifyPurchaseRequestController::class, 'create'])->name('verify.purchase.requests.create');
        Route::post('budget/verify/purchase/requests/{purchase}', [VerifyPurchaseRequestController::class, 'store'])->name('verify.purchase.requests.store');
    });

    Route::middleware('can:finance-review-purchase-request')->group(function () {
        Route::get('review/purchase/requests', [ReviewController::class, 'index'])->name('review.purchase.requests.index');
        Route::get('review/purchase/requests/{purchase}/create', [ReviewController::class, 'create'])->name('review.purchase.requests.create');
        Route::post('review/purchase/requests/{purchase}', [ReviewController::class, 'store'])->name('review.purchase.requests.store');
    });

    Route::middleware('can:approve-purchase-request-form')->group(function () {
        Route::get('approve/purchase/requests', [ApproveController::class, 'index'])->name('approve.purchase.requests.index');
        Route::get('approve/purchase/requests/{purchase}/create', [ApproveController::class, 'create'])->name('approve.purchase.requests.create');
        Route::post('approve/purchase/requests/{purchase}', [ApproveController::class, 'store'])->name('approve.purchase.requests.store');
    });

    Route::middleware('can:view-approved-purchase-request')->group(function () {
        Route::get('approved/purchase/requests', [ApprovedController::class, 'index'])->name('approved.purchase.requests.index');
        Route::get('approved/purchase/requests/{purchase}/show', [ApprovedController::class, 'show'])->name('approved.purchase.requests.show');
        Route::get('approved/purchase/requests/{purchase}/orders', [PurchaseOrderController::class, 'index'])->name('approved.purchase.requests.orders.index');
        Route::get('approved/purchase/requests/{purchase}/grns', [GrnController::class, 'index'])->name('approved.purchase.requests.grns.index');

        Route::get('approved/purchase/requests/order/{purchase}/grns', [GrnController::class, 'poGrnIndex'])->name('approved.purchase.requests.orders.grns.index');

        Route::get('close/purchase/requests/{purchase}/create',[CloseController::class,'create'])->name('close.purchase.requests.create');
        Route::post('close/purchase/requests/{purchase}',[CloseController::class,'store'])->name('close.purchase.requests.store');
        Route::post('open/purchase/requests/{purchase}',[CloseController::class,'open'])->name('open.purchase.requests.store');

        Route::get('closed/purchase/requests',[ClosedController::class,'index'])->name('closed.purchase.requests.index');
        Route::get('closed/purchase/requests/{purchase}/show',[ClosedController::class,'show'])->name('closed.purchase.requests.show');
    });

    Route::middleware('can:review-recommended-purchase-request')->group(function () {
        Route::get('review/recommended/purchase/requests', [ReviewRecommendedController::class, 'index'])->name('review.recommended.purchase.requests.index');
        Route::get('review/recommended/purchase/requests/{purchase}/create', [ReviewRecommendedController::class, 'create'])->name('review.recommended.purchase.requests.create');
        Route::post('review/recommended/purchase/requests/{purchase}/store', [ReviewRecommendedController::class, 'store'])->name('review.recommended.purchase.requests.store');
    });

    Route::middleware('can:approve-recommended-purchase-request')->group(function () {
        Route::get('approve/recommended/purchase/requests', [ApproveRecommendedController::class, 'index'])->name('approve.recommended.purchase.requests.index');
        Route::get('approve/recommended/purchase/requests/{purchase}/create', [ApproveRecommendedController::class, 'create'])->name('approve.recommended.purchase.requests.create');
        Route::post('approve/recommended/purchase/requests/{purchase}/store', [ApproveRecommendedController::class, 'store'])->name('approve.recommended.purchase.requests.store');
    });
    

    Route::middleware('can:purchase-order')->group(function () {
        Route::get('approved/purchase/requests/{purchase}/orders/create', [PurchaseOrderController::class, 'create'])->name('approved.purchase.requests.orders.create');
        Route::post('approved/purchase/requests/{purchase}/orders', [PurchaseOrderController::class, 'store'])->name('approved.purchase.requests.orders.store');
        Route::get('approved/purchase/requests/{purchase}/orders/{order}/edit', [PurchaseOrderController::class, 'edit'])->name('approved.purchase.requests.orders.edit');
        Route::get('approved/purchase/request/orders/{order}/item/create', [PurchaseOrderController::class, 'createItem'])->name('approved.purchase.requests.orders.createItem');
        Route::post('approved/purchase/request/orders/{order}/item/create', [PurchaseOrderController::class, 'editItem'])->name('approved.purchase.requests.orders.editItem');
        Route::get('approved/purchase/request/{purchase}/orders/{order}/item', [PurchaseOrderController::class, 'addItem'])->name('approved.purchase.requests.orders.addItem');
        Route::put('approved/purchase/requests/{purchase}/orders/{order}', [PurchaseOrderController::class, 'updateItem'])->name('approved.purchase.requests.orders.update');

        Route::get('approved/purchase/requests/{purchase}/orders/combine/create', [PurchaseOrderCombineController::class, 'create'])->name('purchase.requests.orders.combine.create');
        Route::post('approved/purchase/requests/{purchase}/orders/combine/', [PurchaseOrderCombineController::class, 'store'])->name('purchase.requests.orders.combine.store');
        Route::get('approved/purchase/requests/{purchase}/orders/{order}/combine/edit', [PurchaseOrderCombineController::class, 'edit'])->name('purchase.requests.orders.combine.edit');
        Route::put('approved/purchase/requests/{purchase}/orders/{order}/combine', [PurchaseOrderCombineController::class, 'update'])->name('purchase.requests.orders.combine.update');
    });

    Route::middleware('can:grn')->group(function () {
        Route::get('approved/purchase/requests/{purchase}/grns/create', [GrnController::class, 'create'])->name('approved.purchase.requests.grns.create');
        Route::post('approved/purchase/requests/{purchase}/grns', [GrnController::class, 'store'])->name('approved.purchase.requests.grns.store');
        Route::get('approved/purchase/requests/{purchase}/grns/{grn}/edit', [GrnController::class, 'edit'])->name('approved.purchase.requests.grns.edit');
        Route::get('approved/purchase/request/{purchase}/grns/{grn}/item', [GrnController::class, 'addItem'])->name('approved.purchase.requests.grns.addItem');
        Route::put('approved/purchase/requests/{purchase}/grns/{grn}', [GrnController::class, 'updateItem'])->name('approved.purchase.requests.grns.update');
    });


});
