<?php

/*
|--------------------------------------------------------------------------
| Application Routes for User Module
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is ordered.
|
 */

use Modules\EmployeeExit\Controllers\EmployeeExitController;
use Modules\EmployeeExit\Controllers\ExitInterviewController;
use Modules\EmployeeExit\Controllers\ExitHandOverNoteController;
use Modules\EmployeeExit\Controllers\ExitAssetHandOverController;
use Modules\EmployeeExit\Controllers\PendingEmployeeExitController;
use Modules\EmployeeExit\Controllers\EmployeeExitPayablesController;
use Modules\EmployeeExit\Controllers\ExitInterviewApproveController;
use Modules\EmployeeExit\Controllers\ExitInterviewApprovedController;
use Modules\EmployeeExit\Controllers\ExitHandOverNoteApproveController;
use Modules\EmployeeExit\Controllers\ExitHandOverNoteProjectController;
use Modules\EmployeeExit\Controllers\ExitAssetHandoverApproveController;
use Modules\EmployeeExit\Controllers\ExitHandOverNoteActivityController;
use Modules\EmployeeExit\Controllers\ExitHandOverNoteApprovedController;
use Modules\EmployeeExit\Controllers\ExitHandOverNoteDocumentController;
use Modules\EmployeeExit\Controllers\ExitAssetHandoverApprovedController;
use Modules\EmployeeExit\Controllers\EmployeeExitApprovePayablesController;
use Modules\EmployeeExit\Controllers\EmployeeExitApprovedPayablesController;

Route::middleware(['web', 'auth', 'logger'])->group(function () {

    Route::middleware('can:manage-employee-exit')->group(function () {
        Route::get('employee/exit', [EmployeeExitController::class, 'index'])->name('employee.exits.index');
        Route::get('employee/exit/create', [EmployeeExitController::class, 'create'])->name('employee.exits.create');
        Route::post('employee/exit', [EmployeeExitController::class, 'store'])->name('employee.exits.store');
        Route::get('employee/exit/{handoverNote}/edit', [EmployeeExitController::class, 'edit'])->name('employee.exits.edit');
        Route::put('employee/exit/{handoverNote}', [EmployeeExitController::class, 'update'])->name('employee.exits.update');
        Route::delete('employee/exit/{handoverNote}/destroy', [EmployeeExitController::class, 'destroy'])->name('employee.exits.destroy');
    });
    Route::get('employee/exit/{handoverNote}/print', [EmployeeExitController::class, 'print'])->name('employee.exits.print');

    Route::get('exit/employee/handover/note/edit', [ExitHandOverNoteController::class, 'edit'])->name('exit.employee.handover.note.edit');
    Route::put('exit/employee/handover/note/{handoverNote}', [ExitHandOverNoteController::class, 'update'])->name('exit.employee.handover.note.update');
    Route::get('exit/employee/handover/note/show', [ExitHandOverNoteController::class, 'show'])->name('exit.employee.handover.note.show');
    Route::get('exit/employee/handover/note/{handoverNote}/view', [ExitHandOverNoteController::class, 'show'])->name('exit.employee.handover.note.view');

    Route::get('exit/employee/handover/asset/edit', [ExitAssetHandOverController::class, 'edit'])->name('exit.employee.handover.asset.edit');
    Route::put('exit/employee/handover/asset/{handover}', [ExitAssetHandOverController::class, 'update'])->name('exit.employee.handover.asset.update');
    Route::get('exit/employee/handover/asset/show', [ExitAssetHandOverController::class, 'show'])->name('exit.employee.handover.asset.show');
    Route::get('exit/employee/handover/asset/{handover}/view', [ExitAssetHandOverController::class, 'show'])->name('exit.employee.handover.asset.view');
    Route::get('exit/employee/handover/asset/print/{handover}', [ExitAssetHandOverController::class, 'print'])->name('exit.employee.handover.asset.print');

    Route::get('exit/employee/interview/edit', [ExitInterviewController::class, 'edit'])->name('exit.employee.interview.edit');
    Route::put('exit/employee/{employeeId}/interview', [ExitInterviewController::class, 'update'])->name('exit.employee.interview.update');

    Route::get('exit/employee/interview/show', [ExitInterviewController::class, 'show'])->name('exit.employee.interview.show');
    Route::get('exit/employee/interview/print/{interview}', [ExitInterviewController::class, 'print'])->name('exit.employee.interview.print');

    Route::get('exitemployee/project/project/{handovernote}', [ExitHandOverNoteProjectController::class, 'index'])->name('exit.handover.note.index');
    Route::get('exitemployee/project/{handovernote}/create', [ExitHandOverNoteProjectController::class, 'create'])->name('project.exit.handover.note.create');
    Route::post('exitemployee/project/{handovernote}', [ExitHandOverNoteProjectController::class, 'store'])->name('project.exit.handover.note.store');
    Route::get('exitemployee/{handovernote}/project/{project}/edit/', [ExitHandOverNoteProjectController::class, 'edit'])->name('project.exit.handover.note.edit');
    Route::put('exitemployee/{handovernote}/project/{project}/', [ExitHandOverNoteProjectController::class, 'update'])->name('project.exit.handover.note.update');
    Route::delete('exitemployee/{handovernote}/project/{project}/destroy', [ExitHandOverNoteProjectController::class, 'destroy'])->name('project.exit.handover.note.destroy');

    Route::get('exitemployee/activity/activity/{handovernote}', [ExitHandOverNoteActivityController::class, 'index'])->name('activity.exit.handover.note.index');
    Route::get('exitemployee/activity/{handovernote}/create', [ExitHandOverNoteActivityController::class, 'create'])->name('exit.handover.activity.note.create');
    Route::post('exitemployee/activity/{handovernote}', [ExitHandOverNoteActivityController::class, 'store'])->name('exit.handover.activity.note.store');
    Route::get('exitemployee/{handovernote}/activity/{activity}/edit/', [ExitHandOverNoteActivityController::class, 'edit'])->name('activity.exit.handover.note.edit');
    Route::put('exitemployee/{handovernote}/activity/{activity}/', [ExitHandOverNoteActivityController::class, 'update'])->name('activity.exit.handover.note.update');
    Route::delete('exitemployee/{handovernote}/activity/{activity}/destroy', [ExitHandOverNoteActivityController::class, 'destroy'])->name('activity.exit.handover.note.destroy');

    Route::get('exitemployee/document/document/{handovernote}', [ExitHandOverNoteDocumentController::class, 'index'])->name('document.exit.handover.note.index');
    Route::get('exitemployee/document/{handovernote}/create', [ExitHandOverNoteDocumentController::class, 'create'])->name('document.exit.handover.note.create');
    Route::post('exitemployee/document/{handovernote}', [ExitHandOverNoteDocumentController::class, 'store'])->name('document.exit.handover.note.store');
    Route::get('exitemployee/{handovernote}/document/{document}/edit/', [ExitHandOverNoteDocumentController::class, 'edit'])->name('document.exit.handover.note.edit');
    Route::post('exitemployee/{handovernote}/document/{document}', [ExitHandOverNoteDocumentController::class, 'update'])->name('document.exit.handover.note.update');
    Route::delete('exitemployee/{handovernote}/document/{document}/destroy', [ExitHandOverNoteDocumentController::class, 'destroy'])->name('document.exit.handover.note.destroy');

    Route::middleware('can:approve-exit-handover-note')->group(function () {
        Route::get('approve/exit/handover/note', [ExitHandOverNoteApproveController::class, 'index'])->name('approve.exit.handover.note.index');
        Route::get('approve/exit/handover/note/{handover}/create', [ExitHandOverNoteApproveController::class, 'create'])->name('approve.exit.handover.note.create');
        Route::post('approve/exit/handover/note/{handover}', [ExitHandOverNoteApproveController::class, 'store'])->name('approve.exit.handover.note.store');

        Route::get('approved/exit/handover/note', [ExitHandOverNoteApprovedController::class, 'index'])->name('approved.exit.handover.note.index');
        Route::get('approved/exit/handover/note/{handover}/show', [ExitHandOverNoteApprovedController::class, 'show'])->name('approved.exit.handover.note.show');
        Route::get('approved/exit/handover/note/{handover}/print', [ExitHandOverNoteApprovedController::class, 'print'])->name('approved.exit.handover.note.print');
    });

    // Route::middleware(['can:approve-exit-interview','can:manage-employee-exit'])->group(function () {
    Route::get('approve/exit/interview', [ExitInterviewApproveController::class, 'index'])->name('approve.exit.interview.index');
    Route::get('approve/exit/interview/{interview}/create', [ExitInterviewApproveController::class, 'create'])->name('approve.exit.interview.create');
    Route::post('approve/exit/interview/{interview}', [ExitInterviewApproveController::class, 'store'])->name('approve.exit.interview.store');
    Route::get('approved/exit/interview', [ExitInterviewApprovedController::class, 'index'])->name('approved.exit.interview.index');
    Route::get('approved/exit/interview/{interview}/show', [ExitInterviewApprovedController::class, 'show'])->name('approved.exit.interview.show');
    // });

    // can approve-exit-asset
    Route::get('approve/exit/handover/asset',[ExitAssetHandoverApproveController::class, 'index'])->name('approve.exit.handover.asset.index');
    Route::get('approve/exit/handover/asset/{handover}/create',[ExitAssetHandoverApproveController::class, 'create'])->name('approve.exit.handover.asset.create');
    Route::post('approve/exit/handover/asset/{handover}',[ExitAssetHandoverApproveController::class, 'store'])->name('approve.exit.handover.asset.store');
    Route::get('approved/exit/handover/asset',[ExitAssetHandoverApprovedController::class, 'index'])->name('approved.exit.handover.asset.index');
    Route::get('approved/exit/handover/asset/{handover}/show',[ExitAssetHandoverApprovedController::class, 'show'])->name('approved.exit.handover.asset.show');

    Route::middleware('can:create-exit-payable')->group(function () {
        Route::get('exitemployee/payable', [EmployeeExitPayablesController::class, 'index'])->name('exit.payable.index');
        Route::get('exitemployee/payable/create', [EmployeeExitPayablesController::class, 'create'])->name('employee.payable.create');
        Route::post('exitemployee/payable', [EmployeeExitPayablesController::class, 'store'])->name('employee.payable.store');
        Route::get('exitemployee/{payableId}/payable/edit', [EmployeeExitPayablesController::class, 'edit'])->name('exit.payable.edit');
        Route::put('exitemployee/{payableId}/payable', [EmployeeExitPayablesController::class, 'update'])->name('exit.payable.update');
        Route::delete('exitemployee/{payableId}/payable/delete', [EmployeeExitPayablesController::class, 'destroy'])->name('exit.payable.delete');
    });

    Route::middleware('can:approve-exit-payable')->group(function () {
        Route::get('exitemployee/approve/payable', [EmployeeExitApprovePayablesController::class, 'index'])->name('exit.approve.payable.index');
        Route::get('exitemployee/approve/{payableId}/payable/create', [EmployeeExitApprovePayablesController::class, 'create'])->name('exit.approve.payable.create');
        Route::post('exitemployee/approve/{payableId}/payable/', [EmployeeExitApprovePayablesController::class, 'store'])->name('exit.approve.payable.store');
    });

    Route::get('exitemployee/approve/{payableId}/payable/show', [EmployeeExitPayablesController::class, 'show'])->name('exit.payable.show');

    Route::get('exitemployee/approved/payable/', [EmployeeExitApprovedPayablesController::class, 'index'])->name('exit.approved.payable.index');
    Route::get('exitemployee/approved/{payableId}/payable/show', [EmployeeExitApprovedPayablesController::class, 'show'])->name('exit.approved.payable.show');

    Route::middleware('can:manage-employee-exit')->group(function () {
        Route::get('employee/exit/pending', [PendingEmployeeExitController::class, 'index'])->name('employee.exit.pending.index');
        Route::get('employee/exit/{handoverNote}/pending/edit', [PendingEmployeeExitController::class, 'edit'])->name('employee.exit.pending.edit');
        Route::put('employee/exit/{handoverNote}/pending', [PendingEmployeeExitController::class, 'update'])->name('employee.exit.pending.update');
    });

});
