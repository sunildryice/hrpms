@extends('layouts.container')

@section('title', 'Review Travel Claim')

@section('page_js')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#navbarVerticalMenu').find('#review-travel-claims-menu').addClass('active');
        });
        var expenseTable = $('#expenseTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('travel.claims.expenses.index', $travelClaim->id) }}",
            bFilter: false,
            bPaginate: false,
            bInfo: false,
            columns: [{
                    data: 'activity',
                    name: 'activity'
                },
                // {
                //     data: 'donor',
                //     name: 'donor'
                // },
                {
                    data: 'expense_date',
                    name: 'expense_date'
                },
                {
                    data: 'expense_description',
                    name: 'expense_description'
                },
                {
                    data: 'expense_amount',
                    name: 'expense_amount'
                },
                {
                    data: 'invoice_bill_number',
                    name: 'invoice_bill_number'
                },
                // {
                //     data: 'charging_office',
                //     name: 'charging_office'
                // },
                {
                    data: 'attachment',
                    name: 'attachment',
                },
            ]
        });

        var claimLocalTravelTable = $('#claimLocalTravelTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('travel.claims.local.travel.index', $travelClaim->id) }}",
            bFilter: false,
            bPaginate: false,
            bInfo: false,
            columns: [{
                    data: 'travel_date',
                    name: 'travel_date'
                },
                {
                    data: 'purpose',
                    name: 'purpose'
                },
                {
                    data: 'departure_place',
                    name: 'departure_place'
                },
                {
                    data: 'arrival_place',
                    name: 'arrival_place'
                },
                {
                    data: 'travel_fare',
                    name: 'travel_fare'
                },
                {
                    data: 'remarks',
                    name: 'remarks'
                },
                {
                    data: 'attachment',
                    name: 'attachment'
                },
            ]
        });

        var itineraryTable = $('#itineraryTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('travel.claims.dsa.index', $travelClaim->id) }}",
            bFilter: false,
            bPaginate: false,
            bInfo: false,
            columns: [{
                    data: 'activities',
                    name: 'activities'
                },
                {
                    data: 'departure_place',
                    name: 'departure_place'
                },
                {
                    data: 'arrival_place',
                    name: 'arrival_place'
                },
                {
                    data: 'departure_date',
                    name: 'departure_date'
                },
                {
                    data: 'arrival_date',
                    name: 'arrival_date'
                },
                {
                    data: 'days_spent',
                    name: 'days_spent'
                },
                {
                    data: 'breakfast',
                    name: 'breakfast'
                },
                {
                    data: 'lunch',
                    name: 'lunch'
                },
                {
                    data: 'dinner',
                    name: 'dinner'
                },
                {
                    data: 'incident_cost',
                    name: 'incident_cost'
                },
                {
                    data: 'total_dsa',
                    name: 'total_dsa'
                },
                {
                    data: 'daily_allowance',
                    name: 'daily_allowance'
                },
                {
                    data: 'lodging_expense',
                    name: 'lodging_expense'
                },
                {
                    data: 'other_expense',
                    name: 'other_expense'
                },
                {
                    data: 'total_amount',
                    name: 'total_amount'
                },
                {
                    data: 'mode_of_travel',
                    name: 'mode_of_travel'
                },
                {
                    data: 'remarks',
                    name: 'remarks'
                },
                {
                    data: 'attachment',
                    name: 'attachment'
                },
            ]
        });

        document.addEventListener('DOMContentLoaded', function(e) {
            const form = document.getElementById('travelClaimReviewForm');
            const fv = FormValidation.formValidation(form, {
                fields: {
                    status_id: {
                        validators: {
                            notEmpty: {
                                message: 'Status is required',
                            },
                        },
                    },
                    log_remarks: {
                        validators: {
                            notEmpty: {
                                message: 'The remarks is required',
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

            $(form).on('change', '[name="status_id"]', function(e) {
                fv.revalidateField('status_id');
            });
        });
    </script>
@endsection
@section('page-content')


    <div class="page-header pb-3 mb-3 border-bottom">
        <div class="d-flex flex-column flex-lg-row align-items-start align-items-lg-center gap-2">
            <div class="brd-crms flex-grow-1">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item">
                            <a href="{!! route('dashboard.index') !!}" class="text-decoration-none text-dark">Home</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('approve.travel.reports.index') }}"
                                class="text-decoration-none text-dark">Travel
                                Report</a>
                        </li>
                        <li class="breadcrumb-item" aria-current="page">@yield('title')</li>
                    </ol>
                </nav>
                <h4 class="m-0 lh1 mt-1 fs-6 text-uppercase fw-bold text-primary">@yield('title')</h4>
            </div>
        </div>
    </div>

    <section class="registration">
        <div class="row">
            <div class="col-lg-3">
                <div class="card">
                    <div class="card-header fw-bold">
                        Travel Request Details
                    </div>
                    @include('TravelRequest::Partials.detail')
                </div>
                <div class="card">
                    <div class="card-header fw-bold">
                        Travel Claim Details
                    </div>
                    @include('TravelRequest::Partials.claim-detail')
                </div>
            </div>
            <div class="col-lg-9">
                <div class="card">
                    <div class="card-header fw-bold">
                        TADA Claim
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="itineraryTable">
                                <thead class="thead-light">
                                    <tr>
                                        <th scope="col" rowspan="2">Activities
                                        </th>
                                        <th scope="col" colspan="2" class="text-center">
                                            {{ __('label.destination') }}</th>
                                        <th scope="col" colspan="2" class="text-center">
                                            {{ __('label.date') }}</th>
                                        <th scope="col" rowspan="2">Days Spent
                                        </th>
                                        <th scope="col" colspan="4" class="text-center">
                                            DSA per day</th>
                                        <th scope="col" rowspan="2">Total DSA
                                        </th>
                                        <th scope="col" rowspan="2">Daily Allowance
                                        </th>
                                        <th scope="col" rowspan="2">Lodging Expense
                                        </th>
                                        <th scope="col" rowspan="2">Other Expense
                                        </th>
                                        <th scope="col" rowspan="2">Total Amount
                                        </th>
                                        <th scope="col" rowspan="2">{{ __('label.mode-of-travel') }}</th>
                                        <th scope="col" rowspan="2">{{ __('label.remarks') }}
                                        </th>
                                        <th scope="col" rowspan="2">{{ __('label.attachment') }}
                                        </th>
                                    </tr>
                                    <tr>
                                        <th scope="col">{{ __('label.from') }}</th>
                                        <th scope="col">{{ __('label.to') }}</th>
                                        <th scope="col">{{ __('label.from') }}</th>
                                        <th scope="col">{{ __('label.to') }}</th>
                                        <th scope="col">Breakfast</th>
                                        <th scope="col">Lunch</th>
                                        <th scope="col">Dinner</th>
                                        <th scope="col">Incidental</th>
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
                        Local Travel Claim
                    </div>
                    <div class="container-fluid-s">
                        <div class="card">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered" id="claimLocalTravelTable">
                                        <thead class="thead-light">
                                            <tr>
                                                <th scope="col" rowspan="2">{{ __('label.date') }}</th>
                                                <th scope="col" rowspan="2">{{ __('label.purpose') }}</th>
                                                <th scope="col" colspan="2" class="text-center">
                                                    {{ __('label.destination') }}</th>
                                                <th scope="col" rowspan="2">Total fare</th>
                                                <th scope="col" rowspan="2">{{ __('label.remarks') }}</th>
                                                <th scope="col" rowspan="2">{{ __('label.attachment') }}</th>
                                            </tr>
                                            <tr>
                                                <th scope="col">{{ __('label.from') }}</th>
                                                <th scope="col">{{ __('label.to') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header fw-bold">
                        Claim Expenses
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="expenseTable">
                                <thead class="thead-light">
                                    <tr>
                                        <th scope="col">{{ __('label.activity') }}</th>
                                        <th scope="col">{{ __('label.date') }}</th>
                                        <th scope="col">{{ __('label.description') }}</th>
                                        <th scope="col">{{ __('label.amount') }}</th>
                                        <th scope="col">{{ __('label.invoice-bill-number') }}</th>
                                        <th scope="col">{{ __('label.attachment') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="3">{{ __('label.sub-total') }}</td>
                                        <td colspan="3" id="total_expense_amount">
                                            {{ number_format($travelClaim->total_expense_amount, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td colspan="3">Total Local Travel</td>
                                        <td colspan="3" id="total_local_travel_amount">
                                            {{ number_format($travelClaim->localTravels->sum('travel_fare'), 2) }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="3">Total TADA</td>
                                        <td colspan="3" id="total_itinerary_amount">
                                            {{ number_format($travelClaim->total_itinerary_amount, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td colspan="3">{{ __('label.grand-total') }}</td>
                                        <td colspan="3" id="total_amount">
                                            {{ number_format($travelClaim->total_amount, 2) }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="3">{{ __('label.advance-amount') }}
                                        </td>
                                        <td colspan="3">
                                            {{ number_format($travelClaim->advance_amount, 2) }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="3">
                                            {{ __('label.refundable-reimbursable-amount') }}
                                        </td>
                                        <td colspan="3">
                                            <input readonly class="form-control" name="refundable_amount"
                                                value="{{ $travelClaim->refundable_amount }}" />
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                </div>

                {{-- @include('TravelRequest::Partials.summary') --}}

                <div class="card">
                    <div class="card-header fw-bold">
                        Process
                    </div>
                    <div class="card-body">
                        <form action="{{ route('review.travel.claims.store', $travelClaim->id) }}"
                            id="travelClaimReviewForm" method="post" enctype="multipart/form-data" autocomplete="off">
                            <div class="card-body">
                                <div class="c-b">
                                    @foreach ($travelClaim->logs as $log)
                                        <div class="d-flex py-2 flex-row gap-2 mb-2 border-bottom ">
                                            <div width="40" height="40" class="rounded-circle mr-3 user-icon">
                                                <i class="bi-person-circle fs-5"></i>
                                            </div>
                                            <div class="w-100">
                                                <div
                                                    class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                                                    <div
                                                        class="d-flex flex-column flex-md-row align-items-start align-items-md-center gap-md-2 mb-2 mb-md-0">
                                                        <label class="form-label mb-0">{{ $log->getCreatedBy() }}</label>
                                                        <span class="badge bg-primary c-badge">
                                                            {!! $log->createdBy->employee->latestTenure->getDesignationName() !!}
                                                        </span>
                                                    </div>
                                                    <small
                                                        title="{{ $log->created_at }}">{{ $log->created_at->format('M d, Y h:i A') }}</small>
                                                </div>
                                                <p class="text-justify comment-text mb-0 mt-1">
                                                    {{ $log->log_remarks }}
                                                </p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="border-top pt-4">
                                    <div class="row mb-2">
                                        <div class="col-lg-3">
                                            <div class="d-flex align-items-start h-100">
                                                <label for="validationleavetype"
                                                    class="form-label required-label">Status</label>
                                            </div>
                                        </div>
                                        <div class="col-lg-9">
                                            <select name="status_id" class="select2 form-control" data-width="100%">
                                                <option value="">Select a Status</option>
                                                <option value="2">Return to Requester</option>
                                                <option value="11">Verify</option>
                                            </select>
                                            @if ($errors->has('status_id'))
                                                <div class="fv-plugins-message-container invalid-feedback">
                                                    <div data-field="status_id">
                                                        {!! $errors->first('status_id') !!}
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="row mb-2">
                                        <div class="col-lg-3">
                                            <div class="d-flex align-items-start h-100">
                                                <label for="validationRemarks"
                                                    class="form-label required-label">Remarks</label>
                                            </div>
                                        </div>
                                        <div class="col-lg-9">
                                            <textarea type="text" class="form-control @if ($errors->has('log_remarks')) is-invalid @endif" name="log_remarks">{{ old('log_remarks') }}</textarea>
                                            @if ($errors->has('log_remarks'))
                                                <div class="fv-plugins-message-container invalid-feedback">
                                                    <div data-field="log_remarks">{!! $errors->first('log_remarks') !!}</div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    {!! csrf_field() !!}
                                </div>
                            </div>

                            <div class="card-footer border-0 justify-content-end d-flex gap-2">
                                <button type="submit" name="btn" value="submit" class="btn btn-success btn-sm">
                                    Submit
                                </button>
                                <a href="{!! route('review.travel.claims.index') !!}" class="btn btn-danger btn-sm">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@stop
