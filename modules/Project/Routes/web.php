<?php

use Illuminate\Support\Facades\Route;
use Modules\Project\Controllers\ProjectController;
use Modules\Project\Controllers\WorkPlanController;
use Modules\Project\Controllers\TimeSheetController;
use Modules\Project\Controllers\WeeklyPlanController;
use Modules\Project\Controllers\ActivityStageController;
use Modules\Project\Controllers\ProjectMembersController;
use Modules\Project\Controllers\WorkPlanDetailController;
use Modules\Project\Controllers\ProjectActivityController;
use Modules\Project\Controllers\EmployeeWorkPlanController;
use Modules\Project\Controllers\MonthlyTimeSheetController;
use Modules\Project\Controllers\ProjectGanttChartController;
use Modules\Project\Controllers\ActivityUpdatePeriodController;
use Modules\Project\Controllers\ProjectActivityExportController;
use Modules\Project\Controllers\ProjectActivityImportController;
use Modules\Project\Controllers\MonthlyTimeSheetApprovedController;
use Modules\Project\Controllers\MonthlyTimeSheetApproverController;
use Modules\Project\Controllers\ProjectActivityExtensionController;
use Modules\Project\Controllers\ProjectActivityTimeSheetController;

Route::middleware(['web', 'auth', 'logger'])->group(function () {
    Route::get('/projects', [ProjectController::class, 'index'])->name('project.index');
    Route::get('/projects/create', [ProjectController::class, 'create'])->name('project.create');
    Route::post('/projects/store', [ProjectController::class, 'store'])->name('project.store');
    Route::get('/projects/{id}/edit', [ProjectController::class, 'edit'])->name('project.edit');
    Route::get('/projects/{id}/show', [ProjectController::class, 'show'])->name('project.show');
    Route::get('/projects/{id}/dashboard', [ProjectController::class, 'dashboard'])->name('project.dashboard');
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
    Route::get('/project-activity/export/{project}/activities', [ProjectActivityExportController::class, 'exportActivity'])->name('project-activity.export.activities');

    Route::get('/project-activity/{project}', [ProjectActivityController::class, 'index'])->name('project-activity.index');
    Route::get('/project-activity/{project}/create', [ProjectActivityController::class, 'create'])->name('project-activity.create');
    Route::post('/project-activity/{project}/store', [ProjectActivityController::class, 'store'])->name('project-activity.store');
    Route::get('/project-activity/{projectActivity}/edit', [ProjectActivityController::class, 'edit'])->name('project-activity.edit');
    Route::post('/project-activity/{projectActivity}/update', [ProjectActivityController::class, 'update'])->name('project-activity.update');
    Route::get('/project-activity/{projectActivity}/show', [ProjectActivityController::class, 'show'])->name('project-activity.show');
    Route::delete('/project-activity/{projectActivity}/delete', [ProjectActivityController::class, 'destroy'])->name('project-activity.destroy');

    Route::post('/project-activity/{projectActivity}/status', [ProjectActivityController::class, 'updateStatus'])->name('project-activity.status.update');

    Route::get('/project-activity/{projectActivity}/extension/create', [ProjectActivityExtensionController::class, 'create'])->name('project-activity.extension.create');
    Route::post('/project-activity/{projectActivity}/extension/store', [ProjectActivityExtensionController::class, 'store'])->name('project-activity.extension.store');

    Route::get('/projects/{id}/gantt', [ProjectGanttChartController::class, 'index'])->name('project.gantt.index');

    Route::get('/project-activity/{projectActivity}/timesheet/data', [ProjectActivityTimeSheetController::class, 'index'])->name('project-activity-timesheet.index');
    Route::get('/project-activity/{projectActivity}/timesheet/create', [ProjectActivityTimeSheetController::class, 'create'])->name('project-activity.timesheet.create');
    Route::get('/project-activity/timesheet/{timesheet}/edit', [ProjectActivityTimeSheetController::class, 'edit'])->name('project-activity.timesheet.edit');
    Route::post('/project-activity/{projectActivity}/timesheet/store', [ProjectActivityTimeSheetController::class, 'store'])->name('project-activity.timesheet.store');
    Route::put('/project-activity/timesheet/{timesheet}/update', [ProjectActivityTimeSheetController::class, 'update'])->name('project-activity.timesheet.update');
    Route::delete('/project-activity/timesheet/{timesheet}/delete', [ProjectActivityTimeSheetController::class, 'destroy'])->name('project-activity-timesheet.destroy');

    Route::get('/timesheet/index', [TimeSheetController::class, 'index'])->name('timesheet.index');
    Route::get('/timesheet/create', [TimeSheetController::class, 'create'])->name('timesheet.create');
    Route::post('/timesheet/store', [TimeSheetController::class, 'store'])->name('timesheet.store');
    Route::get('/timesheet/{timesheet}/edit', [TimeSheetController::class, 'edit'])->name('timesheet.edit');
    Route::get('/timesheet/{timesheet}/show', [TimeSheetController::class, 'show'])->name('timesheet.show');
    Route::put('/timesheet/{timesheet}/update', [TimeSheetController::class, 'update'])->name('timesheet.update');
    Route::delete('/timesheet/{timesheet}/delete', [TimeSheetController::class, 'destroy'])->name('timesheet.destroy');

    Route::get('/monthly-timesheet/index', [MonthlyTimeSheetController::class, 'index'])->name('monthly-timesheet.index');
    Route::get('/monthly-timesheet/{id}/show', [MonthlyTimeSheetController::class, 'show'])->name('monthly-timesheet.show');
    Route::put('/monthly-timesheet/{id}', [MonthlyTimeSheetController::class, 'update'])->name('monthly-timesheet.update');



    Route::get('/work-plan', [WorkPlanController::class, 'index'])->name('work-plan.index');
    Route::post('/work-plan/store', [WorkPlanDetailController::class, 'store'])->name('work-plan.store');
    Route::get('/work-plan/create', [WorkPlanDetailController::class, 'create'])->name('work-plan.create');
    Route::get('/work-plan/{id}/edit', [WorkPlanDetailController::class, 'edit'])->name('work-plan.edit');
    Route::put('/work-plan/{id}/update', [WorkPlanDetailController::class, 'update'])->name('work-plan.update');
    Route::put('/work-plan/{id}/update-status', [WorkPlanDetailController::class, 'updateStatus'])->name('work-plan.update-status');
    Route::delete('/work-plan/{id}/delete', [WorkPlanDetailController::class, 'destroy'])->name('work-plan.destroy');
    Route::get('/work-plan/get-activities', [WorkPlanDetailController::class, 'getActivities'])->name('work-plan.get-activities');
    Route::get('/work-plan/{workPlan}/details', [WorkPlanDetailController::class, 'index'])->name('work-plan.details');


    Route::get('/employee-work-plan', [EmployeeWorkPlanController::class, 'index'])->name('employee-work-plan.index');
});

Route::middleware(['web', 'auth', 'logger'])->group(function () {
    Route::get('approve/monthly-timesheet', [MonthlyTimeSheetApproverController::class, 'index'])->name('approve.monthly-timesheet.index');
    Route::get('approve/monthly-timesheet/{id}/create', [MonthlyTimeSheetApproverController::class, 'create'])->name('approve.monthly-timesheet.create');
    Route::post('approve/monthly-timesheet/{id}', [MonthlyTimeSheetApproverController::class, 'store'])->name('approve.monthly-timesheet.store');
});

Route::middleware(['web', 'auth', 'logger'])->group(function () {
    Route::get('approved/monthly-timesheet', [MonthlyTimeSheetApprovedController::class, 'index'])->name('approved.monthly-timesheet.index');
    Route::get('approved/monthly-timesheet/{id}/show', [MonthlyTimeSheetApprovedController::class, 'show'])->name('approved.monthly-timesheet.show');
});
