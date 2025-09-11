<?php

/*
|--------------------------------------------------------------------------
| Application Routes for Probationary Review Module
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

use Modules\TrainingRequest\Controllers\ApprovedTrainingRequestController;
use Modules\TrainingRequest\Controllers\ApprovedTrainingReportController;
use Modules\TrainingRequest\Controllers\TrainingReportController;
use Modules\TrainingRequest\Controllers\TrainingReportReviewController;
use Modules\TrainingRequest\Controllers\TrainingRequestApprovalController;
use Modules\TrainingRequest\Controllers\TrainingRequestController;
use Modules\TrainingRequest\Controllers\TrainingRequestRecommendController;
use Modules\TrainingRequest\Controllers\TrainingResponseController;


//Route::middleware(['web', 'auth', 'logger', 'can:manage-employee'])->group(function () {
Route::middleware(['web', 'auth', 'logger'])->group(function () {
    Route::middleware('can:training-request')->group(function(){
        //main list from HR
        Route::get('training/requests/', [TrainingRequestController::class, 'index'])
            ->name('training.requests.index');
        Route::get('training/requests/create', [TrainingRequestController::class, 'create'])
            ->name('training.requests.create');
        Route::post('training/requests', [TrainingRequestController::class, 'store'])
            ->name('training.requests.store');
        Route::delete('training/requests/{request}', [TrainingRequestController::class, 'destroy'])
            ->name('training.requests.destroy');
        Route::get('training/requests/{request}/details', [TrainingRequestController::class, 'addDetails'])
            ->name('training.requests.details');
        Route::post('training/requests/details/{request}', [TrainingRequestController::class, 'storeDetails'])
            ->name('training.requests.details.store');
        Route::get('training/requests/{request}/edit', [TrainingRequestController::class, 'edit'])
            ->name('training.requests.edit');
        Route::put('training/requests/{request}', [TrainingRequestController::class, 'update'])
            ->name('training.requests.update');
        Route::get('training/requests/{request}/view', [TrainingRequestController::class, 'view'])
            ->name('training.requests.view');
    });

    Route::middleware('can:hr-review-training-request')->group(function(){
        //for HR to fill response
        Route::get('reponses/training/request', [TrainingResponseController::class, 'index'])
            ->name('reponses.training.request.index');
        Route::get('reponses/training/{request}/request', [TrainingResponseController::class, 'create'])
            ->name('reponses.training.request.create');
        Route::post('reponses/training/{request}/request', [TrainingResponseController::class, 'store'])
            ->name('reponses.training.request.store');
    });

    Route::middleware('can:approve-training-request')->group(function(){
        //for ED for approval
        Route::get('approve/training/requests/', [TrainingRequestApprovalController::class, 'index'])
            ->name('approve.training.requests.index');
        Route::get('approve/training/{request}/request', [TrainingRequestApprovalController::class, 'create'])
            ->name('approve.training.request.create');
        Route::post('approve/training/{request}/request', [TrainingRequestApprovalController::class, 'store'])
            ->name('approve.training.request.store');
    });

    Route::middleware('can:recommend-training-request')->group(function(){
        //for supervisor for recommendation
        Route::get('training/requests/recommend', [TrainingRequestRecommendController::class, 'index'])
            ->name('training.requests.recommend.index');
        Route::get('training/requests/recommend/{request}/create', [TrainingRequestRecommendController::class, 'create'])
            ->name('training.requests.recommend.create');
        Route::post('training/requests/{request}/recommend', [TrainingRequestRecommendController::class, 'store'])
            ->name('training.requests.recommend.store');
    });

    Route::middleware('can:training-report')->group(function(){
        //Training report
        Route::get('training/report', [TrainingReportController::class, 'index'])
            ->name('training.report.index');
        Route::get('training/report/{trainingRequestId}/create', [TrainingReportController::class, 'create'])
            ->name('training.report.create');
        Route::post('training/{trainingRequestId}/report', [TrainingReportController::class, 'store'])
            ->name('training.report.store');
        Route::put('training/report/{request}', [TrainingReportController::class, 'update'])
            ->name('training.report.update');
        Route::get('training/report/{report}/view', [TrainingReportController::class, 'view'])
            ->name('training.report.view');
    });

    Route::middleware('can:approve-training-report')->group(function(){
        Route::get('approve/training/reports', [TrainingReportReviewController::class, 'index'])
            ->name('approve.training.reports.index');
        Route::get('approve/training/reports/{report}/create', [TrainingReportReviewController::class, 'create'])
            ->name('approve.training.reports.create');
        Route::post('approve/training/reports/{report}', [TrainingReportReviewController::class, 'store'])
            ->name('approve.training.reports.store');
    });

    Route::get('approved/training/requests', [ApprovedTrainingRequestController::class, 'index'])->name('approved.training.requests.index');
    Route::get('approved/training/requests/{request}/show', [ApprovedTrainingRequestController::class, 'show'])->name('approved.training.request.show');
    Route::get('training/requests/{request}/print', [ApprovedTrainingRequestController::class, 'print'])->name('training.request.print');

    Route::middleware('can:view-approved-training-report')->group(function () {
        Route::get('approved/training/reports', [ApprovedTrainingReportController::class, 'index'])->name('approved.training.reports.index');
        Route::get('approved/training/reports/{report}/show', [ApprovedTrainingReportController::class, 'show'])->name('approved.training.report.show');
        Route::get('training/reports/{report}/print', [ApprovedTrainingReportController::class, 'print'])->name('training.report.print');
    });
});
