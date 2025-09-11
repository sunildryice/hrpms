@extends('layouts.container')

@section('title', 'View Travel Request')

@section('page_js')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#navbarVerticalMenu').find('#approved-travel-request-menu').addClass('active');
        });

        var itineraryTable = $('#itineraryTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('travel.requests.itinerary.index', $travelRequest->id) }}",
            bFilter: false,
            bPaginate: false,
            bInfo: false,
            columns: [{
                    data: 'departure_date',
                    name: 'departure_date'
                },
                {
                    data: 'departure_place',
                    name: 'departure_place'
                },
                {
                    data: 'arrival_date',
                    name: 'arrival_date'
                },
                {
                    data: 'arrival_place',
                    name: 'arrival_place'
                },
                {
                    data: 'mode_of_travel',
                    name: 'mode_of_travel',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'description',
                    name: 'description',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'activity',
                    name: 'activity',
                },
                {
                    data: 'donor',
                    name: 'donor',
                },
                {
                    data: 'dsa_category',
                    name: 'dsa_category',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'dsa_unit_price',
                    name: 'dsa_unit_price'
                },
                {
                    data: 'dsa_total_price',
                    name: 'dsa_total_price'
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
                    data: 'miscellaneous_amount',
                    name: 'miscellaneous_amount'
                },
                {
                    data: 'miscellaneous_remarks',
                    name: 'miscellaneous_remarks'
                },
                {
                    data: 'total_amount',
                    name: 'total_amount'
                },
                {
                    data: 'advance_amount',
                    name: 'advance_amount'
                },
            ]
        });
    </script>
@endsection
@section('page-content')



            <div class="page-header pb-3 mb-3 border-bottom">
                <div class="d-flex align-items-center">
                    <div class="brd-crms flex-grow-1">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="{!! route('dashboard.index') !!}"
                                        class="text-decoration-none text-dark">Home</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('approved.travel.requests.index') }}"
                                        class="text-decoration-none text-dark">Travel Request</a></li>
                                <li class="breadcrumb-item" aria-current="page">View Travel Request</li>
                            </ol>
                        </nav>
                        <h4 class="m-0 lh1 mt-1 fs-6 text-uppercase fw-bold text-primary">View Travel Request</h4>
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
                            <a class="btn btn-sm btn-primary mt-2"
                                href="{{ route('travel.requests.view', $travelRequest->parentTravelRequest->id) }}"
                                target="_blank" title="Travel Request">View Amended Travel Request</a>
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
                                            <tr>
                                                <th scope="col">{{ __('label.from-date') }}</th>
                                                <th scope="col">{{ __('label.from-place') }}</th>
                                                <th scope="col">{{ __('label.to-date') }}</th>
                                                <th scope="col">{{ __('label.to-place') }}</th>
                                                <th scope="col">{{ __('label.mode-of-travel') }}</th>
                                                <th scope="col">{{ __('label.description') }}</th>
                                                <th scope="col">{{ __('label.activity') }}</th>
                                                <th scope="col">{{ __('label.donor') }}</th>
                                                <th scope="col">{{ __('label.dsa-category') }}</th>
                                                <th scope="col">{{ __('label.dsa-rate') }}</th>
                                                <th scope="col">{{ __('label.total-dsa') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header fw-bold">
                                Travel Cost Estimation
                            </div>
                            <div class='card-body'>
                                <div class="table-responsive">
                                    <table class="table" id="estimationTable">
                                        <thead class="thead-light">
                                            <tr>
                                                <th scope="col">{{ __('label.estimated-dsa') }}</th>
                                                <th scope="col">{{ __('label.estimated-air-fare') }}</th>
                                                <th scope="col">{{ __('label.estimated-vehicle-fare') }}</th>
                                                <th scope="col">{{ __('label.miscellaneous-amount') }}</th>
                                                <th scope="col">{{ __('label.miscellaneous-remarks') }}</th>
                                                <th scope="col">{{ __('label.total-amount') }}</th>
                                                <th scope="col">{{ __('label.advance-amount') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header fw-bold">Process</div>
                            <div class="card-body">
                                    <div class="c-b">
                                        @foreach ($travelRequest->logs as $log)
                                            <div class="d-flex py-2 flex-row gap-2 mb-2 border-bottom ">
                                                <div width="40" height="40" class="rounded-circle mr-3 user-icon">
                                                    <i class="bi-person-circle fs-5"></i>
                                                </div>
                                                <div class="w-100">
                                                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                                                        <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center gap-md-2 mb-2 mb-md-0">
                                                            <label class="form-label mb-0">{{ $log->getCreatedBy() }}</label>
                                                            <span
                                                                class="badge bg-primary c-badge">{!! $log->createdBy->employee->latestTenure->getDesignationName() !!}</span>
                                                        </div>
                                                        <small title="{{$log->created_at}}">{{ $log->created_at->format('M d, Y h:i A') }}</small>
                                                    </div>
                                                    <p class="text-justify comment-text mb-0 mt-1">
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
