<?php

use Illuminate\Support\Facades\Route;
use Modules\OffDayWork\Controllers\Api\HolidayController;

Route::middleware(['web', 'logger'])->prefix('api/offday-work')->group(function () {
    Route::get('holidays', [HolidayController::class, 'index'])->name('api.offday.work.holidays.index');
});
