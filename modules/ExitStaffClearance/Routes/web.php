<?php

use Modules\ExitStaffClearance\Controllers\StaffClearanceApproveController;
use Modules\ExitStaffClearance\Controllers\StaffClearanceCertifyController;
use Modules\ExitStaffClearance\Controllers\StaffClearanceController;
use Modules\ExitStaffClearance\Controllers\StaffClearanceEndorseController;
use Modules\ExitStaffClearance\Controllers\StaffClearanceRecordController;
use Modules\ExitStaffClearance\Controllers\StaffClearanceVerifyController;

Route::middleware(['web', 'auth', 'logger'])->group(function () {

    Route::get('staff/clearance', [StaffClearanceController::class, 'index'])->name('staff.clearance.index');
    Route::get('staff/clearance/{clearance}/form', [StaffClearanceController::class, 'clearanceForm'])->name('staff.clearance.form');
    Route::get('staff/clearance/{clearance}/payable', [StaffClearanceController::class, 'payableIndex'])->name('staff.clearance.payable');
    Route::get('staff/clearance/employee', [StaffClearanceController::class, 'employeeIndex'])->name('clearance.employee.index');
    Route::get('staff/clearance/{clearance}/edit', [StaffClearanceController::class, 'edit'])->name('staff.clearance.edit');
    Route::get('staff/clearance/{clearance}/print', [StaffClearanceController::class, 'print'])->name('staff.clearance.print');
    Route::get('staff/clearance/{clearance}/show', [StaffClearanceController::class, 'show'])->name('staff.clearance.show');

    Route::get('staff/clearance/{clearance}/record', [StaffClearanceRecordController::class, 'index'])->name('clearance.record.index');
    Route::post('staff/clearance/{clearance}/record', [StaffClearanceRecordController::class, 'store'])->name('clearance.record.store');
    Route::post('staff/clearance/{clearance}/record/all', [StaffClearanceRecordController::class, 'storeMany'])->name('clearance.record.store.all');
    Route::post('staff/clearance/{clearance}/record/get', [StaffClearanceRecordController::class, 'get'])->name('clearance.record.get');
    Route::delete('staff/clearance/record/{record}/destroy', [StaffClearanceRecordController::class, 'destroy'])->name('clearance.record.destroy');

    // supervisor verify
    // Route::get('staff/clearance/verify', [StaffClearanceVerifyController::class, 'index'])->name('staff.clearance.verify.index');
    // Route::get('staff/clearance/{clearance}/verify/create', [StaffClearanceVerifyController::class, 'create'])->name('staff.clearance.verify.create');
    Route::post('staff/clearance/{clearance}/verify', [StaffClearanceVerifyController::class, 'store'])->name('staff.clearance.verify.store');

    // HR verify2 (certify)
    // Route::get('staff/clearance/certify', [StaffClearanceCertifyController::class, 'index'])->name('staff.clearance.certify.index');
    Route::post('staff/clearance/{clearance}/certify', [StaffClearanceCertifyController::class, 'store'])->name('staff.clearance.certify.store');
    // Route::any('staff/clearance/{id}/certify/create', [StaffClearanceCertifyController::class, 'create'])->name('staff.clearance.certify.create');

    // PD verify3 (endorse)
    Route::middleware('can:endorse-staff-clearance')->group(function () {
        Route::get('staff/clearance/endorse', [StaffClearanceEndorseController::class, 'index'])->name('staff.clearance.endorse.index');
        Route::get('staff/clearance/{clearance}/endorse/create', [StaffClearanceEndorseController::class, 'create'])->name('staff.clearance.endorse.create');
        Route::post('staff/clearance/{clearance}/endorse', [StaffClearanceEndorseController::class, 'store'])->name('staff.clearance.endorse.store');
    });

    // AFD approve
    Route::middleware('can:approve-staff-clearance')->group(function () {
        Route::get('staff/clearance/approve', [StaffClearanceApproveController::class, 'index'])->name('staff.clearance.approve.index');
        Route::get('staff/clearance/{clearance}/approve/create', [StaffClearanceApproveController::class, 'create'])->name('staff.clearance.approve.create');
        Route::post('staff/clearance/{clearance}/approve', [StaffClearanceApproveController::class, 'store'])->name('staff.clearance.approve.store');
    });
});
