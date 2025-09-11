<?php

use Modules\Announcement\Controllers\AnnouncementController;

Route::middleware(['web', 'auth', 'logger'])->group(function () {
    Route::get('announcement', [AnnouncementController::class, 'index'])->name('announcement.index');
    Route::get('announcement/create', [AnnouncementController::class, 'create'])->name('announcement.create');
    Route::post('announcement', [AnnouncementController::class, 'store'])->name('announcement.store');
    Route::get('announcement/{id}/show', [AnnouncementController::class, 'show'])->name('announcement.show');
    Route::get('announcement/{id}/edit', [AnnouncementController::class, 'edit'])->name('announcement.edit');
    Route::put('announcement/{id}/update', [AnnouncementController::class, 'update'])->name('announcement.update');
    Route::delete('announcement/{id}/destroy', [AnnouncementController::class, 'destroy'])->name('announcement.destroy');
});