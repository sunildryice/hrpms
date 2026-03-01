<?php

use Illuminate\Support\Facades\Route;
use Modules\Project\Controllers\Api\TimeSheetController;
use Modules\Project\Controllers\Api\ProjectController;

Route::middleware(['web', 'auth'])->group(function () {
    Route::get('/timesheet/activities-by-project', [TimeSheetController::class, 'getActivitiesByProject'])
        ->name('timesheet.get-activities-by-project');

});

Route::middleware(['web', 'logger'])->prefix('api/projects')->group(function () {
    Route::get('{project}/show', [ProjectController::class, 'show'])
        ->name('api.projects.show');
});
