@extends('layouts.container')

@section('title', 'View Travel Request')

@section('page_js')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#navbarVerticalMenu').find('#travel-request-menu').addClass('active');
        });

        var itineraryTable = $('#itineraryTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('travel.requests.day-itinerary.index', $travelRequest->id) }}",
            bFilter: false,
            bPaginate: false,
            bInfo: false,
            columns: [{
                    data: 'date',
                    name: 'date'
                },
                {
                    data: 'planned_activities',
                    name: 'planned_activities'
                },
                {
                    data: 'accommodation',
                    name: 'accommodation',
                    orderable: false,
                    searchable: false,
                },

                {
                    data: 'air_ticket',
                    name: 'air_ticket',
                    orderable: false,
                    searchable: false,
                },
                {
                    data: 'vehicle',
                    name: 'vehicle',
                    orderable: false,
                    searchable: false,
                },
            ]
        });

        var estimateTable = $('#estimationTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('travel.requests.estimate.index', $travelRequest->id) }}",
            bFilter: false,
            bPaginate: false,
            bInfo: false,
            columns: [{
                    data: 'estimated_dsa',
                    name: 'estimated_dsa'
                },
                {
                    data: 'estimated_air_fare',
                    name: 'estimated_air_fare'
                },
                {
                    data: 'estimated_vehicle_fare',
                    name: 'estimated_vehicle_fare'
                },
                {
                    data: 'estimated_hotel_accommodation',
                    name: 'estimated_hotel_accommodation'
                },
                {
                    data: 'estimated_airport_taxi',
                    name: 'estimated_airport_taxi'
                },
                {
                    data: 'miscellaneous_amount',
                    name: 'miscellaneous_amount'
                },
                {
                    data: 'estimated_event_activities_cost',
                    name: 'estimated_event_activities_cost'
                },
                {
                    data: 'miscellaneous_remarks',
                    name: 'miscellaneous_remarks'
                },
                {
                    data: 'total_amount',
                    name: 'total_amount'
                },
            ]
        });
    </script>
@endsection
@section('page-content')



    <div class="pb-3 mb-3 page-header border-bottom">
        <div class="d-flex align-items-center">
            <div class="brd-crms flex-grow-1">
                <nav aria-label="breadcrumb">
                    <ol class="m-0 breadcrumb">
                        <li class="breadcrumb-item"><a href="{!! route('dashboard.index') !!}"
                                class="text-decoration-none text-dark">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('travel.requests.index') }}"
                                class="text-decoration-none text-dark">Travel Request</a></li>
                        <li class="breadcrumb-item" aria-current="page">View Travel Request</li>
                    </ol>
                </nav>
                <h4 class="m-0 mt-1 lh1 fs-6 text-uppercase fw-bold text-primary">View Travel Request</h4>
            </div>
        </div>
    </div>
    <section class="registration">
        <div class="row">
            <div class="col-lg-3">
                <div class="card">
                    <div class="card-header fw-bold">
                        Travel Information
                    </div>
                    @include('TravelRequest::Partials.detail')
                </div>
                @if ($travelRequest->parentTravelRequest)
                    <a class="mt-2 btn btn-sm btn-primary"
                        href="{{ route('travel.requests.view', $travelRequest->parentTravelRequest->id) }}" target="_blank"
                        title="Travel Request">View Amended Travel Request</a>
                @endif
            </div>
            <div class="col-lg-9">
                <div class="card">
                    <div class="card-header fw-bold">
                        Travel Itinerary
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table" id="itineraryTable">
                                <thead class="thead-light">
                                    <th style="width: 120px;">{{ __('label.date') }}</th>
                                    <th>{{ __('label.planned-activities') }}</th>
                                    <th class="text-center">{{ __('label.accommodation') }}</th>
                                    <th class="text-center">{{ __('label.air-ticket') }}</th>
                                    <th class="text-center">{{ __('label.vehicle') }}</th>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header fw-bold">
                        Travel Advance Request
                    </div>
                    <div class='card-body'>
                        <div class="table-responsive">
                            <table class="table" id="estimationTable">
                                <thead class="thead-light">
                                    <tr>
                                        <th scope="col">{{ __('label.estimated-dsa') }}</th>
                                        <th scope="col">{{ __('label.estimated-air-fare') }}</th>
                                        <th scope="col">{{ __('label.estimated-vehicle-fare') }}</th>
                                        <th scope="col">{{ __('label.estimated-hotel-accommodation') }}</th>
                                        <th scope="col">{{ __('label.estimated-airport-taxi') }}</th>
                                        <th scope="col">{{ __('label.miscellaneous-amount') }}</th>
                                        <th scope="col">{{ __('label.estimated-event-activities-cost') }}</th>
                                        <th scope="col">{{ __('label.miscellaneous-remarks') }}</th>
                                        <th scope="col">{{ __('label.total-amount') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header fw-bold">Travel Request process</div>
                    <div class="card-body">
                        <div class="c-b">
                            @foreach ($travelRequest->logs as $log)
                                <div class="flex-row gap-2 py-2 mb-2 d-flex border-bottom">
                                    <div width="40" height="40" class="mr-3 rounded-circle user-icon">
                                        <i class="bi-person-circle fs-5"></i>
                                    </div>
                                    <div class="w-100">
                                        <div
                                            class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                                            <div
                                                class="mb-2 d-flex flex-column flex-md-row align-items-start align-items-md-center gap-md-2 mb-md-0">
                                                <label class="mb-0 form-label">{{ $log->getCreatedBy() }}</label>
                                                <span class="badge bg-primary c-badge">{!! $log->createdBy->employee->latestTenure->getDesignationName() !!}</span>
                                            </div>
                                            <small
                                                title="{{ $log->created_at }}">{{ $log->created_at->format('M d, Y h:i A') }}</small>
                                        </div>
                                        <p class="mt-1 mb-0 text-justify comment-text">
                                            {{ $log->log_remarks }}
                                        </p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

@stop
