<?php

/*
|--------------------------------------------------------------------------
| Application Routes for User Module
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
 */

use Modules\LeaveRequest\Controllers\PaymentController;
use Modules\LeaveRequest\Controllers\LeaveEncashController;
use Modules\LeaveRequest\Controllers\LeaveRequestController;
use Modules\LeaveRequest\Controllers\ReviewLeaveEncashController;
use Modules\LeaveRequest\Controllers\ApproveLeaveEncashController;
use Modules\LeaveRequest\Controllers\ReviewLeaveRequestController;
use Modules\LeaveRequest\Controllers\ApprovedLeaveEncashController;
use Modules\LeaveRequest\Controllers\ApproveLeaveRequestController;
use Modules\LeaveRequest\Controllers\ApprovedLeaveRequestController;
use Modules\LeaveRequest\Controllers\HrApproveLeaveRequestController;

//Route::middleware(['web', 'auth', 'logger', 'can:manage-employee'])->group(function () {
Route::middleware(['web', 'auth', 'logger'])->group(function () {
    Route::middleware('can:leave-request')->group(function () {
        Route::get('leave/requests', [LeaveRequestController::class, 'index'])->name('leave.requests.index');
        Route::get('leave/requests/create', [LeaveRequestController::class, 'create'])->name('leave.requests.create');
        Route::post('leave/requests', [LeaveRequestController::class, 'store'])->name('leave.requests.store');
        Route::get('leave/requests/{leave}/show', [LeaveRequestController::class, 'show'])->name('leave.requests.show');
        Route::get('leave/requests/{leave}/edit', [LeaveRequestController::class, 'edit'])->name('leave.requests.edit');
        Route::put('leave/requests/{leave}', [LeaveRequestController::class, 'update'])->name('leave.requests.update');
        Route::delete('leave/requests/{leave}/destroy', [LeaveRequestController::class, 'destroy'])->name('leave.requests.destroy');
        Route::post('leave/requests/{leave}/amend', [LeaveRequestController::class, 'amend'])->name('leave.requests.amend.store');

        Route::get('approved/leave/requests', [ApprovedLeaveRequestController::class, 'index'])->name('approved.leave.requests.index');
    });
    Route::get('leave/requests/{leave}/detail', [LeaveRequestController::class, 'detail'])->name('leave.requests.detail');
    Route::get('leave/requests/{leave}/print', [LeaveRequestController::class, 'printLeave'])->name('leave.requests.print');

    // HR review leave request
    Route::middleware('can:review-leave-request')->group(function () {
        Route::get('review/leave/requests', [ReviewLeaveRequestController::class, 'index'])->name('review.leave.requests.index');
        Route::get('review/leave/requests/{leave}/create', [ReviewLeaveRequestController::class, 'create'])->name('review.leave.requests.create');
        Route::post('review/leave/requests/{leave}', [ReviewLeaveRequestController::class, 'store'])->name('review.leave.requests.store');
    });

    Route::middleware('can:approve-leave-request')->group(function () {
        Route::get('approve/leave/requests', [ApproveLeaveRequestController::class, 'index'])->name('approve.leave.requests.index');
        Route::get('approve/leave/requests/{leave}/create', [ApproveLeaveRequestController::class, 'create'])->name('approve.leave.requests.create');
        Route::post('approve/leave/requests/{leave}', [ApproveLeaveRequestController::class, 'store'])->name('approve.leave.requests.store');
    });
    Route::prefix('hr')
        ->middleware('can:hr-approve-leave-request')
        ->group(function () {

            Route::get('approve/leave/requests', [HrApproveLeaveRequestController::class, 'index'])->name('hr.approve.leave.requests.index');
            Route::get('approve/leave/requests/{leave}/create', [HrApproveLeaveRequestController::class, 'create'])->name('hr.approve.leave.requests.create');
            Route::post('approve/leave/requests/{leave}', [HrApproveLeaveRequestController::class, 'store'])->name('hr.approve.leave.requests.store');
        });



    Route::middleware('can:leave-encash')->group(function () {
        Route::get('leave/encash', [LeaveEncashController::class, 'index'])->name('leave.encash.index');
        Route::get('leave/encash/create', [LeaveEncashController::class, 'create'])->name('leave.encash.create');
        Route::post('leave/encash', [LeaveEncashController::class, 'store'])->name('leave.encash.store');
        Route::get('leave/encash/{encash}/show', [LeaveEncashController::class, 'show'])->name('leave.encash.show');
        Route::get('leave/encash/{encash}/edit', [LeaveEncashController::class, 'edit'])->name('leave.encash.edit');
        Route::put('leave/encash/{encash}', [LeaveEncashController::class, 'update'])->name('leave.encash.update');
        Route::delete('leave/encash/{encash}/destroy', [LeaveEncashController::class, 'destroy'])->name('leave.encash.destroy');
        Route::get('leave/encash/{encash}/print', [LeaveEncashController::class, 'print'])->name('leave.encash.print');
        Route::get('leave/encash/{encash}/amend', [LeaveEncashController::class, 'amend'])->name('leave.encash.amend');
        Route::get('approved/leave/encash', [ApprovedLeaveEncashController::class, 'index'])->name('approved.leave.encash.index');
        Route::get('approved/leave/encash/{encash}/show', [ApprovedLeaveEncashController::class, 'show'])->name('approved.leave.encash.show');
    });

    Route::middleware('can:review-leave-encash')->group(function () {
        Route::get('review/leave/encash', [ReviewLeaveEncashController::class, 'index'])->name('review.leave.encash.index');
        Route::get('review/leave/encash/{encash}/create', [ReviewLeaveEncashController::class, 'create'])->name('review.leave.encash.create');
        Route::post('review/leave/encash/{encash}', [ReviewLeaveEncashController::class, 'store'])->name('review.leave.encash.store');
    });

    Route::middleware('can:approve-leave-encash')->group(function () {
        Route::get('approve/leave/encash', [ApproveLeaveEncashController::class, 'index'])->name('approve.leave.encash.index');
        Route::get('approve/leave/encash/{encash}/create', [ApproveLeaveEncashController::class, 'create'])->name('approve.leave.encash.create');
        Route::post('approve/leave/encash/{encash}', [ApproveLeaveEncashController::class, 'store'])->name('approve.leave.encash.store');
    });

    Route::middleware('can:pay-leave-encash')->group(function () {
        Route::get('approved/leave/encash/{encash}/pay/create', [PaymentController::class, 'create'])->name('approved.leave.encash.pay.create');
        Route::post('approved/leave/encash/{encash}/pay', [PaymentController::class, 'store'])->name('approved.leave.encash.pay.store');
        Route::get('paid/leave/encash', [PaymentController::class, 'index'])->name('paid.leave.encash.index');
        Route::get('paid/leave/encash/{encash}/show', [PaymentController::class, 'show'])->name('paid.leave.encash.show');
    });
});
