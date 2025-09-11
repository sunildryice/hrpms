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

use Modules\Payroll\Controllers\ApprovedBatchController;
use Modules\Payroll\Controllers\PaymentItemController;
use Modules\Payroll\Controllers\BatchController;
use Modules\Payroll\Controllers\BatchApproveController;
use Modules\Payroll\Controllers\BatchReviewController;
use Modules\Payroll\Controllers\SheetController;
use Modules\Payroll\Controllers\SheetDetailController;

Route::middleware(['web', 'auth', 'logger'])->group(function () {
    Route::middleware('can:payroll')->group(function () {
        Route::get('master/payment/items', [PaymentItemController::class, 'index'])->name('master.payment.items.index');

        Route::get('payroll/batches', [BatchController::class, 'index'])->name('payroll.batches.index');
        Route::get('payroll/batches/create', [BatchController::class, 'create'])->name('payroll.batches.create');
        Route::post('payroll/batches', [BatchController::class, 'store'])->name('payroll.batches.store');
        Route::get('payroll/batches/{batch}/process', [BatchController::class, 'process'])->name('payroll.batches.process');
        Route::delete('payroll/batches/{batch}', [BatchController::class, 'destroy'])->name('payroll.batches.destroy');
        Route::post('payroll/batches/{batch}', [BatchController::class, 'update'])->name('payroll.batches.update');

        Route::get('payroll/batches/{batch}/sheets/{sheet}/reconcile', [SheetController::class, 'reconcile'])->name('payroll.batches.sheets.reconcile');
        Route::get('payroll/batches/{batch}/sheets/{sheet}/edit', [SheetController::class, 'edit'])->name('payroll.batches.sheets.edit');

        Route::get('payroll/batches/{batch}/sheets/{sheet}/details/create', [SheetDetailController::class, 'create'])->name('payroll.batches.sheets.details.create');
        Route::post('payroll/batches/{batch}/sheets/{sheet}/details', [SheetDetailController::class, 'store'])->name('payroll.batches.sheets.details.store');
        Route::get('payroll/batches/{batch}/sheets/{sheet}/details/{detail}/edit', [SheetDetailController::class, 'edit'])->name('payroll.batches.sheets.details.edit');
        Route::put('payroll/batches/{batch}/sheets/{sheet}/details/{detail}', [SheetDetailController::class, 'update'])->name('payroll.batches.sheets.details.update');
        Route::delete('payroll/batches/{batch}/sheets/{sheet}/details/{detail}', [SheetDetailController::class, 'destroy'])->name('payroll.batches.sheets.details.destroy');

        Route::get('approved/payroll/batches', [ApprovedBatchController::class, 'index'])->name('approved.payroll.batches.index');
    });
    Route::get('payroll/batches/{batch}/sheets', [SheetController::class, 'index'])->name('payroll.batches.sheets.index');
    Route::get('payroll/batches/{batch}/sheets/{sheet}/show', [SheetController::class, 'show'])->name('payroll.batches.sheets.show');

    Route::middleware('can:verify-payroll')->group(function () {
        Route::get('review/payroll/batches', [BatchReviewController::class, 'index'])->name('payroll.batches.review.index');
        Route::get('review/payroll/batches/{batch}/create', [BatchReviewController::class, 'create'])->name('payroll.batches.review.create');
        Route::post('review/payroll/batches/{batch}', [BatchReviewController::class, 'store'])->name('payroll.batches.review.store');
    });

    Route::middleware('can:approve-payroll')->group(function () {
        Route::get('approve/payroll/batches', [BatchApproveController::class, 'index'])->name('payroll.batches.approve.index');
        Route::get('approve/payroll/batches/{batch}/create', [BatchApproveController::class, 'create'])->name('payroll.batches.approve.create');
        Route::post('approve/payroll/batches/{batch}', [BatchApproveController::class, 'store'])->name('payroll.batches.approve.store');
    });
});
