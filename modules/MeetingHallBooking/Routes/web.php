<?php

/*
|--------------------------------------------------------------------------
| Application Routes for Meeting Hall Booking Module
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

use Illuminate\Support\Facades\Route;
use Modules\MeetingHallBooking\Controllers\MeetingHallBookingController;
use Modules\MeetingHallBooking\Controllers\MeetingHallBookingReviewController;


//Route::middleware(['web', 'auth', 'logger', 'can:manage-employee'])->group(function () {
Route::middleware(['web', 'auth', 'logger'])->group(function () {
    Route::get('meeting/hall/bookings', [MeetingHallBookingController::class, 'index'])
        ->name('meeting.hall.bookings.index');
    Route::get('meeting/hall/bookings/create', [MeetingHallBookingController::class, 'create'])
        ->name('meeting.hall.bookings.create');
    Route::post('meeting/hall/bookings', [MeetingHallBookingController::class, 'store'])
        ->name('meeting.hall.bookings.store');

    Route::get('meeting/hall/bookings/{booking}/edit', [MeetingHallBookingController::class, 'edit'])
        ->name('meeting.hall.bookings.edit');
    Route::put('meeting/hall/bookings/{booking}', [MeetingHallBookingController::class, 'update'])
        ->name('meeting.hall.bookings.update');
    Route::delete('meeting/hall/bookings/{booking}', [MeetingHallBookingController::class, 'destroy'])
        ->name('meeting.hall.bookings.destroy');

    Route::post('meeting/hall/bookings/{booking}/cancel', [MeetingHallBookingController::class, 'cancel'])->name('meeting.hall.bookings.cancel.store');
    Route::post('meeting/hall/bookings/{booking}/cancel/reverse', [MeetingHallBookingController::class, 'reverseCancel'])->name('meeting.hall.bookings.cancel.reverse');

    Route::post('meeting/hall/bookings/{booking}/amend', [MeetingHallBookingController::class, 'amend'])->name('meeting.hall.bookings.amend');
});
