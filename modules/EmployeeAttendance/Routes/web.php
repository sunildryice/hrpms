<?php

/*
|--------------------------------------------------------------------------
| Application Routes for EmployeeAttendance Module
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is ordered.
|
*/

use Modules\EmployeeAttendance\Controllers\ReviewController;
use Modules\EmployeeAttendance\Controllers\ApproveController;
use Modules\EmployeeAttendance\Controllers\PendingController;
use Modules\EmployeeAttendance\Controllers\ApprovedController;
use Modules\EmployeeAttendance\Controllers\AttendanceController;
use Modules\EmployeeAttendance\Controllers\DailyAttendanceController;
use Modules\EmployeeAttendance\Controllers\AttendanceDetailController;
use Modules\EmployeeAttendance\Controllers\AttendanceReviewController;
use Modules\EmployeeAttendance\Controllers\AttendanceDetailDonorController;

Route::middleware(['web', 'auth', 'logger'])->prefix('attendance')->as('attendance.')->group(function () {

    Route::post('submit', [AttendanceController::class, 'submit'])->name('submit');

    Route::get('', [AttendanceController::class, 'index'])->name('index');
    Route::get('create', [AttendanceController::class, 'create'])->name('create');
    Route::post('', [AttendanceController::class, 'store'])->name('store');
    Route::get('{employeeId}/show', [AttendanceController::class, 'show'])->name('show');
    Route::get('{employeeId}/view', [AttendanceController::class, 'view'])->name('view');
    Route::get('{attendanceId}/edit', [AttendanceController::class, 'edit'])->name('edit');
    Route::put('{attendanceId}', [AttendanceController::class, 'update'])->name('update');
    Route::post('{attendanceId}/amend', [AttendanceController::class, 'amend'])->name('amend');

    Route::post('/attendance/checkin/today', [AttendanceController::class, 'checkInToday'])->name('checkin.today');
    Route::post('/attendance/checkout/today', [AttendanceController::class, 'checkOutToday'])->name('checkout.today');

    Route::get('approved/index', [ApprovedController::class, 'index'])->name('approved.index');

    Route::get('pending/index', [PendingController::class, 'index'])->name('pending.index');

    Route::post('import', [AttendanceController::class, 'import'])->name('import');
});

Route::middleware(['web', 'auth', 'logger'])->group(function () {
    Route::match(['get', 'post'], '/daily-attendance', [DailyAttendanceController::class, 'index'])
        ->name('daily.attendance.index');

    Route::post('daily-attendance/update-time', [DailyAttendanceController::class, 'updateTime'])
        ->name('attendance.update.checkin.checkout');
});
Route::middleware(['web', 'auth', 'logger'])
    ->prefix('attendance/detail')->as('attendance.detail.')->group(function () {
        Route::get('', [AttendanceDetailController::class, 'index'])->name('index');
        Route::get('create', [AttendanceDetailController::class, 'create'])->name('create');
        Route::post('', [AttendanceDetailController::class, 'store'])->name('store');
        Route::get('{attendenceId}/show', [AttendanceDetailController::class, 'show'])->name('show');
        Route::get('{attendenceId}/view', [AttendanceDetailController::class, 'view'])->name('view');
        Route::get('{attendanceId}/edit', [AttendanceDetailController::class, 'edit'])->name('edit');
        Route::get('{attendanceId}/recalculate', [AttendanceDetailController::class, 'recalculate'])->name('recalculate');
        // Route::get('{attendanceDetailId}/edit', [AttendanceDetailController::class, 'edit'])->name('edit');
        Route::get('{attendanceDetailId}', [AttendanceDetailController::class, 'update'])->name('update');
        Route::delete('{attendanceDetailId}', [AttendanceDetailController::class, 'destroy'])->name('delete');

        Route::get('{attendenceId}/print', [AttendanceDetailController::class, 'print'])->name('print');

        Route::get('{attendanceId}/worklogs', [AttendanceDetailDonorController::class, 'index'])->name('worklogs');
        Route::any('{attendanceId}/worklogs/print', [AttendanceDetailDonorController::class, 'print'])->name('worklogs.print');
        Route::get('{attendanceId}/donor/{donor}/create', [AttendanceDetailDonorController::class, 'create'])->name('donor.create');
        // Route::post('{attendenceId}/donor/{donor}/store', [AttendanceDetailDonorController::class, 'store'])->name('donor.store');
    });


Route::middleware(['web', 'auth', 'logger'])->prefix('attendance')->as('attendance.')->group(function () {

    Route::get('review', [ReviewController::class, 'index'])->name('review.index');
    Route::get('{attendanceId}/review/create', [ReviewController::class, 'create'])->name('review.create');
    Route::post('{attendanceId}/review/store', [ReviewController::class, 'store'])->name('review.store');
    Route::get('{attendanceId}/review/view', [ReviewController::class, 'view'])->name('review.view');

    Route::get('approve', [ApproveController::class, 'index'])->name('approve.index');
    Route::get('{attendanceId}/approve/create', [ApproveController::class, 'create'])->name('approve.create');
    Route::post('{attendanceId}/approve/store', [ApproveController::class, 'store'])->name('approve.store');
    Route::get('{attendanceId}/approve/view', [ApproveController::class, 'view'])->name('approve.view');

});
