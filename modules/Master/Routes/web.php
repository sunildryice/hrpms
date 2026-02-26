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

use Illuminate\Support\Facades\Route;
use Modules\Master\Controllers\AccountCodeController;
use Modules\Master\Controllers\ActivityAreaController;
use Modules\Master\Controllers\ActivityCodeController;
use Modules\Master\Controllers\ActivityCodeImportController;
use Modules\Master\Controllers\BillCategoryController;
use Modules\Master\Controllers\BrandController;
use Modules\Master\Controllers\DepartmentController;
use Modules\Master\Controllers\DesignationController;
use Modules\Master\Controllers\DistrictController;
use Modules\Master\Controllers\DonorCodeController;
use Modules\Master\Controllers\DsaCategoryController;
use Modules\Master\Controllers\ExecutionTypeController;
use Modules\Master\Controllers\ExitFeedbackController;
use Modules\Master\Controllers\ExitQuestionController;
use Modules\Master\Controllers\ExitRatingController;
use Modules\Master\Controllers\ExpenseCategoryController;
use Modules\Master\Controllers\ExpenseTypeController;
use Modules\Master\Controllers\FamilyRelationController;
use Modules\Master\Controllers\HealthFacilityController;
use Modules\Master\Controllers\HolidayController;
use Modules\Master\Controllers\InventoryCategoryController;
use Modules\Master\Controllers\ItemController;
use Modules\Master\Controllers\LeaveTypeController;
use Modules\Master\Controllers\LocalLevelController;
use Modules\Master\Controllers\MeetingHallController;
use Modules\Master\Controllers\OfficeController;
use Modules\Master\Controllers\OfficeTypeController;
use Modules\Master\Controllers\PackageController;
use Modules\Master\Controllers\PackageItemController;
use Modules\Master\Controllers\PartnerOrganizationController;
use Modules\Master\Controllers\ProbationaryIndicatorController;
use Modules\Master\Controllers\ProbationaryQuestionController;
use Modules\Master\Controllers\ProjectCodeController;
use Modules\Master\Controllers\ProvinceController;
use Modules\Master\Controllers\TrainingQuestionController;
use Modules\Master\Controllers\UnitController;
use Modules\Master\Controllers\VehicleController;

Route::middleware(['web', 'auth', 'logger'])->group(function () {
    Route::prefix('master')->group(function () {
        Route::middleware('can:manage-account-code')->group(function () {
            Route::get('account-codes', [AccountCodeController::class, 'index'])->name('master.account.codes.index');
            Route::get('account-codes/create', [AccountCodeController::class, 'create'])->name('master.account.codes.create');
            Route::post('account-codes', [AccountCodeController::class, 'store'])->name('master.account.codes.store');
            Route::get('account-codes/{accountCode}', [AccountCodeController::class, 'show'])->name('master.account.codes.show');
            Route::get('account-codes/{accountCode}/edit', [AccountCodeController::class, 'edit'])->name('master.account.codes.edit');
            Route::put('account-codes/{accountCode}', [AccountCodeController::class, 'update'])->name('master.account.codes.update');
            Route::delete('account-codes/{accountCode}', [AccountCodeController::class, 'destroy'])->name('master.account.codes.destroy');
        });

        Route::middleware('can:manage-activity-code')->group(function () {
            Route::get('activity-codes', [ActivityCodeController::class, 'index'])->name('master.activity.codes.index');
            Route::get('activity-codes/create', [ActivityCodeController::class, 'create'])->name('master.activity.codes.create');
            Route::post('activity-codes', [ActivityCodeController::class, 'store'])->name('master.activity.codes.store');
            Route::get('activity-codes/{activityCode}/edit', [ActivityCodeController::class, 'edit'])->name('master.activity.codes.edit');
            Route::put('activity-codes/{activityCode}', [ActivityCodeController::class, 'update'])->name('master.activity.codes.update');
            Route::delete('activity-codes/{activityCode}', [ActivityCodeController::class, 'destroy'])->name('master.activity.codes.destroy');

            Route::get('activity-codes/import', [ActivityCodeImportController::class, 'create'])->name('master.activity.codes.import.create');
            Route::post('activity-codes/import', [ActivityCodeImportController::class, 'store'])->name('master.activity.codes.import.store');

            Route::get('activity-codes/{activityCode}', [ActivityCodeController::class, 'show'])->name('master.activity.codes.show');
        });

        Route::middleware('can:manage-activity-area')->group(function () {
            Route::get('activity-areas', [ActivityAreaController::class, 'index'])->name('master.activity.areas.index');
            Route::get('activity-areas/create', [ActivityAreaController::class, 'create'])->name('master.activity.areas.create');
            Route::post('activity-areas', [ActivityAreaController::class, 'store'])->name('master.activity.areas.store');
            Route::get('activity-areas/{activityArea}', [ActivityAreaController::class, 'show'])->name('master.activity.areas.show');
            Route::get('activity-areas/{activityArea}/edit', [ActivityAreaController::class, 'edit'])->name('master.activity.areas.edit');
            Route::put('activity-areas/{activityArea}', [ActivityAreaController::class, 'update'])->name('master.activity.areas.update');
            Route::delete('activity-areas/{activityArea}', [ActivityAreaController::class, 'destroy'])->name('master.activity.areas.destroy');
        });

        Route::middleware('can:manage-bill-category')->group(function () {
            Route::get('bill/categories', [BillCategoryController::class, 'index'])->name('master.bill.categories.index');
            Route::get('bill/categories/create', [BillCategoryController::class, 'create'])->name('master.bill.categories.create');
            Route::post('bill/categories', [BillCategoryController::class, 'store'])->name('master.bill.categories.store');
            Route::get('bill/categories/{billCategory}', [BillCategoryController::class, 'show'])->name('master.bill.categories.show');
            Route::get('bill/categories/{billCategory}/edit', [BillCategoryController::class, 'edit'])->name('master.bill.categories.edit');
            Route::put('bill/categories/{billCategory}', [BillCategoryController::class, 'update'])->name('master.bill.categories.update');
            Route::delete('bill/categories/{billCategory}', [BillCategoryController::class, 'destroy'])->name('master.bill.categories.destroy');
        });

        Route::middleware('can:manage-department')->group(function () {
            Route::get('departments', [DepartmentController::class, 'index'])->name('master.departments.index');
            Route::get('departments/create', [DepartmentController::class, 'create'])->name('master.departments.create');
            Route::post('departments', [DepartmentController::class, 'store'])->name('master.departments.store');
            Route::get('departments/{department}', [DepartmentController::class, 'show'])->name('master.departments.show');
            Route::get('departments/{department}/edit', [DepartmentController::class, 'edit'])->name('master.departments.edit');
            Route::put('departments/{department}', [DepartmentController::class, 'update'])->name('master.departments.update');
            Route::delete('departments/{department}', [DepartmentController::class, 'destroy'])->name('master.departments.destroy');
        });

        Route::middleware('can:manage-designation')->group(function () {
            Route::get('designations', [DesignationController::class, 'index'])->name('master.designations.index');
            Route::get('designations/create', [DesignationController::class, 'create'])->name('master.designations.create');
            Route::post('designations', [DesignationController::class, 'store'])->name('master.designations.store');
            Route::get('designations/{designation}', [DesignationController::class, 'show'])->name('master.designations.show');
            Route::get('designations/{designation}/edit', [DesignationController::class, 'edit'])->name('master.designations.edit');
            Route::put('designations/{designation}', [DesignationController::class, 'update'])->name('master.designations.update');
            Route::delete('designations/{designation}', [DesignationController::class, 'destroy'])->name('master.designations.destroy');
        });

        Route::middleware('can:manage-donor-code')->group(function () {
            Route::get('donor-codes', [DonorCodeController::class, 'index'])->name('master.donor.codes.index');
            Route::get('donor-codes/create', [DonorCodeController::class, 'create'])->name('master.donor.codes.create');
            Route::post('donor-codes', [DonorCodeController::class, 'store'])->name('master.donor.codes.store');
            Route::get('donor-codes/{donorCode}', [DonorCodeController::class, 'show'])->name('master.donor.codes.show');
            Route::get('donor-codes/{donorCode}/edit', [DonorCodeController::class, 'edit'])->name('master.donor.codes.edit');
            Route::put('donor-codes/{donorCode}', [DonorCodeController::class, 'update'])->name('master.donor.codes.update');
            Route::delete('donor-codes/{donorCode}', [DonorCodeController::class, 'destroy'])->name('master.donor.codes.destroy');
        });

        Route::middleware('can:manage-dsa-category')->group(function () {
            Route::get('dsa-categories', [DsaCategoryController::class, 'index'])->name('master.dsa.categories.index');
            Route::get('dsa-categories/create', [DsaCategoryController::class, 'create'])->name('master.dsa.categories.create');
            Route::post('dsa-categories', [DsaCategoryController::class, 'store'])->name('master.dsa.categories.store');
            Route::get('dsa-categories/{dsaCategory}', [DsaCategoryController::class, 'show'])->name('master.dsa.categories.show');
            Route::get('dsa-categories/{dsaCategory}/edit', [DsaCategoryController::class, 'edit'])->name('master.dsa.categories.edit');
            Route::put('dsa-categories/{dsaCategory}', [DsaCategoryController::class, 'update'])->name('master.dsa.categories.update');
            Route::delete('dsa-categories/{dsaCategory}', [DsaCategoryController::class, 'destroy'])->name('master.dsa.categories.destroy');
        });

        Route::middleware('can:manage-execution-type')->group(function () {
            Route::get('execution-types', [ExecutionTypeController::class, 'index'])->name('master.execution.types.index');
            Route::get('execution-types/create', [ExecutionTypeController::class, 'create'])->name('master.execution.types.create');
            Route::post('execution-types', [ExecutionTypeController::class, 'store'])->name('master.execution.types.store');
            Route::get('execution-types/{execution}', [ExecutionTypeController::class, 'show'])->name('master.execution.types.show');
            Route::get('execution-types/{execution}/edit', [ExecutionTypeController::class, 'edit'])->name('master.execution.types.edit');
            Route::put('execution-types/{execution}', [ExecutionTypeController::class, 'update'])->name('master.execution.types.update');
            Route::delete('execution-types/{execution}', [ExecutionTypeController::class, 'destroy'])->name('master.execution.types.destroy');
        });

        Route::middleware('can:manage-exit-question')->group(function () {
            Route::get('exit/feedbacks', [ExitFeedbackController::class, 'index'])->name('master.exit.feedbacks.index');
            Route::get('exit/feedbacks/create', [ExitFeedbackController::class, 'create'])->name('master.exit.feedbacks.create');
            Route::post('exit/feedbacks', [ExitFeedbackController::class, 'store'])->name('master.exit.feedbacks.store');
            Route::get('exit/feedbacks/{exitFeedback}', [ExitFeedbackController::class, 'show'])->name('master.exit.feedbacks.show');
            Route::get('exit/feedbacks/{exitFeedback}/edit', [ExitFeedbackController::class, 'edit'])->name('master.exit.feedbacks.edit');
            Route::put('exit/feedbacks/{exitFeedback}', [ExitFeedbackController::class, 'update'])->name('master.exit.feedbacks.update');
            Route::delete('exit/feedbacks/{exitFeedback}', [ExitFeedbackController::class, 'destroy'])->name('master.exit.feedbacks.destroy');

            Route::get('exit/questions', [ExitQuestionController::class, 'index'])->name('master.exit.questions.index');
            Route::get('exit/questions/create', [ExitQuestionController::class, 'create'])->name('master.exit.questions.create');
            Route::post('exit/questions', [ExitQuestionController::class, 'store'])->name('master.exit.questions.store');
            Route::get('exit/questions/{exitQuestion}', [ExitQuestionController::class, 'show'])->name('master.exit.questions.show');
            Route::get('exit/questions/{exitQuestion}/edit', [ExitQuestionController::class, 'edit'])->name('master.exit.questions.edit');
            Route::put('exit/questions/{exitQuestion}', [ExitQuestionController::class, 'update'])->name('master.exit.questions.update');
            Route::delete('exit/questions/{exitQuestion}', [ExitQuestionController::class, 'destroy'])->name('master.exit.questions.destroy');

            Route::get('exit/ratings', [ExitRatingController::class, 'index'])->name('master.exit.ratings.index');
            Route::get('exit/ratings/create', [ExitRatingController::class, 'create'])->name('master.exit.ratings.create');
            Route::post('exit/ratings', [ExitRatingController::class, 'store'])->name('master.exit.ratings.store');
            Route::get('exit/ratings/{exitRating}', [ExitRatingController::class, 'show'])->name('master.exit.ratings.show');
            Route::get('exit/ratings/{exitRating}/edit', [ExitRatingController::class, 'edit'])->name('master.exit.ratings.edit');
            Route::put('exit/ratings/{exitRating}', [ExitRatingController::class, 'update'])->name('master.exit.ratings.update');
            Route::delete('exit/ratings/{exitRating}', [ExitRatingController::class, 'destroy'])->name('master.exit.ratings.destroy');
        });

        Route::middleware('can:manage-expense-category')->group(function () {
            Route::get('expense/categories', [ExpenseCategoryController::class, 'index'])->name('master.expense.categories.index');
            Route::get('expense/categories/create', [ExpenseCategoryController::class, 'create'])->name('master.expense.categories.create');
            Route::post('expense/categories', [ExpenseCategoryController::class, 'store'])->name('master.expense.categories.store');
            Route::get('expense/categories/{expenseCategory}', [ExpenseCategoryController::class, 'show'])->name('master.expense.categories.show');
            Route::get('expense/categories/{expenseCategory}/edit', [ExpenseCategoryController::class, 'edit'])->name('master.expense.categories.edit');
            Route::put('expense/categories/{expenseCategory}', [ExpenseCategoryController::class, 'update'])->name('master.expense.categories.update');
            Route::delete('expense/categories/{expenseCategory}', [ExpenseCategoryController::class, 'destroy'])->name('master.expense.categories.destroy');
        });

        Route::middleware('can:manage-expense-type')->group(function () {
            Route::get('expense/types', [ExpenseTypeController::class, 'index'])->name('master.expense.types.index');
            Route::get('expense/types/create', [ExpenseTypeController::class, 'create'])->name('master.expense.types.create');
            Route::post('expense/types', [ExpenseTypeController::class, 'store'])->name('master.expense.types.store');
            Route::get('expense/types/{expenseType}', [ExpenseTypeController::class, 'show'])->name('master.expense.types.show');
            Route::get('expense/types/{expenseType}/edit', [ExpenseTypeController::class, 'edit'])->name('master.expense.types.edit');
            Route::put('expense/types/{expenseType}', [ExpenseTypeController::class, 'update'])->name('master.expense.types.update');
            Route::delete('expense/types/{expenseType}', [ExpenseTypeController::class, 'destroy'])->name('master.expense.types.destroy');
        });

        Route::middleware('can:manage-family-relation')->group(function () {
            Route::get('family/relations', [FamilyRelationController::class, 'index'])->name('master.family.relations.index');
            Route::get('family/relations/create', [FamilyRelationController::class, 'create'])->name('master.family.relations.create');
            Route::post('family/relations', [FamilyRelationController::class, 'store'])->name('master.family.relations.store');
            Route::get('family/relations/{familyRelation}', [FamilyRelationController::class, 'show'])->name('master.family.relations.show');
            Route::get('family/relations/{familyRelation}/edit', [FamilyRelationController::class, 'edit'])->name('master.family.relations.edit');
            Route::put('family/relations/{familyRelation}', [FamilyRelationController::class, 'update'])->name('master.family.relations.update');
            Route::delete('family/relations/{familyRelation}', [FamilyRelationController::class, 'destroy'])->name('master.family.relations.destroy');
            Route::post('family/relations/sort/order', [FamilyRelationController::class, 'sortOrder'])->name('master.family.relations.sort.order');
        });

        //        Route::middleware('can:manage-holiday')->group(function () {
        Route::get('holidays', [HolidayController::class, 'index'])->name('master.holidays.index');
        Route::get('holidays/create', [HolidayController::class, 'create'])->name('master.holidays.create');
        Route::post('holidays', [HolidayController::class, 'store'])->name('master.holidays.store');
        Route::get('holidays/{holiday}', [HolidayController::class, 'show'])->name('master.holidays.show');
        Route::get('holidays/{holiday}/edit', [HolidayController::class, 'edit'])->name('master.holidays.edit');
        Route::put('holidays/{holiday}', [HolidayController::class, 'update'])->name('master.holidays.update');
        Route::delete('holidays/{holiday}', [HolidayController::class, 'destroy'])->name('master.holidays.destroy');
        //        });

        Route::middleware('can:manage-inventory-category')->group(function () {
            Route::get('inventory/categories', [InventoryCategoryController::class, 'index'])->name('master.inventory.categories.index');
            Route::get('inventory/categories/create', [InventoryCategoryController::class, 'create'])->name('master.inventory.categories.create');
            Route::post('inventory/categories', [InventoryCategoryController::class, 'store'])->name('master.inventory.categories.store');
            Route::get('inventory/categories/{inventoryCategory}', [InventoryCategoryController::class, 'show'])->name('master.inventory.categories.show');
            Route::get('inventory/categories/{inventoryCategory}/edit', [InventoryCategoryController::class, 'edit'])->name('master.inventory.categories.edit');
            Route::put('inventory/categories/{inventoryCategory}', [InventoryCategoryController::class, 'update'])->name('master.inventory.categories.update');
            Route::delete('inventory/categories/{inventoryCategory}', [InventoryCategoryController::class, 'destroy'])->name('master.inventory.categories.destroy');
        });

        Route::middleware('can:manage-item')->group(function () {
            Route::get('items', [ItemController::class, 'index'])->name('master.items.index');
            Route::get('items/create', [ItemController::class, 'create'])->name('master.items.create');
            Route::post('items', [ItemController::class, 'store'])->name('master.items.store');
            Route::get('items/{item}', [ItemController::class, 'show'])->name('master.items.show');
            Route::get('items/{item}/edit', [ItemController::class, 'edit'])->name('master.items.edit');
            Route::put('items/{item}', [ItemController::class, 'update'])->name('master.items.update');
            Route::delete('items/{item}', [ItemController::class, 'destroy'])->name('master.items.destroy');
        });

        Route::middleware('can:manage-leave-type')->group(function () {
            Route::get('leave/types', [LeaveTypeController::class, 'index'])->name('master.leave.types.index');
            Route::get('leave/types/create', [LeaveTypeController::class, 'create'])->name('master.leave.types.create');
            Route::post('leave/types', [LeaveTypeController::class, 'store'])->name('master.leave.types.store');
            Route::get('leave/types/{leaveType}', [LeaveTypeController::class, 'show'])->name('master.leave.types.show');
            Route::get('leave/types/{leaveType}/edit', [LeaveTypeController::class, 'edit'])->name('master.leave.types.edit');
            Route::put('leave/types/{leaveType}', [LeaveTypeController::class, 'update'])->name('master.leave.types.update');
            Route::delete('leave/types/{leaveType}', [LeaveTypeController::class, 'destroy'])->name('master.leave.types.destroy');
        });

        Route::middleware('can:manage-meeting-hall')->group(function () {
            Route::get('meeting-halls', [MeetingHallController::class, 'index'])->name('master.meeting.hall.index');
            Route::get('meeting-halls/create', [MeetingHallController::class, 'create'])->name('master.meeting.hall.create');
            Route::post('meeting-halls', [MeetingHallController::class, 'store'])->name('master.meeting.hall.store');
            Route::get('meeting-halls/{meetingHall}', [MeetingHallController::class, 'show'])->name('master.meeting.hall.show');
            Route::get('meeting-halls/{meetingHall}/edit', [MeetingHallController::class, 'edit'])->name('master.meeting.hall.edit');
            Route::put('meeting-halls/{meetingHall}', [MeetingHallController::class, 'update'])->name('master.meeting.hall.update');
            Route::delete('meeting-halls/{meetingHall}', [MeetingHallController::class, 'destroy'])->name('master.meeting.hall.destroy');
        });

        Route::middleware('can:manage-office')->group(function () {
            Route::get('offices', [OfficeController::class, 'index'])->name('master.offices.index');
            Route::get('offices/create', [OfficeController::class, 'create'])->name('master.offices.create');
            Route::post('offices', [OfficeController::class, 'store'])->name('master.offices.store');
            Route::get('offices/{office}', [OfficeController::class, 'show'])->name('master.offices.show');
            Route::get('offices/{office}/edit', [OfficeController::class, 'edit'])->name('master.offices.edit');
            Route::put('offices/{office}', [OfficeController::class, 'update'])->name('master.offices.update');
            Route::delete('offices/{office}', [OfficeController::class, 'destroy'])->name('master.offices.destroy');
            Route::put('offices/change/status', [OfficeController::class, 'changeStatus'])->name('master.offices.change.status');
        });

        Route::get('offices/office-type/{officeType}', [OfficeController::class, 'getParentOffices'])->name('master.offices.get.by.office.type');

        Route::middleware('can:manage-office-type')->group(function () {
            Route::get('office/types', [OfficeTypeController::class, 'index'])->name('master.office.types.index');
            Route::get('office/types/create', [OfficeTypeController::class, 'create'])->name('master.office.types.create');
            Route::post('office/types', [OfficeTypeController::class, 'store'])->name('master.office.types.store');
            Route::get('office/types/{officeType}', [OfficeTypeController::class, 'show'])->name('master.office.types.show');
            Route::get('office/types/{officeType}/edit', [OfficeTypeController::class, 'edit'])->name('master.office.types.edit');
            Route::put('office/types/{officeType}', [OfficeTypeController::class, 'update'])->name('master.office.types.update');
            Route::delete('office/types/{officeType}', [OfficeTypeController::class, 'destroy'])->name('master.office.types.destroy');
        });

        Route::middleware('can:manage-probationary-indicator')->group(function () {
            Route::get('probationary/indicators', [ProbationaryIndicatorController::class, 'index'])->name('master.probationary.indicators.index');
            Route::get('probationary/indicators/create', [ProbationaryIndicatorController::class, 'create'])->name('master.probationary.indicators.create');
            Route::post('probationary/indicators', [ProbationaryIndicatorController::class, 'store'])->name('master.probationary.indicators.store');
            Route::get('probationary/indicators/{probationaryIndicator}', [ProbationaryIndicatorController::class, 'show'])->name('master.probationary.indicators.show');
            Route::get('probationary/indicators/{probationaryIndicator}/edit', [ProbationaryIndicatorController::class, 'edit'])->name('master.probationary.indicators.edit');
            Route::put('probationary/indicators/{probationaryIndicator}', [ProbationaryIndicatorController::class, 'update'])->name('master.probationary.indicators.update');
            Route::delete('probationary/indicators/{probationaryIndicator}', [ProbationaryIndicatorController::class, 'destroy'])->name('master.probationary.indicators.destroy');
        });

        Route::middleware('can:manage-probationary-question')->group(function () {
            Route::get('probationary/questions', [ProbationaryQuestionController::class, 'index'])->name('master.probationary.questions.index');
            Route::get('probationary/questions/create', [ProbationaryQuestionController::class, 'create'])->name('master.probationary.questions.create');
            Route::post('probationary/questions', [ProbationaryQuestionController::class, 'store'])->name('master.probationary.questions.store');
            Route::get('probationary/questions/{probationaryQuestion}', [ProbationaryQuestionController::class, 'show'])->name('master.probationary.questions.show');
            Route::get('probationary/questions/{probationaryQuestion}/edit', [ProbationaryQuestionController::class, 'edit'])->name('master.probationary.questions.edit');
            Route::put('probationary/questions/{probationaryQuestion}', [ProbationaryQuestionController::class, 'update'])->name('master.probationary.questions.update');
            Route::delete('probationary/questions/{probationaryQuestion}', [ProbationaryQuestionController::class, 'destroy'])->name('master.probationary.questions.destroy');
        });

        Route::middleware('can:manage-projects')->group(function () {
            Route::get('project-codes', [ProjectCodeController::class, 'index'])->name('master.project.codes.index');
            Route::get('project-codes/create', [ProjectCodeController::class, 'create'])->name('master.project.codes.create');
            Route::post('project-codes', [ProjectCodeController::class, 'store'])->name('master.project.codes.store');
            Route::get('project-codes/{projectCode}', [ProjectCodeController::class, 'show'])->name('master.project.codes.show');
            Route::get('project-codes/{projectCode}/edit', [ProjectCodeController::class, 'edit'])->name('master.project.codes.edit');
            Route::put('project-codes/{projectCode}', [ProjectCodeController::class, 'update'])->name('master.project.codes.update');
            Route::delete('project-codes/{projectCode}', [ProjectCodeController::class, 'destroy'])->name('master.project.codes.destroy');
        });

        // Route::middleware('can:manage-partner-organization')->group(function () {
        Route::get('organizations/partner/create', [PartnerOrganizationController::class, 'create'])->name('master.partner.org.create');
        Route::get('organizations/partner', [PartnerOrganizationController::class, 'index'])->name('master.partner.org.index');
        Route::post('organizations/partner', [PartnerOrganizationController::class, 'store'])->name('master.partner.org.store');
        Route::get('organizations/partner/{projectCode}', [PartnerOrganizationController::class, 'show'])->name('master.partner.org.show');
        Route::get('organizations/partner/{projectCode}/edit', [PartnerOrganizationController::class, 'edit'])->name('master.partner.org.edit');
        Route::put('organizations/partner/{projectCode}', [PartnerOrganizationController::class, 'update'])->name('master.partner.org.update');
        Route::delete('organizations/partner/{projectCode}', [PartnerOrganizationController::class, 'destroy'])->name('master.partner.org.destroy');
        // });

        Route::middleware('can:manage-training-question')->group(function () {
            Route::get('training/questions', [TrainingQuestionController::class, 'index'])->name('master.training.questions.index');
            Route::get('training/questions/create', [TrainingQuestionController::class, 'create'])->name('master.training.questions.create');
            Route::post('training/questions', [TrainingQuestionController::class, 'store'])->name('master.training.questions.store');
            Route::get('training/questions/{trainingQuestion}', [TrainingQuestionController::class, 'show'])->name('master.training.questions.show');
            Route::get('training/questions/{trainingQuestion}/edit', [TrainingQuestionController::class, 'edit'])->name('master.training.questions.edit');
            Route::put('training/questions/{trainingQuestion}', [TrainingQuestionController::class, 'update'])->name('master.training.questions.update');
            Route::delete('training/questions/{trainingQuestion}', [TrainingQuestionController::class, 'destroy'])->name('master.training.questions.destroy');
        });

        Route::middleware('can:manage-unit')->group(function () {
            Route::get('units', [UnitController::class, 'index'])->name('master.units.index');
            Route::get('units/create', [UnitController::class, 'create'])->name('master.units.create');
            Route::post('units', [UnitController::class, 'store'])->name('master.units.store');
            Route::get('units/{unit}', [UnitController::class, 'show'])->name('master.units.show');
            Route::get('units/{unit}/edit', [UnitController::class, 'edit'])->name('master.units.edit');
            Route::put('units/{unit}', [UnitController::class, 'update'])->name('master.units.update');
            Route::delete('units/{unit}', [UnitController::class, 'destroy'])->name('master.units.destroy');
        });

        Route::middleware('can:manage-vehicle')->group(function () {
            Route::get('vehicles', [VehicleController::class, 'index'])->name('master.vehicles.index');
            Route::get('vehicles/create', [VehicleController::class, 'create'])->name('master.vehicles.create');
            Route::post('vehicles', [VehicleController::class, 'store'])->name('master.vehicles.store');
            Route::get('vehicles/{vehicle}', [VehicleController::class, 'show'])->name('master.vehicles.show');
            Route::get('vehicles/{vehicle}/edit', [VehicleController::class, 'edit'])->name('master.vehicles.edit');
            Route::put('vehicles/{vehicle}', [VehicleController::class, 'update'])->name('master.vehicles.update');
            Route::delete('vehicles/{vehicle}', [VehicleController::class, 'destroy'])->name('master.vehicles.destroy');
        });

        Route::middleware('can:manage-master')->group(function () {
            Route::get('provinces', [ProvinceController::class, 'index'])->name('master.provinces.index');
            Route::get('provinces/{province}/edit', [ProvinceController::class, 'edit'])->name('master.provinces.edit');
            Route::put('provinces/{province}', [ProvinceController::class, 'update'])->name('master.provinces.update');

            Route::get('districts', [DistrictController::class, 'index'])->name('master.districts.index');
            Route::get('districts/{province}/edit', [DistrictController::class, 'edit'])->name('master.districts.edit');
            Route::put('districts/{province}', [DistrictController::class, 'update'])->name('master.districts.update');

            Route::get('local-levels', [LocalLevelController::class, 'index'])->name('master.local.levels.index');
            Route::get('local-levels/{localLevel}/edit', [LocalLevelController::class, 'edit'])->name('master.local.levels.edit');
            Route::put('local-levels/{localLevel}', [LocalLevelController::class, 'update'])->name('master.local.levels.update');
        });

        Route::middleware('can:manage-health-facility')->group(function () {
            Route::get('health/facilities', [HealthFacilityController::class, 'index'])->name('master.health.facilities.index');
            Route::get('health/facilities/create', [HealthFacilityController::class, 'create'])->name('master.health.facilities.create');
            Route::post('health/facilities', [HealthFacilityController::class, 'store'])->name('master.health.facilities.store');
            Route::get('health/facilities/{healthFacility}', [HealthFacilityController::class, 'show'])->name('master.health.facilities.show');
            Route::get('health/facilities/{healthFacility}/edit', [HealthFacilityController::class, 'edit'])->name('master.health.facilities.edit');
            Route::put('health/facilities/{healthFacility}', [HealthFacilityController::class, 'update'])->name('master.health.facilities.update');
            Route::delete('health/facilities/{healthFacility}', [HealthFacilityController::class, 'destroy'])->name('master.health.facilities.destroy');
        });

        Route::middleware('can:manage-pr-package')->group(function () {
            Route::get('purchase/request/packages', [PackageController::class, 'index'])->name('master.packages.index');
            Route::get('purchase/request/packages/create', [PackageController::class, 'create'])->name('master.packages.create');
            Route::post('purchase/request/packages', [PackageController::class, 'store'])->name('master.packages.store');
            Route::get('purchase/request/packages/{package}', [PackageController::class, 'show'])->name('master.packages.show');
            Route::get('purchase/request/packages/{package}/edit', [PackageController::class, 'edit'])->name('master.packages.edit');
            Route::put('purchase/request/packages/{package}', [PackageController::class, 'update'])->name('master.packages.update');
            Route::delete('purchase/request/packages/{package}', [PackageController::class, 'destroy'])->name('master.packages.destroy');

            Route::get('purchase/request/packages/{package}/items', [PackageItemController::class, 'index'])->name('master.packages.items.index');
            Route::get('purchase/request/packages/{package}/items/create', [PackageItemController::class, 'create'])->name('master.packages.items.create');
            Route::post('purchase/request/packages/{package}/items', [PackageItemController::class, 'store'])->name('master.packages.items.store');
            Route::get('purchase/request/packages/{package}/items/{item}', [PackageItemController::class, 'show'])->name('master.packages.items.show');
            Route::get('purchase/request/packages/{package}/items/{item}/edit', [PackageItemController::class, 'edit'])->name('master.packages.items.edit');
            Route::put('purchase/request/packages/{package}/items/{item}', [PackageItemController::class, 'update'])->name('master.packages.items.update');
            Route::delete('purchase/request/packages/{package}/items/{item}', [PackageItemController::class, 'destroy'])->name('master.packages.items.destroy');
        });

        Route::middleware('can:manage-brands')->group(function () {
            Route::get('brands', [BrandController::class, 'index'])->name('master.brands.index');
            Route::get('brands/create', [BrandController::class, 'create'])->name('master.brands.create');
            Route::post('brands', [BrandController::class, 'store'])->name('master.brands.store');
            Route::get('brands/{brand}', [BrandController::class, 'show'])->name('master.brands.show');
            Route::get('brands/{brand}/edit', [BrandController::class, 'edit'])->name('master.brands.edit');
            Route::put('brands/{brand}', [BrandController::class, 'update'])->name('master.brands.update');
            Route::delete('brands/{brand}', [BrandController::class, 'destroy'])->name('master.brands.destroy');
        });
    });
});
