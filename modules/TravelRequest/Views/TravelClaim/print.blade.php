@extends('layouts.container-report')

@section('title', 'Travel Claim')
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
            width: 10%;
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

        @media print {
            .pagebreak {
                page-break-after: always;
            }
        }
    </style>
@endsection
@section('page_js')

@endsection

@section('page-content')
    <script type="text/javascript">
        window.print();
    </script>

    <section class="print-info bg-white p-3" id="print-info">
        <div class="travel-claim">
            <div class="print-header">
                <div class="row">
                    <div class="col-lg-8">
                        <div class="print-code fs-6 fw-bold mb-3"> Travel Claim Form</div>
                        <div class="print-code fs-6 fw-bold mb-3"> Travel Number:
                            {{ $travelClaim->travelRequest->getTravelRequestNumber() }}</div>
                        <div class="print-code fs-6 fw-bold mb-3"> {{ $travelClaim->travelRequest->office->getOfficeName() }}
                        </div>
                        <div class="print-header-info mb-3">
                            <ul class="list-unstyled m-0 p-0 fs-7">
                                <li><span class="fw-bold me-2"> All Claims must be Accompanied by original TA</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="d-flex flex-column justify-content-end">
                            <div class="d-flex flex-column justify-content-end brand-logo mb-4 flex-grow-1">
                                <div class="d-flex flex-column justify-content-end float-right">
                                    <img src="{{ asset('img/logonp.png') }}" alt="" class="align-self-end pe-5"
                                        style="width: 200px;">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="print-body mb-5">
                <table class="table border">
                    <thead>
                        <tr>
                            <th>Name:</th>
                            <td>{{ $travelClaim->getRequesterName() }}</td>
                            <th>Date:</th>
                            <td>{{ $travelClaim->getApprovedDate() }}</td>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- <tr>
                            <td>Date</td>
                            <td style="width: 50%">Description of Expenses (Other than DSA)</td>
                            <td>Amount</td>
                            <td>Activity</td>
                        </tr>
                        @foreach ($travelClaim->expenses as $expense)
                            <tr>
                                <td>{{ $expense->getExpenseDate() }}</td>
                                <td>{{ $expense->expense_description }}</td>
                                <td>{{ $expense->expense_amount }}</td>
                                <td>{{ $expense->activityCode->getActivityCode() }}</td>
                            </tr>
                        @endforeach --}}
                    </tbody>
                    <tfoot>
                        {{-- <tr>
                            <td colspan="2" class="text-end">Sub-Total (A)</td>
                            <td colspan="2">{{ $travelClaim->total_expense_amount }}</td>
                        </tr> --}}
                    </tfoot>
                </table>

                <h6 class="fw-bold mt-4">TADA Claim</h6>
                <table class="table border">
                    <thead>
                        <tr>
                            <th>Activities</th>
                            <th colspan="2">{{ __('label.destination') }}</th>
                            <th class="text-center">Date</th>
                            <th>Days Spent</th>
                            <th>Total DSA</th>
                            <th>Daily Allowance</th>
                            <th>Lodging Expense</th>
                            <th>Other Expense</th>
                            <th>Total Amount</th>
                            <th>Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($travelClaim->dsaClaim as $dsaClaim)
                            <tr>
                                <td rowspan="2">
                                    {{ $dsaClaim->activities }}</td>
                                <td>DEP:</td>
                                <td>{{ $dsaClaim->departure_place }}</td>
                                <td>{{ $dsaClaim->getDepartureDate() }}</td>
                                <td rowspan="2">{{ $dsaClaim->days_spent }}</td>
                                <td rowspan="2">{{ $dsaClaim?->total_dsa }}</td>
                                <td rowspan="2">{{ $dsaClaim?->daily_allowance }}</td>
                                <td rowspan="2">{{ $dsaClaim?->lodging_expense }}</td>
                                <td rowspan="2">{{ $dsaClaim?->other_expense }}</td>
                                <td rowspan="2">{{ $dsaClaim?->total_amount }}</td>
                                <td rowspan="2">
                                    {{ $dsaClaim?->remarks }}</td>
                            </tr>
                            <tr>
                                <td>ARR:</td>
                                <td>{{ $dsaClaim?->arrival_place }}</td>
                                <td>{{ $dsaClaim?->getArrivalDate() }}</td>
                            </tr>
                        @endforeach
                        {{-- <tr>
                            <td colspan="4" class="text-end">Sub-Total (B)</td>
                            <td>{{ $travelClaim->total_itinerary_amount }}</td>
                            <td colspan="2"></td>
                        </tr>
                        <tr>
                            <td colspan="4" class="text-end">Grand Total A+B</td>
                            <td>{{ $travelClaim->total_expense_amount + $travelClaim->total_itinerary_amount }}</td>
                            <td colspan="2"></td>
                        </tr>
                        <tr>
                            <td colspan="4" class="text-end">Advance Taken</td>
                            <td>{{ $travelClaim->advance_amount }}</td>
                            <td colspan="2"></td>
                        </tr>
                        <tr>
                            <td colspan="4" class="text-end">Amount refundable/(reimbursable)</td>
                            <td>{{ $travelClaim->refundable_amount }}</td>
                            <td colspan="2"></td>
                        </tr>
                        <tr>
                            <td colspan="8">
                                I certify that the following information is correct and per the approved Travel
                                authorization. I authorize HERDi to treat this as the final claim and I will repay any
                                travel allowances to which I am not entitled. If office provides breakfast, lunch, dinner or
                                accommodation, this must be deducted from claim, i.e. % change should be 100%-deducted %
                            </td>
                        </tr> --}}
                    </tbody>
                </table>

                <h6 class="fw-bold mt-4">Local Travel Claim</h6>
                <table class="table table-bordered">
                    <thead class="thead-light">
                        <tr>
                            <th rowspan="2">{{ __('label.date') }}</th>
                            <th rowspan="2">{{ __('label.purpose') }}</th>
                            <th colspan="2" class="text-center">{{ __('label.destination') }}</th>
                            <th rowspan="2">Total fare</th>
                            <th rowspan="2">{{ __('label.remarks') }}</th>
                        </tr>
                        <tr>
                            <th>{{ __('label.from') }}</th>
                            <th>{{ __('label.to') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($travelClaim->localTravels as $lt)
                            <tr>
                                <td>{{ $lt->getTravelDate() }}</td>
                                <td>{{ $lt->purpose }}</td>
                                <td>{{ $lt->departure_place }}</td>
                                <td>{{ $lt->arrival_place }}</td>
                                <td class="text-end">{{ number_format($lt->travel_fare, 2) }}</td>
                                <td>{{ $lt->remarks }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <h6 class="fw-bold mt-4">Claim Expenses</h6>
                <table class="table table-bordered">
                    <thead class="thead-light">
                        <tr>
                            <th>{{ __('label.activity') }}</th>
                            <th>{{ __('label.date') }}</th>
                            <th>{{ __('label.description') }}</th>
                            <th>{{ __('label.amount') }}</th>
                            <th>{{ __('label.invoice-bill-number') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($travelClaim->expenses as $exp)
                            <tr>
                                <td>{{ $exp->activityCode?->getActivityCodeDescription() }}</td>
                                <td>{{ $exp->getExpenseDate() }}</td>
                                <td>{{ $exp->expense_description }}</td>
                                <td class="text-end">{{ number_format($exp->expense_amount, 2) }}</td>
                                <td>{{ $exp->invoice_bill_number }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" class="text-end fw-bold">{{ __('label.sub-total') }}</td>
                            <td colspan="2" class="text-end fw-bold">
                                {{ number_format($travelClaim->total_expense_amount, 2) }}</td>
                        </tr>
                        <tr>
                            <td colspan="3" class="text-end">Total Local Travel</td>
                            <td colspan="2" class="text-end">
                                {{ number_format($travelClaim->localTravels->sum('travel_fare'), 2) }}</td>
                        </tr>
                        <tr>
                            <td colspan="3" class="text-end">Total TADA</td>
                            <td colspan="2" class="text-end">
                                {{ number_format($travelClaim->total_itinerary_amount, 2) }}</td>
                        </tr>
                        <tr>
                            <td colspan="3" class="text-end fw-bold">{{ __('label.grand-total') }}</td>
                            <td colspan="2" class="text-end fw-bold">{{ number_format($travelClaim->total_amount, 2) }}
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3" class="text-end">{{ __('label.advance-amount') }}</td>
                            <td colspan="2" class="text-end">{{ number_format($travelClaim->advance_amount, 2) }}</td>
                        </tr>
                        <tr>
                            <td colspan="3" class="text-end">{{ __('label.refundable-reimbursable-amount') }}</td>
                            <td colspan="2" class="text-end">{{ $travelClaim->refundable_amount }}</td>
                        </tr>
                         <tr>
                            <td colspan="5">
                                I certify that the following information is correct and per the approved Travel
                                authorization. I authorize HERDi to treat this as the final claim and I will repay any
                                travel allowances to which I am not entitled. If office provides breakfast, lunch, dinner or
                                accommodation, this must be deducted from claim, i.e. % change should be 100%-deducted %
                            </td>
                        </tr>
                    </tfoot>
                </table>

                {{-- <table class="table border">
                    <thead>
                        <tr>
                            <th colspan="5">Summary of Travel Claim</th>
                        </tr>
                        <tr>
                            <th scope="col">{{ __('label.activity') }}</th>
                            <th scope="col">Subledger</th>
                            <th scope="col">{{ __('label.donor') }}</th>
                            <th scope="col">Charging Office</th>
                            <th scope="col">{{ __('label.amount') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($summaries as $summary)
                            <tr>
                                <td>{{ $summary->getActivityTitle() }}</td>
                                <td>{{ $summary->subledger }}</td>
                                <td>{{ $summary->getDonorDescription() }}</td>
                                <td>{{ $summary->office->office_name }}</td>
                                <td>{{ $summary->getAmount() }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="2">{{ __('label.total-amount') }}</td>
                            <td>
                                {{ $travelClaim->total_expense_amount + $travelClaim->total_itinerary_amount }}</td>
                        </tr>
                    </tfoot>
                </table> --}}

                @if (!empty($travelClaim->reviewedLog))
                    <div class="mb-3">
                        <div><strong>Finance Comment:</strong></div>
                        <div class="ms-height">
                            <p>{{ $travelClaim->reviewedLog->log_remarks }}</p>
                        </div>
                    </div>
                @endif

                <div class="row mt-4">
                    <div class="col-lg-6 mb-4">
                        <div><strong>Claimed By:</strong></div>
                        <div><strong>Name:</strong>{{ $travelClaim->getRequesterName() }}</div>
                        <div><strong>Title:</strong>{{ $travelClaim->requester->employee->getDesignationName() }}</div>
                        <div>
                            <strong>Date:</strong>{{ $travelClaim->submittedLog ? $travelClaim->submittedLog->created_at : '' }}
                        </div>
                    </div>
                    <div class="col-lg-6 mb-4">
                        <div><strong>Checked By:</strong></div>
                        <div><strong>Name:</strong> {{ $travelClaim->reviewedLog ? $travelClaim->getReviewerName() : '' }}
                        </div>
                        <div><strong>Title:</strong>
                            {{ $travelClaim->reviewedLog ? $travelClaim->reviewer->employee->getDesignationName() : '' }}
                        </div>
                        <div><strong>Date:</strong>
                            {{ $travelClaim->reviewedLog ? $travelClaim->reviewedLog->created_at : '' }}</div>
                    </div>
                    <div class="col-lg-6 mb-4">
                        <div><strong>Certified By:</strong></div>
                        <div><strong>Name:</strong>
                            {{ $travelClaim->recommendedLog ? $travelClaim->getRecommenderName() : '' }}</div>
                        <div><strong>Title:</strong>
                            {{ $travelClaim->recommendedLog ? $travelClaim->recommender->employee->getDesignationName() : '' }}
                        </div>
                        <div><strong>Date:</strong>
                            {{ $travelClaim->recommendedLog ? $travelClaim->reviewedLog->created_at : '' }}</div>
                    </div>
                    <div class="col-lg-6 mb-4">
                        <div><strong>Approved By:</strong></div>
                        <div><strong>Name:</strong> {{ $travelClaim->approvedLog ? $travelClaim->getApproverName() : '' }}
                        </div>
                        <div><strong>Title:</strong>
                            {{ $travelClaim->approvedLog ? $travelClaim->approver->employee->getDesignationName() : '' }}
                        </div>
                        <div><strong>Date:</strong>{!! $travelClaim->approvedLog ? $travelClaim->approvedLog->created_at : '' !!}</div>
                    </div>
                </div>


            </div>
        </div>

    </section>

@endsection
