<?php

use Illuminate\Support\Facades\Route;
use Modules\WorkFromHome\Controllers\ApproveController;
use Modules\WorkFromHome\Controllers\RequestController;
use Modules\WorkFromHome\Controllers\ApprovedController;

Route::middleware(['web', 'auth', 'logger'])->group(function () {

    Route::get('wfh/requests', [RequestController::class, 'index'])->name('wfh.requests.index');
    Route::get('wfh/requests/create', [RequestController::class, 'create'])->name('wfh.requests.create');
    Route::post('wfh/requests', [RequestController::class, 'store'])->name('wfh.requests.store');
    Route::get('wfh/{id}/requests', [RequestController::class, 'show'])->name('wfh.requests.show');
});

Route::middleware(['web', 'auth', 'logger'])->prefix('approve')
    ->group(function () {
        Route::get('wfh/requests', [ApproveController::class, 'index'])->name('approve.wfh.requests.index');
        Route::get('wfh/{id}/requests', [ApproveController::class, 'show'])->name('approve.wfh.requests.show');
        Route::post('wfh/{id}/requests', [ApproveController::class, 'update'])->name('approve.wfh.requests.update');
    });


Route::middleware(['web', 'auth', 'logger'])->prefix('approved')
    ->group(function () {
        Route::get('wfh/requests', [ApprovedController::class, 'index'])->name('approved.wfh.requests.index');
        Route::get('wfh/{id}/requests', [ApprovedController::class, 'show'])->name('approved.wfh.requests.show');
        Route::post('wfh/{id}/requests', [ApprovedController::class, 'update'])->name('approved.wfh.requests.update');
    });
