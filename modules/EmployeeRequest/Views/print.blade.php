@extends('layouts.container-report')

@section('title', 'Employee Requistion')
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

        .table tr th {
            padding: 0.45rem 0.75rem;
            width: 5%;
        }

        .table tr td {
            padding: 0.25rem 0.75rem;
        }
    </style>
@endsection
@section('page_js')
@endsection

@section('page-content')
    <!-- CSS only -->
    <div class="print-title fw-bold mb-3 translate-middle text-center ">
        <div class="fs-5"> HERD International</div>
        <div class="fs-8">{{$requester->office->getOfficeName()}}</div>
        <div class="fs-8"> Employee Requisition Form</div>
    </div>
    <div class="print-header">
        <div class="row">
            <div class="col-lg-8">
                <div class="print-code fs-6 fw-bold mb-3">  </div>
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

    <div class="print-body mb-5">
        <p> <strong>INSTRUCTIONS: </strong> Kindly accomplish the form completely. Check the item that corresponds to your request and write
            the details needed on the appropriate
            places. Thank you</p>
        <table class="table border mb-4">
            <thead>
                <tr>
                    <th colspan="4">Position Information</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <th scope="row">Position Title</th>
                    <td>{{$employeeRequest->position_title}} </td>
                    <th scope="row">Requested Level</th>
                    <td>{{$employeeRequest->position_level}}</td>
                </tr>
                <tr>
                    <th scope="row">Work Station</th>
                    <td>{{$employeeRequest->employee_type_id == '0'? $employeeRequest->employee_type_other:$employeeRequest->getDutyStation()}}</td>
                    <th scope="row">Requested Date</th>
                    <td>{{@array_key_exists('submitted_date', $dates)? $dates['submitted_date']: ''}}</td>
                </tr>
            </tbody>

        </table>
        <table class="table border mb-4">
            <tbody>
                <tr>
                    <th scope="row">Types of Employment</th>
                    <td>{{$employeeRequest->getEmployeeType()}}</td>
                    <th scope="row">Date required from</th>
                    <td>{{@array_key_exists('required_date', $dates)? $dates['required_date']: ''}}</td>

                </tr>
                <tr>
                    <th scope="row">For Fiscal Year</th>
                    <td>{{ $employeeRequest->getFiscalYear() }}</td>
                    <th scope="row">Replacement for</th>
                    <td>{{ $employeeRequest->replacement_for }}</td>
                </tr>
            </tbody>

        </table>
        <table class="table border mb-4">
            <thead>
                <tr>
                    <th colspan="4">Reason For Request</th>
                </tr>
                <tr>
                    <td colspan="4">{{ $employeeRequest->reason_for_request }}</td>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="4">Is this Position Budgeted ? @if($employeeRequest->budgeted == 1) Yes @else No @endif</td>
                </tr>
                <tr>
                    <th scope="row">Account Code</th>
                    <td>{{ $employeeRequest->accountCode->getAccountCode() }}</td>
                    <th scope="row">Activity Code</th>
                    <td>{{ $employeeRequest->activityCode->getActivityCode() }}</td>
                </tr>
                <tr>
                    <th scope="row">Donor Code</th>
                    <td>{{ $employeeRequest->donorCode->getDonorCodeWithDescription() }}</td>
                    <th scope="row"></th>
                    <td></td>
                </tr>
                <tr>
                    <th scope="row">Workload (Hours per week): </th>
                    <td>{{ $employeeRequest->work_load }}</td>
                    <th scope="row">Duration :</th>
                    <td>{{ $employeeRequest->duration }}</td>
                </tr>
            </tbody>
        </table>
        <table class="table border mb-4">
            <thead>
                <tr>
                    <th colspan="3">Qualifications</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <th colspan="2" scope="column" class="text-center">Required</th>
                    <th scope="column">Preferred</th>
                </tr>
                <tr>
                    <th scope="column">Education</th>
                    <td>{{ $employeeRequest->education_required }}</td>
                    <td>{{ $employeeRequest->education_preferred }}</td>
                </tr>
                <tr>
                    <th scope="column">Work Experience</th>
                    <td>{{ $employeeRequest->experience_required }}</td>
                    <td>{{ $employeeRequest->experience_preferred }}</td>
                </tr>
                <tr>
                    <th scope="column">Skill</th>
                    <td>{{ $employeeRequest->skills_required }}</td>
                    <td>{{ $employeeRequest->skills_preferred }}</td>
                </tr>
                <tr>
                    <th scope="column">TOR/JD submitted?</th>
                    @if($employeeRequest->tor_jd_submitted == 1)
                        <td colspan="2"> YES <i class="bi bi-check"></i></br>NO
                    @else
                        <td> YES </br> NO <i class="bi bi-check"></i></td>
                        <td>If no, tentative date of submission: {{@array_key_exists('tentative_submission_date', $dates)? $dates['tentative_submission_date']: ''}}
                    @endif
                        </td>

                </tr>
            </tbody>
        </table>

        <table class="table border mb-4">
            <thead>
                <tr>
                    <th colspan="3">Logistics Requirements</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ $employeeRequest->logistics_requirement }}</td>
                </tr>
            </tbody>
        </table>

        <div class="row mt-4">
            <div class="col-lg-4 mb-4">
                <div><strong>Requested By:</strong></div>
                <div><strong>Name:</strong> {{ $employeeRequest->submittedLog->getCreatedBy() }} </div>
                <div><strong>Designation :</strong> {{ $employeeRequest->submittedLog->getDesignation() }}</div>
                <div><strong>Date:</strong> {{ $employeeRequest->submittedLog->getCreatedDate() }} </div>
            </div>
            <div class="col-lg-4 mb-4">
                <div><strong>Reviewed By:</strong></div>
                <div><strong>Name:</strong> {{ $employeeRequest->reviewedLog->getCreatedBy() }} </div>
                <div><strong>Designation :</strong> {{ $employeeRequest->reviewedLog->getDesignation() }}</div>
                <div><strong>Date:</strong> {{ $employeeRequest->reviewedLog->getCreatedDate() }} </div>
            </div>
            {{-- <div class="col-lg-4 mb-4">
                <div><strong>Recommended By:</strong></div>
                <div><strong>Name:</strong> {{ $employeeRequest->recommendedLog->getCreatedBy() }} </div>
                <div><strong>Designation :</strong> {{ $employeeRequest->recommendedLog->getDesignation() }}</div>
                <div><strong>Date:</strong> {{ $employeeRequest->recommendedLog->getCreatedDate() }} </div>
            </div> --}}
            <div class="col-lg-4 mb-4">
                <div><strong>Approved By:</strong></div>
                <div><strong>Name:</strong> {{ $employeeRequest->approvedLog->getCreatedBy() }} </div>
                <div><strong>Designation :</strong> {{ $employeeRequest->approvedLog->getDesignation() }}</div>
                <div><strong>Date:</strong> {{ $employeeRequest->approvedLog->getCreatedDate() }} </div>
            </div>
        </div>
    </div>
    <script>
        window.onload = print;
    </script>
@endsection
