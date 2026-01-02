<?php

use Illuminate\Support\Facades\Route;
use Modules\Project\Controllers\ActivityStageController;
use Modules\Project\Controllers\ProjectController;
use Modules\Project\Controllers\ProjectMembersController;


Route::middleware(['web', 'auth'])->group(function () {
    Route::get('/projects', [ProjectController::class, 'index'])->name('project.index');
    Route::get('/projects/create', [ProjectController::class, 'create'])->name('project.create');
    Route::post('/projects/store', [ProjectController::class, 'store'])->name('project.store');
    Route::get('/projects/{id}/edit', [ProjectController::class, 'edit'])->name('project.edit');
    Route::get('/projects/{id}/show', [ProjectController::class, 'show'])->name('project.show');
    Route::post('/projects/{id}/update', [ProjectController::class, 'update'])->name('project.update');
    Route::delete('/projects/{id}/delete', [ProjectController::class, 'destroy'])->name('project.destroy');


    Route::post('/project/members/{id}/update', [ProjectMembersController::class, 'update'])->name('project.members.update');


    Route::get('/activity-stages', [ActivityStageController::class, 'index'])->name('activity-stages.index');
    Route::get('/activity-stages/create', [ActivityStageController::class, 'create'])->name('activity-stage.create');
    Route::post('/activity-stages/store', [ActivityStageController::class, 'store'])->name('activity-stage.store');
    Route::get('/activity-stages/{id}/edit', [
        ActivityStageController::class,
        'edit'
    ])->name('activity-stage.edit');
    Route::post('/activity-stages/{id}/update', [ActivityStageController::class, 'update'])->name('activity-stage.update');
    Route::get('/activity-stages/{id}/show', [ActivityStageController::class, 'show'])->name('activity-stage.show');
    Route::delete('/activity-stages/{id}/delete', [ActivityStageController::class, 'destroy'])->name('activity-stages.destroy');
});
