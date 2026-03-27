<?php

use Modules\PerformanceReview\Controllers\PerformanceReviewAnswerController;
use Modules\PerformanceReview\Controllers\PerformanceReviewApproveController;
use Modules\PerformanceReview\Controllers\PerformanceReviewAssistantController;
use Modules\PerformanceReview\Controllers\PerformanceReviewChallengeController;
use Modules\PerformanceReview\Controllers\PerformanceReviewController;
use Modules\PerformanceReview\Controllers\PerformanceReviewCoreCompetencyController;
use Modules\PerformanceReview\Controllers\PerformanceReviewKeyGoalController;
use Modules\PerformanceReview\Controllers\PerformanceReviewRecommendController;
use Modules\PerformanceReview\Controllers\PerformanceReviewReviewController;

Route::middleware(['web', 'auth', 'logger'])->group(function () {

    Route::get('performance', [PerformanceReviewController::class, 'index'])->name('performance.index');
    Route::get('performance/employee', [PerformanceReviewController::class, 'employeeIndex'])->name('performance.employee.index');
    Route::get('performance/create', [PerformanceReviewController::class, 'create'])->name('performance.create');
    Route::post('performance', [PerformanceReviewController::class, 'store'])->name('performance.store');
    Route::get('performance/{id}/edit', [PerformanceReviewController::class, 'edit'])->name('performance.edit');
    Route::put('performance/{id}', [PerformanceReviewController::class, 'update'])->name('performance.update');
    Route::get('performance/{id}/print', [PerformanceReviewController::class, 'print'])->name('performance.print');
    Route::get('performance/{id}/show', [PerformanceReviewController::class, 'show'])->name('performance.show');
    Route::get('performance/{id}/employee/show', [PerformanceReviewController::class, 'employeeShow'])->name('performance.employee.show');
    Route::any('performance/{id}/destroy', [PerformanceReviewController::class, 'destroy'])->name('performance.destroy');
    Route::get('performance/{id}/fill', [PerformanceReviewController::class, 'fill'])->name('performance.fill');
    Route::any('performance/{id}/submit', [PerformanceReviewController::class, 'submit'])->name('performance.submit');


    Route::post('performance/answer', [PerformanceReviewAnswerController::class, 'store'])->name('performance.answer.store');
    Route::post('performance/answer/all', [PerformanceReviewAnswerController::class, 'storeMany'])->name('performance.answer.store.all');
    Route::post('performance/answer/get', [PerformanceReviewAnswerController::class, 'get'])->name('performance.answer.get');


    Route::post('performance/keygoal', [PerformanceReviewKeyGoalController::class, 'store'])->name('performance.keygoal.store');
    Route::post('performance/keygoal/edit', [PerformanceReviewKeyGoalController::class, 'edit'])->name('performance.keygoal.edit');

    Route::post('performance/keygoal/update', [PerformanceReviewKeyGoalController::class, 'update'])->name('performance.keygoal.update');
    Route::get('perfromance/keygoal/{id}/editOne', [PerformanceReviewKeyGoalController::class, 'editOne'])->name('performance.keygoal.editOne');
    Route::put('performance/keygoal/{id}/updateOne', [PerformanceReviewKeyGoalController::class, 'updateOne'])->name('performance.keygoal.updateOne');
    Route::post('performance/keygoal/append', [PerformanceReviewKeyGoalController::class, 'append'])->name('performance.keygoal.append');
    Route::post('performance/keygoal/employee/get', [PerformanceReviewKeyGoalController::class, 'getKeyGoalsEmployee'])->name('performance.keygoal.employee.get');
    Route::post('performance/keygoal/supervisor/get', [PerformanceReviewKeyGoalController::class, 'getKeyGoalsSupervisor'])->name('performance.keygoal.supervisor.get');
    Route::post('performance/employee/current/keygoal/get', [PerformanceReviewKeyGoalController::class, 'getEmployeeCurrentKeyGoals'])->name('performance.employee.current.keygoal.get');
    Route::post('performance/keygoal/delete', [PerformanceReviewKeyGoalController::class, 'destroy'])->name('performance.keygoal.destroy');

    Route::post('performance/{id}/keygoals/save', [PerformanceReviewKeyGoalController::class, 'saveDraft'])->name('performance.keygoals.save-draft');
    Route::post('performance/devplan/save', [PerformanceReviewKeyGoalController::class, 'updateDevPlan'])->name('performance.devplan.update');

    Route::post('performance/challenge/store', [PerformanceReviewChallengeController::class, 'store'])->name('performance.challenge.store');
    Route::post('performance/challenge/destroy', [PerformanceReviewChallengeController::class, 'destroy'])->name('performance.challenge.destroy');

    Route::post('performance/core-competency/store', [PerformanceReviewCoreCompetencyController::class, 'store'])->name('performance.corecompetency.store');

    Route::get('performance/review', [PerformanceReviewReviewController::class, 'index'])->name('performance.review.index');
    Route::post('performance/review', [PerformanceReviewReviewController::class, 'store'])->name('performance.review.store');
    Route::any('performance/{id}/review/create', [PerformanceReviewReviewController::class, 'create'])->name('performance.review.create');


    Route::get('performance/recommend', [PerformanceReviewRecommendController::class, 'index'])->name('performance.recommend.index');
    Route::post('performance/recommend', [PerformanceReviewRecommendController::class, 'store'])->name('performance.recommend.store');
    Route::any('performance/{id}/recommend/create', [PerformanceReviewRecommendController::class, 'create'])->name('performance.recommend.create');


    Route::get('performance/approve', [PerformanceReviewApproveController::class, 'index'])->name('performance.approve.index');
    Route::post('performance/approve', [PerformanceReviewApproveController::class, 'store'])->name('performance.approve.store');
    Route::any('performance/{id}/approve/create', [PerformanceReviewApproveController::class, 'create'])->name('performance.approve.create');

    Route::get('performance/reviews/assistant', [PerformanceReviewAssistantController::class, 'index'])->name('performance.reviews.assistant.index');

    Route::get('performance/reviews/{per}/previous/show', [PerformanceReviewController::class, 'showPrevious'])->name('performance.previous.show');
});
