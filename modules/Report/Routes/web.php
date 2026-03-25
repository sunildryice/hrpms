<?php

use Modules\Report\Controllers\Admin\LocalTravelRequestController;
use Modules\Report\Controllers\Admin\TravelRequestController;
use Modules\Report\Controllers\Admin\VehicleMovementController;
use Modules\Report\Controllers\AssetDispositionController;
use Modules\Report\Controllers\AssetDispositionReportController;
use Modules\Report\Controllers\HumanResources\AssignedActivityController;
use Modules\Report\Controllers\HumanResources\ConsultantProfileController;
use Modules\Report\Controllers\HumanResources\EmployeeExitClearanceController;
use Modules\Report\Controllers\HumanResources\EmployeeExitInterviewController;
use Modules\Report\Controllers\HumanResources\EmployeeInsuranceController;
use Modules\Report\Controllers\HumanResources\EmployeeProfileController;
use Modules\Report\Controllers\HumanResources\EmployeeRequisitionController;
use Modules\Report\Controllers\HumanResources\LeaveRequestController;
use Modules\Report\Controllers\HumanResources\LeaveSummaryController;
use Modules\Report\Controllers\HumanResources\OffDayWorkReportController;
use Modules\Report\Controllers\HumanResources\PerformanceReviewController;
use Modules\Report\Controllers\HumanResources\ProjectSummaryController;
use Modules\Report\Controllers\HumanResources\WorkFromHomeController;
use Modules\Report\Controllers\LogisticsProcurement\AssetBookController;
use Modules\Report\Controllers\LogisticsProcurement\MaintenanceRequestController;
use Modules\Report\Controllers\LogisticsProcurement\StockBookController;
use Modules\Report\Controllers\LogisticsProcurement\StockBookDistributionController;
use Modules\Report\Controllers\LogisticsProcurement\StockBookOfficeUseController;

Route::middleware(['web', 'auth', 'logger'])->prefix('report')->as('report.')->group(function ()
{
    // Asset Book
    Route::any('asset/book/index', [AssetBookController::class, 'index'])->name('asset.book.index');
    Route::any('asset/book/export', [AssetBookController::class, 'export'])->name('asset.book.export');

    //Asset Disposition
    Route::any('asset/disposition/index', [AssetBookController::class, 'dispositionIndex'])->name('asset.disposition.index');
    Route::any('asset/disposition/export', [AssetBookController::class, 'dispositionExport'])->name('asset.disposition.export');

    // Assigned Activity Report
    Route::get('assigned/activity',        [AssignedActivityController::class, 'index'])->name('assigned.activity.index');
    Route::get('assigned/activity/export', [AssignedActivityController::class, 'export'])->name('assigned.activity.export');

    // Consultant Profile
    Route::any('consultant/profile/index', [ConsultantProfileController::class, 'index'])->name('consultant.profile.index');
    Route::any('consultant/profile/export', [ConsultantProfileController::class, 'export'])->name('consultant.profile.export');

    // Employee Exit Interview
    Route::any('employee/exit/interview/index', [EmployeeExitInterviewController::class, 'index'])->name('employee.exit.interview.index');
    Route::any('employee/exit/interview/export', [EmployeeExitInterviewController::class, 'export'])->name('employee.exit.interview.export');

    // Employee Exit Clearance
    Route::any('employee/exit/clearance/index', [EmployeeExitClearanceController::class, 'index'])->name('employee.exit.clearance.index');
    Route::any('employee/exit/clearance/export', [EmployeeExitClearanceController::class, 'export'])->name('employee.exit.clearance.export');

    // Employee Profile
    Route::any('employee/profile/index', [EmployeeProfileController::class, 'index'])->name('employee.profile.index');
    Route::any('employee/profile/export', [EmployeeProfileController::class, 'export'])->name('employee.profile.export');

    // Employee Insurance
    Route::any('employee/family/detail/index', [EmployeeInsuranceController::class, 'index'])->name('employee.insurance.index');
    Route::any('employee/family/detail/export', [EmployeeInsuranceController::class, 'export'])->name('employee.insurance.export');

    // Employee Requisition
    Route::any('employee/requisition/index', [EmployeeRequisitionController::class, 'index'])->name('employee.requisition.index');
    Route::any('employee/requisition/export', [EmployeeRequisitionController::class, 'export'])->name('employee.requisition.export');

    // Maintenance Request
    Route::any('maintenance/request/index', [MaintenanceRequestController::class, 'index'])->name('maintenance.request.index');
    Route::any('maintenance/request/export', [MaintenanceRequestController::class, 'export'])->name('maintenance.request.export');

    // Leave Request report
    Route::any('leave/requests', [LeaveRequestController::class, 'index'])->name('leave.requests.index');
    Route::any('leave/requests/export', [LeaveRequestController::class, 'export'])->name('leave.requests.export');

    // Leave Summary report
    Route::any('leave/summary/index', [LeaveSummaryController::class, 'index'])->name('leave.summary.index');
    Route::any('leave/summary/export', [LeaveSummaryController::class, 'export'])->name('leave.summary.export');

    // Local Travel Request
    Route::any('local/travel/request/index', [LocalTravelRequestController::class, 'index'])->name('local.travel.request.index');
    Route::any('local/travel/request/export', [LocalTravelRequestController::class, 'export'])->name('local.travel.request.export');

    // Off Day Work report
    Route::any('off/day/work',   [OffDayWorkReportController::class, 'index'])->name('off.day.work.index');
    Route::any('off/day/work/export', [OffDayWorkReportController::class, 'export'])->name('off.day.work.export');

    // Performance Review
    Route::any('performance/review/index', [PerformanceReviewController::class, 'index'])->name('performance.review.index');
    Route::any('performance/review/export', [PerformanceReviewController::class, 'export'])->name('performance.review.export');

    // Project Summary
    Route::any('project/summary/index', [ProjectSummaryController::class, 'index'])->name('project.summary.index');
    Route::any('project/summary/export', [ProjectSummaryController::class, 'export'])->name('project.summary.export');

    // Stock Book
    Route::any('stock/book/index', [StockBookController::class, 'index'])->name('stock.book.index');
    Route::any('stock/book/export', [StockBookController::class, 'export'])->name('stock.book.export');

    // Stock Book Office Use
    Route::any('stock/book/office/use/index', [StockBookOfficeUseController::class, 'index'])->name('stock.book.office.use.index');
    Route::any('stock/book/office/use/export', [StockBookOfficeUseController::class, 'export'])->name('stock.book.office.use.export');

    // Stock Book distribution Use
    Route::any('stock/book/distribution/index', [StockBookDistributionController::class, 'index'])->name('stock.book.distribution.index');
    Route::any('stock/book/distribution/export', [StockBookDistributionController::class, 'export'])->name('stock.book.distribution.export');

    // Travel Request
    Route::any('travel/request/index', [TravelRequestController::class, 'index'])->name('travel.request.index');
    Route::any('travel/request/export', [TravelRequestController::class, 'export'])->name('travel.request.export');

    // Vehicle Movement
    Route::any('vehicle/movement/index', [VehicleMovementController::class, 'index'])->name('vehicle.movement.index');
    Route::any('vehicle/movement/export', [VehicleMovementController::class, 'export'])->name('vehicle.movement.export');

    // Work From Home report
    Route::get('work/from/home', [WorkFromHomeController::class, 'index'])->name('work.from.home.index');
    Route::get('work/from/home/export', [WorkFromHomeController::class, 'export'])->name('work.from.home.export');
});
