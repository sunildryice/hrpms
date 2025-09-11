<?php

/*
|--------------------------------------------------------------------------
| Application Routes for Work Logs Module
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

use Modules\WorkLog\Controllers\ApproveController;
use Modules\WorkLog\Controllers\ApprovedController;
use Modules\WorkLog\Controllers\AllWorkPlanController;
use Modules\WorkLog\Controllers\WorkPlanController;
use Modules\WorkLog\Controllers\WorkPlanDailyLogController;

Route::middleware(['web', 'auth', 'logger'])->group(function () {
    Route::middleware('can:view-all-work-log')->group(function () {
        Route::get('all/monthly/work/logs', [AllWorkPlanController::class, 'index'])->name('all.monthly.work.logs.index');
        Route::get('all/monthly/work/logs/{worklog}/show', [AllWorkPlanController::class, 'show'])
            ->name('all.monthly.work.logs.show');
        Route::get('all/monthly/work/logs/{log}/print', [AllWorkPlanController::class, 'print'])->name('all.monthly.work.log.print');

    });

    Route::middleware('can:work-log')->group(function () {
        Route::get('monthly/work/logs', [WorkPlanController::class, 'index'])
            ->name('monthly.work.logs.index');
        Route::get('monthly/work/logs/create', [WorkPlanController::class, 'create'])
            ->name('monthly.work.logs.create');
        Route::post('monthly/work/logs', [WorkPlanController::class, 'store'])
            ->name('monthly.work.logs.store');
        Route::get('monthly/work/logs/{worklog}/edit', [WorkPlanController::class, 'edit'])
            ->name('monthly.work.logs.edit');
        Route::put('monthly/work/logs/{worklog}/update', [WorkPlanController::class, 'update'])
            ->name('monthly.work.logs.update');
        Route::delete('monthly/work/logs/{worklog}', [WorkPlanController::class, 'destroy'])
            ->name('monthly.work.logs.destroy');

        Route::post('monthly/work/logs/{worklog}/submit', [WorkPlanController::class, 'submit'])
            ->name('monthly.work.logs.submit');

        Route::get('daily/work/logs/{worklog}', [WorkPlanDailyLogController::class, 'index'])
            ->name('daily.work.logs.index');
        Route::get('daily/work/logs/create/{worklog}', [WorkPlanDailyLogController::class, 'create'])
            ->name('daily.work.logs.create');
        Route::post('daily/work/logs/{worklog}', [WorkPlanDailyLogController::class, 'store'])
            ->name('daily.work.logs.store');
        Route::get('daily/work/logs/{dailylog}/edit', [WorkPlanDailyLogController::class, 'edit'])
            ->name('daily.work.logs.edit');
        Route::put('daily/work/logs/{dailylog}/update', [WorkPlanDailyLogController::class, 'update'])
            ->name('daily.work.logs.update');
        Route::delete('daily/work/logs/{dailylog}', [WorkPlanDailyLogController::class, 'destroy'])
            ->name('daily.work.logs.destroy');
    });
    Route::get('monthly/work/logs/{worklog}/show', [WorkPlanController::class, 'show'])
        ->name('monthly.work.logs.show');

    Route::middleware('can:approve-work-log')->group(function () {
        Route::get('approve/work/logs', [ApproveController::class, 'index'])
            ->name('approve.work.logs.index');
        Route::get('approve/work/logs/{worklog}/create', [ApproveController::class, 'create'])
            ->name('approve.work.logs.create');
        Route::post('approve/work/logs/{worklog}', [ApproveController::class, 'store'])
            ->name('approve.work.logs.store');
    });

    Route::middleware('can:view-approved-work-log')->group(function () {
        Route::get('approved/monthly/work/logs', [ApprovedController::class, 'index'])->name('approved.monthly.work.logs.index');
        Route::get('approved/monthly/work/logs/{log}/show', [ApprovedController::class, 'show'])->name('approved.monthly.work.log.show');
    });
    Route::get('monthly/work/logs/{log}/print', [ApprovedController::class, 'print'])->name('monthly.work.log.print');
});
