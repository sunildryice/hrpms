@extends('layouts.container-report')

@section('title', 'Exit Handover Print')
@section('page_css')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Mukta:wght@200;500;600;800&display=swap" rel="stylesheet">
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
            font-weight: 600;
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

    </style>
@endsection
@section('page_js')

@endsection

@section('page-content')

    <!-- CSS only -->


    <section class="print-info bg-white p-3" id="print-info">

        <div class="print-title fw-bold mb-3 translate-middle text-center ">
            <div class="fs-5"> HERD International</div>
            <div class="fs-8"> Exit Handover Note</div>
        </div>

        <div class="print-header">
            <div class="row">
                <div class="col-lg-8">
                    <div class="print-header-info mb-4">
                        <ul class="list-unstyled m-0 p-0 fs-7">
                            <li><span class="fw-bold me-2">Employee Name :</span><span>{{ $handOverNote->employee->getFullName() }}</span></li>
                            <li><span class="fw-bold me-2">Designation:</span><span>{{ $handOverNote->employee->latestTenure->getDesignationName() }}</span>
                            </li>
                            <li><span class="fw-bold me-2">Duty Station:</span><span>{{ $handOverNote->employee->latestTenure->getDutyStation() }}</span></li>
                            <li><span class="fw-bold me-2">Joined Date :</span><span>{{ $handOverNote->employee->latestTenure->getJoinedDate() }}</span></li>
                            <li><span class="fw-bold me-2">Resigned Date :</span><span> {{ $handOverNote->getLastDutyDate() }}</span></li>
                            <li><span class="fw-bold me-2">Last Working Date :</span><span> {{ $handOverNote->getLastDutyDate() }}</span></li>
                        </ul>
                    </div>

                </div>
                <div class="col-lg-4">
                    <div class="d-flex flex-column justify-content-end">
                        <div class="d-flex flex-column justify-content-end brand-logo mb-4 flex-grow-1">
                            <div class="d-flex flex-column justify-content-end float-right">
                                <img src="{{ asset('img/logonp.png') }}" alt="" class="align-self-end pe-5">
                            </div>

                        </div>
                    </div>

                </div>
            </div>
        </div>
        <div class="print-body">
            <div>The above mentioned staff member is leaving OHW and under clearance So Please indicate outstanding, if any
                against his / her name.</div>
            <table class="table border">
                <tbody>
                    <tr>
                        <td colspan="4">A. Clearance</td>
                    </tr>
                    <tr>
                        <td rowspan="2">Department</td>
                        <td colspan="3">Cleared by</td>
                        <td rowspan="2">Remarks</td>
                    </tr>
                    <tr>
                        <td>Name</td>
                        <td>Sign</td>
                        <td>Date</td>
                    </tr>
                    <tr>
                        <td colspan="5">Finance</td>
                    </tr>
                    <tr>
                        <td>Outstanding Advance</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td>{{ $handOverNote->employeeExitPayable->advance_amount }}</td>
                    </tr>
                    <tr>
                        <td>Loan</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td>{{ $handOverNote->employeeExitPayable->loan_amount }}</td>
                    </tr>
                    <tr>
                        <td>Other Payables</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td>{{ $handOverNote->employeeExitPayable->other_payable_amount }}</td>
                    </tr>
                </tbody>
            </table>
            <div class="row mb-4">
                <div class="col-lg-6">
                    <div> <strong>Submitted By:</strong> {{ $handOverNote->employee->getFullName() }}</div>
                </div>
                <div class="col-lg-6">
                    <div> <strong>Date:</strong> {{ $handOverNote->submittedLog ?->created_at }}</div>
                </div>
            </div>
            <div> <strong>Supervisors clearance:</strong>  I hereby certify that Mr/Mrs/Ms {{ $handOverNote->employee->getFullName() }} has properly
                handed-over the all documents/holding as per attached handover/takeover note:
            </div>
            <br>
            <div class="row mb-4">
                <div class="col-lg-6">
                    <div> Supervisor's Name and Signature:{{ $handOverNote->getApproverName() }} /
                        {{ $handOverNote->approvedLog ?->created_at }}</div>
                </div>
            </div>

            <table class="table border">
                <tbody>
                    <tr>
                        <td>B. Payable details</td>
                        <td>Date From</td>
                        <td>Date To</td>
                    </tr>
                    <tr>
                        <td>Outstanding Salary</td>
                        <td>{{ $handOverNote->employeeExitPayable->salary_date_from }}</td>
                        <td>{{ $handOverNote->employeeExitPayable->salary_date_to }}</td>
                    </tr>
                    <tr>
                        <td>Prorated Festival (Dashain) Bonus</td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>Annual and Sick Leave Balance</td>
                        <td colspan="2">{{ $handOverNote->employeeExitPayable->leave_balance }} days</td>
                    </tr>
                    <tr>
                        <td>Severance pay/Gratuity if any</td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>Others (Give details, if any)</td>
                        <td></td>
                        <td></td>
                    </tr>
                </tbody>
            </table>


            <div class="row mb-4">
                <div class="col-lg-6">
                    <div> Human Resources Representative:</div>
                    <div>Date:</div>
                </div>
                <div class="col-lg-6">
                    <div>Employee: </div>
                    <div>Date:</div>
                </div>
            </div>
        </div>
    </section>

@endsection
