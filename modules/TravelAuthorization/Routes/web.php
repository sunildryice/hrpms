<?php

use Modules\TravelAuthorization\Controllers\TravelAuthorizationApproveController as ApproveController;
use Modules\TravelAuthorization\Controllers\TravelAuthorizationApprovedController as ApprovedController;
use Modules\TravelAuthorization\Controllers\TravelAuthorizationController;
use Modules\TravelAuthorization\Controllers\TravelAuthorizationEstimateController as EstimateController;
use Modules\TravelAuthorization\Controllers\TravelAuthorizationItineraryController as ItineraryController;
use Modules\TravelAuthorization\Controllers\TravelAuthorizationOfficialController as OfficialController;

Route::middleware(['web', 'auth', 'logger'])->group(function () {

    Route::middleware('can:travel-authorization')->group(function () {
        Route::get('travel/authorization/requests', [TravelAuthorizationController::class, 'index'])->name('ta.requests.index');
        // Route::get('travel/authorization/requests/create', [TravelAuthorizationController::class, 'create'])->name('ta.requests.create');
        Route::post('travel/authorization/requests', [TravelAuthorizationController::class, 'store'])->name('ta.requests.store');
        Route::get('travel/authorization/{travel}/edit', [TravelAuthorizationController::class, 'edit'])->name('ta.requests.edit');
        Route::put('travel/authorization/requests/{travel}', [TravelAuthorizationController::class, 'update'])->name('ta.requests.update');
        Route::delete('travel/authorization/request/{travel}', [TravelAuthorizationController::class, 'destroy'])
            ->name('ta.requests.destroy');
        Route::get('travel/authorization/request/{travel}/submit', [TravelAuthorizationController::class, 'submittravel'])
            ->name('ta.requests.submit');

        Route::get('travel/authorization/requests/{travel}/officials', [OfficialController::class, 'index'])
            ->name('ta.requests.official.index');
        Route::get('travel/authorization/requests/{travel}/official/create', [OfficialController::class, 'create'])
            ->name('ta.requests.official.create');
        Route::post('travel/authorization/requests/{travel}/official', [OfficialController::class, 'store'])
            ->name('ta.requests.official.store');
        Route::get('travel/authorization/requests/{travel}/official/{official}/edit', [OfficialController::class, 'edit'])
            ->name('ta.requests.official.edit');
        Route::put('travel/authorization/requests/official/{official}', [OfficialController::class, 'update'])
            ->name('ta.requests.official.update');
        Route::delete('travel/authorization/requests/{travel}/official/{official}', [OfficialController::class, 'destroy'])
            ->name('ta.requests.official.destroy');

        Route::get('travel/authorization/requests/{travel}/itineraries', [ItineraryController::class, 'index'])
            ->name('ta.requests.itinerary.index');
        Route::get('travel/authorization/requests/{travel}/itinerary/create', [ItineraryController::class, 'create'])
            ->name('ta.requests.itinerary.create');
        Route::post('travel/authorization/requests/{travel}/itinerary', [ItineraryController::class, 'store'])
            ->name('ta.requests.itinerary.store');
        Route::get('travel/authorization/requests/{travel}/itinerary/{itinerary}/edit', [ItineraryController::class, 'edit'])
            ->name('ta.requests.itinerary.edit');
        Route::put('travel/authorization/requests/{travel}/itinerary/{itinerary}', [ItineraryController::class, 'update'])
            ->name('ta.requests.itinerary.update');
        Route::delete('travel/authorization/requests/{travel}/itinerary/{itinerary}', [ItineraryController::class, 'destroy'])
            ->name('ta.requests.itinerary.destroy');

        Route::get('travel/authorization/requests/{travel}/estimates', [EstimateController::class, 'index'])
            ->name('ta.requests.estimate.index');
        Route::get('travel/authorization/requests/{travel}/estimate/create', [EstimateController::class, 'create'])
            ->name('ta.requests.estimate.create');
        Route::post('travel/authorization/requests/{travel}/estimate', [EstimateController::class, 'store'])
            ->name('ta.requests.estimate.store');
        Route::get('travel/authorization/requests/{travel}/estimate/{estimate}/edit', [EstimateController::class, 'edit'])
            ->name('ta.requests.estimate.edit');
        Route::put('travel/authorization/requests/estimate/{estimate}', [EstimateController::class, 'update'])
            ->name('ta.requests.estimate.update');
        Route::delete('travel/authorization/requests/{travel}/estimate/{estimate}', [EstimateController::class, 'destroy'])
            ->name('ta.requests.estimate.destroy');

        Route::post('travel/authorization/requests/{leave}/amend', [TravelAuthorizationController::class, 'amend'])->name('ta.requests.amend.store');
        // Route::get('travel/authorization/requests/{leave}/cancel', [travelController::class, 'cancelCreate'])->name('ta.requests.cancel.create');
        // Route::post('travel/authorization/requests/{leave}/cancel', [travelController::class, 'cancel'])->name('ta.requests.cancel.store');
    });
    Route::get('travel/authorization/requests/{travel}/view', [TravelAuthorizationController::class, 'view'])->name('ta.requests.view');
    Route::middleware(['can:approve-travel-authorization'])->group(function () {
        Route::get('approve/travel/authorization/requests', [ApproveController::class, 'index'])
            ->name('approve.ta.requests.index');
        Route::get('approve/travel/authorization/requests/{travel}/create', [ApproveController::class, 'create'])
            ->name('approve.ta.requests.create');
        Route::post('approve/travel/authorization/requests/{travel}', [ApproveController::class, 'store'])
            ->name('approve.ta.requests.store');

        Route::get('approve/travel/authorization/requests/cancel', [ApproveController::class, 'cancelIndex'])
            ->name('approve.ta.requests.cancel.index');
        Route::get('approve/travel/authorization/requests/{travel}/cancel', [ApproveController::class, 'cancelCreate'])
            ->name('approve.ta.requests.cancel.create');
        Route::post('approve/travel/authorization/requests/{travel}/cancel', [ApproveController::class, 'cancel'])
            ->name('approve.ta.requests.cancel.store');
    });

});

Route::middleware(['web', 'auth', 'logger'])->group(function () {
    Route::middleware('can:view-approved-travel-authorization')->group(function () {
        Route::get('approved/travel/authorization/requests', [ApprovedController::class, 'index'])->name('approved.ta.requests.index');
        Route::get('approved/travel/authorization/requests/{travelAuthorization}/show', [ApprovedController::class, 'show'])->name('approved.ta.requests.show');
    });

    Route::get('travel/authorization/requests/{travelAuthorization}/print', [ApprovedController::class, 'print'])->name('ta.request.print');
});
