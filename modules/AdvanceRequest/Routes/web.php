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

use Modules\AdvanceRequest\Controllers\CloseController;
use Modules\AdvanceRequest\Controllers\ClosedController;
use Modules\AdvanceRequest\Controllers\VerifyController;
use Modules\AdvanceRequest\Controllers\ApproveController;
use Modules\AdvanceRequest\Controllers\SettlementPaymentController;
use Modules\AdvanceRequest\Controllers\AdvancePaymentController;
use Modules\AdvanceRequest\Controllers\ApprovedController;
use Modules\AdvanceRequest\Controllers\SettlementController;
use Modules\AdvanceRequest\Controllers\AdvanceRequestController;
use Modules\AdvanceRequest\Controllers\ReviewSettlementController;
use Modules\AdvanceRequest\Controllers\VerifySettlementController;
use Modules\AdvanceRequest\Controllers\ApproveSettlementController;
use Modules\AdvanceRequest\Controllers\SettlementExpenseController;
use Modules\AdvanceRequest\Controllers\ApprovedSettlementController;
use Modules\AdvanceRequest\Controllers\SettlementActivityController;
use Modules\AdvanceRequest\Controllers\AdvanceRequestDetailController;
use Modules\AdvanceRequest\Controllers\SettlementAttachmentController;
use Modules\AdvanceRequest\Controllers\SettlementExpenseDetailController;

Route::middleware(['web', 'auth', 'logger'])->group(function () {
    Route::middleware('can:advance-request')->group(function () {
        Route::get('advance/requests', [AdvanceRequestController::class, 'index'])->name('advance.requests.index');
        Route::get('advance/requests/create', [AdvanceRequestController::class, 'create'])->name('advance.requests.create');
        Route::post('advance/requests', [AdvanceRequestController::class, 'store'])->name('advance.requests.store');
        Route::get('advance/requests/{advance}/edit', [AdvanceRequestController::class, 'edit'])->name('advance.requests.edit');
        Route::put('advance/requests/{advance}', [AdvanceRequestController::class, 'update'])->name('advance.requests.update');
        Route::get('advance/requests/{advance}/show', [AdvanceRequestController::class, 'show'])->name('advance.requests.show');
        Route::delete('advance/requests/{advance}/destroy', [AdvanceRequestController::class, 'destroy'])->name('advance.requests.destroy');

        Route::get('advance/requests/{advance}/details/create', [AdvanceRequestDetailController::class, 'create'])->name('advance.requests.details.create');
        Route::post('advance/requests/{advance}/details', [AdvanceRequestDetailController::class, 'store'])->name('advance.requests.details.store');
        Route::get('advance/requests/{advance}/details/{detail}/edit', [AdvanceRequestDetailController::class, 'edit'])->name('advance.requests.details.edit');
        Route::post('advance/requests/{advance}/details/{detail}', [AdvanceRequestDetailController::class, 'update'])->name('advance.requests.details.update');
        Route::delete('advance/requests/{advance}/details/{detail}/destroy', [AdvanceRequestDetailController::class, 'destroy'])->name('advance.requests.details.destroy');
        Route::delete('advance/requests/details/{detail}/attachment/delete', [AdvanceRequestDetailController::class, 'deleteAttachment'])->name('advance.requests.details.attachment.delete');
    });
    Route::get('advance/requests/{advance}/details', [AdvanceRequestDetailController::class, 'index'])->name('advance.requests.details.index');

    Route::middleware('can:approve-advance-request')->group(function () {
        Route::get('approve/advance/requests', [ApproveController::class, 'index'])->name('approve.advance.requests.index');
        Route::get('approve/advance/requests/{advance}/create', [ApproveController::class, 'create'])->name('approve.advance.requests.create');
        Route::post('approve/advance/requests/{advance}', [ApproveController::class, 'store'])->name('approve.advance.requests.store');
    });

    Route::middleware('can:verify-advance-request')->group(function () {
        Route::get('verify/advance/requests', [VerifyController::class, 'index'])->name('verify.advance.requests.index');
        Route::get('verify/advance/requests/{advance}/create', [VerifyController::class, 'create'])->name('verify.advance.requests.create');
        Route::post('verify/advance/requests/{advance}', [VerifyController::class, 'store'])->name('verify.advance.requests.store');
    });

    Route::middleware('can:view-approved-advance-request')->group(function () {
        Route::get('approved/advance/requests', [ApprovedController::class, 'index'])->name('approved.advance.requests.index');
        Route::get('approved/advance/requests/{advance}/show', [ApprovedController::class, 'show'])->name('approved.advance.requests.show');

        Route::get('close/advance/requests/{advance}/create', [CloseController::class, 'create'])->name('close.advance.requests.create');
        Route::post('close/advance/requests/{advance}', [CloseController::class, 'store'])->name('close.advance.requests.store');

        Route::get('closed/advance/requests', [ClosedController::class, 'index'])->name('closed.advance.requests.index');
        Route::get('closed/advance/requests/{advance}/show', [ClosedController::class, 'show'])->name('closed.advance.requests.show');
    });
    Route::get('advance/requests/{request}/print', [ApprovedController::class, 'print'])->name('advance.request.print');

    Route::middleware('can:advance-request')->group(function () {
        Route::get('advance/settlement/requests', [SettlementController::class, 'index'])->name('advance.settlement.index');
        Route::get('advance/settlement/requests/{advance}/create', [SettlementController::class, 'create'])->name('advance.settlement.create');
        Route::post('advance/settlement/requests/{advance}/', [SettlementController::class, 'store'])->name('advance.settlement.store');
        Route::get('advance/settlement/requests/{settlement}/edit', [SettlementController::class, 'edit'])->name('advance.settlement.edit');
        Route::put('advance/settlement/requests/{settlement}', [SettlementController::class, 'update'])->name('advance.settlement.update');
        Route::delete('advance/settlement/requests/{settlement}', [SettlementController::class, 'destroy'])->name('advance.settlement.destroy');
        Route::post('advance/settlement/requests/{settlement}/amend', [SettlementController::class, 'amend'])->name('advance.settlement.amend');

        Route::get('advance/settlement/{settlement}/activities/create', [SettlementActivityController::class, 'create'])->name('advance.settlement.activities.create');
        Route::post('advance/settlement/{settlement}/activities', [SettlementActivityController::class, 'store'])->name('advance.settlement.activities.store');
        Route::get('advance/settlement/{settlement}/activities/{activity}/edit', [SettlementActivityController::class, 'edit'])->name('advance.settlement.activities.edit');
        Route::put('advance/settlement/{settlement}/activities/{activity}', [SettlementActivityController::class, 'update'])->name('advance.settlement.activities.update');
        Route::delete('advance/settlement/{settlement}/activities/{activity}', [SettlementActivityController::class, 'destroy'])->name('advance.settlement.activities.destroy');

        Route::get('advance/settlement/{settlement}/attachment', [SettlementAttachmentController::class, 'index'])->name('advance.settlement.attachment.index');
        Route::get('advance/settlement/{settlement}/attachment/create', [SettlementAttachmentController::class, 'create'])->name('advance.settlement.attachment.create');
        Route::post('advance/settlement/{settlement}/attachment', [SettlementAttachmentController::class, 'store'])->name('advance.settlement.attachment.store');
        Route::get('advance/settlement/attachment/{attachmentId}/show', [SettlementAttachmentController::class, 'show'])->name('advance.settlement.attachment.show');
        Route::get('advance/settlement/attachment/{attachmentId}/edit', [SettlementAttachmentController::class, 'edit'])->name('advance.settlement.attachment.edit');
        Route::put('advance/settlement/attachment/{attachmentId}/update', [SettlementAttachmentController::class, 'update'])->name('advance.settlement.attachment.update');
        Route::delete('advance/settlement/attachment/{attachmentId}/destroy', [SettlementAttachmentController::class, 'destroy'])->name('advance.settlement.attachment.destroy');

        // expense
//        Route::get('advance/settlement/{settlement}/expense/create', [SettlementExpenseController::class, 'create'])->name('advance.settlement.expense.create');
//        Route::post('advance/settlement/{settlement}/expense', [SettlementExpenseController::class, 'store'])->name('advance.settlement.expense.store');
        Route::get('advance/settlement/{settlement}/expense/{expense}/edit', [SettlementExpenseController::class, 'edit'])->name('advance.settlement.expense.edit');
        Route::put('advance/settlement/{settlement}/expense/{expense}', [SettlementExpenseController::class, 'update'])->name('advance.settlement.expense.update');
//        Route::delete('advance/settlement/{settlement}/expense/{expense}', [SettlementExpenseController::class, 'destroy'])->name('advance.settlement.expense.destroy');

        Route::get('advance/settlement/expense/{expense}/detail/create', [SettlementExpenseDetailController::class, 'create'])->name('advance.settlement.expense.details.create');
        Route::post('advance/settlement/expense/{expense}/detail', [SettlementExpenseDetailController::class, 'store'])->name('advance.settlement.expense.details.store');
        Route::get('advance/settlement/expense/{expense}/detail/{detail}/edit', [SettlementExpenseDetailController::class, 'edit'])->name('advance.settlement.expense.details.edit');
        Route::put('advance/settlement/expense/{expense}/detail/{detail}', [SettlementExpenseDetailController::class, 'update'])->name('advance.settlement.expense.details.update');
        Route::delete('advance/settlement/expense/{expense}/detail/{detail}', [SettlementExpenseDetailController::class, 'destroy'])->name('advance.settlement.expense.details.destroy');
        Route::delete('advance/settlement/expense/detail/{detail}/attachment/delete', [SettlementExpenseDetailController::class, 'deleteAttachment'])->name('advance.settlement.expense.details.attachment.delete');
    });
    Route::get('advance/settlement/requests/{settlement}/show', [SettlementController::class, 'show'])->name('advance.settlement.show');
    Route::get('advance/settlement/{settlement}/activities', [SettlementActivityController::class, 'index'])->name('advance.settlement.activities.index');
    Route::get('advance/settlement/{settlement}/expenses', [SettlementExpenseController::class, 'index'])->name('advance.settlement.expense.index');
    Route::get('advance/settlement/{settlement}/expenses/summary', [SettlementExpenseController::class, 'summary'])->name('advance.settlement.expense.summary');

    Route::middleware('can:finance-review-advance-settlement')->group(function () {
        Route::get('review/advance/settlements', [ReviewSettlementController::class, 'index'])
            ->name('review.advance.settlements.index');
        Route::get('review/advance/settlements/{settlement}/create', [ReviewSettlementController::class, 'create'])
            ->name('review.advance.settlements.create');
        Route::post('review/advance/settlements/{settlement}', [ReviewSettlementController::class, 'store'])
            ->name('review.advance.settlements.store');
    });

    Route::middleware('can:finance-review-advance-settlement')->group(function () {
        Route::get('verify/advance/settlements', [VerifySettlementController::class, 'index'])
            ->name('verify.advance.settlements.index');
        Route::get('verify/advance/settlements/{settlement}/create', [VerifySettlementController::class, 'create'])
            ->name('verify.advance.settlements.create');
        Route::post('verify/advance/settlements/{settlement}', [VerifySettlementController::class, 'store'])
            ->name('verify.advance.settlements.store');
    });

    Route::middleware('can:approve-advance-settlement-form')->group(function () {
        Route::get('approve/advance/settlements', [ApproveSettlementController::class, 'index'])->name('approve.advance.settlements.index');
        Route::get('approve/advance/settlements/{settlement}/create', [ApproveSettlementController::class, 'create'])->name('approve.advance.settlements.create');
        Route::post('approve/advance/settlements/{settlement}', [ApproveSettlementController::class, 'store'])->name('approve.advance.settlements.store');
    });
    Route::get('approved/settlements/advance/requests', [ApprovedSettlementController::class, 'index'])->name('approved.advance.settlements.index');
    Route::get('approved/settlements/advance/requests/{settlement}/show', [ApprovedSettlementController::class, 'show'])->name('approved.advance.settlements.show');
    Route::get('advance/requests/settlement/{request}/print', [ApprovedSettlementController::class, 'print'])->name('advance.request.settlement.print');

    Route::middleware('can:pay-advance-settlement')->group(function(){
        Route::get('approved/advance/{advance}/pay/create',[AdvancePaymentController::class,'create'])->name('approved.advance.pay.create');
        Route::post('approved/advance/{advance}/pay',[AdvancePaymentController::class,'store'])->name('approved.advance.pay.store');
        Route::get('paid/advance/',[AdvancePaymentController::class,'index'])->name('paid.advance.index');
        Route::get('paid/advance/{advance}/show',[AdvancePaymentController::class,'show'])->name('paid.advance.show');

        Route::get('approved/settlements/{settlement}/pay/create',[SettlementPaymentController::class,'create'])->name('approved.settlement.pay.create');
        Route::post('approved/settlements/{settlement}/pay',[SettlementPaymentController::class,'store'])->name('approved.settlement.pay.store');
        Route::get('paid/advance/settlement',[SettlementPaymentController::class,'index'])->name('paid.advance.settlement.index');
        Route::get('paid/advance/settlement/{settlement}/show',[SettlementPaymentController::class,'show'])->name('paid.advance.settlement.show');
    });
});
