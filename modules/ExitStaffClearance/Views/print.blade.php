@extends('layouts.container-report')

@section('title', 'Exit Staff Clearance Form')
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

        .parent {
            font-weight: 900;
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

        <div class="mb-3 text-center print-title fw-bold translate-middle">
            <div class="fs-5"> HERD International</div>
            <div class="fs-8"> Exit Staff Clearance Form</div>
        </div>

        <div class="print-header">
            <div class="row">
                <div class="col-lg-8">
                    <div class="print-header-info">
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="d-flex flex-column justify-content-end">
                        <div class="d-flex flex-column justify-content-end brand-logo flex-grow-1">
                            <div class="float-right d-flex flex-column justify-content-end">
                                <img src="{{ asset('img/logonp.png') }}" alt="" class="align-self-end pe-5">
                            </div>

                        </div>
                    </div>

                </div>
            </div>
        </div>
        <div class="print-body">
            <div class="mb-2 employee-details">
                <ul class="p-0 m-0 list-unstyled fs-7">
                    <li><span class="fw-bold me-2">Employee Name
                            :</span><span>{{ $staffClearance->employee->getFullName() }}</span></li>
                    <li><span
                            class="fw-bold me-2">Designation:</span><span>{{ $staffClearance->employee->latestTenure->getDesignationName() }}</span>
                    </li>
                    <li><span class="fw-bold me-2">Duty
                            Station:</span><span>{{ $staffClearance->employee->latestTenure->getDutyStation() }}</span></li>
                    <li><span class="fw-bold me-2">Joined Date
                            :</span><span>{{ $staffClearance->employee->latestTenure->getJoinedDate() }}</span></li>
                    <li><span class="fw-bold me-2">Resigned Date :</span><span>
                            {{ $staffClearance->getLastDutyDate() }}</span></li>
                    <li><span class="fw-bold me-2">Last Working Date :</span><span>
                            {{ $staffClearance->getLastDutyDate() }}</span></li>
                </ul>
            </div>

            <div>The above mentioned staff member is leaving OHW and under clearance So Please indicate outstanding, if any
                against his / her name.</div>
            <table class="table border">
                <thead>
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
                </thead>
                <tbody>
                    @foreach ($departments as $department)
                        <tr class="parent">
                            <td class="fw-bold">{{ $department->title }}</td>
                            <td colspan="5"></td>
                        </tr>
                        @foreach ($department->childrens as $children)
                            <tr>
                                <td>- {{ $children->title }}</td>
                                @php
                                    $record = $records->firstWhere('clearance_department_id', $children->id);
                                @endphp
                                <td class="">{{ $record?->getClearedByName() }}</td>
                                <td></td>
                                <td class="">{{ $record?->getClearedDate() }}</td>
                                <td class="">{{ $record?->remarks }}</td>
                            </tr>
                        @endforeach
                    @endforeach
                </tbody>
            </table>
            <div class="mb-4 row">
                <div class="col-lg-6">
                    <div> <strong>Submitted By:</strong> {{ $staffClearance->employee->getFullName() }}</div>
                </div>
                <div class="col-lg-6">
                    <div> <strong>Date:</strong> {{ $staffClearance->created_at->format('Y-m-d H:i:s') }}</div>
                </div>
            </div>
            <div> <strong>Supervisors clearance:</strong> I hereby certify that Mr/Mrs/Ms
                {{ $staffClearance->employee->getFullName() }} has properly
                handed-over the all documents/holding as per attached handover/takeover note:
            </div>
            <br>
            <div class="mt-5 mb-4 row">
                <div class="col-lg-6">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="fot-info w-100">
                                <div class="mb-2 d-flex flex-grow-1">
                                    <span class="pb-1 border-bottom d-flex flex-grow-1 w-75">
                                    </span>
                                </div>
                                <div class="mb-2 d-flex flex-grow-1">
                                    <span class="d-flex flex-grow-1 w-75">
                                        Supervisor Name: {{ $staffClearance->getSupervisorName() }}
                                    </span>
                                </div>
                                <div class="mb-2 d-flex flex-grow-1">
                                    <span class="pb-1 d-flex flex-grow-1 w-75">
                                        Date: {{ $staffClearance->verified_at?->format('Y-m-d H:i:s') }}
                                        {{-- Date: {{ $staffClearance->supervisor?->employee?->getDesignationName() }} --}}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="fot-info w-100">
                                <div class="mb-2 d-flex flex-grow-1">
                                    <span class="pb-1 border-bottom d-flex flex-grow-1 w-75">
                                    </span>
                                </div>
                                <div class="mb-2 d-flex flex-grow-1">
                                    <span class="d-flex flex-grow-1 w-75">
                                        Endorsed By: {{ $staffClearance->getEndorserName() }}
                                    </span>
                                </div>
                                <div class="mb-2 d-flex flex-grow-1">
                                    <span class="pb-1 d-flex flex-grow-1 w-75">
                                        Date: {{ $staffClearance->endorsed_at?->format('Y-m-d H:i:s') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                @php
                    $fiscalYear = \Modules\Master\Models\FiscalYear::select(['id', 'title'])
                        ->where('title', $staffClearance->handoverNote->resignation_date?->format('Y'))->first();
                    $leaves = app(\Modules\Employee\Repositories\LeaveRepository::class)
                        ->getEmployeeLeaves($staffClearance->employeeExitPayable->employee_id, $fiscalYear?->id)
                        ->where('leaveType.encashment', 1);
                    $payable = $staffClearance->employeeExitPayable;
                    $totalBalance = 0;
                    foreach ($leaves as $leave) {
                        $balance = $leave->balance;
                        $totalBalance += $balance;
                    }
                @endphp

                <table class="table mt-2 border">
                    <tbody>
                        <tr>
                            <td>B. Payable details</td>
                            <td>Date From</td>
                            <td>Date To</td>
                        </tr>
                        <tr>
                            <td>Outstanding Salary</td>
                            <td>{{ $staffClearance->employeeExitPayable->salary_date_from?->format('Y-m-d') }}</td>
                            <td>{{ $staffClearance->employeeExitPayable->salary_date_to?->format('Y-m-d') }}</td>
                        </tr>
                        <tr>
                            <td>Prorated Festival (Dashain) Bonus</td>
                            <td>{{ $staffClearance->employeeExitPayable->festival_bonus_date_from?->format('Y-m-d') }}</td>
                            <td>{{ $staffClearance->employeeExitPayable->festival_bonus_date_to?->format('Y-m-d') }}</td>
                        </tr>
                        <tr>
                            <td>Leave Balance (Hours)</td>
                            <td colspan="2">{{ $totalBalance }} </td>
                            {{-- <td colspan="2">{{ $staffClearance->employeeExitPayable->leave_balance }} </td> --}}
                        </tr>
                        <tr>
                            <td>Severance pay/Gratuity if any</td>
                            <td colspan="2">{{ $staffClearance->employeeExitPayable->gratuity_amount }}</td>
                        </tr>
                        <tr>
                            <td>Others (Give details, if any)</td>
                            <td colspan="">{{ $staffClearance->employeeExitPayable->other_amount }} </td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>


                <div class="mb-4 row">
                    <div class="col-lg-6">
                        <div> Human Resources Representative: {{ $staffClearance->certifier->getFullName() }}</div>
                        <div>Date: {{ $staffClearance->certified_at?->format('Y-m-d H:i:s') }}</div>
                    </div>
                    <div class="col-lg-6">
                        <div>Approved By: {{ $staffClearance->approver->getFullName() }} </div>
                        <div>Date: {{ $staffClearance->approved_at?->format('Y-m-d H:i:s') }}</div>
                    </div>
                </div>
            </div>
    </section>

@endsection
