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

use Modules\TransportationBill\Controllers\TransportationBillController;
use Modules\TransportationBill\Controllers\TransportationBillDetailController;
use Modules\TransportationBill\Controllers\ApproveController;
use Modules\TransportationBill\Controllers\ApprovedController;

Route::middleware(['web', 'auth', 'logger'])->group(function () {
    Route::middleware('can:transportation-bill')->group(function () {
        Route::get('transportation/bills', [TransportationBillController::class, 'index'])->name('transportation.bills.index');
        Route::get('transportation/bills/create', [TransportationBillController::class, 'create'])->name('transportation.bills.create');
        Route::post('transportation/bills', [TransportationBillController::class, 'store'])->name('transportation.bills.store');
        Route::get('transportation/bills/{transportationBill}/show', [TransportationBillController::class, 'show'])->name('transportation.bills.show');
        Route::get('transportation/bills/{transportationBill}/edit', [TransportationBillController::class, 'edit'])->name('transportation.bills.edit');
        Route::put('transportation/bills/{transportationBill}', [TransportationBillController::class, 'update'])->name('transportation.bills.update');
        Route::delete('transportation/bills/{transportationBill}/destroy', [TransportationBillController::class, 'destroy'])->name('transportation.bills.destroy');

        Route::get('transportation/bills/{transportationBill}/details/create', [TransportationBillDetailController::class, 'create'])->name('transportation.bills.details.create');
        Route::post('transportation/bills/{transportationBill}/details', [TransportationBillDetailController::class, 'store'])->name('transportation.bills.details.store');
        Route::get('transportation/bills/{transportationBill}/details/{detail}/edit', [TransportationBillDetailController::class, 'edit'])->name('transportation.bills.details.edit');
        Route::put('transportation/bills/{transportationBill}/details/{detail}', [TransportationBillDetailController::class, 'update'])->name('transportation.bills.details.update');
        Route::delete('transportation/bills/{transportationBill}/details/{detail}/destroy', [TransportationBillDetailController::class, 'destroy'])->name('transportation.bills.details.destroy');
    });
    Route::get('transportation/bills/{transportationBill}/details', [TransportationBillDetailController::class, 'index'])->name('transportation.bills.details.index');

    Route::middleware('can:approve-transportation-bill')->group(function () {
        Route::get('approve/transportation/bills', [ApproveController::class, 'index'])->name('approve.transportation.bills.index');
        Route::get('approve/transportation/bills/{transportationBill}/create', [ApproveController::class, 'create'])->name('approve.transportation.bills.create');
        Route::post('approve/transportation/bills/{transportationBill}', [ApproveController::class, 'store'])->name('approve.transportation.bills.store');
    });

    Route::middleware('can:view-approved-transportation-bill')->group(function () {
        Route::get('approved/transportation/bills', [ApprovedController::class, 'index'])->name('approved.transportation.bills.index');
        Route::get('approved/transportation/bills/{transportationBill}/show', [ApprovedController::class, 'show'])->name('approved.transportation.bills.show');
    });

    Route::get('transportation/bills/{transportationBill}/print', [TransportationBillController::class, 'printBill'])->name('transportation.bills.print');
});
