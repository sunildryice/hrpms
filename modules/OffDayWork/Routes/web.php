<?php


use Illuminate\Support\Facades\Route;
use Modules\OffDayWork\Controllers\ApproveController;
use Modules\OffDayWork\Controllers\RequestController;
use Modules\OffDayWork\Controllers\ApprovedController;
use Modules\OffDayWork\Controllers\RejectedController;

Route::middleware(['web', 'auth'])->prefix('offday-work')->group(function () {
    Route::get('requests', [RequestController::class, 'index'])->name('off.day.work.index');
    Route::get('requests/create', [RequestController::class, 'create'])->name('off.day.work.create');
    Route::post('requests/store', [RequestController::class, 'store'])->name('off.day.work.store');
    Route::get('requests/{offDayWork}/edit', [RequestController::class, 'edit'])->name('off.day.work.edit');
    Route::get('requests/{offDayWork}/show', [RequestController::class, 'show'])->name('off.day.work.show');
    Route::put('requests/{offDayWork}/update', [RequestController::class, 'update'])->name('off.day.work.update');
    Route::delete('requests/{offDayWork}/delete', [RequestController::class, 'destroy'])->name('off.day.work.delete');
});

Route::middleware(['web', 'auth'])->prefix('approve/offday-work')->group(function () {
    Route::get('requests', [ApproveController::class, 'index'])->name('approve.off.day.work.index');
    Route::get('requests/{offDayWork}/show', [ApproveController::class, 'show'])->name('approve.off.day.work.show');
    Route::post('requests/{offDayWork}/update', [ApproveController::class, 'update'])->name('approve.off.day.work.update');
});


Route::middleware(['web', 'auth'])->prefix('rejected/offday-work')->group(function () {
    Route::get('requests', [RejectedController::class, 'index'])->name('rejected.off.day.work.index');
    Route::get('requests/{offDayWork}/show', [RejectedController::class, 'show'])->name('rejected.off.day.work.show');
});



Route::middleware(['web', 'auth'])->prefix('approved/offday-work')->group(function () {
    Route::get('requests', [ApprovedController::class, 'index'])->name('approved.off.day.work.index');
    Route::get('requests/{offDayWork}/show', [ApprovedController::class, 'show'])->name('approved.off.day.work.show');
});
