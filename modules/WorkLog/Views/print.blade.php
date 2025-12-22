@extends('layouts.container-report')

@section('title', 'Monthly Work Log')
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
            border-bottom-width: 0px;
            text-align: justify;
            vertical-align: top;
        }
    </style>
@endsection

@section('page-content')
    <script type="text/javascript">
        window.print();
    </script>
    <!-- CSS only -->
    <section class="print-info bg-white p-3" id="print-info">

        <div class="print-title fw-bold mb-3 translate-middle text-center">
            <div class="fs-5"> HERD International</div>
            <div class="fs-8">{{ $requester->getOfficeName() }}</div>
            <div class="fs-8"> Monthly Work Plan</div>
        </div>

        <div class="print-header">
            <div class="row">
                <div class="col-lg-8">
                    <div class="print-header-info my-3">
                        <ul class="list-unstyled m-0 p-0 fs-7">
                            <li><strong>Name of Staff: </strong> {{ $workPlan->getRequester() }} </li>
                            <li><strong>Designation: </strong> {{ $requester->getDesignationName() }}</li>
                            <li><strong>Districts: </strong> {{ $requester->latestTenure->office->getOfficeName() }}</li>
                            <li><strong>Year: </strong> {{ $workPlan->year }}</li>
                            <li><strong>Month: </strong> {{ date('F', mktime(0, 0, 0, $workPlan->month, 10)) }}</li>

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
        <div class="print-body mt-4">
            <table class="table border">
                <thead>
                    <th>Date</th>
                    <th>Day</th>
                    <th width="35%">Major Activities/Planned Tasks </th>
                    <th>Activity Area</th>
                    <th>Priority</th>
                    <th>Status</th>
                    <th>Other activities conducted</th>
                    <th>Remarks</th>
                </thead>
                <tbody>
                    @foreach ($logData as $key => $log_data)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $log_data['day'] }}</td>
                            <td>{{ $log_data['major_activities'] }}</td>
                            <td>{{ $log_data['activity_area'] }}</td>
                            <td>{{ $log_data['priority'] }}</td>
                            <td>{{ $log_data['status'] }}</td>
                            <td>{{ $log_data['other_activities'] }}</td>
                            <td>{{ $log_data['remarks'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <table class="table">
                <thead>
                    <th>Summary of Major tasks</th>
                    <th>Planned</th>
                    <th>Completed</th>
                </thead>
                <tbody>
                    <tr>
                        <td width="30%">{{ $workPlan->summary }}</td>
                        <td width="30%">{{ $workPlan->planned }}</td>
                        <td width="30%">{{ $workPlan->completed }}</td>
                    </tr>
                </tbody>
            </table>
            <div class="row">
                @if ($workPlan->status_id != config('constant.CREATED_STATUS'))
                    <div class="col-lg-6">Submitted by: {{ $workPlan->getRequester() }}</div>
                @else
                    <div class="col-lg-6"></div>
                @endif
                <div class="col-lg-6">Approved by:
                    @if ($workPlan->status_id == config('constant.APPROVED_STATUS'))
                        {{ $workPlan->getApprover() }}
                    @endif
                </div>
            </div>
        </div>
    </section>
@endsection
