<?php
use Illuminate\Support\Facades\Route;
use Modules\EventCompletion\Controllers\ApproveController;
use Modules\EventCompletion\Controllers\ApprovedController;
use Modules\EventCompletion\Controllers\EventCompletionController;
use Modules\EventCompletion\Controllers\EventParticipantController;


/*
|--------------------------------------------------------------------------
| Application Routes for Event Completion Module
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is ordered.
|
*/


Route::middleware(['web', 'auth', 'logger'])->group(function () {


    Route::middleware('can:event-completion')->group(function(){
        Route::get('event/completion', [EventCompletionController::class, 'index'])->name('event.completion.index');
        Route::get('event/completion/{eventCompletion}/show', [EventCompletionController::class, 'show'])->name('event.completion.show');
        Route::get('event/completion/create', [EventCompletionController::class, 'create'])->name('event.completion.create');
        Route::post('event/completion', [EventCompletionController::class, 'store'])->name('event.completion.store');
        Route::get('event/completion/{eventCompletion}/edit', [EventCompletionController::class, 'edit'])->name('event.completion.edit');
        Route::put('event/completion/{eventCompletion}', [EventCompletionController::class, 'update'])->name('event.completion.update');
        Route::delete('event/completion/{eventCompletion}/destroy', [EventCompletionController::class, 'destroy'])->name('event.completion.destroy');
        Route::get('event/completion/{eventCompletion}/submit', [EventCompletionController::class, 'submit'])->name('event.completion.submit');

        Route::get('event/completion/{eventCompletion}/participants/create', [EventParticipantController::class, 'create'])
        ->name('event.completion.participants.create');
        Route::get('event/completion/{eventCompletion}/participants/{eventCompletionParticipant}/edit', [EventParticipantController::class, 'edit'])
        ->name('event.completion.participants.edit');
        Route::put('event/completion/{eventCompletion}/participants/{eventCompletionParticipant}', [EventParticipantController::class, 'update'])
        ->name('event.completion.participants.update');
        Route::delete('event/completion/{eventCompletion}/participants/{eventCompletionParticipant}/destroy', [EventParticipantController::class, 'destroy'])
        ->name('event.completion.participants.destroy');
        Route::post('event/completion/{eventCompletion}/participants', [EventParticipantController::class, 'store'])
        ->name('event.completion.participants.store');
       
    });

    Route::get('event/completion/{eventCompletion}/participants', [EventParticipantController::class, 'index'])
    ->name('event.completion.participants.index');
    Route::post('event/completion/{event}/cancel', [EventCompletionController::class, 'cancel'])->name('event.completion.cancel.store');
   

  
    Route::middleware('can:approve-event-form')->group(function(){
        Route::get('approve/event/completion', [ApproveController::class, 'index'])->name('approve.event.completion.index');
        Route::get('approve/event/completion/{eventCompletion}/create', [ApproveController::class, 'create'])->name('approve.event.completion.create');
        Route::post('approve/event/completion/{eventCompletion}/store', [ApproveController::class, 'store'])->name('approve.event.completion.store');
    });
   

    Route::middleware('can:view-approved-event-completion')->group(function(){
        Route::get('approved/event/completion', [ApprovedController::class, 'index'])->name('approved.event.completion.index');
        Route::get('approved/event/completion/{eventCompletion}/show', [ApprovedController::class, 'show'])->name('approved.event.completion.show');
    });
   
    Route::get('event/completion/{eventCompletion}/print', [ApprovedController::class, 'print'])->name('event.completion.print');
  
});
