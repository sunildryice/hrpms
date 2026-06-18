<?php

use Modules\Tracker\Controllers\BusinessDevelopmentController;
use Modules\Tracker\Controllers\HrEventController;
use Modules\Tracker\Controllers\ResearchCommunicationController;
use Modules\Tracker\Controllers\RiskController;

Route::middleware(['web', 'auth', 'logger'])->group(function () {
    Route::get('risk', [RiskController::class, 'index'])->name('risk.index');
    Route::get('risk/create', [RiskController::class, 'create'])->name('risk.create');
    Route::post('risk', [RiskController::class, 'store'])->name('risk.store');
    Route::get('risk/{id}/show', [RiskController::class, 'show'])->name('risk.show');
    Route::get('risk/{id}/edit', [RiskController::class, 'edit'])->name('risk.edit');
    Route::put('risk/{id}/update', [RiskController::class, 'update'])->name('risk.update');
    Route::delete('risk/{id}/destroy', [RiskController::class, 'destroy'])->name('risk.destroy');

    Route::get('business-development', [BusinessDevelopmentController::class, 'index'])->name('business-development.index');
    Route::get('business-development/create', [BusinessDevelopmentController::class, 'create'])->name('business-development.create');
    Route::post('business-development', [BusinessDevelopmentController::class, 'store'])->name('business-development.store');
    Route::get('business-development/{id}/show', [BusinessDevelopmentController::class, 'show'])->name('business-development.show');
    Route::get('business-development/{id}/edit', [BusinessDevelopmentController::class, 'edit'])->name('business-development.edit');
    Route::put('business-development/{id}/update', [BusinessDevelopmentController::class, 'update'])->name('business-development.update');
    Route::delete('business-development/{id}/destroy', [BusinessDevelopmentController::class, 'destroy'])->name('business-development.destroy');

    Route::get('hr-event', [HrEventController::class, 'index'])->name('hr-event.index');
    Route::get('hr-event/create', [HrEventController::class, 'create'])->name('hr-event.create');
    Route::post('hr-event', [HrEventController::class, 'store'])->name('hr-event.store');
    Route::get('hr-event/{id}/show', [HrEventController::class, 'show'])->name('hr-event.show');
    Route::get('hr-event/{id}/edit', [HrEventController::class, 'edit'])->name('hr-event.edit');
    Route::put('hr-event/{id}/update', [HrEventController::class, 'update'])->name('hr-event.update');
    Route::delete('hr-event/{id}/destroy', [HrEventController::class, 'destroy'])->name('hr-event.destroy');

    Route::get('research-communication', [ResearchCommunicationController::class, 'index'])->name('research-communication.index');
    Route::get('research-communication/create', [ResearchCommunicationController::class, 'create'])->name('research-communication.create');
    Route::post('research-communication', [ResearchCommunicationController::class, 'store'])->name('research-communication.store');
    Route::get('research-communication/{id}/show', [ResearchCommunicationController::class, 'show'])->name('research-communication.show');
    Route::get('research-communication/{id}/edit', [ResearchCommunicationController::class, 'edit'])->name('research-communication.edit');
    Route::put('research-communication/{id}/update', [ResearchCommunicationController::class, 'update'])->name('research-communication.update');
    Route::delete('research-communication/{id}/destroy', [ResearchCommunicationController::class, 'destroy'])->name('research-communication.destroy');
});
