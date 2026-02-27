<?php

/*
|--------------------------------------------------------------------------
| Application Routes for Travel Request Module
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

use Modules\TravelRequest\Controllers\ClaimController;
use Modules\TravelRequest\Controllers\ClaimDsaController;
use Modules\TravelRequest\Controllers\ClaimReviewController;
use Modules\TravelRequest\Controllers\LocalTravelController;
use Modules\TravelRequest\Controllers\ClaimApproveController;
use Modules\TravelRequest\Controllers\ClaimExpenseController;
use Modules\TravelRequest\Controllers\ClaimPaymentController;
use Modules\TravelRequest\Controllers\TravelReportController;
use Modules\TravelRequest\Controllers\ClaimApprovedController;
use Modules\TravelRequest\Controllers\TravelRequestController;
use Modules\TravelRequest\Controllers\ClaimItineraryController;
use Modules\TravelRequest\Controllers\ClaimLocalTravelController;
use Modules\TravelRequest\Controllers\LocalTravelApproveController;
use Modules\TravelRequest\Controllers\LocalTravelPaymentController;
use Modules\TravelRequest\Controllers\TravelReportReviewController;
use Modules\TravelRequest\Controllers\LocalTravelApprovedController;
use Modules\TravelRequest\Controllers\LocalTravelItineraryController;
use Modules\TravelRequest\Controllers\TravelReportApprovedController;
use Modules\TravelRequest\Controllers\TravelRequestAdvanceController;
use Modules\TravelRequest\Controllers\TravelRequestApproveController;
use Modules\TravelRequest\Controllers\TravelRequestApprovedController;
use Modules\TravelRequest\Controllers\TravelRequestEstimateController;
use Modules\TravelRequest\Controllers\TravelRequestItineraryController;
use Modules\TravelRequest\Controllers\TravelRequestDayItineraryController;

Route::middleware(['web', 'auth', 'logger'])->group(function () {
    Route::middleware('can:travel-request')->group(function () {
        Route::get('travel/requests', [TravelRequestController::class, 'index'])->name('travel.requests.index');
        Route::get('travel/requests/create', [TravelRequestController::class, 'create'])->name('travel.requests.create');
        Route::post('travel/requests', [TravelRequestController::class, 'store'])->name('travel.requests.store');
        Route::get('travel/requests/{travelRequest}/edit', [TravelRequestController::class, 'edit'])->name('travel.requests.edit');
        Route::put('travel/requests/{travelRequest}', [TravelRequestController::class, 'update'])->name('travel.requests.update');
        Route::delete('travel/request/{travelRequest}', [TravelRequestController::class, 'destroy'])
            ->name('travel.requests.destroy');
        Route::get('travel/request/{travelRequest}/submit', [TravelRequestController::class, 'submitTravelRequest'])
            ->name('travel.requests.submit');

        Route::get('travel/requests/{travelRequest}/itinerary/create', [TravelRequestItineraryController::class, 'create'])
            ->name('travel.requests.itinerary.create');
        Route::post('travel/requests/{travelRequest}/itinerary', [TravelRequestItineraryController::class, 'store'])
            ->name('travel.requests.itinerary.store');
        Route::get('travel/requests/{travelRequest}/itinerary/{itinerary}/edit', [TravelRequestItineraryController::class, 'edit'])
            ->name('travel.requests.itinerary.edit');
        Route::put('travel/requests/{travelRequest}/itinerary/{itinerary}', [TravelRequestItineraryController::class, 'update'])
            ->name('travel.requests.itinerary.update');
        Route::delete('travel/requests/{travelRequest}/itinerary/{itinerary}', [TravelRequestItineraryController::class, 'destroy'])
            ->name('travel.requests.itinerary.destroy');

        Route::get('travel/requests/{travelRequest}/estimate/create', [TravelRequestEstimateController::class, 'create'])
            ->name('travel.requests.estimate.create');
        Route::post('travel/requests/{travelRequest}/estimate', [TravelRequestEstimateController::class, 'store'])
            ->name('travel.requests.estimate.store');
        Route::get('travel/requests/{travelRequest}/estimate/{estimate}/edit', [TravelRequestEstimateController::class, 'edit'])
            ->name('travel.requests.estimate.edit');
        Route::put('travel/requests/{travelRequest}/estimate/{estimate}', [TravelRequestEstimateController::class, 'update'])
            ->name('travel.requests.estimate.update');
        Route::delete('travel/requests/{travelRequest}/estimate/{estimate}', [TravelRequestEstimateController::class, 'destroy'])
            ->name('travel.requests.estimate.destroy');

        Route::get('travel/requests/{travelRequest}/day-itinerary', [TravelRequestDayItineraryController::class, 'index'])
            ->name('travel.requests.day-itinerary.index');
        Route::post('travel/requests/{travelRequest}/day-itinerary', [TravelRequestDayItineraryController::class, 'store'])
            ->name('travel.requests.day-itinerary.store');
        Route::put('travel/requests/{travelRequest}/day-itinerary/{dayItinerary}', [TravelRequestDayItineraryController::class, 'update'])
            ->name('travel.requests.day-itinerary.update');
        Route::delete('travel/requests/{travelRequest}/day-itinerary/{dayItinerary}', [TravelRequestDayItineraryController::class, 'destroy'])
            ->name('travel.requests.day-itinerary.destroy');

        Route::post('travel/requests/{travel}/amend', [TravelRequestController::class, 'amend'])->name('travel.requests.amend.store');
        Route::post('travel/requests/{travel}/advance', [TravelRequestController::class, 'advance'])->name('travel.requests.advance.store');
        Route::get('travel/requests/{leave}/cancel', [TravelRequestController::class, 'cancelCreate'])->name('travel.requests.cancel.create');
        Route::post('travel/requests/{leave}/cancel', [TravelRequestController::class, 'cancel'])->name('travel.requests.cancel.store');
    });
    Route::get('travel/requests/{travelRequest}/view', [TravelRequestController::class, 'view'])->name('travel.requests.view');
    Route::get('travel/requests/{travelRequest}/itineraries', [TravelRequestItineraryController::class, 'index'])
        ->name('travel.requests.itinerary.index');
    Route::get('travel/requests/{travelRequest}/estimates', [TravelRequestEstimateController::class, 'index'])
        ->name('travel.requests.estimate.index');

    Route::middleware(['can:approve-travel-form'])->group(function () {
        Route::get('approve/travel/requests', [TravelRequestApproveController::class, 'index'])
            ->name('approve.travel.requests.index');
        Route::get('approve/travel/requests/{requests}/create', [TravelRequestApproveController::class, 'create'])
            ->name('approve.travel.requests.create');
        Route::post('approve/travel/requests/{requests}', [TravelRequestApproveController::class, 'store'])
            ->name('approve.travel.requests.store');

        Route::get('approve/travel/requests/cancel', [TravelRequestApproveController::class, 'cancelIndex'])
            ->name('approve.travel.requests.cancel.index');
        Route::get('approve/travel/requests/{travelRequest}/cancel', [TravelRequestApproveController::class, 'cancelCreate'])
            ->name('approve.travel.requests.cancel.create');
        Route::post('approve/travel/requests/{travelRequest}/cancel', [TravelRequestApproveController::class, 'cancel'])
            ->name('approve.travel.requests.cancel.store');
    });

    Route::get('approve/travel/requests/advance', [TravelRequestAdvanceController::class, 'index'])
        ->name('approve.travel.requests.advance.index');
    Route::get('approve/travel/requests/{requests}/advance/create', [TravelRequestAdvanceController::class, 'create'])
        ->name('approve.travel.requests.advance.create');
    Route::post('approve/travel/requests/{requests}/advance', [TravelRequestAdvanceController::class, 'store'])
        ->name('approve.travel.requests.advance.store');

    Route::middleware('can:travel-request')->group(function () {
        Route::get('travel/reports', [TravelReportController::class, 'index'])->name('travel.reports.index');
        Route::get('travel/{travelRequestId}/reports/create', [TravelReportController::class, 'create'])->name('travel.reports.create');
        Route::post('travel/{travelRequestId}/reports', [TravelReportController::class, 'store'])->name('travel.reports.store');
        Route::get('travel/reports/{report}/edit', [TravelReportController::class, 'edit'])->name('travel.reports.edit');
        Route::put('travel/reports/{report}', [TravelReportController::class, 'update'])->name('travel.reports.update');
        Route::delete('travel/reports/{report}', [TravelReportController::class, 'destroy'])->name('travel.reports.destroy');
    });
    Route::get('travel/reports/{report}/show', [TravelReportController::class, 'show'])->name('travel.reports.show');

    Route::middleware('can:approve-travel-report')->group(function () {
        Route::get('approve/travel/reports', [TravelReportReviewController::class, 'index'])
            ->name('approve.travel.reports.index');
        Route::get('approve/travel/reports/{travelRequest}/create', [TravelReportReviewController::class, 'create'])
            ->name('approve.travel.reports.create');
        Route::post('approve/travel/reports/{reports}', [TravelReportReviewController::class, 'store'])
            ->name('approve.travel.reports.store');
    });

    Route::middleware('can:travel-request')->group(function () {
        Route::get('travel/claims', [ClaimController::class, 'index'])->name('travel.claims.index');
        Route::post('travel/{travelRequestId}/claims', [ClaimController::class, 'store'])->name('travel.claims.store');
        Route::get('travel/claims/{claims}/edit', [ClaimController::class, 'edit'])->name('travel.claims.edit');
        Route::put('travel/claims/{claims}', [ClaimController::class, 'update'])->name('travel.claims.update');
        Route::delete('travel/claims/{claims}', [ClaimController::class, 'destroy'])->name('travel.claims.destroy');
        Route::get('travel/claims/{claims}/view', [ClaimController::class, 'view'])->name('travel.claims.view');

        Route::get('travel/claims/{travelClaim}/expenses/create', [ClaimExpenseController::class, 'create'])->name('travel.claims.expenses.create');
        Route::post('travel/claims/{travelClaim}/expenses', [ClaimExpenseController::class, 'store'])->name('travel.claims.expenses.store');
        Route::get('travel/claims/{travelClaim}/expenses/{expense}/edit', [ClaimExpenseController::class, 'edit'])->name('travel.claims.expenses.edit');
        Route::post('travel/claims/{travelClaim}/expenses/{expense}', [ClaimExpenseController::class, 'update'])->name('travel.claims.expenses.update');
        Route::delete('travel/claims/{travelClaim}/expenses/{expense}/destroy', [ClaimExpenseController::class, 'destroy'])->name('travel.claims.expenses.destroy');

        Route::get('travel/claims/{travelClaim}/local/travel/create', [ClaimLocalTravelController::class, 'create'])->name('travel.claims.local.travel.create');
        Route::post('travel/claims/{travelClaim}/local/travel', [ClaimLocalTravelController::class, 'store'])->name('travel.claims.local.travel.store');
        Route::get('travel/claims/{travelClaim}/local/travel/{localTravel}/edit', [ClaimLocalTravelController::class, 'edit'])->name('travel.claims.local.travel.edit');
        Route::post('travel/claims/{travelClaim}/local/travel/{localTravel}', [ClaimLocalTravelController::class, 'update'])->name('travel.claims.local.travel.update');
        Route::delete('travel/claims/{travelClaim}/local/travel/{localTravel}/destroy', [ClaimLocalTravelController::class, 'destroy'])->name('travel.claims.local.travel.destroy');

        Route::get('travel/claims/{travelClaim}/dsa/create', [ClaimDsaController::class, 'create'])->name('travel.claims.dsa.create');
        Route::post('travel/claims/{travelClaim}/dsa', [ClaimDsaController::class, 'store'])->name('travel.claims.dsa.store');
        Route::get('travel/claims/{travelClaim}/dsa/{dsa}/edit', [ClaimDsaController::class, 'edit'])->name('travel.claims.dsa.edit');
        Route::post('travel/claims/{travelClaim}/dsa/{dsa}', [ClaimDsaController::class, 'update'])->name('travel.claims.dsa.update');
        Route::delete('travel/claims/{travelClaim}/dsa/{dsa}/destroy', [ClaimDsaController::class, 'destroy'])->name('travel.claims.dsa.destroy');

        Route::get('travel/claims/{travelClaim}/itineraries/{expense}/edit', [ClaimItineraryController::class, 'edit'])->name('travel.claims.itineraries.edit');
        Route::post('travel/claims/{travelClaim}/itineraries/{expense}', [ClaimItineraryController::class, 'update'])->name('travel.claims.itineraries.update');
    });
    Route::get('travel/claims/{travelClaim}/dsa', [ClaimDsaController::class, 'index'])->name('travel.claims.dsa.index');
    Route::get('travel/claims/{travelClaim}/local/travel', [ClaimLocalTravelController::class, 'index'])->name('travel.claims.local.travel.index');
    Route::get('travel/claims/{travelClaim}/expenses', [ClaimExpenseController::class, 'index'])->name('travel.claims.expenses.index');
    Route::get('travel/claims/{travelClaim}/itineraries', [ClaimItineraryController::class, 'index'])->name('travel.claims.itineraries.index');

    Route::middleware('can:finance-review-travel-claim')->group(function () {
        Route::get('review/travel/claims', [ClaimReviewController::class, 'index'])
            ->name('review.travel.claims.index');
        Route::get('review/travel/claims/{claim}/create', [ClaimReviewController::class, 'create'])
            ->name('review.travel.claims.create');
        Route::post('review/travel/claims/{claim}', [ClaimReviewController::class, 'store'])
            ->name('review.travel.claims.store');
    });
    Route::middleware('can:approve-travel-claim')->group(function () {
        Route::get('approve/travel/claims', [ClaimApproveController::class, 'index'])
            ->name('approve.travel.claims.index');
        Route::get('approve/travel/claims/{claim}/create', [ClaimApproveController::class, 'create'])
            ->name('approve.travel.claims.create');
        Route::post('approve/travel/claims/{claim}', [ClaimApproveController::class, 'store'])
            ->name('approve.travel.claims.store');
    });

    Route::get('approved/travel/claims', [ClaimApprovedController::class, 'index'])
        ->name('approved.travel.claims.index');
    Route::get('approved/travel/claims/{id}/print', [ClaimApprovedController::class, 'print'])
        ->name('approved.travel.claims.print');

});

Route::middleware(['web', 'auth', 'logger'])->group(function () {
    Route::middleware('can:local-travel')->group(function () {
        Route::get('local/travel/reimbursements', [LocalTravelController::class, 'index'])->name('local.travel.reimbursements.index');
        Route::get('local/travel/reimbursements/create', [LocalTravelController::class, 'create'])->name('local.travel.reimbursements.create');
        Route::post('local/travel/reimbursements', [LocalTravelController::class, 'store'])->name('local.travel.reimbursements.store');
        Route::get('local/travel/reimbursements/{localTravel}/edit', [LocalTravelController::class, 'edit'])->name('local.travel.reimbursements.edit');
        Route::put('local/travel/reimbursements/{localTravel}', [LocalTravelController::class, 'update'])->name('local.travel.reimbursements.update');
        Route::delete('local/travel/reimbursements/{localTravel}', [LocalTravelController::class, 'destroy'])->name('local.travel.reimbursements.destroy');

        Route::get('local/travel/reimbursements/{localTravel}/itineraries/create', [LocalTravelItineraryController::class, 'create'])->name('local.travel.reimbursements.itineraries.create');
        Route::post('local/travel/reimbursements/{localTravel}/itineraries', [LocalTravelItineraryController::class, 'store'])->name('local.travel.reimbursements.itineraries.store');
        Route::get('local/travel/reimbursements/{localTravel}/itineraries/{itinerary}/edit', [LocalTravelItineraryController::class, 'edit'])->name('local.travel.reimbursements.itineraries.edit');
        Route::put('local/travel/reimbursements/{localTravel}/itineraries/{itinerary}', [LocalTravelItineraryController::class, 'update'])->name('local.travel.reimbursements.itineraries.update');
        Route::delete('local/travel/reimbursements/{localTravel}/itineraries/{itinerary}', [LocalTravelItineraryController::class, 'destroy'])->name('local.travel.reimbursements.itineraries.destroy');
    });
    Route::get('local/travel/reimbursements/{localTravel}/show', [LocalTravelController::class, 'show'])->name('local.travel.reimbursements.show');
    Route::get('local/travel/reimbursements/{localTravel}/itineraries', [LocalTravelItineraryController::class, 'index'])->name('local.travel.reimbursements.itineraries.index');

    Route::middleware('can:approve-local-travel')->group(function () {
        Route::get('approve/local/travel/reimbursements', [LocalTravelApproveController::class, 'index'])->name('approve.local.travel.reimbursements.index');
        Route::get('approve/local/travel/reimbursements/{localTravel}/create', [LocalTravelApproveController::class, 'create'])->name('approve.local.travel.reimbursements.create');
        Route::post('approve/local/travel/reimbursements/{localTravel}', [LocalTravelApproveController::class, 'store'])->name('approve.local.travel.reimbursements.store');
    });

    Route::middleware('can:view-approved-local-travel')->group(function () {
        Route::get('approved/local/travel/reimbursements', [LocalTravelApprovedController::class, 'index'])->name('approved.local.travel.reimbursements.index');
    });

    Route::middleware('can:pay-local-travel')->group(function () {
        Route::get('approved/local/travel/reimbursements/{localTravel}/pay/create', [LocalTravelPaymentController::class, 'create'])->name('approved.local.travel.reimbursements.pay.create');
        Route::post('approved/local/travel/reimbursements/{localTravel}/pay', [LocalTravelPaymentController::class, 'store'])->name('approved.local.travel.reimbursements.pay.store');
        Route::get('paid/local/travel', [LocalTravelPaymentController::class, 'index'])->name('paid.local.travel.reimbursements.index');
        Route::get('paid/local/travel/{localTravel}/show', [LocalTravelPaymentController::class, 'show'])->name('paid.local.travel.reimbursements.show');
    });

    Route::middleware('can:view-approved-travel-request')->group(function () {
        Route::get('approved/travel/requests', [TravelRequestApprovedController::class, 'index'])->name('approved.travel.requests.index');
        Route::get('approved/travel/ticket/requests', [TravelRequestApprovedController::class, 'ticketIndex'])->name('approved.travel.ticket.requests.index');
        Route::get('approved/travel/requests/{travelRequest}/show', [TravelRequestApprovedController::class, 'show'])->name('approved.travel.requests.show');

        Route::get('approved/travel/reports', [TravelReportApprovedController::class, 'index'])->name('approved.travel.reports.index');
        Route::get('approved/travel/reports/{id}/show', [TravelReportApprovedController::class, 'show'])->name('approved.travel.reports.show');
    });

    Route::get('travel/requests/{travelRequest}/print', [TravelRequestApprovedController::class, 'print'])->name('travel.request.print');
    Route::get('travel/reports/{id}/print', [TravelReportApprovedController::class, 'print'])->name('travel.report.print');
    Route::get('travel/claims/{id}/print', [ClaimController::class, 'print'])->name('travel.claim.print');
    Route::get('local/travel/reimbursements/{id}/print', [LocalTravelApprovedController::class, 'print'])->name('local.travel.reimbursements.print');

    Route::middleware('can:pay-travel-claim')->group(function () {
        Route::get('approved/travel/claims/{travelClaim}/pay/create', [ClaimPaymentController::class, 'create'])->name('approved.travel.claims.pay.create');
        Route::post('approved/travel/claims/{travelClaim}/pay', [ClaimPaymentController::class, 'store'])->name('approved.travel.claims.pay.store');
        Route::get('paid/travel/claims', [ClaimPaymentController::class, 'index'])->name('paid.travel.claims.index');
        Route::get('paid/travel/claims/{travelClaim}/show', [ClaimPaymentController::class, 'show'])->name('paid.travel.claims.show');
    });
});
