@extends('layouts.container-report')

@section('title', 'Advance Request Settlement')

@section('page-content')
    <script type="text/javascript">
        window.print();
    </script>

    <div class="print-title fw-bold mb-3 translate-middle text-center ">
        <div class="fs-5"> HERD International</div>
        <div class="fs-8">{{ $settlement->advanceRequest->getOfficeName() }}</div>
        <div class="fs-8">Advance Settlement/Return/Expenses Reimbursement Form</div>
    </div>
    <div class="print-header">
        <div class="row">
            <div class="col-lg-8">
                <div class="print-code fs-6 fw-bold mb-3">

                </div>

                <div class="print-header-info mb-3 mt-5 pt-5">
                    <ul class="list-unstyled m-0 p-0 fs-7">
                        <li><span class="fw-bold me-2"> Staff Name
                                :</span><span>{{ $settlement->getRequesterName() }}</span>
                        </li>
                        <li><span class="fw-bold me-2"> Title
                                :</span><span>{{ $settlement->requester->employee->getDesignationName() }}</span>
                        </li>
                        <li><span class="fw-bold me-2">Original Advance Issue Date
                                :</span><span>{{ $settlement->advanceRequest->getApprovedDate() }}</span>
                        </li>

                    </ul>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="d-flex flex-column justify-content-end">
                    <div class="d-flex flex-column justify-content-end brand-logo mb-4 flex-grow-1">
                        <div class="d-flex flex-column justify-content-end float-right">
                            <img src="{{ asset('img/logonp.png') }}" alt="" class="align-self-end pe-5 logo-img">
                        </div>

                    </div>
                    <ul class="list-unstyled m-0 p-0 fs-7 align-self-end">
                        <li><span class="fw-bold me-2">Ref #
                                :</span><span>{{ $settlement->advanceRequest->getAdvanceRequestNumber() }}</span>
                        </li>
                        <li><span class="fw-bold me-2">Program Completion Date
                                :</span><span>{{ $settlement->getCompletionDate() }}</span>
                        </li>
                        <li><span class="fw-bold me-2">Program Settlement Date
                                :</span><span>{{ $settlement->getApprovedDate() }}</span>
                        </li>
                        <li><span class="fw-bold me-2">Total days:</span><span> {{ $settlement->getTotalTurnAroundDays() }}
                            </span></li>

                    </ul>
                </div>

            </div>
        </div>
        <div class="row my-3 fs-i-s">
            <div class="col-lg-4">
                <table class="table table-bordered">
                    <tbody>
                        <tr>
                            <td>Amount of Original Advance</td>
                            <td class="amount-td">{{ $settlement->advance_amount }}</td>
                        </tr>
                        <tr>
                            <td>Expenditure Paid</td>
                            <td class="amount-td">{{ $settlement->expenditurePaid() }}</td>
                        </tr>
                        <tr>
                            <td>Cash Surplus or Deficit</td>
                            <td class="amount-td">{{ $settlement->getCashSurplusDeficit() }}</td>
                        </tr>

                    </tbody>
                </table>
            </div>
            <div class="col-lg-7 offset-lg-1">
                <table class="table table-bordered ">
                    <tbody>
                        @if ($settlement->settlementActivities->count())
                            <tr>
                                <td style="width: 15%;" rowspan="4">Activity details</td>
                            </tr>
                            @foreach ($settlement->settlementActivities as $activity)
                                <tr>
                                    <td>{{ $activity->description }}</td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="print-body ">
        <h5 class="fs-7 text-uppercase fw-bold">Advance Use details</h5>
        <table class="table mb-3 fs-i-s">
            <thead>
                <tr>
                    <th>SN.</th>
                    <th style="width: 20%;">Narrative</th>
                    <th>Project</th>
                    <th>District</th>
                    <th>Location</th>
                    <th>Act. Code</th>
                    <th>Donor Code</th>
                    <th class="amount-td">Total Expense</th>
                    <th class="amount-td">Less Tax</th>
                    <th class="amount-td">Net Settlement</th>
                    <th>Trgt Achieved</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($settlement->settlementExpenses as $expense)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $expense->narration }}</td>
                        <td>{{ $settlement->getProjectCode() }}</td>
                        <td>{{ $expense->getDistrictName() }}</td>
                        <td>{{ $expense->location }}</td>
                        <td>{{ $expense->activityCode->getActivityCode() }}</td>
                        <td>{{ $expense->getDonorCode() }}</td>
                        <td class="amount-td">{{ $expense->gross_amount }}</td>
                        <td class="amount-td">{{ $expense->tax_amount }}</td>
                        <td class="amount-td">{{ $expense->net_amount }}</td>
                        <td></td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th class="amount-td" colspan="6"></th>
                    <th class="amount-td">Total</th>
                    <th class="amount-td">{{ $settlement->getTotalExpenses() }}</th>
                    <th class="amount-td">{{ $settlement->getTotalTDS() }}</th>
                    <th class="amount-td">{{ $settlement->expenditurePaid() }}</th>
                    <th></th>
                </tr>
            </tfoot>
        </table>
        <h5 class="fs-7 text-uppercase fw-bold">Expense Details</h5>
        <table class="table mb-3 fs-i-s">
            <thead>
                <tr>
                    <th>SN.</th>
                    <th style="width: 20%;">Activity</th>
                    <th>Date</th>
                    <th>Description</th>
                    <th>Bill/Invoice NO</th>
                    <th>Gross Amount</th>
                    <th class="amount-td">Tax Amount (Less)</th>
                    <th class="amount-td">Net Amount Paid</th>
                    <th class="amount-td">Expense Type</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($settlement->settlementExpenses as $expense)
                    @foreach ($expense->details as $detail)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $expense->narration }}</td>
                            <td>{{ $detail->getExpenseDate() }}</td>
                            <td>{{ $detail->getExpenseCategory() }}</td>
                            <td>{{ $detail->bill_number }}</td>
                            <td class="amount-td">{{ $detail->gross_amount }}</td>
                            <td class="amount-td">{{ $detail->tax_amount }}</td>
                            <td class="amount-td">{{ $detail->net_amount }}</td>
                            <td>{{ $detail->getExpenseType() }}</td>
                        </tr>
                    @endforeach
                    <tr>
                        <th class="amount-td" colspan="4"></th>
                        <th class="amount-td">Total</th>
                        <th class="amount-td">{{ $expense->details()->sum('gross_amount') }}</th>
                        <th class="amount-td">{{ $expense->details()->sum('tax_amount') }}</th>
                        <th class="amount-td">{{ $expense->details()->sum('net_amount') }}</th>
                        <th></th>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <h5 class="fs-7 text-uppercase fw-bold">Expense Summary</h5>
        <table class="table mb-3 fs-i-s">
            <thead>
                <tr>
                    <th scope="col">Description</th>
                    <th scope="col">Gross Amount</th>
                    <th scope="col">Less Tax</th>
                    <th scope="col">Net Amount Paid</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $sum_total_gross_amount = 0;
                    $sum_total_tax_amount = 0;
                    $sum_total_net_amount = 0;
                @endphp
                @foreach ($expenseSummary as $summary)
                    <tr>
                        <td>{{ $summary->getExpenseType() }}</td>
                        <td>{{ $summary->total_gross_amount }}</td>
                        <td>{{ $summary->total_tax_amount }}</td>
                        <td>{{ $summary->total_net_amount }}</td>
                    </tr>
                    @php
                        $sum_total_gross_amount += $summary->total_gross_amount;
                        $sum_total_tax_amount += $summary->total_tax_amount;
                        $sum_total_net_amount += $summary->total_net_amount;
                    @endphp
                @endforeach
                <tr>
                    <th class="">Total</th>
                    <th class="">{{ $sum_total_gross_amount }}</th>
                    <th class="">{{ $sum_total_tax_amount }}</th>
                    <th class="">{{ $sum_total_net_amount }}</th>
                </tr>
            </tbody>
        </table>


        <h5 class="fs-7 text-uppercase fw-bold">Authorization and Checking</h5>
        <p class="mb-4">Reason for over/underspending and agreed by approver :
            <span>{{ $settlement->reason_for_over_or_under_spending }}</span>
        </p>
    </div>

    {{-- @if (!empty($settlement->reviewedLog))
        <div class="mb-3">
            <div><strong>Finance Comment:</strong></div>
            <div class="ms-height">
                <p>{{ $settlement->reviewedLog->log_remarks }}</p>
            </div>
        </div>
    @endif --}}

    <div class="print-footer pt-5">
        <div class="row">
            <div class="col-lg-4">
                <h5 class="fs-7 text-uppercase fw-bold">Checked By</h5>
                <div class="fot-info w-100">
                    <div class="d-flex flex-grow-1 mb-2">
                        <span class="w-25">Name </span><span
                            class="border-bottom d-flex flex-grow-1 w-75 pb-1">{{ $settlement->getReviewerName() }}</span>
                    </div>
                    <div class="d-flex flex-grow-1 mb-2">
                        <span class="w-25">Title </span><span
                            class="border-bottom d-flex flex-grow-1 w-75 pb-1">{{ $settlement->reviewer->employee->getDesignationName() }}</span>
                    </div>
                    <div class="d-flex flex-grow-1 mb-2">
                        <span class="w-25">Date </span><span
                            class="border-bottom d-flex flex-grow-1 w-75 pb-1">{{ $settlement->reviewedLog ? $settlement->reviewedLog->created_at : '' }}</span>
                    </div>
                    <div class="d-flex flex-grow-1 mb-2">
                        <span class="w-25">Comments </span><span
                            class="d-flex flex-grow-1 w-75 pb-1">{{ $settlement->reviewedLog ? $settlement->reviewedLog->log_remarks : '' }}</span>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <h5 class="fs-7 text-uppercase fw-bold">Recommended By</h5>
                <div class="fot-info w-100">
                    <div class="d-flex flex-grow-1 mb-2">
                        <span class="w-25">Name </span><span
                            class="border-bottom d-flex flex-grow-1 w-75 pb-1">{{ $settlement->getRecommenderName() }}</span>
                    </div>
                    <div class="d-flex flex-grow-1 mb-2">
                        <span class="w-25">Title </span><span
                            class="border-bottom d-flex flex-grow-1 w-75 pb-1">{{ $settlement->recommender->employee->getDesignationName() }}</span>
                    </div>
                    <div class="d-flex flex-grow-1 mb-2">
                        <span class="w-25">Date </span><span
                            class="border-bottom d-flex flex-grow-1 w-75 pb-1">{{ $settlement->recommendedLog ? $settlement->recommendedLog->created_at : '' }}</span>
                    </div>
                    <div class="d-flex flex-grow-1 mb-2">
                        <span class="w-25">Comments </span><span
                            class="d-flex flex-grow-1 w-75 pb-1">{{ $settlement->recommendedLog ? $settlement->recommendedLog->log_remarks : '' }}</span>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <h5 class="fs-7 text-uppercase fw-bold">Authorized By</h5>
                <div class="fot-info w-100">
                    <div class="d-flex flex-grow-1 mb-2">
                        <span class="w-25">Name </span><span
                            class="border-bottom d-flex flex-grow-1 w-75 pb-1">{{ $settlement->getApproverName() }}</span>
                    </div>
                    <div class="d-flex flex-grow-1 mb-2">
                        <span class="w-25">Title </span><span
                            class="border-bottom d-flex flex-grow-1 w-75 pb-1">{{ $settlement->approver->employee->getDesignationName() }}</span>
                    </div>
                    <div class="d-flex flex-grow-1 mb-2">
                        <span class="w-25">Date </span><span
                            class="border-bottom d-flex flex-grow-1 w-75 pb-1">{{ $settlement->approvedLog ? $settlement->approvedLog->created_at : '' }}</span>
                    </div>
                    <div class="d-flex flex-grow-1 mb-2">
                        <span class="w-25">Comments </span><span
                            class="d-flex flex-grow-1 w-75 pb-1">{{ $settlement->approvedLog ? $settlement->approvedLog->log_remarks : '' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

@stop
