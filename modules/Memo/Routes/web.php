<?php

/*
|--------------------------------------------------------------------------
| Application Routes for Memo Module
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

use Modules\Memo\Controllers\ApprovedMemoController;
use Modules\Memo\Controllers\MemoController;
use Modules\Memo\Controllers\ApproveMemoController;


//Route::middleware(['web', 'auth', 'logger', 'can:manage-employee'])->group(function () {
Route::middleware(['web', 'auth', 'logger'])->group(function () {
    Route::get('memo/', [MemoController::class, 'index'])->name('memo.index');
    Route::get('memo/create', [MemoController::class, 'create'])->name('memo.create');
    Route::post('memo/', [MemoController::class, 'store'])->name('memo.store');
    Route::get('memo/{memo}/edit', [MemoController::class, 'edit'])->name('memo.edit');
    Route::put('memo/{memo}/', [MemoController::class, 'update'])->name('memo.update');
    Route::get('memo/{memo}/view', [MemoController::class, 'view'])->name('memo.view');
    Route::delete('memo/{memo}', [MemoController::class, 'destroy'])
        ->name('memo.destroy');
    Route::post('memo/{memo}/amend', [MemoController::class, 'amend'])
        ->name('memo.amend');

    Route::middleware('can:approve-memo')->group(function(){
        Route::get('approve/memo', [ApproveMemoController::class, 'index'])
            ->name('approve.memo.index');
        Route::get('approve/memo/{memo}/create', [ApproveMemoController::class, 'create'])
            ->name('approve.memo.create');
        Route::post('approve/memo/{memo}', [ApproveMemoController::class, 'store'])
            ->name('approve.memo.store');
    });

    Route::middleware('can:view-approved-memo')->group(function () {
        Route::get('approved/memo', [ApprovedMemoController::class, 'index'])->name('approved.memo.index');
        Route::get('approved/memo/{memo}/show', [ApprovedMemoController::class, 'show'])->name('approved.memo.show');
        Route::get('approved/memo/{memo}/print', [ApprovedMemoController::class, 'print'])->name('approved.memo.print');
    });

    Route::get('memo/{memo}/print', [MemoController::class, 'printMemo'])->name('memo.print');
});
