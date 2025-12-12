<?php


use Illuminate\Support\Facades\Route;
use Modules\LieuLeave\Controllers\RequestController;
use Modules\LieuLeave\Controllers\ApproveController;
use Modules\LieuLeave\Controllers\RejectedController;
use Modules\LieuLeave\Controllers\ApprovedController;

Route::middleware(['web', 'auth'])->prefix('lieu-leave')->group(function () {
    Route::get('requests', [RequestController::class, 'index'])->name('lieu.leave.requests.index');
    Route::get('requests/create', [RequestController::class, 'create'])->name('lieu.leave.requests.create');
    Route::post('requests/store', [RequestController::class, 'store'])->name('lieu.leave.requests.store');
    Route::get('requests/{lieuLeave}/edit', [RequestController::class, 'edit'])->name('lieu.leave.requests.edit');
    Route::get('requests/{lieuLeave}/show', [RequestController::class, 'show'])->name('lieu.leave.requests.show');
    Route::put('requests/{lieuLeave}/update', [RequestController::class, 'update'])->name('lieu.leave.requests.update');
    Route::delete('requests/{lieuLeave}/delete', [RequestController::class, 'destroy'])->name('lieu.leave.requests.delete');
});

Route::middleware(['web', 'auth'])->prefix('approve/lieu-leave')->group(function () {
    Route::get('requests', [ApproveController::class, 'index'])->name('approve.lieu.leave.requests.index');
    Route::get('requests/{lieuLeave}/show', [ApproveController::class, 'show'])->name('approve.lieu.leave.requests.show');
    Route::post('requests/{lieuLeave}/update', [ApproveController::class, 'update'])->name('approve.lieu.leave.requests.update');
});

Route::middleware(['web', 'auth'])->prefix('rejected/lieu-leave')->group(function () {
    Route::get('requests', [RejectedController::class, 'index'])->name('rejected.lieu.leave.requests.index');
    Route::get('requests/{lieuLeave}/show', [RejectedController::class, 'show'])->name('rejected.lieu.leave.requests.show');
});

Route::middleware(['web', 'auth'])->prefix('approved/lieu-leave')->group(function () {
    Route::get('requests', [ApprovedController::class, 'index'])->name('approved.lieu.leave.requests.index');
    Route::get('requests/{lieuLeave}/show', [ApprovedController::class, 'show'])->name('approved.lieu.leave.requests.show');
});
