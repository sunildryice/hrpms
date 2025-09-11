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

use Modules\PaymentSheet\Controllers\ApproveController;
use Modules\PaymentSheet\Controllers\ApprovedController;
use Modules\PaymentSheet\Controllers\ApproveRecommendedController;
use Modules\PaymentSheet\Controllers\PaymentBillController;
use Modules\PaymentSheet\Controllers\PaymentController;
use Modules\PaymentSheet\Controllers\PaymentSheetAttachmentController;
use Modules\PaymentSheet\Controllers\PaymentSheetController;
use Modules\PaymentSheet\Controllers\PaymentSheetDetailController;
use Modules\PaymentSheet\Controllers\ReviewRecommendedController;
use Modules\PaymentSheet\Controllers\VerifyController;

Route::middleware(['web', 'auth', 'logger'])->group(function () {
    Route::middleware('can:add-payment-bill')->group(function () {
        Route::get('payment/bills', [PaymentBillController::class, 'index'])->name('payment.bills.index');
        Route::get('payment/bills/create', [PaymentBillController::class, 'create'])->name('payment.bills.create');
        Route::post('payment/bills', [PaymentBillController::class, 'store'])->name('payment.bills.store');
        Route::get('payment/bills/{paymentBill}/show', [PaymentBillController::class, 'show'])->name('payment.bills.show');
        Route::get('payment/bills/{paymentBill}/edit', [PaymentBillController::class, 'edit'])->name('payment.bills.edit');
        Route::put('payment/bills/{paymentBill}', [PaymentBillController::class, 'update'])->name('payment.bills.update');
        Route::delete('payment/bills/{paymentBill}/destroy', [PaymentBillController::class, 'destroy'])->name('payment.bills.destroy');
    });
    Route::middleware('can:payment-sheet')->group(function () {
        Route::get('payment/sheets', [PaymentSheetController::class, 'index'])->name('payment.sheets.index');
        Route::get('payment/sheets/create', [PaymentSheetController::class, 'create'])->name('payment.sheets.create');
        Route::post('payment/sheets', [PaymentSheetController::class, 'store'])->name('payment.sheets.store');
        Route::get('payment/sheets/{paymentSheet}/show', [PaymentSheetController::class, 'show'])->name('payment.sheets.show');
        Route::get('payment/sheets/{paymentSheet}/edit', [PaymentSheetController::class, 'edit'])->name('payment.sheets.edit');
        Route::put('payment/sheets/{paymentSheet}', [PaymentSheetController::class, 'update'])->name('payment.sheets.update');
        Route::delete('payment/sheets/{paymentSheet}/destroy', [PaymentSheetController::class, 'destroy'])->name('payment.sheets.destroy');
        Route::post('payment/sheets/{paymentSheet}/amend', [PaymentSheetController::class, 'amend'])->name('payment.sheets.amend');

        Route::get('payment/sheets/{paymentSheet}/details/create', [PaymentSheetDetailController::class, 'create'])->name('payment.sheets.details.create');
        Route::post('payment/sheets/{paymentSheet}/details', [PaymentSheetDetailController::class, 'store'])->name('payment.sheets.details.store');
        Route::get('payment/sheets/{paymentSheet}/details/{detail}/edit', [PaymentSheetDetailController::class, 'edit'])->name('payment.sheets.details.edit');
        Route::put('payment/sheets/{paymentSheet}/details/{detail}', [PaymentSheetDetailController::class, 'update'])->name('payment.sheets.details.update');
        Route::delete('payment/sheets/{paymentSheet}/details/{detail}/destroy', [PaymentSheetDetailController::class, 'destroy'])->name('payment.sheets.details.destroy');

        Route::get('payment/sheets/{paymentSheetId}/attachment', [PaymentSheetAttachmentController::class, 'index'])->name('payment.sheets.attachment.index');
        Route::get('payment/sheets/{paymentSheetId}/attachment/create', [PaymentSheetAttachmentController::class, 'create'])->name('payment.sheets.attachment.create');
        Route::post('payment/sheets/{paymentSheetId}/attachment', [PaymentSheetAttachmentController::class, 'store'])->name('payment.sheets.attachment.store');
        Route::get('payment/sheets/attachment/{attachmentId}/show', [PaymentSheetAttachmentController::class, 'show'])->name('payment.sheets.attachment.show');
        Route::get('payment/sheets/attachment/{attachmentId}/edit', [PaymentSheetAttachmentController::class, 'edit'])->name('payment.sheets.attachment.edit');
        Route::put('payment/sheets/attachment/{attachmentId}/update', [PaymentSheetAttachmentController::class, 'update'])->name('payment.sheets.attachment.update');
        Route::delete('payment/sheets/attachment/{attachmentId}/destroy', [PaymentSheetAttachmentController::class, 'destroy'])->name('payment.sheets.attachment.destroy');
    });

    Route::get('payment/sheets/{paymentSheet}/details', [PaymentSheetDetailController::class, 'index'])->name('payment.sheets.details.index');
    Route::get('payment/sheets/{paymentSheet}/print', [PaymentSheetController::class, 'print'])->name('payment.sheets.print');

    Route::middleware('can:verify-payment-sheet')->group(function () {
        Route::get('verify/payment/sheets', [VerifyController::class, 'index'])->name('verify.payment.sheets.index');
        Route::get('verify/payment/sheets/{paymentSheet}/create', [VerifyController::class, 'create'])->name('verify.payment.sheets.create');
        Route::post('verify/payment/sheets/{paymentSheet}', [VerifyController::class, 'store'])->name('verify.payment.sheets.store');
    });

    Route::middleware('can:approve-payment-sheet-form')->group(function () {
        Route::get('approve/payment/sheets', [ApproveController::class, 'index'])->name('approve.payment.sheets.index');
        Route::get('approve/payment/sheets/{paymentSheet}/create', [ApproveController::class, 'create'])->name('approve.payment.sheets.create');
        Route::post('approve/payment/sheets/{paymentSheet}', [ApproveController::class, 'store'])->name('approve.payment.sheets.store');
    });

    Route::middleware('can:view-approved-payment-sheet')->group(function () {
        Route::get('approved/payment/sheets', [ApprovedController::class, 'index'])->name('approved.payment.sheets.index');
        Route::get('approved/payment/sheets/{paymentSheet}/show', [ApprovedController::class, 'show'])->name('approved.payment.sheets.show');
        Route::get('approved/payment/sheets/{paymentSheet}/print', [ApprovedController::class, 'print'])->name('approved.payment.sheets.print');
    });

    Route::middleware('can:review-recommended-payment-sheet')->group(function () {
        Route::get('review/recommended/payment/sheets', [ReviewRecommendedController::class, 'index'])->name('review.recommended.payment.sheets.index');
        Route::get('review/recommended/payment/sheets/{paymentSheet}/create', [ReviewRecommendedController::class, 'create'])->name('review.recommended.payment.sheets.create');
        Route::post('review/recommended/payment/sheets/{paymentSheet}/store', [ReviewRecommendedController::class, 'store'])->name('review.recommended.payment.sheets.store');
    });

    Route::middleware('can:approve-recommended-payment-sheet')->group(function () {
        Route::get('approve/recommended/payment/sheets', [ApproveRecommendedController::class, 'index'])->name('approve.recommended.payment.sheets.index');
        Route::get('approve/recommended/payment/sheets/{paymentSheet}/create', [ApproveRecommendedController::class, 'create'])->name('approve.recommended.payment.sheets.create');
        Route::post('approve/recommended/payment/sheets/{paymentSheet}/store', [ApproveRecommendedController::class, 'store'])->name('approve.recommended.payment.sheets.store');
    });

    Route::middleware('can:pay-payment-sheet')->group(function () {
        Route::get('approved/payment/sheets/{paymentSheet}/pay/create', [PaymentController::class, 'create'])->name('approved.payment.sheets.pay.create');
        Route::post('approved/payment/sheets/{paymentSheet}/pay', [PaymentController::class, 'store'])->name('approved.payment.sheets.pay.store');
        Route::get('paid/payment/sheets', [PaymentController::class, 'index'])->name('paid.payment.sheets.index');
        Route::get('paid/payment/sheets/{paymentSheet}/show', [PaymentController::class, 'show'])->name('paid.payment.sheets.show');
    });
});
