@extends('layouts.container-report')

@section('title', 'Fund Request Print')
@section('page_css')
    <style>
        table {
            border: 1px solid;
        }

        .table thead th {
            font-size: 0.94375rem;

        }


        tbody,
        td,
        tfoot,
        th,
        thead,
        tr {
            border-width: 0.1px;
        }

        .table tr th,
        .table tr td {
            padding: 0.25rem 0.75rem;
        }
    </style>
@endsection
@section('page_js')

@endsection

@section('page-content')

    <!-- CSS only -->
    <section class="p-3 bg-white print-info" id="print-info">

        <div class="mb-5 text-center print-title fw-bold translate-middle">
            <div class="fs-5"> HERD International</div>
            <div class="fs-8"> Fund Request
                @if ($fundRequest->status_id == config('constant.CANCELLED_STATUS'))
                    <span class="text-danger">(Cancelled)<span>
                @endif
            </div>
        </div>

        <div class="print-header">
            <div class="row">
                <div class="col-lg-8">
                    <div class="my-3 print-header-info">
                        <ul class="p-0 m-0 list-unstyled fs-7">
                            <li><span class="fw-bold me-2"> Year: {{ $fundRequest->getFiscalYear() }}</span></li>
                            <li><span class="fw-bold me-2"> Month: {{ $fundRequest->getMonthName() }}</span></li>
                            <li><span class="fw-bold me-2"> Fund Requested By (Office):
                                    {{ $fundRequest->office->getOfficeName() }}</span></li>
                            <li><span class="fw-bold me-2">Fund Request For (Office):
                                    {{ $fundRequest->requestForOffice->getOfficeName() }}</span></li>
                            <li><span class="fw-bold me-2">Project: {{ $fundRequest->getProjectCode() }}</span></li>
                            <li><span class="fw-bold me-2">NOS:</span></li>

                        </ul>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="d-flex flex-column justify-content-end">
                        <div class="mb-4 d-flex flex-column justify-content-end brand-logo flex-grow-1">
                            <div class="float-right d-flex flex-column justify-content-end">
                                <img src="{{ asset('img/logonp.png') }}" alt="" class="align-self-end pe-5">
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="print-body">
            <table class="table mb-5">
                <thead>
                    <tr>
                        <th>{{ __('label.activity-code') }}</th>
                        <th>{{ __('label.activity-name') }}</th>
                        <th>{{ __('label.estimated-amount') }}</th>
                        <th>{{ __('label.budget-amount') }}</th>
                        <th>{{ __('label.project-target-unit') }}</th>
                        <th>{{ __('label.dip-target-unit') }}</th>
                        <th>{{ __('label.budget-variance') }}</th>
                        <th>{{ __('label.target-variance') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $totalEstimatedAmount = 0;
                        $totalBudgetAmount = 0;
                        $totalVarianceBudgetAmount = 0;
                    @endphp
                    @if ($fundRequest->fundRequestActivities->isNotEmpty())
                        @foreach ($fundRequest->fundRequestActivities as $activity)
                            @php
                                $totalEstimatedAmount += $activity->estimated_amount;
                                $totalBudgetAmount += $activity->budget_amount;
                                $totalVarianceBudgetAmount += $activity->variance_budget_amount;
                            @endphp
                            <tr>
                                <td>{{ $activity->activityCode->title }}</td>
                                <td>{{ $activity->activityCode->description }}</td>
                                <td>{{ $activity->estimated_amount }}</td>
                                <td>{{ $activity->budget_amount }}</td>
                                <td>{{ $activity->project_target_unit }}</td>
                                <td>{{ $activity->dip_target_unit }}</td>
                                <td>{{ $activity->variance_budget_amount }}</td>
                                <td>{{ $activity->variance_target_unit }}</td>
                            </tr>
                        @endforeach
                    @endif
                    <tr>
                        <td colspan="2">TOTAL</td>
                        <td colspan="1">{{ $totalEstimatedAmount }}</td>
                        <td colspan="1">{{ $totalBudgetAmount }}</td>
                        <td colspan="1"></td>
                        <td colspan="1"></td>
                        <td colspan="1">{{ $totalVarianceBudgetAmount }}</td>
                        <td colspan="1"></td>
                    </tr>
                    {{-- <tr>
                    <td colspan="2">TOTAL FUND REQUIRED</td>
                    <td colspan="4">{{ $fundRequest->required_amount }}</td>
                    <td colspan="2"></td>
                </tr> --}}
                    <tr>
                        <td colspan="2">Fund Surplus/(Deficit)</td>
                        <td colspan="4">{{ $fundRequest->estimated_surplus }}</td>
                        <td colspan="2"></td>
                    </tr>
                    <tr>
                        <td colspan="2">NET FUND REQUIRED</td>
                        <td colspan="4">{{ $fundRequest->net_amount }}</td>
                        <td colspan="2"></td>
                    </tr>
                </tbody>
            </table>

            <div>Must be filled by field office.</div>
            <table class="table">
                <tbody>
                    <tr>
                        <td>Account Number</td>
                        <td>{{ $fundRequest->office?->account_number }}</td>
                    </tr>
                    <tr>
                        <td style="width: 15%;">Bank Name</td>
                        <td>{{ $fundRequest->office?->bank_name }}</td>
                    </tr>
                    <tr>
                        <td>Branch</td>
                        <td>{{ $fundRequest->office?->branch_name }}</td>
                    </tr>
                </tbody>
            </table>

            {{-- @if (!empty($fundRequest->reviewedLog))
                <div class="mb-3">
                    <div><strong>Finance Comment:</strong></div>
                    <div class="ms-height">
                        <p>{{ $fundRequest->reviewedLog->log_remarks }}</p>
                    </div>
                </div>
            @endif --}}

            <div class="my-3 row justify-content-between">
                <div class="col-lg-3">
                    <ul class="list-unstyled">
                        <li><strong>Prepared By:</strong></li>
                        <li><strong class="me-1">Name:</strong> {{ $fundRequest->getRequesterName() }} </li>
                        <li><strong class="me-1">Title:</strong>
                            {{ $fundRequest->requester->employee->getDesignationName() }} </li>
                        <li><strong class="me-1">Date:</strong>{!! $fundRequest->submittedLog ? $fundRequest->submittedLog->created_at?->toFormattedDateString() : '' !!}
                        </li>
                    </ul>
                </div>
                @isset($fundRequest->checker_id)
                    <div class="col-lg-3">
                        <ul class="list-unstyled">
                            <li><strong>Checked By:</strong></li>
                            <li><strong class="me-1">Name:</strong> {!! $fundRequest->checkedLog ? $fundRequest->checkedLog->getCreatedBy() : '' !!}
                            </li>
                            <li><strong class="me-1">Title:</strong>
                                {!! $fundRequest->checkedLog ? $fundRequest->checkedLog->createdBy->employee->getDesignationName() : '' !!}
                            </li>
                            <li><strong class="me-1">Date:</strong>{!! $fundRequest->checkedLog ? $fundRequest->checkedLog->created_at?->toFormattedDateString() : '' !!}
                            </li>
                        </ul>
                    </div>
                @endisset
                @isset($fundRequest->certifier_id)
                    <div class="col-lg-3">
                        <ul class="list-unstyled">
                            <li><strong>Certified By:</strong></li>
                            <li><strong class="me-1">Name:</strong> {!! $fundRequest->certifiedLog ? $fundRequest->certifiedLog->getCreatedBy() : '' !!}
                            </li>
                            <li><strong class="me-1">Title:</strong>
                                {!! $fundRequest->certifiedLog ? $fundRequest->certifiedLog->createdBy->employee->getDesignationName() : '' !!}
                            </li>
                            <li><strong class="me-1">Date:</strong>{!! $fundRequest->certifiedLog ? $fundRequest->certifiedLog->created_at?->toFormattedDateString() : '' !!}
                            </li>
                        </ul>
                    </div>
                @endisset
                <div class="col-lg-3">
                    <ul class="list-unstyled">
                        <li><strong>Reviewed By:</strong></li>
                        <li><strong class="me-1">Name:</strong> {!! $fundRequest->reviewedLog ? $fundRequest->reviewedLog->getCreatedBy() : '' !!}
                        </li>
                        <li><strong class="me-1">Title:</strong>
                            {!! $fundRequest->reviewedLog ? $fundRequest->reviewedLog->createdBy->employee->getDesignationName() : '' !!}
                        </li>
                        <li><strong class="me-1">Date:</strong>{!! $fundRequest->reviewedLog ? $fundRequest->reviewedLog->created_at?->toFormattedDateString() : '' !!}
                        </li>
                    </ul>
                </div>

                <div class="col-lg-3">
                    <ul class="list-unstyled">
                        <li><strong>Recommended By:</strong></li>
                        <li><strong class="me-1">Name:</strong> {!! $fundRequest->recommendedLog ? $fundRequest->recommendedLog->getCreatedBy() : '' !!}
                        </li>
                        <li><strong class="me-1">Title:</strong>
                            {!! $fundRequest->recommendedLog ? $fundRequest->recommendedLog->createdBy->employee->getDesignationName() : '' !!}
                        </li>
                        <li><strong class="me-1">Date:</strong>{!! $fundRequest->recommendedLog ? $fundRequest->recommendedLog->created_at?->toFormattedDateString() : '' !!}
                        </li>
                    </ul>
                </div>
                <div class="col-lg-3">
                    <ul class="list-unstyled">
                        <li><strong>Approved By:</strong></li>
                        <li><strong class="me-1">Name:</strong> {!! $fundRequest->getApproverName() !!}</li>
                        <li><strong class="me-1">Title:</strong>
                            {{ $fundRequest->approver->employee->getDesignationName() }}</li>
                        <li><strong class="me-1">Date:</strong>{!! $fundRequest->approvedLog ? $fundRequest->approvedLog->created_at?->toFormattedDateString() : '' !!}
                        </li>
                    </ul>
                </div>
            </div>

            {{-- <div>Admin will decide the requester</div> --}}


        </div>
        <div class="print-footer">
        </div>
    </section>


    <script>
        window.onload = print;
    </script>

@endsection
