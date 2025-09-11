@extends('layouts.container')

@section('title', 'Approve Travel Request Advance')

@section('page_js')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#navbarVerticalMenu').find('#approve-travel-advance-menu').addClass('active');
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
                    name: 'activity'
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

        document.addEventListener('DOMContentLoaded', function(e) {
            const form = document.getElementById('travelRequestAddForm');
            const fv = FormValidation.formValidation(form, {
                fields: {
                    received_advance_amount: {
                        validators: {
                            notEmpty: {
                                message: 'Amount is required',
                            },
                        },
                    },
                    advance_received_at: {
                        validators: {
                            notEmpty: {
                                message: 'Advance date is required.',
                            },
                        },
                    },
                    finance_remarks: {
                        validators: {
                            notEmpty: {
                                message: 'Remarks is required.',
                            },
                        },
                    },
                },
                plugins: {
                    trigger: new FormValidation.plugins.Trigger(),
                    bootstrap5: new FormValidation.plugins.Bootstrap5(),
                    submitButton: new FormValidation.plugins.SubmitButton(),
                    defaultSubmit: new FormValidation.plugins.DefaultSubmit(),
                    icon: new FormValidation.plugins.Icon({
                        valid: 'bi bi-check2-square',
                        invalid: 'bi bi-x-lg',
                        validating: 'bi bi-arrow-repeat',
                    }),
                },
            });

            $(form.querySelector('[name="advance_received_at"]')).datepicker({
                language: 'en-GB',
                autoHide: true,
                format: 'yyyy-mm-dd',
            }).on('change', function(e) {
                fv.revalidateField('advance_received_at');
            });
        });
    </script>
@endsection
@section('page-content')


    <div class="pb-3 mb-3 page-header border-bottom">
        <div class="d-flex align-items-center">
            <div class="brd-crms flex-grow-1">
                <nav aria-label="breadcrumb">
                    <ol class="m-0 breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{!! route('dashboard.index') !!}" class="text-decoration-none text-dark">Home</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('approve.travel.requests.index') }}"
                                class="text-decoration-none text-dark">Travel
                                Request</a>
                        </li>
                        <li class="breadcrumb-item" aria-current="page">@yield('title')</li>
                    </ol>
                </nav>
                <h4 class="m-0 mt-1 lh1 fs-6 text-uppercase fw-bold text-primary">@yield('title')</h4>
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
                    <div class="card-body">
                        @include('TravelRequest::Partials.detail')
                    </div>
                </div>
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
            </div>
            <div class="card">
                <div class="card-header fw-bold"></div>
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
                    <div class="pt-4 border-top">
                        <form action="{{ route('approve.travel.requests.advance.store', $travelRequest->id) }}"
                            id="travelRequestAddForm" method="post" enctype="multipart/form-data" autocomplete="off">
                            <div class="card-body">
                                <div class="mb-2 row">
                                    <div class="col-lg-3">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="validationleavetype" class="form-label">Requested
                                                Advance Amount</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-9">
                                        <div class="row">
                                            <div class="col-lg">
                                                <input type="text" class="form-control"
                                                    value="{{ $travelRequest->formattedRequestedAmount() }}" disabled>
                                            </div>
                                            <div class="col-lg">
                                                <div class="row">
                                                    <div class="col-lg">
                                                        <label for="validationleavetype" class="form-label">Requested
                                                            At</label>
                                                    </div>
                                                    <div class="col-lg">
                                                        <input type="text" class="form-control"
                                                            value="{{ $travelRequest->getAdvanceRequestDate() }}" disabled>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>

                                <div class="mb-2 row">
                                    <div class="col-lg-3">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="validationleavetype" class="form-label required-label">Advance
                                                Amount</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-9">
                                        <input type="text" class="form-control" name="received_advance_amount"
                                            value="{{old('received_advance_amount') ?: 0}}">
                                    </div>
                                </div>

                                <div class="mb-2 row">
                                    <div class="col-lg-3">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="validationleavetype" class="form-label required-label">Advance
                                                Assigned Date</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-9">
                                        <input type="text" value="{{old('advance_received_at')}}" class="form-control" name="advance_received_at" readonly>
                                        @if ($errors->has('advance_received_at'))
                                            <div class="fv-plugins-message-container invalid-feedback">
                                                <div data-field="advance_received_at">
                                                    {!! $errors->first('advance_received_at') !!}
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <div class="mb-2 row">
                                    <div class="col-lg-3">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="validationRemarks"
                                                class="form-label required-label">Remarks</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-9">
                                        <textarea type="text" class="form-control @if ($errors->has('finance_remarks')) is-invalid @endif" name="finance_remarks">{{ old('finance_remarks') }}</textarea>
                                        @if ($errors->has('finance_remarks'))
                                            <div class="fv-plugins-message-container invalid-feedback">
                                                <div data-field="finance_remarks">{!! $errors->first('finance_remarks') !!}</div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                {!! csrf_field() !!}
                            </div>
                            <div class="gap-2 justify-content-end d-flex">
                                <button type="submit" name="btn" value="submit" class="btn btn-success btn-sm">
                                    Submit
                                </button>
                                <a href="{!! route('approve.travel.requests.index') !!}" class="btn btn-danger btn-sm">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@stop
