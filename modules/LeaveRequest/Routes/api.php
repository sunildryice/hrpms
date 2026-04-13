<?php

use Illuminate\Support\Facades\Route;
use Modules\LeaveRequest\Controllers\Api\HolidayController;

Route::middleware(['web', 'logger'])->prefix('api/leave')->group(function () {
    Route::get('holidays', [HolidayController::class, 'index'])->name('api.leave.holidays.index');
});
