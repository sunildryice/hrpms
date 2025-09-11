<?php

/*
|--------------------------------------------------------------------------
| Application Routes for Construction Track Module
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is ordered.
|
*/

use Modules\ConstructionTrack\Controllers\ConstructionAmendmentController;
use Modules\ConstructionTrack\Controllers\ConstructionAttachmentController;
use Modules\ConstructionTrack\Controllers\ConstructionController;
use Modules\ConstructionTrack\Controllers\ConstructionInstallmentApproveController;
use Modules\ConstructionTrack\Controllers\ConstructionPartyController;
use Modules\ConstructionTrack\Controllers\ConstructionProgressController;
use Modules\ConstructionTrack\Controllers\ConstructionSettlementController;
use Modules\ConstructionTrack\Controllers\ConstructionInstallmentController;
use Modules\ConstructionTrack\Controllers\ConstructionInstallmentReviewController;
use Modules\ConstructionTrack\Controllers\ConstructionProgressAttachmentController;

Route::middleware(['web', 'auth', 'logger'])->group(function () {
    Route::get('construction', [ConstructionController::class, 'index'])->name('construction.index');
    Route::get('construction/create', [ConstructionController::class, 'create'])->name('construction.create');
    Route::post('construction', [ConstructionController::class, 'store'])->name('construction.store');
    Route::get('construction/{construction}/edit', [ConstructionController::class, 'edit'])->name('construction.edit');
    Route::get('construction/{construction}/edit/progress', [ConstructionController::class, 'editProgress'])->name('construction.edit.progress');
    Route::put('construction/{construction}', [ConstructionController::class, 'update'])->name('construction.update');
    Route::get('construction/{construction}/show', [ConstructionController::class, 'show'])->name('construction.show');
    Route::delete('construction/{construction}/destroy', [ConstructionController::class, 'destroy'])->name('construction.destroy');

    Route::get('construction/{constructionId}/parties', [ConstructionPartyController::class, 'index'])->name('construction.parties.index');
    Route::get('construction/{constructionId}/parties/create', [ConstructionPartyController::class, 'create'])->name('construction.parties.create');
    Route::post('construction/{constructionId}/parties', [ConstructionPartyController::class, 'store'])->name('construction.parties.store');
    Route::get('construction/parties/{constructionParty}/edit', [ConstructionPartyController::class, 'edit'])->name('construction.parties.edit');
    Route::put('construction/parties/{constructionParty}', [ConstructionPartyController::class, 'update'])->name('construction.parties.update');
    Route::delete('construction/parties/{constructionParty}/destroy', [ConstructionPartyController::class, 'destroy'])->name('construction.parties.destroy');

    Route::get('construction/{constructionId}/progress', [ConstructionProgressController::class, 'index'])->name('construction.progress.index');
    Route::get('construction/{constructionId}/progress/create', [ConstructionProgressController::class, 'create'])->name('construction.progress.create');
    Route::post('construction/{constructionId}/progress', [ConstructionProgressController::class, 'store'])->name('construction.progress.store');
    Route::get('construction/{constructionId}/progress/{constructionProgress}/edit', [ConstructionProgressController::class, 'edit'])->name('construction.progress.edit');
    Route::put('construction/{constructionId}/progress/{constructionProgress}', [ConstructionProgressController::class, 'update'])->name('construction.progress.update');
    Route::delete('construction/{constructionId}/progress/{constructionProgress}/destroy', [ConstructionProgressController::class, 'destroy'])->name('construction.progress.destroy');

    Route::get('construction/progress/{progressId}/attachment/index', [ConstructionProgressAttachmentController::class, 'index'])->name('construction.progress.attachment.index');
    Route::get('construction/progress/{progressId}/attachment/create', [ConstructionProgressAttachmentController::class, 'create'])->name('construction.progress.attachment.create');
    Route::post('construction/progress/{progressId}/attachment/store', [ConstructionProgressAttachmentController::class, 'store'])->name('construction.progress.attachment.store');
    Route::delete('construction/progress/attachment/{attachmentId}/destroy', [ConstructionProgressAttachmentController::class, 'destroy'])->name('construction.progress.attachment.destroy');

    Route::get('construction/{constructionId}/settlement', [ConstructionSettlementController::class, 'edit'])->name('construction.settlement.edit');
    
    Route::get('construction/{constructionId}/installment', [ConstructionInstallmentController::class, 'index'])->name('construction.installment.index');
    Route::get('construction/{constructionId}/installment/create', [ConstructionInstallmentController::class, 'create'])->name('construction.installment.create');
    Route::post('construction/{constructionId}/installment', [ConstructionInstallmentController::class, 'store'])->name('construction.installment.store');
    Route::get('construction/{constructionId}/installment/{constructionInstallment}/edit', [ConstructionInstallmentController::class, 'edit'])->name('construction.installment.edit');
    Route::put('construction/{constructionId}/installment/{constructionInstallment}', [ConstructionInstallmentController::class, 'update'])->name('construction.installment.update');
    Route::delete('construction/{constructionId}/installment/{constructionInstallment}/destroy', [ConstructionInstallmentController::class, 'destroy'])->name('construction.installment.destroy');
    Route::post('construction/installment/totalAmount', [ConstructionInstallmentController::class, 'totalAmount'])->name('construction.installment.totalAmount');
    Route::post('construction/installment/{installmentId}/submit/store', [ConstructionInstallmentController::class, 'submit'])->name('construction.installment.submit');
    Route::get('construction/installment/{installmentId}/submit/create', [ConstructionInstallmentController::class, 'createSubmit'])->name('construction.installment.submit.create');

    
    Route::any('construction/installment/review/index', [ConstructionInstallmentReviewController::class, 'index'])->name('construction.installment.review.index');
    Route::any('construction/installment/{installmentId}/review/create', [ConstructionInstallmentReviewController::class, 'create'])->name('construction.installment.review.create');
    Route::any('construction/installment/{installmentId}/review/store', [ConstructionInstallmentReviewController::class, 'store'])->name('construction.installment.review.store');
    
    
    Route::any('construction/installment/approve/index', [ConstructionInstallmentApproveController::class, 'index'])->name('construction.installment.approve.index');
    Route::any('construction/installment/{installmentId}/approve/create', [ConstructionInstallmentApproveController::class, 'create'])->name('construction.installment.approve.create');
    Route::any('construction/installment/{installmentId}/approve/store', [ConstructionInstallmentApproveController::class, 'store'])->name('construction.installment.approve.store');


    Route::get('construction/{constructionId}/attachment', [ConstructionAttachmentController::class, 'index'])->name('construction.attachment.index');
    Route::get('construction/{constructionId}/attachment/create', [ConstructionAttachmentController::class, 'create'])->name('construction.attachment.create');
    Route::post('construction/{constructionId}/attachment', [ConstructionAttachmentController::class, 'store'])->name('construction.attachment.store');
    Route::get('construction/attachment/{attachmentId}/show', [ConstructionAttachmentController::class, 'show'])->name('construction.attachment.show');
    Route::get('construction/attachment/{attachmentId}/edit', [ConstructionAttachmentController::class, 'edit'])->name('construction.attachment.edit');
    Route::put('construction/attachment/{attachmentId}/update', [ConstructionAttachmentController::class, 'update'])->name('construction.attachment.update');
    Route::delete('construction/attachment/{attachmentId}/destroy', [ConstructionAttachmentController::class, 'destroy'])->name('construction.attachment.destroy');

    Route::get('construction/{constructionId}/amendment', [ConstructionAmendmentController::class, 'index'])->name('construction.amendment.index');
    Route::get('construction/{constructionId}/amendment/create', [ConstructionAmendmentController::class, 'create'])->name('construction.amendment.create');
    Route::post('construction/{constructionId}/amendment', [ConstructionAmendmentController::class, 'store'])->name('construction.amendment.store');
    Route::get('construction/amendment/{amendmentId}/show', [ConstructionAmendmentController::class, 'show'])->name('construction.amendment.show');
    Route::get('construction/amendment/{amendmentId}/edit', [ConstructionAmendmentController::class, 'edit'])->name('construction.amendment.edit');
    Route::put('construction/amendment/{amendmentId}/update', [ConstructionAmendmentController::class, 'update'])->name('construction.amendment.update');
    Route::delete('construction/amendment/{amendmentId}/destroy', [ConstructionAmendmentController::class, 'destroy'])->name('construction.amendment.destroy');
});
