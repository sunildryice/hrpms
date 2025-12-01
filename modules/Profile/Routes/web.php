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

use Modules\Profile\Controllers\AddressController;
use Modules\Profile\Controllers\AssetController;
use Modules\Profile\Controllers\DocumentUploadController;
use Modules\Profile\Controllers\EducationController;
use Modules\Profile\Controllers\EmployeeController;
use Modules\Profile\Controllers\ExperienceController;
use Modules\Profile\Controllers\FamilyDetailController;
use Modules\Profile\Controllers\MedicalController;
use Modules\Profile\Controllers\SocialMediaController;
use Modules\Profile\Controllers\TrainingController;

Route::middleware(['web', 'auth', 'logger'])->group(function () {
    Route::get('profile', [EmployeeController::class, 'show'])->name('profile.show');
    Route::get('profile/edit', [EmployeeController::class, 'edit'])->name('profile.edit');
    Route::put('profile', [EmployeeController::class, 'update'])->name('profile.update');

    Route::put('profile/document', [DocumentUploadController::class, 'store'])->name('profile.document.store');
    Route::post('profile/address', [AddressController::class, 'store'])->name('profile.address.store');
    Route::put('profile/address/{address}', [AddressController::class, 'update'])->name('profile.address.update');

    Route::post('profile/education', [EducationController::class, 'store'])->name('profile.education.store');
    Route::get('profile/education/{education}/edit', [EducationController::class, 'edit'])->name('profile.education.edit');
    Route::put('profile/education/{education}', [EducationController::class, 'update'])->name('profile.education.update');

    Route::post('profile/experiences', [ExperienceController::class, 'store'])->name('profile.experiences.store');
    Route::get('profile/experiences/{experience}/edit', [ExperienceController::class, 'edit'])->name('profile.experiences.edit');
    Route::put('profile/experiences/{experience}', [ExperienceController::class, 'update'])->name('profile.experiences.update');

    Route::post('profile/family/details', [FamilyDetailController::class, 'store'])->name('profile.family.details.store');
    Route::get('profile/family/details/{family}/edit', [FamilyDetailController::class, 'edit'])->name('profile.family.details.edit');
    Route::put('profile/family/details/{family}', [FamilyDetailController::class, 'update'])->name('profile.family.details.update');

    Route::post('profile/medical', [MedicalController::class, 'store'])->name('profile.medical.store');
    Route::put('profile/medical/{medical}', [MedicalController::class, 'update'])->name('profile.medical.update');

    Route::post('profile/trainings', [TrainingController::class, 'store'])->name('profile.trainings.store');
    Route::get('profile/trainings/{training}/edit', [TrainingController::class, 'edit'])->name('profile.trainings.edit');
    Route::put('profile/trainings/{training}', [TrainingController::class, 'update'])->name('profile.trainings.update');

    Route::post('profile/{employee}/social-media', [SocialMediaController::class, 'update'])->name('profile.social-media.update');

    Route::get('profile/assets', [AssetController::class, 'index'])->name('profile.assets.index');
    //    Route::get('profile/assets/{asset}/handover/create', [HandoverController::class, 'create'])->name('assets.handover.create');
    //    Route::post('profile/assets/{asset}/handover', [HandoverController::class, 'store'])->name('assets.handover.store');

});
