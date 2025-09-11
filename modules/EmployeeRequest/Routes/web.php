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

use Modules\EmployeeRequest\Controllers\EmployeeRequestController;
use Modules\EmployeeRequest\Controllers\ApproveController;
use Modules\EmployeeRequest\Controllers\ApprovedController;
use Modules\EmployeeRequest\Controllers\ReviewController;

Route::middleware(['web', 'auth', 'logger'])->group(function () {
    Route::middleware('can:employee-requisition')->group(function () {
        Route::get('employee/requests', [EmployeeRequestController::class, 'index'])->name('employee.requests.index');
        Route::get('employee/requests/create', [EmployeeRequestController::class, 'create'])->name('employee.requests.create');
        Route::post('employee/requests', [EmployeeRequestController::class, 'store'])->name('employee.requests.store');
        Route::get('employee/requests/{employeeRequest}/show', [EmployeeRequestController::class, 'show'])->name('employee.requests.show');
        Route::get('employee/requests/{employeeRequest}/edit', [EmployeeRequestController::class, 'edit'])->name('employee.requests.edit');
        Route::put('employee/requests/{employeeRequest}', [EmployeeRequestController::class, 'update'])->name('employee.requests.update');
        Route::delete('employee/requests/{employeeRequest}/destroy', [EmployeeRequestController::class, 'destroy'])->name('employee.requests.destroy');
    });

    Route::middleware('can:review-employee-requisition')->group(function () {
        Route::get('review/employee/requests', [ReviewController::class, 'index'])->name('review.employee.requests.index');
        Route::get('review/employee/requests/{employeeRequest}/create', [ReviewController::class, 'create'])->name('review.employee.requests.create');
        Route::post('review/employee/requests/{employeeRequest}', [ReviewController::class, 'store'])->name('review.employee.requests.store');
    });

    Route::group([], function() {
        Route::get('approve/employee/requests', [ApproveController::class, 'index'])->name('approve.employee.requests.index');
        Route::get('approve/employee/requests/{employeeRequest}/create', [ApproveController::class, 'create'])->name('approve.employee.requests.create');
        Route::post('approve/employee/requests/{employeeRequest}', [ApproveController::class, 'store'])->name('approve.employee.requests.store');
    });

    Route::middleware('can:view-approved-employee-requisition')->group(function () {
        Route::get('approved/employee/requests', [ApprovedController::class, 'index'])->name('approved.employee.requests.index');
        Route::get('approved/employee/requests/{employeeRequest}/show', [ApprovedController::class, 'show'])->name('approved.employee.requests.show');
    });

    Route::get('approved/employee/requests/{employeeRequest}/print', [ApprovedController::class, 'print'])->name('approved.employee.requests.print');
});
