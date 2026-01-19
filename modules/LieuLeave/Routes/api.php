<?php

use Illuminate\Support\Facades\Route;
use Modules\LieuLeave\Controllers\Api\LeaveBalanceController;
use Modules\LieuLeave\Controllers\Api\OffDayWorkController;

Route::middleware(['web', 'logger'])->prefix('api/lieu-leave')->group(function () {
    Route::get('check-status/{month}', [LeaveBalanceController::class, 'index'])->name('api.lieu.leave.check.status');
    Route::get('offDayWork/user/{date}', [OffDayWorkController::class, 'index'])->name('api.lieu.leave.offdaywork.user');
});
