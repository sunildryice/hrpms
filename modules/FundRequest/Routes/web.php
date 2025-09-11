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

use Illuminate\Support\Facades\Route;
use Modules\FundRequest\Controllers\ApproveController;
use Modules\FundRequest\Controllers\ApprovedController;
use Modules\FundRequest\Controllers\CertifyController;
use Modules\FundRequest\Controllers\CheckController;
use Modules\FundRequest\Controllers\FundRequestActivityController;
use Modules\FundRequest\Controllers\FundRequestController;
use Modules\FundRequest\Controllers\ReviewController;
use Modules\PurchaseOrder\Controllers\CancelledController;

Route::middleware(['web', 'auth', 'logger'])->group(function () {
    Route::middleware('can:fund-request')->group(function () {
        Route::get('fund/requests', [FundRequestController::class, 'index'])->name('fund.requests.index');
        Route::get('fund/requests/create', [FundRequestController::class, 'create'])->name('fund.requests.create');
        Route::post('fund/requests', [FundRequestController::class, 'store'])->name('fund.requests.store');
        Route::get('fund/requests/{fund}/show', [FundRequestController::class, 'show'])->name('fund.requests.show');
        Route::get('fund/requests/{fund}/edit', [FundRequestController::class, 'edit'])->name('fund.requests.edit');
        Route::put('fund/requests/{fund}', [FundRequestController::class, 'update'])->name('fund.requests.update');
        Route::delete('fund/requests/{fund}/destroy', [FundRequestController::class, 'destroy'])->name('fund.requests.destroy');
        Route::post('fund/requests/{fund}/cancel', [FundRequestController::class, 'cancel'])->name('fund.requests.cancel');
        Route::post('fund/requests/{fund}/amend/store', [FundRequestController::class, 'amend'])->name('fund.requests.amend.store');
        Route::post('fund/requests/{fund}/replicate/store', [FundRequestController::class, 'replicate'])->name('fund.requests.replicate.store');

        Route::get('fund/requests/{fund}/activities/create', [FundRequestActivityController::class, 'create'])->name('fund.requests.activities.create');
        Route::post('fund/requests/{fund}/activities', [FundRequestActivityController::class, 'store'])->name('fund.requests.activities.store');
        Route::get('fund/requests/{fund}/activities/{item}/edit', [FundRequestActivityController::class, 'edit'])->name('fund.requests.activities.edit');
        Route::put('fund/requests/{fund}/activities/{item}', [FundRequestActivityController::class, 'update'])->name('fund.requests.activities.update');
        Route::delete('fund/requests/{fund}/activities/{item}/destroy', [FundRequestActivityController::class, 'destroy'])->name('fund.requests.activities.destroy');
    });
    Route::get('fund/requests/{fund}/activities', [FundRequestActivityController::class, 'index'])->name('fund.requests.activities.index');

    Route::middleware('can:check-fund-request')->group(function () {
        Route::get('check/fund/requests', [CheckController::class, 'index'])->name('check.fund.requests.index');
        Route::get('check/fund/requests/{fund}/create', [CheckController::class, 'create'])->name('check.fund.requests.create');
        Route::post('check/fund/requests/{fund}', [CheckController::class, 'store'])->name('check.fund.requests.store');
    });

    Route::middleware('can:certify-fund-request')->group(function () {
        Route::get('certify/fund/requests', [CertifyController::class, 'index'])->name('certify.fund.requests.index');
        Route::get('certify/fund/requests/{fund}/create', [CertifyController::class, 'create'])->name('certify.fund.requests.create');
        Route::post('certify/fund/requests/{fund}', [CertifyController::class, 'store'])->name('certify.fund.requests.store');
    });

    Route::middleware('can:review-fund-request')->group(function () {
        Route::get('review/fund/requests', [ReviewController::class, 'index'])->name('review.fund.requests.index');
        Route::get('review/fund/requests/{fund}/create', [ReviewController::class, 'create'])->name('review.fund.requests.create');
        Route::post('review/fund/requests/{fund}', [ReviewController::class, 'store'])->name('review.fund.requests.store');
    });

    Route::middleware('can:approve-fund-request')->group(function () {
        Route::get('approve/fund/requests', [ApproveController::class, 'index'])->name('approve.fund.requests.index');
        Route::get('approve/fund/requests/{fund}/create', [ApproveController::class, 'create'])->name('approve.fund.requests.create');
        Route::post('approve/fund/requests/{fund}', [ApproveController::class, 'store'])->name('approve.fund.requests.store');

        Route::get('approve/fund/requests/cancel', [ApproveController::class, 'cancelIndex'])->name('approve.fund.requests.cancel.index');
        Route::get('approve/fund/requests/{fund}/cancel/create', [ApproveController::class, 'cancelCreate'])->name('approve.fund.requests.cancel.create');
        Route::post('approve/fund/requests/{fund}/cancel', [ApproveController::class, 'cancelStore'])->name('approve.fund.requests.cancel.store');
    });

    Route::middleware('can:view-approved-fund-request')->group(function () {
        Route::get('approved/fund/requests', [ApprovedController::class, 'index'])->name('approved.fund.requests.index');
        Route::get('approved/fund/requests/{fund}/show', [ApprovedController::class, 'show'])->name('approved.fund.requests.show');

        // Route::get('cancelled/fund/requests', [CancelledController::class, 'index'])->name('cancelled.fund.requests.index');
        // Route::get('cancelled/fund/requests/{order}/show', [CancelledController::class, 'show'])->name('cancelled.fund.requests.show');

    });
    Route::get('approved/fund/requests/{fund}/print', [ApprovedController::class, 'print'])->name('approved.fund.requests.print');

});
