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
                {
                    data: 'donor',
                    name: 'donor'
                },
                {
                    data: 'expense_date',
                    name: 'expense_date'
                },
                {
                    data: 'expense_description',
                    name: 'expense_description'
                },
                {
                    data: 'attachment',
                    name: 'attachment',
                },
                {
                    data: 'expense_amount',
                    name: 'expense_amount'
                },
                {
                    data: 'charging_office',
                    name: 'charging_office'
                }
            ]
        });

        var itineraryTable = $('#itineraryTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('travel.claims.itineraries.index', $travelClaim->id) }}",
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
                    data: 'attachment',
                    name: 'attachment',
                },
                {
                    data: 'overnights',
                    name: 'overnights'
                },
                {
                    data: 'dsa_unit_price',
                    name: 'dsa_unit_price'
                },
                {
                    data: 'percentage_charged',
                    name: 'percentage_charged'
                },
                {
                    data: 'activity_code',
                    name: 'activitiy_code'
                },
                {
                    data: 'total_amount',
                    name: 'total_amount'
                },
                {
                    data: 'charging_office',
                    name: 'charging_office'
                },
                {
                    data: 'description',
                    name: 'description'
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
                        Travel Expenses
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table" id="expenseTable">
                                <thead class="thead-light">
                                    <tr>
                                        <th scope="col">{{ __('label.activity') }}</th>
                                        <th scope="col">{{ __('label.donor') }}</th>
                                        <th scope="col">{{ __('label.date') }}</th>
                                        <th scope="col">{{ __('label.description') }}</th>
                                        <th scope="col">{{ __('label.attachment') }}</th>
                                        <th scope="col">{{ __('label.amount') }}</th>
                                        <th scope="col">Charging Office</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="5">{{ __('label.sub-total') }}</td>
                                        <td id="total_expense_amount">
                                            {{ $travelClaim->total_expense_amount }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                </div>
                <div class="card">
                    <div class="card-header fw-bold">
                        Travel Itineraries
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table" id="itineraryTable">
                                <thead class="thead-light">
                                    <tr>
                                        <th scope="col">{{ __('label.from-date') }}</th>
                                        <th scope="col">{{ __('label.from') }}</th>
                                        <th scope="col">{{ __('label.to-date') }}</th>
                                        <th scope="col">{{ __('label.to') }}</th>
                                        <th scope="col">{{ __('label.attachment') }}</th>
                                        <th scope="col">{{ __('label.overnights') }}</th>
                                        <th scope="col">{{ __('label.dsa-rate') }}</th>
                                        <th scope="col">{{ __('label.percentage') }}</th>
                                        <th scope="col">{{ __('label.activity-code') }}</th>
                                        <th scope="col">{{ __('label.total-dsa') }}</th>
                                        <th scope="col">Charging Office</th>
                                        <th scope="col">{{ __('label.remarks') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="9">{{ __('label.sub-total') }}</td>
                                        <td id="total_itinerary_amount">
                                            {{ $travelClaim->total_itinerary_amount }}</td>
                                            <td></td>
                                            <td></td>
                                    </tr>
                                    <tr>
                                        <td colspan="9">{{ __('label.grand-total') }}</td>
                                        <td id="grand_total_amount">
                                            {{ $travelClaim->total_amount }}
                                        </td>
                                        <td></td>
                                            <td></td>

                                    </tr>
                                    <tr>
                                        <td colspan="9">{{ __('label.advance-amount') }}</td>
                                        <td id="advance_amount">
                                            {{ $travelClaim->advance_amount }}
                                        </td>
                                        <td></td>
                                            <td></td>

                                    </tr>
                                    <tr>
                                        <td colspan="9">
                                            {{ __('label.refundable-reimbursable-amount') }}</td>
                                        <td id="refundable_amount">
                                            {{ $travelClaim->refundable_amount }}
                                        </td>
                                        <td></td>
                                            <td></td>

                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
                @include('TravelRequest::Partials.summary')
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
                                                    <small title="{{$log->created_at}}">{{ $log->created_at->format('M d, Y h:i A') }}</small>
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
                                                <label for="validationleavetype" class="form-label required-label">Status</label>
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
                                                <label for="validationRemarks" class="form-label required-label">Remarks</label>
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
