<?php

use Illuminate\Support\Facades\Route;
use Modules\Project\Controllers\ActivityUpdatePeriodController;
use Modules\Project\Controllers\ProjectController;
use Modules\Project\Controllers\ActivityStageController;
use Modules\Project\Controllers\ProjectMembersController;
use Modules\Project\Controllers\ProjectActivityController;
use Modules\Project\Controllers\ProjectActivityExportController;
use Modules\Project\Controllers\ProjectActivityImportController;

Route::middleware(['web', 'auth'])->group(function () {
    Route::get('/projects', [ProjectController::class, 'index'])->name('project.index');
    Route::get('/projects/create', [ProjectController::class, 'create'])->name('project.create');
    Route::post('/projects/store', [ProjectController::class, 'store'])->name('project.store');
    Route::get('/projects/{id}/edit', [ProjectController::class, 'edit'])->name('project.edit');
    Route::get('/projects/{id}/show', [ProjectController::class, 'show'])->name('project.show');
    Route::post('/projects/{id}/update', [ProjectController::class, 'update'])->name('project.update');
    Route::delete('/projects/{id}/delete', [ProjectController::class, 'destroy'])->name('project.destroy');


    Route::get('/activity-stages', [ActivityStageController::class, 'index'])->name('activity-stages.index');
    Route::get('/activity-stages/create', [ActivityStageController::class, 'create'])->name('activity-stage.create');
    Route::post('/activity-stages/store', [ActivityStageController::class, 'store'])->name('activity-stage.store');
    Route::get('/activity-stages/{id}/edit', [ActivityStageController::class, 'edit'])->name('activity-stage.edit');
    Route::post('/activity-stages/{id}/update', [ActivityStageController::class, 'update'])->name('activity-stage.update');
    Route::get('/activity-stages/{id}/show', [ActivityStageController::class, 'show'])->name('activity-stage.show');
    Route::delete('/activity-stages/{id}/delete', [ActivityStageController::class, 'destroy'])->name('activity-stages.destroy');

    Route::get('/activity-update-periods', [ActivityUpdatePeriodController::class, 'index'])->name('activity-update-periods.index');
    Route::get('/activity-update-periods/create', [ActivityUpdatePeriodController::class, 'create'])->name('activity-update-periods.create');
    Route::post('/activity-update-periods/store', [ActivityUpdatePeriodController::class, 'store'])->name('activity-update-periods.store');
    Route::get('/activity-update-periods/{id}/show', [ActivityUpdatePeriodController::class, 'show'])->name('activity-update-periods.show');
    Route::get('/activity-update-periods/{id}/edit', [ActivityUpdatePeriodController::class, 'edit'])->name('activity-update-periods.edit');
    Route::post('/activity-update-periods/{id}/update', [ActivityUpdatePeriodController::class, 'update'])->name('activity-update-periods.update');
    Route::delete('/activity-update-periods/{id}/delete', [ActivityUpdatePeriodController::class, 'destroy'])->name('activity-update-periods.destroy');

    Route::get('/project-activity/import/{project}', [ProjectActivityImportController::class, 'create'])->name('project-activity.import.create');
    Route::post('/project-activity/import/{project}/store', [ProjectActivityImportController::class, 'store'])->name('project-activity.import.store');

    Route::get('/project-activity/export/{project}', [ProjectActivityExportController::class, 'export'])->name('project-activity.export');

    Route::get('/project-activity/{project}', [ProjectActivityController::class, 'index'])->name('project-activity.index');
    Route::get('/project-activity/{project}/create', [ProjectActivityController::class, 'create'])->name('project-activity.create');
    Route::post('/project-activity/{project}/store', [ProjectActivityController::class, 'store'])->name('project-activity.store');
    Route::get('/project-activity/{projectActivity}/edit', [ProjectActivityController::class, 'edit'])->name('project-activity.edit');
    Route::post('/project-activity/{projectActivity}/update', [ProjectActivityController::class, 'update'])->name('project-activity.update');
    Route::get('/project-activity/{projectActivity}/show', [ProjectActivityController::class, 'show'])->name('project-activity.show');
    Route::delete('/project-activity/{projectActivity}/delete', [ProjectActivityController::class, 'destroy'])->name('project-activity.destroy');
});
