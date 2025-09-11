<?php

use Modules\Mfr\Controllers\AgreementAmendmentController;
use Modules\Mfr\Controllers\AgreementController;
use Modules\Mfr\Controllers\TransactionVerifyController as VerifyController;
use Modules\Mfr\Controllers\TransactionRecommendController as RecommendController;
use Modules\Mfr\Controllers\TransactionApprovedController as ApprovedController;
use Modules\Mfr\Controllers\TransactionApproveController as ApproveController;
use Modules\Mfr\Controllers\TransactionController;
use Modules\Mfr\Controllers\TransactionReviewController as ReviewController;

Route::middleware(['web', 'auth', 'logger'])->prefix('mfr/agreements')->as('mfr.agreement.')->group(function () {
    Route::get('', [AgreementController::class, 'index'])->name('index');
    Route::get('create', [AgreementController::class, 'create'])->name('create');
    Route::post('', [AgreementController::class, 'store'])->name('store');
    Route::get('{agreement}/show', [AgreementController::class, 'show'])->name('show');
    Route::get('{agreement}/show/transactions', [AgreementController::class, 'showTransactions'])->name('show.transactions');
    Route::get('{agreement}/edit', [AgreementController::class, 'edit'])->name('edit');
    Route::put('{agreement}', [AgreementController::class, 'update'])->name('update');
    Route::post('{agreement}/amend', [AgreementController::class, 'amend'])->name('amend');
    Route::delete('{agreement}/destroy', [AgreementController::class, 'destroy'])->name('destroy');
    // Route::get('/{agreement}/print', [AgreementController::class, 'print'])->name('print');
    // Route::get('approved/index', [ApprovedController::class, 'index'])->name('approved.index');

    Route::get('{agreement}/amendment', [AgreementAmendmentController::class, 'index'])->name('amendment.index');
    Route::get('{agreement}/amendment/create', [AgreementAmendmentController::class, 'create'])->name('amendment.create');
    Route::post('{agreement}/amendment', [AgreementAmendmentController::class, 'store'])->name('amendment.store');
    Route::get('amendment/{amendment}/show', [AgreementAmendmentController::class, 'show'])->name('amendment.show');
    Route::get('amendment/{amendment}/edit', [AgreementAmendmentController::class, 'edit'])->name('amendment.edit');
    Route::put('amendment/{amendment}/update', [AgreementAmendmentController::class, 'update'])->name('amendment.update');
    Route::delete('amendment/{amendment}/destroy', [AgreementAmendmentController::class, 'destroy'])->name('amendment.destroy');
});

Route::middleware(['web', 'auth', 'logger'])
    ->prefix('mfr/agreements/transactions')->as('mfr.transaction.')->group(function () {
        Route::get('', [TransactionController::class, 'index'])->name('index');
        Route::get('/{agreement}/create', [TransactionController::class, 'create'])->name('create');
        Route::post('/{agreement}/store', [TransactionController::class, 'store'])->name('store');
        Route::get('{transaction}/show', [TransactionController::class, 'show'])->name('show');
        Route::get('/edit/{transactionId}', [TransactionController::class, 'edit'])->name('edit');
        Route::put('/update/{transactionId}', [TransactionController::class, 'update'])->name('update');
        Route::delete('{transaction}', [TransactionController::class, 'destroy'])->name('delete');
        Route::get('{transaction}/print', [TransactionController::class, 'print'])->name('print');
    });

Route::middleware(['web', 'auth', 'logger', 'can:review-mfr-transaction'])->prefix('mfr/transactions')->as('mfr.transaction.')->group(function () {
    Route::get('review', [ReviewController::class, 'index'])->name('review.index');
    Route::get('{transaction}/review/create', [ReviewController::class, 'create'])->name('review.create');
    Route::post('{transaction}/review/store', [ReviewController::class, 'store'])->name('review.store');
    Route::get('{transaction}/review/view', [ReviewController::class, 'view'])->name('review.view');
});

Route::middleware(['web', 'auth', 'logger', 'can:verify-mfr-transaction'])->prefix('mfr/transactions')->as('mfr.transaction.')->group(function () {
    Route::get('verify', [VerifyController::class, 'index'])->name('verify.index');
    Route::get('{transaction}/verify/create', [VerifyController::class, 'create'])->name('verify.create');
    Route::post('{transaction}/verify/store', [VerifyController::class, 'store'])->name('verify.store');
});

Route::middleware(['web', 'auth', 'logger', 'can:recommend-mfr-transaction'])->prefix('mfr/transactions')->as('mfr.transaction.')->group(function () {
    Route::get('recommend', [RecommendController::class, 'index'])->name('recommend.index');
    Route::get('{transaction}/recommend/create', [RecommendController::class, 'create'])->name('recommend.create');
    Route::post('{transaction}/recommend/store', [RecommendController::class, 'store'])->name('recommend.store');
});

Route::middleware(['web', 'auth', 'logger', 'can:approve-mfr-transaction'])->prefix('mfr/transactions')->as('mfr.transaction.')->group(function () {
    Route::get('approve', [ApproveController::class, 'index'])->name('approve.index');
    Route::get('{transaction}/approve/create', [ApproveController::class, 'create'])->name('approve.create');
    Route::post('{transaction}/approve/store', [ApproveController::class, 'store'])->name('approve.store');
});

Route::middleware(['web', 'auth', 'logger' ])->prefix('mfr/transactions')->as('mfr.transaction.')->group(function () {
    Route::get('approved', [ApprovedController::class, 'index'])->name('approved.index');
    Route::get('{transaction}/approved/show', [ApprovedController::class, 'show'])->name('approved.show');
});
