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

use Modules\ProbationaryReview\Controllers\ApprovedController;
use Modules\ProbationaryReview\Controllers\EmployeeProbationaryReviewController;
use Modules\ProbationaryReview\Controllers\ProbationaryReviewApprovalController;
use Modules\ProbationaryReview\Controllers\ProbationaryReviewRequestController;
use Modules\ProbationaryReview\Controllers\ReviewDetailController;


//Route::middleware(['web', 'auth', 'logger', 'can:manage-employee'])->group(function () {
Route::middleware(['web', 'auth', 'logger'])->group(function () {
    Route::middleware('can:probation-review-request')->group(function(){
        //main list from HR
        Route::get('probation/review/requests/', [ProbationaryReviewRequestController::class, 'index'])
            ->name('probation.review.requests.index');
        Route::get('probation/review/requests/list', [ProbationaryReviewRequestController::class, 'list'])
            ->name('probation.review.requests.list');
        Route::get('probation/review/requests/create', [ProbationaryReviewRequestController::class, 'create'])
            ->name('probation.review.requests.create');
        Route::post('probation/review/requests', [ProbationaryReviewRequestController::class, 'store'])
            ->name('probation.review.requests.store');
        Route::delete('probation/review/requests/{request}', [ProbationaryReviewRequestController::class, 'destroy'])
            ->name('probation.review.requests.destroy');
        Route::get('probation/review/requests/{request}/edit', [ProbationaryReviewRequestController::class, 'edit'])
            ->name('probation.review.requests.edit');
        Route::put('probation/review/requests/{request}', [ProbationaryReviewRequestController::class, 'update'])
            ->name('probation.review.requests.update');

        //for HR to send the PR to ED
        Route::get('send/probation/review/{request}/request', [ProbationaryReviewRequestController::class, 'sendTo'])
            ->name('send.probation.review.request.sendTo');
        Route::post('send/probation/review/{request}/request', [ProbationaryReviewRequestController::class, 'sendToStore'])
            ->name('send.probation.review.request.sendToStore');
    });
    Route::get('probation/review/request/{request}/view', [ProbationaryReviewRequestController::class, 'view'])
        ->name('probation.review.request.view');

    Route::middleware('can:approve-probation-review-request')->group(function(){
        //for ED for approval
        Route::get('approve/probation/review/requests/', [ProbationaryReviewApprovalController::class, 'index'])
            ->name('approve.probation.review.requests.index');
        Route::get('approve/probation/review/{request}/request', [ProbationaryReviewApprovalController::class, 'create'])
            ->name('approve.probation.review.request.create');
        Route::post('approve/probation/review/{request}/request', [ProbationaryReviewApprovalController::class, 'store'])
            ->name('approve.probation.review.request.store');
    });
    Route::middleware('can:probation-review-detail')->group(function(){
        //for supervisor for details
        Route::get('probation/review/detail/requests/', [ReviewDetailController::class, 'index'])
            ->name('probation.review.detail.requests.index');
        Route::get('probation/review/detail/requests/{request}/create', [ReviewDetailController::class, 'create'])
            ->name('probation.review.detail.requests.create');
        Route::post('probation/review/detail/{request}/requests', [ReviewDetailController::class, 'store'])
            ->name('probation.review.detail.requests.store');
        Route::get('probation/review/detail/requests/{request}/edit', [ReviewDetailController::class, 'edit'])
            ->name('probation.review.detail.requests.edit');
        Route::post('probation/review/detail/requests/{request}', [ReviewDetailController::class, 'update'])
            ->name('probation.review.detail.requests.update');

        //recommend to HR after employee add comments
        Route::get('probation/review/detail/requests/{request}/recommend', [ReviewDetailController::class, 'recommend'])
            ->name('probation.review.detail.requests.recommend');
        Route::post('probation/review/detail/requests/{request}/recommend', [ReviewDetailController::class, 'storeRecommend'])
            ->name('probation.review.detail.requests.storeRecommend');

    });
    //for Employee to add remarks
    Route::get('employeeProbation/review/detail/requests/', [EmployeeProbationaryReviewController::class, 'index'])
        ->name('employeeProbation.review.detail.requests.index');
    Route::get('employeeProbation/review/detail/requests/{request}/create', [EmployeeProbationaryReviewController::class, 'create'])
        ->name('employeeProbation.review.detail.requests.create');
    Route::post('employeeProbation/review/detail/{request}/requests', [EmployeeProbationaryReviewController::class, 'store'])
        ->name('employeeProbation.review.detail.requests.store');

    Route::get('approved/probation/reviews', [ApprovedController::class, 'index'])
        ->name('approved.probation.review.requests.index');
    Route::get('approved/probation/reviews/{review}/show', [ApprovedController::class, 'show'])
        ->name('approved.probation.review.request.show');
    Route::get('probation/reviews/{review}/print', [ApprovedController::class, 'print'])
        ->name('approved.probation.review.request.print');
});
