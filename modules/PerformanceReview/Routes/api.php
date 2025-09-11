<?php

use Modules\PerformanceReview\Controllers\Api\PerformanceReviewKeyGoalController;

/*
|--------------------------------------------------------------------------
| Application Routes for Lta Module
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
 */

Route::middleware(['api', 'logger'])->prefix('api/performance')->group(function () {
    Route::get('/{performance}/keygoals', [PerformanceReviewKeyGoalController::class, 'getKeyGoals']);
});
