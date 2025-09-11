@extends('layouts.container-report')

@section('title', 'Event Completion Report')
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


        .about-table td,
        .about-table th {
            border: none;
        }

        .main-title {
            font-size: 1.5rem;
            font-weight: bold;
            margin-bottom: 20px;
        }
    </style>
@endsection
@section('page-content')
    <script type="text/javascript"></script>

    <section class="print-info bg-white p-3" id="print-info">
        <div class="print-title fw-bold mb-3 translate-middle text-center ">
            <div class="fs-5"> One Heart Worldwide</div>
            <div class="fs-8">{{ $eventCompletion->office->getOfficeName() }}</div>
            <div class="fs-8 main-title"> Event Completion Report</div>
        </div>

        <div class="print-header">
            <div class="row">
                <div class="col-lg-8">

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

            <div class="row">
                <div class="col-lg-12">
                    {{-- <table class="table border about-table mb-4">
                        <tbody>
                            <tr>
                                <th scope="row" class="col-md-2">Name of District:</th>
                                <td>{{ $eventCompletion->getDistrictName() }}</td>

                            </tr>
                            <tr>
                                <th scope="row">Name of Activity:</th>
                                <td>{{ $eventCompletion->getActivityName() }}</td>


                            </tr>
                            <tr>
                                <th scope="row">Venue:</th>
                                <td>{{ $eventCompletion->venue }}</td>
                            </tr>
                            <tr>
                                <th scope="row">Program Date:</th>
                                <td colspan="3">{{ $eventCompletion->getProgramDate() }}</td>

                            </tr>
                            <tr>
                                <th scope="row">Background:</th>
                                <td colspan="3">{{ $eventCompletion->background }}</td>
                            </tr>
                            <tr>
                                <th scope="row">Objectives:</th>
                                <td colspan="3">{{ $eventCompletion->objectives }}</td>
                            </tr>
                            <tr>
                                <th scope="row">Process:</th>
                                <td colspan="3">{{ $eventCompletion->process }}</td>
                            </tr>
                            <tr>
                                <th scope="row">Closing:</th>
                                <td colspan="3">{{ $eventCompletion->closing }}</td>
                            </tr>

                        </tbody>
                    </table> --}}

                    <div class="row">
                        <div class="col-9">
                            <h4 class="m-0 lh1 mt-0 mb-2 fs-6 text-uppercase fw-bold">Event Details:
                            </h4>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-lg-2">
                            <div class="d-flex align-items-start">
                                <label for="validationfullname" class="m-0 text-end flex-grow-1 fw-bold">Name of District:
                                </label>
                            </div>
                        </div>
                        <div class="col-lg-5">

                            {{ $eventCompletion->getDistrictName() }}
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-lg-2">
                            <div class="d-flex align-items-start">
                                <label for="validationfullname" class="m-0 text-end flex-grow-1 fw-bold">Name of Activity:
                                </label>
                            </div>
                        </div>
                        <div class="col-lg-5">
                            {{ $eventCompletion->getActivityName() }}

                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-lg-2">
                            <div class="d-flex align-items-start">
                                <label for="validationfullname" class="m-0 text-end flex-grow-1 fw-bold">Venue:
                                </label>
                            </div>
                        </div>
                        <div class="col-lg-5">
                            {{ $eventCompletion->venue }}}

                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-lg-2">
                            <div class="d-flex align-items-start">
                                <label for="validationfullname" class="m-0 text-end flex-grow-1 fw-bold">Program Date:
                                </label>
                            </div>
                        </div>
                        <div class="col-lg-5">

                            {{ $eventCompletion->getProgramDate() }}
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-lg-2">
                            <div class="d-flex align-items-start">
                                <label for="validationfullname" class="m-0 text-end flex-grow-1 fw-bold">Background:
                                </label>
                            </div>
                        </div>
                        <div class="col-lg-5">
                            {{ $eventCompletion->background }}

                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-lg-2">
                            <div class="d-flex align-items-start">
                                <label for="validationfullname" class="m-0 text-end flex-grow-1 fw-bold">Objectives:
                                </label>
                            </div>
                        </div>
                        <div class="col-lg-5">

                            {{ $eventCompletion->objectives }}
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-lg-2">
                            <div class="d-flex align-items-start">
                                <label for="validationfullname" class="m-0 text-end flex-grow-1 fw-bold">Process:
                                </label>
                            </div>
                        </div>
                        <div class="col-lg-5">
                            {{ $eventCompletion->process }}

                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-lg-2">
                            <div class="d-flex align-items-start">
                                <label for="validationfullname" class="m-0 text-end flex-grow-1 fw-bold">Closing:
                                </label>
                            </div>
                        </div>
                        <div class="col-lg-5">

                            {{ $eventCompletion->closing }}
                        </div>
                    </div>
                    <h4 class="m-0 lh1 mt-4 mb-2 fs-6 text-uppercase fw-bold">List of Participants:
                    </h4>
                    <table class="table border mb-4">
                        <thead>
                            <tr>
                                <th>S.N</th>
                                <th>Name of Participant</th>
                                <th>Office</th>
                                <th>Designation</th>
                                <th>Contact</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($eventCompletion->participants as $index => $participant)
                                <tr>
                                    <td>{{ ++$index }}</td>
                                    <td>{{ $participant->getFullName() }}</td>
                                    <td>{{ $participant->office }}</td>
                                    <td>{{ $participant->designation }}</td>
                                    <td>{{ $participant->contact }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>


                    <div class="row mt-4">
                        <div class="col-lg-4 mb-4">
                            <div><strong>Prepared By:</strong></div>
                            <div><strong>Name:</strong> {{ $eventCompletion->getRequesterName() }} </div>
                            <div><strong>Title:</strong> {{ $requester->getDesignationName() }} </div>
                            <div>
                                <strong>Date:</strong>
                                {{ $eventCompletion->submittedLog?->getCreatedAt() }}
                            </div>
                        </div>
                        <div class="col-lg-4 mb-4">
                            <div><strong>Approved By:</strong></div>
                            <div><strong>Name:</strong> {{ $eventCompletion->getApproverName() }} </div>
                            <div><strong>Title:</strong> {{ $eventCompletion->approver->employee->getDesignationName() }}
                            </div>
                            <div><strong>Date:</strong>
                                {{ $eventCompletion->approvedLog?->getCreatedAt() }} </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection
