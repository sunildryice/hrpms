<?php
use Illuminate\Support\Facades\Route;
use Modules\AssetDisposition\Controllers\ApproveController;
use Modules\AssetDisposition\Controllers\ApprovedController;
use Modules\AssetDisposition\Controllers\DispositionRequestController;


/*
|--------------------------------------------------------------------------
| Application Routes for Asset Disposition Module
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is ordered.
|
*/

Route::middleware(['web', 'auth', 'logger'])->group(function () {


    Route::middleware('can:asset-disposition')->group(function(){
        Route::get('asset/dispose', [DispositionRequestController::class, 'index'])->name('asset.disposition.index');
        Route::get('asset/dispose/{disposition}/show', [DispositionRequestController::class, 'show'])->name('asset.disposition.show');
        Route::get('asset/dispose/create', [DispositionRequestController::class, 'create'])->name('asset.disposition.create');
        Route::post('asset/dispose', [DispositionRequestController::class, 'store'])->name('asset.disposition.store');
        Route::get('asset/dispose/{disposition}/edit', [DispositionRequestController::class, 'edit'])->name('asset.disposition.edit');
        Route::put('asset/dispose/{disposition}', [DispositionRequestController::class, 'update'])->name('asset.disposition.update');
        Route::delete('asset/dispose/{disposition}/destroy', [DispositionRequestController::class, 'destroy'])->name('asset.disposition.destroy');
       
    });

    Route::post('asset/dispose/{asset}/cancel', [DispositionRequestController::class, 'cancel'])->name('asset.disposition.cancel.store');
   

  
    Route::middleware('can:approve-asset-disposition')->group(function(){
        Route::get('approve/asset/dispose', [ApproveController::class, 'index'])->name('approve.asset.disposition.index');
        Route::get('approve/asset/dispose/{disposition}/create', [ApproveController::class, 'create'])->name('approve.asset.disposition.create');
        Route::post('approve/asset/dispose/{disposition}/store', [ApproveController::class, 'store'])->name('approve.asset.disposition.store');
    });
   

    Route::middleware('can:view-approved-asset-disposition')->group(function(){
        Route::get('approved/asset/dispose', [ApprovedController::class, 'index'])->name('approved.asset.disposition.index');
        Route::get('approved/asset/dispose/{disposition}/show', [ApprovedController::class, 'show'])->name('approved.asset.disposition.show');
    });
   
    Route::get('asset/dispose/{disposition}/print', [ApprovedController::class, 'print'])->name('asset.disposition.print');
  
});
