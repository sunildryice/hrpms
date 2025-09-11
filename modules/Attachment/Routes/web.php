<?php

use Modules\Attachment\Controllers\AttachmentController;

Route::middleware(['web', 'auth', 'logger'])->group(function () {
    Route::get('attachments', [AttachmentController::class, 'index'])->name('attachments.index');
    Route::get('attachments/create', [AttachmentController::class, 'create'])->name('attachments.create');
    Route::post('attachments', [AttachmentController::class, 'store'])->name('attachments.store');
    Route::get('attachments/{id}/edit', [AttachmentController::class, 'edit'])->name('attachments.edit');
    Route::put('attachments/{id}/update', [AttachmentController::class, 'update'])->name('attachments.update');
    Route::delete('attachments/{id}/destroy', [AttachmentController::class, 'destroy'])->name('attachments.destroy');

    Route::get('attachments/list', [AttachmentController::class, 'list'])->name('attachments.list');
});