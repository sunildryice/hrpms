<?php

use Modules\Report\Controllers\AssetDispositionController;
use Modules\Report\Controllers\Admin\TravelRequestController;
use Modules\Report\Controllers\AssetDispositionReportController;
use Modules\Report\Controllers\Finance\FundRequestController;
use Modules\Report\Controllers\Finance\PaymentSheetController;
use Modules\Report\Controllers\Admin\VehicleMovementController;
use Modules\Report\Controllers\Finance\AdvanceRequestController;
use Modules\Report\Controllers\Admin\ConstructionReportController;
use Modules\Report\Controllers\LogisticsProcurement\GrnController;
use Modules\Report\Controllers\Finance\MonthlyFundRequestController;
use Modules\Report\Controllers\HumanResources\LeaveRequestController;
use Modules\Report\Controllers\HumanResources\LeaveSummaryController;
use Modules\Report\Controllers\HumanResources\EmployeeProfileController;
use Modules\Report\Controllers\HumanResources\TrainingRequestController;
use Modules\Report\Controllers\LogisticsProcurement\AssetBookController;
use Modules\Report\Controllers\LogisticsProcurement\StockBookController;
use Modules\Report\Controllers\Finance\ConsolidatedFundRequestController;
use Modules\Report\Controllers\HumanResources\ConsultantProfileController;
use Modules\Report\Controllers\HumanResources\EmployeeInsuranceController;
use Modules\Report\Controllers\HumanResources\PerformanceReviewController;
use Modules\Report\Controllers\HumanResources\EmployeeRequisitionController;
use Modules\Report\Controllers\LogisticsProcurement\PurchaseOrderController;
use Modules\Report\Controllers\HumanResources\EmployeeExitClearanceController;
use Modules\Report\Controllers\HumanResources\EmployeeExitInterviewController;
use Modules\Report\Controllers\LogisticsProcurement\PurchaseRequestController;
use Modules\Report\Controllers\LogisticsProcurement\MaintenanceRequestController;
use Modules\Report\Controllers\LogisticsProcurement\StockBookOfficeUseController;
use Modules\Report\Controllers\LogisticsProcurement\StockBookDistributionController;

Route::middleware(['web', 'auth', 'logger'])->prefix('report')->as('report.')->group(function () {

    // Purchase Request
    Route::any('purchase/request/index', [PurchaseRequestController::class, 'index'])->name('purchase.request.index');
    Route::any('purchase/request/export', [PurchaseRequestController::class, 'export'])->name('purchase.request.export');

    // Purchase Order
    Route::any('purchase/order/index', [PurchaseOrderController::class, 'index'])->name('purchase.order.index');
    Route::any('purchase/order/export', [PurchaseOrderController::class, 'export'])->name('purchase.order.export');

    // GRN
    Route::any('grn/index', [GrnController::class, 'index'])->name('grn.index');
    Route::any('grn/export', [GrnController::class, 'export'])->name('grn.export');

    // Employee Profile
    Route::any('employee/profile/index', [EmployeeProfileController::class, 'index'])->name('employee.profile.index');
    Route::any('employee/profile/export', [EmployeeProfileController::class, 'export'])->name('employee.profile.export');

    // Consultant Profile
    Route::any('consultant/profile/index', [ConsultantProfileController::class, 'index'])->name('consultant.profile.index');
    Route::any('consultant/profile/export', [ConsultantProfileController::class, 'export'])->name('consultant.profile.export');

    // Employee Insurance
    Route::any('employee/family/detail/index', [EmployeeInsuranceController::class, 'index'])->name('employee.insurance.index');
    Route::any('employee/family/detail/export', [EmployeeInsuranceController::class, 'export'])->name('employee.insurance.export');

    // Employee Requisition
    Route::any('employee/requisition/index', [EmployeeRequisitionController::class, 'index'])->name('employee.requisition.index');
    Route::any('employee/requisition/export', [EmployeeRequisitionController::class, 'export'])->name('employee.requisition.export');

    // Travel Request
    Route::any('travel/request/index', [TravelRequestController::class, 'index'])->name('travel.request.index');
    Route::any('travel/request/export', [TravelRequestController::class, 'export'])->name('travel.request.export');

    // Training Request
    Route::any('training/request/index', [TrainingRequestController::class, 'index'])->name('training.request.index');
    Route::any('training/request/export', [TrainingRequestController::class, 'export'])->name('training.request.export');

    // Maintenance Request
    Route::any('maintenance/request/index', [MaintenanceRequestController::class, 'index'])->name('maintenance.request.index');
    Route::any('maintenance/request/export', [MaintenanceRequestController::class, 'export'])->name('maintenance.request.export');

    // Payment Sheet
    Route::any('payment/sheet/index', [PaymentSheetController::class, 'index'])->name('payment.sheet.index');
    Route::any('payment/sheet/export', [PaymentSheetController::class, 'export'])->name('payment.sheet.export');

    // Fund Request
    Route::any('fund/request/index', [FundRequestController::class, 'index'])->name('fund.request.index');
    Route::any('fund/request/export', [FundRequestController::class, 'export'])->name('fund.request.export');

    // Consolidated Fund Request
    Route::any('consolidated/fund/request/index', [ConsolidatedFundRequestController::class, 'index'])->name('consolidated.fund.request.index');
    Route::any('consolidated/fund/request/export', [ConsolidatedFundRequestController::class, 'export'])->name('consolidated.fund.request.export');
    Route::any('consolidated/fund/request/print', [ConsolidatedFundRequestController::class, 'print'])->name('consolidated.fund.request.print');

    // Monthly Fund Request
    Route::any('monthly/fund/request/index', [MonthlyFundRequestController::class, 'index'])->name('monthly.fund.request.index');
    Route::any('monthly/fund/request/export', [MonthlyFundRequestController::class, 'export'])->name('monthly.fund.request.export');

    // Advance Request (& Settlement)
    Route::any('advance/request/index', [AdvanceRequestController::class, 'index'])->name('advance.request.index');
    Route::any('advance/request/export', [AdvanceRequestController::class, 'export'])->name('advance.request.export');

    // Vehicle Movement
    Route::any('vehicle/movement/index', [VehicleMovementController::class, 'index'])->name('vehicle.movement.index');
    Route::any('vehicle/movement/export', [VehicleMovementController::class, 'export'])->name('vehicle.movement.export');

    // Leave Request report
    Route::any('leave/requests', [LeaveRequestController::class, 'index'])->name('leave.requests.index');
    Route::any('leave/requests/export', [LeaveRequestController::class, 'export'])->name('leave.requests.export');

    // Leave Summary report
    Route::any('leave/summary/index', [LeaveSummaryController::class, 'index'])->name('leave.summary.index');
    Route::any('leave/summary/export', [LeaveSummaryController::class, 'export'])->name('leave.summary.export');

    // Employee Exit Interview
    Route::any('employee/exit/interview/index', [EmployeeExitInterviewController::class, 'index'])->name('employee.exit.interview.index');
    Route::any('employee/exit/interview/export', [EmployeeExitInterviewController::class, 'export'])->name('employee.exit.interview.export');

    // Employee Exit Clearance
    Route::any('employee/exit/clearance/index', [EmployeeExitClearanceController::class, 'index'])->name('employee.exit.clearance.index');
    Route::any('employee/exit/clearance/export', [EmployeeExitClearanceController::class, 'export'])->name('employee.exit.clearance.export');

    // Performance Review
    Route::any('performance/review/index', [PerformanceReviewController::class, 'index'])->name('performance.review.index');
    Route::any('performance/review/export', [PerformanceReviewController::class, 'export'])->name('performance.review.export');

    // Stock Book
    Route::any('stock/book/index', [StockBookController::class, 'index'])->name('stock.book.index');
    Route::any('stock/book/export', [StockBookController::class, 'export'])->name('stock.book.export');

    // Stock Book Office Use
    Route::any('stock/book/office/use/index', [StockBookOfficeUseController::class, 'index'])->name('stock.book.office.use.index');
    Route::any('stock/book/office/use/export', [StockBookOfficeUseController::class, 'export'])->name('stock.book.office.use.export');

    // Stock Book distribution Use
    Route::any('stock/book/distribution/index', [StockBookDistributionController::class, 'index'])->name('stock.book.distribution.index');
    Route::any('stock/book/distribution/export', [StockBookDistributionController::class, 'export'])->name('stock.book.distribution.export');

    // Asset Book
    Route::any('asset/book/index', [AssetBookController::class, 'index'])->name('asset.book.index');
    Route::any('asset/book/export', [AssetBookController::class, 'export'])->name('asset.book.export');

    //Aseet Disposition
    Route::any('asset/disposition/index',  [AssetBookController::class, 'dispositionIndex'])->name('asset.disposition.index');
    Route::any('asset/disposition/export', [AssetBookController::class, 'dispositionExport'])->name('asset.disposition.export');

    // Construction
    Route::any('construction/index', [ConstructionReportController::class, 'index'])->name('construction.index');
    Route::any('construction/export', [ConstructionReportController::class, 'export'])->name('construction.export');
});
