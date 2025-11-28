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

use Modules\Employee\Controllers\AddressController;
use Modules\Employee\Controllers\AssetController;
use Modules\Employee\Controllers\CommandController;
use Modules\Employee\Controllers\ConsultantController;
use Modules\Employee\Controllers\DocumentUploadController;
use Modules\Employee\Controllers\EducationController;
use Modules\Employee\Controllers\EmployeeController;
use Modules\Employee\Controllers\EmployeeHourController;
use Modules\Employee\Controllers\ExperienceController;
use Modules\Employee\Controllers\FinanceController;
use Modules\Employee\Controllers\FamilyDetailController;
use Modules\Employee\Controllers\InsuranceController;
use Modules\Employee\Controllers\LeaveController;
use Modules\Employee\Controllers\LeaveImportController;
use Modules\Employee\Controllers\MedicalController;
use Modules\Employee\Controllers\PaymentDetailController;
use Modules\Employee\Controllers\PaymentMasterController;
use Modules\Employee\Controllers\SocialMediaController;
use Modules\Employee\Controllers\TenureController;
use Modules\Employee\Controllers\TrainingController;
use Modules\Employee\Controllers\UserController;

Route::middleware(['web', 'auth', 'logger'])->group(function () {
    Route::get('employees/{employee}/profile', [EmployeeController::class, 'profile'])->name('employees.profile');
    Route::middleware('can:manage-employee')->group(function () {
        Route::get('employees', [EmployeeController::class, 'index'])->name('employees.index');
        Route::get('employees/create', [EmployeeController::class, 'create'])->name('employees.create');
        Route::post('employees', [EmployeeController::class, 'store'])->name('employees.store');
        Route::get('employees/{employee}', [EmployeeController::class, 'show'])->name('employees.show');

        Route::get('employees/{employee}/edit', [EmployeeController::class, 'edit'])->name('employees.edit');
        Route::put('employees/{employee}', [EmployeeController::class, 'update'])->name('employees.update');
        Route::delete('employees/{employee}', [EmployeeController::class, 'destroy'])->name('employees.destroy');

        Route::get('employees/{employee}/personal-information', [EmployeeController::class, 'info'])->name('employees.info');

        Route::put('employees/{employee}/document', [DocumentUploadController::class, 'store'])->name('employees.document.store');
        Route::post('employees/{employee}/address', [AddressController::class, 'store'])->name('employees.address.store');
        Route::put('employees/{employee}/address/{address}', [AddressController::class, 'update'])->name('employees.address.update');

        Route::post('employees/{employee}/education', [EducationController::class, 'store'])->name('employees.education.store');
        Route::get('employees/{employee}/education/{education}/edit', [EducationController::class, 'edit'])->name('employees.education.edit');
        Route::put('employees/{employee}/education/{education}', [EducationController::class, 'update'])->name('employees.education.update');

        Route::post('employees/{employee}/experiences', [ExperienceController::class, 'store'])->name('employees.experiences.store');
        Route::get('employees/{employee}/experiences/{experience}/edit', [ExperienceController::class, 'edit'])->name('employees.experiences.edit');
        Route::put('employees/{employee}/experiences/{experience}', [ExperienceController::class, 'update'])->name('employees.experiences.update');

        Route::post('employees/{employee}/family/details', [FamilyDetailController::class, 'store'])->name('employees.family.details.store');
        Route::get('employees/{employee}/family/details/{family}/edit', [FamilyDetailController::class, 'edit'])->name('employees.family.details.edit');
        Route::put('employees/{employee}/family/details/{family}', [FamilyDetailController::class, 'update'])->name('employees.family.details.update');

        Route::post('employees/{employee}/medical', [MedicalController::class, 'store'])->name('employees.medical.store');
        Route::put('employees/{employee}/medical/{medical}', [MedicalController::class, 'update'])->name('employees.medical.update');

        Route::post('employees/{employee}/trainings', [TrainingController::class, 'store'])->name('employees.trainings.store');
        Route::get('employees/{employee}/trainings/{training}/edit', [TrainingController::class, 'edit'])->name('employees.trainings.edit');
        Route::put('employees/{employee}/trainings/{training}', [TrainingController::class, 'update'])->name('employees.trainings.update');


        Route::get('employees/update/employee/leave', [CommandController::class, 'updateEmployeeLeave'])->name('employees.update.leave');


        Route::get('employees/{employee}/assets', [AssetController::class, 'index'])->name('employees.assets.index');
    });
    Route::middleware('can:manage-tenure')->group(function () {
        Route::post('employees/{employee}/tenures', [TenureController::class, 'store'])->name('employees.tenures.store');
        Route::get('employees/{employee}/tenures/{tenure}/edit', [TenureController::class, 'edit'])->name('employees.tenures.edit');
        Route::put('employees/{employee}/tenures/{tenure}', [TenureController::class, 'update'])->name('employees.tenures.update');

        Route::post('employees/{employee}/hours', [EmployeeHourController::class, 'store'])->name('employees.hours.store');
        Route::get('employees/{employee}/hours/{tenure}/edit', [EmployeeHourController::class, 'edit'])->name('employees.hours.edit');
        Route::put('employees/{employee}/hours/{tenure}', [EmployeeHourController::class, 'update'])->name('employees.hours.update');

        Route::post('employees/{employee}/finance', [FinanceController::class, 'store'])->name('employees.finance.store');
        Route::put('employees/{employee}/finance/{finance}', [FinanceController::class, 'update'])->name('employees.finance.update');

        Route::post('employees/{employee}/insurance', [InsuranceController::class, 'store'])->name('employees.insurance.store');
        Route::get('employees/{employee}/insurance/{insurance}/edit', [InsuranceController::class, 'edit'])->name('employees.insurance.edit');
        Route::put('employees/{employee}/insurance/{insurance}', [InsuranceController::class, 'update'])->name('employees.insurance.update');
    });

    Route::middleware('can:update-user-role')->group(function () {
        Route::post('employees/{employee}/user', [UserController::class, 'store'])->name('employees.user.store');
        Route::put('employees/{employee}/user', [UserController::class, 'update'])->name('employees.user.update');

        Route::post('consultant/{consultant}/user', [UserController::class, 'storeConsultant'])->name('consultant.user.store');
        Route::put('consultant/{consultant}/user', [UserController::class, 'updateConsultant'])->name('consultant.user.update');
    });

    Route::middleware('can:manage-employee')->group(function () {
        Route::get('employees/{employee}/leaves/{leave}/show', [LeaveController::class, 'show'])->name('employees.leaves.show');
        Route::get('employees/{employee}/leaves/{leave}/edit', [LeaveController::class, 'edit'])->name('employees.leaves.edit');
        Route::put('employees/{employee}/leaves/{leave}', [LeaveController::class, 'update'])->name('employees.leaves.update');
        Route::get('employees/{employee}/leaves/export', [LeaveController::class, 'export'])->name('employees.leaves.export');
        Route::get('employees/{employee}/leaves/{year}/export', [LeaveController::class, 'exportYear'])->name('employees.leaves.export.year');

        Route::get('employees/leaves/import', [LeaveImportController::class, 'create'])->name('employees.leaves.import.create');
        Route::post('employees/leaves/import', [LeaveImportController::class, 'store'])->name('employees.leaves.import.store');
    });

    Route::middleware('can:manage-employee')->group(function () {
        Route::get('employees/{employee}/payments/masters', [PaymentMasterController::class, 'index'])->name('employees.payments.masters.index');
        Route::get('employees/{employee}/payments/masters/create', [PaymentMasterController::class, 'create'])->name('employees.payments.masters.create');
        Route::post('employees/{employee}/payments/masters', [PaymentMasterController::class, 'store'])->name('employees.payments.masters.store');
        Route::get('employees/{employee}/payments/masters/{payment}/show', [PaymentMasterController::class, 'show'])->name('employees.payments.masters.show');
        Route::get('employees/{employee}/payments/masters/{payment}/edit', [PaymentMasterController::class, 'edit'])->name('employees.payments.masters.edit');
        Route::put('employees/{employee}/payments/masters/{payment}', [PaymentMasterController::class, 'update'])->name('employees.payments.masters.update');
        Route::delete('employees/{employee}/payments/masters/{payment}', [PaymentMasterController::class, 'destroy'])->name('employees.payments.masters.destroy');

        Route::get('employees/payments/masters/{payment}/details', [PaymentDetailController::class, 'index'])->name('employees.payments.masters.details.index');
        Route::get('employees/payments/masters/{payment}/details/create', [PaymentDetailController::class, 'create'])->name('employees.payments.masters.details.create');
        Route::post('employees/payments/masters/{payment}/details', [PaymentDetailController::class, 'store'])->name('employees.payments.masters.details.store');
        Route::get('employees/payments/masters/{payment}/details/{detail}/edit', [PaymentDetailController::class, 'edit'])->name('employees.payments.masters.details.edit');
        Route::put('employees/payments/masters/{payment}/details/{detail}', [PaymentDetailController::class, 'update'])->name('employees.payments.masters.details.update');
        Route::delete('employees/payments/masters/{payment}/details/{detail}', [PaymentDetailController::class, 'destroy'])->name('employees.payments.masters.details.destroy');
    });

    Route::middleware('can:manage-employee')->group(function () {
        Route::get('consultant', [ConsultantController::class, 'index'])->name('consultant.index');
        Route::get('consultant/create', [ConsultantController::class, 'create'])->name('consultant.create');
        Route::post('consultant', [ConsultantController::class, 'store'])->name('consultant.store');
        Route::get('consultant/{consultant}/edit', [ConsultantController::class, 'edit'])->name('consultant.edit');
        Route::put('consultant/{consultant}', [ConsultantController::class, 'update'])->name('consultant.update');
        Route::delete('consultant/{consultant}', [ConsultantController::class, 'destroy'])->name('consultant.destroy');

        Route::get('consultant/{consultant}/profile', [ConsultantController::class, 'profile'])->name('consultant.profile');
    });


    // Route::middleware('can:manage-employee')->group(function () {

    Route::post('employees/{employee}/social-media', [SocialMediaController::class, 'update'])->name('employees.social-media.update');
    // });
});
