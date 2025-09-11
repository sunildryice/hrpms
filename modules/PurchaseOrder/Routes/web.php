<?php

/*
|--------------------------------------------------------------------------
| Application Routes for User Module
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is ordered.
|
*/

use Illuminate\Routing\RouteUri;
use Modules\PurchaseOrder\Controllers\GrnController;
use Modules\PurchaseOrder\Controllers\ReviewController;
use Modules\PurchaseOrder\Controllers\ApproveController;
use Modules\PurchaseOrder\Controllers\ApprovedController;
use Modules\PurchaseOrder\Controllers\CancelledController;
use Modules\PurchaseOrder\Controllers\PaymentSheetController;
use Modules\PurchaseOrder\Controllers\PurchaseOrderController;
use Modules\PurchaseOrder\Controllers\PurchaseOrderItemController;

Route::middleware(['web', 'auth', 'logger'])->group(function () {
    Route::middleware('can:purchase-order')->group(function () {
        Route::get('purchase/orders', [PurchaseOrderController::class, 'index'])->name('purchase.orders.index');
        Route::get('purchase/orders/{order}/show', [PurchaseOrderController::class, 'show'])->name('purchase.orders.show');
        Route::get('purchase/orders/{order}/edit', [PurchaseOrderController::class, 'edit'])->name('purchase.orders.edit');
        Route::put('purchase/orders/{order}', [PurchaseOrderController::class, 'update'])->name('purchase.orders.update');
        Route::delete('purchase/orders/{order}/destroy', [PurchaseOrderController::class, 'destroy'])->name('purchase.orders.destroy');

        // Route::post('approve/purchase/orders/{order}/reverse', [PurchaseOrderController::class, 'reverse'])->name('purchase.orders.reverse');
        Route::post('purchase/orders/{order}/cancel', [PurchaseOrderController::class, 'cancel'])->name('purchase.orders.cancel');
        Route::get('purchase/orders/{order}/items/create', [PurchaseOrderItemController::class, 'create'])->name('purchase.orders.items.create');
        Route::post('purchase/orders/{order}/items', [PurchaseOrderItemController::class, 'store'])->name('purchase.orders.items.store');
        Route::get('purchase/requests/{purchase}/orders/{order}/add', [PurchaseOrderItemController::class, 'addItem'])->name('purchase.orders.items.addItem');
        Route::put('purchase/requests/{purchase}/orders/{order}/add', [PurchaseOrderItemController::class, 'storeItem'])->name('purchase.orders.items.storeItem');
        Route::get('purchase/orders/{order}/items/{item}/edit', [PurchaseOrderItemController::class, 'edit'])->name('purchase.orders.items.edit');
        Route::put('purchase/orders/{order}/items/{item}', [PurchaseOrderItemController::class, 'update'])->name('purchase.orders.items.update');
        Route::delete('purchase/orders/{order}/items/{item}/destroy', [PurchaseOrderItemController::class, 'destroy'])->name('purchase.orders.items.destroy');
    });
    Route::get('purchase/orders/{order}/items', [PurchaseOrderItemController::class, 'index'])->name('purchase.orders.items.index');
    Route::get('purchase/orders/{order}/print', [PurchaseOrderController::class, 'printOrder'])->name('purchase.orders.print');

    Route::middleware('can:review-purchase-order')->group(function () {
        Route::get('review/purchase/orders', [ReviewController::class, 'index'])->name('review.purchase.orders.index');
        Route::get('review/purchase/orders/{order}/create', [ReviewController::class, 'create'])->name('review.purchase.orders.create');
        Route::post('review/purchase/orders/{order}', [ReviewController::class, 'store'])->name('review.purchase.orders.store');
    });

    Route::middleware('can:approve-purchase-order')->group(function () {
        Route::get('approve/purchase/orders', [ApproveController::class, 'index'])->name('approve.purchase.orders.index');
        Route::get('approve/purchase/orders/{order}/create', [ApproveController::class, 'create'])->name('approve.purchase.orders.create');
        Route::post('approve/purchase/orders/{order}', [ApproveController::class, 'store'])->name('approve.purchase.orders.store');

        Route::get('approve/purchase/orders/cancel', [ApproveController::class, 'cancelIndex'])->name('approve.purchase.orders.cancel.index');
        Route::get('approve/purchase/orders/{order}/cancel/create', [ApproveController::class, 'cancelCreate'])->name('approve.purchase.orders.cancel.create');
        Route::post('approve/purchase/orders/{order}/cancel', [ApproveController::class, 'cancelStore'])->name('approve.purchase.orders.cancel.store');
    });

    Route::middleware('can:view-approved-purchase-order')->group(function () {
        Route::get('approved/purchase/orders', [ApprovedController::class, 'index'])->name('approved.purchase.orders.index');
        Route::get('approved/purchase/orders/{order}/show', [ApprovedController::class, 'show'])->name('approved.purchase.orders.show');
        Route::get('approved/purchase/orders/{order}/grns', [GrnController::class, 'index'])->name('approved.purchase.orders.grns.index');

        Route::get('cancelled/purchase/orders', [CancelledController::class, 'index'])->name('cancelled.purchase.orders.index');
        Route::get('cancelled/purchase/orders/{order}/show', [CancelledController::class, 'show'])->name('cancelled.purchase.orders.show');
    });

    Route::middleware('can:grn')->group(function () {
        Route::get('approved/purchase/orders/{order}/grns/create', [GrnController::class, 'create'])->name('approved.purchase.orders.grns.create');
        Route::post('approved/purchase/orders/{order}/grns', [GrnController::class, 'store'])->name('approved.purchase.orders.grns.store');
    });

    Route::middleware('can:payment-sheet')->group(function () {
        Route::get('approved/purchase/orders/{order}/payment/sheet',[PaymentSheetController::class, 'index'])->name('approved.purchase.orders.payment.sheet.index');
        Route::get('approved/purchase/orders/{order}/payment/sheet/create',[PaymentSheetController::class, 'create'])->name('approved.purchase.orders.payment.sheet.create');
        Route::post('approved/purchase/orders/{order}/payment/sheet',[PaymentSheetController::class, 'store'])->name('approved.purchase.orders.payment.sheet.store');
    });
});
