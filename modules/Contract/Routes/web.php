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

use Modules\Contract\Controllers\ContractAmendController;
use Modules\Contract\Controllers\ContractController;

Route::middleware(['web', 'auth', 'logger'])->group(function () {
    Route::middleware('can:manage-contract')->group(function () {
        Route::get('contracts', [ContractController::class, 'index'])->name('contracts.index');
        Route::get('contracts/export', [ContractController::class, 'export'])->name('contracts.export');
        Route::get('contracts/create', [ContractController::class, 'create'])->name('contracts.create');
        Route::post('contracts', [ContractController::class, 'store'])->name('contracts.store');
        Route::get('contracts/{contract}', [ContractController::class, 'show'])->name('contracts.show');
        Route::get('contracts/{contract}/edit', [ContractController::class, 'edit'])->name('contracts.edit');
        Route::post('contracts/{contract}', [ContractController::class, 'update'])->name('contracts.update');
        Route::delete('contracts/{contract}', [ContractController::class, 'destroy'])->name('contracts.destroy');
        Route::get('contracts/{contract}/detail', [ContractController::class, 'detail'])->name('contracts.detail');


        Route::get('contracts/{contract}/amendments', [ContractAmendController::class, 'index'])->name('contracts.amendments.index');
        Route::get('contracts/{contract}/amendments/create', [ContractAmendController::class, 'create'])->name('contracts.amendments.create');
        Route::post('contracts/{contract}/amendments', [ContractAmendController::class, 'store'])->name('contracts.amendments.store');
        Route::get('contracts/{contract}/amendments/{amendment}/edit', [ContractAmendController::class, 'edit'])->name('contracts.amendments.edit');
        Route::put('contracts/{contract}/amendments/{amendment}', [ContractAmendController::class, 'update'])->name('contracts.amendments.update');
        Route::delete('contracts/{contract}/amendments/{amendment}', [ContractAmendController::class, 'destroy'])->name('contracts.amendments.destroy');
    });
});
