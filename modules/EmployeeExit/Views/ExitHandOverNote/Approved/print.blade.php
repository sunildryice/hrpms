@extends('layouts.container-report')

@section('title', 'Exit Handover Note')
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

        .detailTable th {
            font-weight: 600;
            width: 1%;
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

    <script type="text/javascript">
        window.print();
    </script>
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
                            <li><span class="fw-bold me-2">Employee Name
                                    :</span><span>{{ $handOverNote->employee->getFullName() }}</span></li>
                            <li><span
                                    class="fw-bold me-2">Designation:</span><span>{{ $handOverNote->employee->latestTenure->getDesignationName() }}</span>
                            </li>
                            <li><span class="fw-bold me-2">Duty
                                    Station:</span><span>{{ $handOverNote->employee->latestTenure->getDutyStation() }}</span>
                            </li>
                            <li><span class="fw-bold me-2">Joined Date
                                    :</span><span>{{ $handOverNote->employee->latestTenure->getJoinedDate() }}</span></li>
                            <li><span class="fw-bold me-2">Resigned Date :</span><span>
                                    {{ $handOverNote->getLastDutyDate() }}</span></li>
                            <li><span class="fw-bold me-2">Last Working Date :</span><span>
                                    {{ $handOverNote->getLastDutyDate() }}</span></li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="d-flex flex-column justify-content-end">
                        <div class="d-flex flex-column justify-content-end brand-logo mb-4 flex-grow-1">
                            <div class="d-flex flex-column justify-content-end float-right">
                                <img src="{{ asset('img/logonp.png') }}" alt=""
                                    class="align-self-end pe-5 logo-img">
                            </div>

                        </div>
                    </div>

                </div>
            </div>
        </div>
        <div class="print-body">
            <table class="table mb-5 detailTable">
                <tbody>
                    <tr>
                        <th>Brief Description of Duties:</th>
                        <td colspan="3">{{ $handOverNote->duty_description }}</td>
                    </tr>
                    <tr>
                        <th>Reporting Procedure:</th>
                        <td colspan="3">{{ $handOverNote->reporting_procedures }} </td>
                    </tr>
                    <tr>
                        <th>Meeting Description:</th>
                        <td colspan="3">{{ $handOverNote->meeting_description }}</td>
                    </tr>
                    <tr>
                        <th>Contact After Exit:</th>
                        <td colspan="3">{{ $handOverNote->contact_after_exit }}</td>
                    </tr>

                </tbody>
            </table>
            @if (count($handOverNote->handOverProjects))
                <div class="my-3 fw-bold fs-6">@lang('label.project-status')</div>
                <table class="table border">
                    <tbody>
                        <tr>
                            <th scope="row" class="col-md-2">Name of Project</th>
                            <th scope="row">Action Needed</th>
                            <th scope="row">Partners</th>
                            <th scope="row">Budget</th>
                            <th scope="row">Critical Issues</th>
                            <th scope="row">Status</th>

                        </tr>
                        @foreach ($handOverNote->handOverProjects as $project)
                            <tr>
                                <td>{{ $project->getProjectCode() }}</td>
                                <td>{{ $project->action_needed }}</td>
                                <td>{{ $project->partners }}</td>
                                <td>{{ $project->budget }}</td>
                                <td>{{ $project->critical_issues }}</td>
                                <td>{{ $project->project_status }}</td>

                            </tr>
                        @endforeach

                    </tbody>
                </table>
            @endif

            @if (count($handOverNote->handOverActivities))
                <div class="my-3 fw-bold fs-6">Activities </div>
                <table class="table border">
                    <tbody>
                        <tr>
                            <th scope="row"class="col-md-3">Name</th>
                            <th scope="row">Organization</th>
                            <th scope="row">Phone</th>
                            <th scope="row">Email</th>
                            <th scope="row" class="col-md-4">Comments</th>
                        </tr>
                        @foreach ($handOverNote->handoverActivities as $activity)
                            <tr>
                                <td>{{ $activity->getActivityCode() }}</td>
                                <td>{{ $activity->organization }}</td>
                                <td>{{ $activity->phone }}</td>
                                <td>{{ $activity->email }}</td>
                                <td>{{ $activity->comments }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif



            <div class="row mb-4">
                <div class="col-lg-6">
                    <div> <strong>Approver:</strong> {{ $handOverNote->getApproverName() }}</div>
                    <div><strong>Date:</strong> {{ $handOverNote->approvedLog?->created_at->format('M d, Y h:i A') }}</div>
                </div>
                <div class="col-lg-6">
                    <div><strong>Employee</strong>: {{ $handOverNote->employee->getFullName() }} </div>
                    <div><strong>Date:</strong> {{ $handOverNote->submittedLog?->created_at->format('M d, Y h:i A') }}
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection
