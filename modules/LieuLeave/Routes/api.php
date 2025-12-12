<?php

use Illuminate\Support\Facades\Route;
use Modules\LieuLeave\Controllers\Api\LeaveBalanceController;

Route::middleware(['web', 'logger'])->prefix('api/lieu-leave')->group(function () {
    Route::get('check-status/{month}', [LeaveBalanceController::class, 'index'])->name('api.lieu.leave.check.status');
});
