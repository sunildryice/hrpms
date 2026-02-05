<?php

use Illuminate\Support\Facades\Route;
use Modules\Project\Controllers\Api\TimeSheetController;

Route::middleware(['web', 'auth'])->group(function () {
    Route::get('/timesheet/activities-by-project', [TimeSheetController::class, 'getActivitiesByProject'])
        ->name('timesheet.get-activities-by-project');
});
