@extends('layouts.container-report')

@section('title', 'Fund Release/MFR Approval')
@section('page_css')
    <style>
        table {
            border: 1px solid;
        }

        .table thead th {
            font-size: 0.94375rem;

        }


        .wrap-col {
            min-width: 300px;
            max-width: 350px;
            white-space: normal;
            word-wrap: break-word;
            overflow-wrap: break-word;
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
            <div class="fs-8">Partner Organization (PO) Fund Release/MFR Approval
            </div>
        </div>

        <div class="print-header">
            <div class="row">
                <div class="col-lg-8">
                    <div class="my-3 print-header-info">
                        {{--     <ul class="p-0 m-0 list-unstyled fs-7"> --}}
                        {{--         <li><span class="fw-bold me-2"> Year: {{ $fundRequest->getFiscalYear() }}</span></li> --}}
                        {{--         <li><span class="fw-bold me-2"> Month: {{ $fundRequest->getMonthName() }}</span></li> --}}
                        {{--         <li><span class="fw-bold me-2"> Fund Requested By (Office): --}}
                        {{--                 {{ $fundRequest->office->getOfficeName() }}</span></li> --}}
                        {{--         <li><span class="fw-bold me-2">District: {{ $fundRequest->getDistrictName() }}</span></li> --}}
                        {{--         <li><span class="fw-bold me-2">Project: {{ $fundRequest->getProjectCode() }}</span></li> --}}
                        {{--         <li><span class="fw-bold me-2">NOS:</span></li> --}}
                        {{-- --}}
                        {{--     </ul> --}}
                        {{-- </div> --}}
                    </div>
                    <div class="col-lg-4">
                        <div class="d-flex flex-column justify-content-end">
                            <div class="mb-4 d-flex flex-column justify-content-end brand-logo flex-grow-1">
                                <div class="float-right d-flex flex-column justify-content-end">
                                    <img src="{{ asset('img/logonp.png') }}" alt=""
                                        class="align-self-end pe-5 logo-img">
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="print-body">

                {{-- <div>Must be filled by field office.</div> --}}
                <table class="table">
                    <tbody>
                        <tr>
                            <td>Partner Organization</td>
                            <td>{{ $agreement->partnerOrganization->name }}</td>
                        </tr>
                        <tr>
                            <td style="width: 15%;">District</td>
                            <td>{{ $agreement->district->district_name }}</td>
                        </tr>
                        <tr>
                            <td>Project Title</td>
                            <td>{{ $agreement->project->title }}</td>
                        </tr>
                        <tr>
                            <td>Grant Agreement Number</td>
                            <td>{{ $agreement->grant_number }}</td>
                        </tr>
                        <tr>
                            <td>Agreement Period</td>
                            <td>{{ $agreement->getEffectiveFromDate() }} - {{ $agreement->getEffectiveToDate() }}</td>
                        </tr>
                        <tr>
                            <td>Approved Budget NPR</td>
                            <td>{{ number_format($agreement->getApprovedBudget(), 2) }}</td>
                        </tr>
                    </tbody>
                </table>
                @php
                    $amount = $transaction->reimbursed_amount ?? $transaction->release_amount;
                    $amountWords = \App\Helper::convertCurrencyToWords($amount);
                @endphp
                <div>Current Transaction Details</div>
                <table class="table mb-3">
                    <tbody>
                        <tr>
                            <td>Fund Release/ MFR Approval Sheet</td>
                            <td>{{ $transaction->getType() }}</td>
                        </tr>
                        <tr>
                            <td style="width: 15%;">Transaction Date</td>
                            <td>{{ $transaction->transaction_date->format('Y-m-d') }}</td>
                        </tr>
                        <tr>
                            <td>Amount NPR</td>
                            <td>{{ number_format($amount, 2) }}</td>
                        </tr>
                        <tr>
                            <td>Amount in Words</td>
                            <td>{{ $amountWords }}</td>
                        </tr>
                        <tr>
                            <td>Remarks</td>
                            <td>{{ $transaction->remarks }}</td>
                        </tr>

                    </tbody>
                </table>

                <div>Account Details</div>
                <table class="table table-bordered" id="transactionTable">
                    <thead class="thead-light">
                        <tr>
                            <th scope="col" rowspan="2" class="text-center align-top">
                                {{ __('label.date') }}</th>
                            <th scope="col" rowspan="2" class="text-center align-top">
                                {{ __('label.description') }}</th>
                            <th scope="col" rowspan="2">{{ __('label.advance-released') }} NPR
                            </th>
                            <th scope="col" colspan="3" class="text-center">Expenditure NPR
                            </th>
                        </tr>
                        <tr>
                            <th scope="col">{{ __('label.mfr-expenditure') }}
                            </th>
                            <th scope="col">{{ __('label.expenditure-reimbursed') }}
                            </th>
                            <th scope="col">{{ __('label.questioned-cost') }}
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            if (isset($transaction) && !isset($agreement)) {
                                $agreement = $transaction->agreement;
                            }
                            $transactions = $agreement->transactions();
                            if (isset($transaction)) {
                                $transactions->whereDate('transaction_date', '<=', $transaction->transaction_date);
                            }
                            $transactions = $transactions->orderBy('transaction_date', 'asc')->get();
                            $totalRelease = $transactions->sum('release_amount');
                            $totalExpense = $transactions->sum('expense_amount');
                            $totalReimbursed = $transactions->sum('reimbursed_amount');
                            $totalQuestionedCost = number_format($totalExpense - $totalReimbursed, 2);
                            $advancePayable = number_format($totalRelease - $totalExpense, 2);
                            $fundTransferPercentage = round(($totalRelease / $agreement->getApprovedBudget()) * 100, 2);
                            $fundUtilizationPercentage = round(($totalReimbursed / $totalRelease) * 100, 2);
                            $totalRelease = number_format($totalRelease, 2);
                            $totalExpense = number_format($totalExpense, 2);
                            $totalReimbursed = number_format($totalReimbursed, 2);
                        @endphp
                        @foreach ($transactions as $value)
                            <tr>
                                <td>{{ $value->transaction_date->format('Y-m-d') }}</td>
                                <td class="wrap-col">{{ $value->remarks }}</td>
                                <td class="text-end">{{ number_format($value->release_amount, 2) }}</td>
                                <td class="text-end">{{ number_format($value->expense_amount, 2) }}</td>
                                <td class="text-end">{{ number_format($value->reimbursed_amount, 2) }}</td>
                                <td class="text-end">
                                    {{ number_format($value->expense_amount - $value->reimbursed_amount, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="2">{{ __('label.total') }}</td>
                            <td id="" class="text-end"> {{ $totalRelease }}</td>
                            <td id=""class="text-end"> {{ $totalExpense }}</td>
                            <td id=""class="text-end"> {{ $totalReimbursed }}</td>
                            <td id=""class="text-end"> {{ $totalQuestionedCost }}</td>
                        </tr>
                        <tr>
                            <td colspan="2">Advance/ (Payable)</td>
                            <td id="grand_total_amount" class="text-end"> {{ $advancePayable }} </td>
                        </tr>
                        <tr>
                            <td colspan="2">Fund Transfer %</td>
                            <td id="advance_amount" class="text-end"> {{ $fundTransferPercentage }}%
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">Fund Utilization %</td>
                            <td id="advance_amount" class="text-end">
                                {{ $fundUtilizationPercentage }}% </td>
                        </tr>
                    </tfoot>
                </table>

                <div>Comments on Question cost</div>
                <table class="table mb-3">
                    <tbody>
                        @forelse ($transactions->whereNotNull('question_remarks') as $value)
                            <tr>
                                <td>{{ $transaction->question_remarks }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td> </td>
                            </tr>
                        @endforelse

                    </tbody>
                </table>

                <div class="my-3 row justify-content-between">
                    <div class="col-lg-2">
                        <ul class="list-unstyled">
                            <li><strong>Prepared By:</strong></li>
                            <li><strong class="me-1">Name:</strong> {{ $transaction->getRequester() }} </li>
                            <li><strong class="me-1">Title:</strong>
                                {{ $transaction->requester->employee->getDesignationName() }} </li>
                        </ul>
                    </div>

                    <div class="col-lg-3">
                        <ul class="list-unstyled">
                            <li><strong>Reviewed By:</strong></li>
                            <li><strong class="me-1">Name:</strong> {!! $transaction->getReviewer() !!}
                            </li>
                            <li><strong class="me-1">Title:</strong>
                                {{ $transaction->reviewer->employee->getDesignationName() }} </li>
                        </ul>
                    </div>

                    <div class="col-lg-2">
                        <ul class="list-unstyled">
                            <li><strong>Verified By:</strong></li>
                            <li><strong class="me-1">Name:</strong> {!! $transaction->getVerifier() !!}
                            </li>
                            <li><strong class="me-1">Title:</strong>
                                {{ $transaction->verifier->employee->getDesignationName() }} </li>
                        </ul>
                    </div>

                    <div class="col-lg-3">
                        <ul class="list-unstyled">
                            <li><strong>Recommended By:</strong></li>
                            <li><strong class="me-1">Name:</strong> {!! $transaction->getRecommender() !!}
                            </li>
                            <li><strong class="me-1">Title:</strong>
                                {{ $transaction->recommender->employee->getDesignationName() }} </li>
                        </ul>
                    </div>

                    <div class="col-lg-2">
                        <ul class="list-unstyled">
                            <li><strong>Approved By:</strong></li>
                            <li><strong class="me-1">Name:</strong> {!! $transaction->getApprover() !!}</li>
                            <li><strong class="me-1">Title:</strong>
                                {{ $transaction->approver->employee->getDesignationName() }}</li>
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
