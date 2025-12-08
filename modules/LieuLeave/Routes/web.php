<?php


use Illuminate\Support\Facades\Route;
use Modules\LieuLeave\Controllers\RequestController;


Route::middleware(['web', 'auth'])->prefix('lieu-leave')->group(function () {
    Route::get('requests', [RequestController::class, 'index'])->name('lieu.leave.requests.index');
    Route::get('requests/create', [RequestController::class, 'create'])->name('lieu.leave.requests.create');
    Route::post('requests/store', [RequestController::class, 'store'])->name('lieu.leave.requests.store');
    Route::get('requests/{lieuLeave}/edit', [RequestController::class, 'edit'])->name('lieu.leave.requests.edit');
    Route::put('requests/{lieuLeave}/update', [RequestController::class, 'update'])->name('lieu.leave.requests.update');
    Route::delete('requests/{lieuLeave}/delete', [RequestController::class, 'destroy'])->name('lieu.leave.requests.delete');
});
