@extends('layouts.container-report')

@section('title', 'Exit Interview Form Print')
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

    <script type="text/javascript">
        window.print();
    </script>
    <!-- CSS only -->


    <section class="print-info bg-white p-3" id="print-info">

        <div class="print-title fw-bold mb-3 translate-middle text-center ">
            <div class="fs-5"> HERD International</div>
            <div class="fs-8"> Exit Interview Form</div>
        </div>

        <div class="print-header">
            <div class="row">
                <div class="col-lg-8">

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
            <div>The objective of this questionnaire is to elicit your honest feedback using which the organization can
                learn
                from its shortcomings. All information provided by you will be kept confidential and used only for the
                purpose
                of organizational improvement. Consider this feedback as a parting gift to us.
            </div>
            <div class="my-3 fw-bold fs-6">Probation Record </div>
            <table class="table border">
                <tbody>
                    <tr>
                        <th scope="row">Employee Name:</th>
                        <td>{{ $data->employee->getFullName() }}</td>

                        <td>Immediate Head</td>
                        <td>{{ $data->employee->latestTenure->getSupervisorName() }}</td>
                    </tr>
                    <tr>
                        <td>Division / Dept.</td>
                        <td>{{ $data->employee->latestTenure->getDepartmentName() }}</td>

                        <td>Job Title</td>
                        <td>{{ $data->employee->latestTenure->getDesignationName() }}</td>
                    </tr>
                    <tr>
                        <td>Hire Date</td>
                        <td>{{ $data->employee->latestTenure->getJoinedDate() }}</td>

                        <td>Resignation/Termination Date</td>
                        <td>{{ $data->exitHandOverNote->getResignationDate() }}</td>
                    </tr>
                </tbody>
            </table>

            @php
                $next_key_carryover = 0;
            @endphp

            <table class="table border">
                <tbody>
                    @foreach ($data->exitInterviewAnswers as $key => $item)
                        <tr>
                            <td>{{ ++$key }}. {{ $item->exitQuestionsAnswer->question }}</td>
                        </tr>
                        <tr>
                            @if ($item->exitQuestionsAnswer->answer_type == 'textarea')
                                <td>{{ ucfirst($item->answer) }}</td>
                            @endif
                            @if ($item->exitQuestionsAnswer->answer_type == 'boolean')
                                <td>{{ strtolower($item->answer) == 'off' ? 'No' : 'Yes' }}</td>
                            @endif
                            @if ($item->exitQuestionsAnswer->answer_type == 'selectbox')
                                <td>{{ ucfirst($item->answer) }}</td>
                            @endif
                        </tr>
                        @if ($loop->last)
                            @php
                                $next_key_carryover = $key;
                            @endphp
                        @endif
                    @endforeach
                </tbody>
            </table>

            <table class="table border">
                <tbody>
                    <tr>
                        <td colspan="5">{{ ++$next_key_carryover }}. What did you think of your immediate head /
                            supervisor on the following points?</td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>Almost</td>
                        <td>Always</td>
                        <td>Usually</td>
                        <td>Sometimes Never</td>
                    </tr>
                    @foreach ($data->exitInterviewFeedbackAnswers as $item)
                        <tr>
                            <td>{{ $item->exitFeedback->title }}</td>
                            <td>{!! $item->always ? "<i class='bi bi-check'></i>" : '' !!}</td>
                            <td>{!! $item->almost ? "<i class='bi bi-check'></i>" : '' !!}</td>
                            <td>{!! $item->usually ? "<i class='bi bi-check'></i>" : '' !!}</td>
                            <td>{!! $item->sometimes ? "<i class='bi bi-check'></i>" : '' !!}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <table class="table border">
                <tbody>
                    <tr>
                        <td colspan="5">{{ ++$next_key_carryover }}. How would you rate the following?</td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>Excellent</td>
                        <td>Good</td>
                        <td>Fair</td>
                        <td>Poor</td>
                    </tr>
                    @foreach ($data->exitInterviewRatingAnswers as $item)
                        <tr>
                            <td>{{ $item->exitRating->title }}</td>
                            <td>{!! $item->excellent ? "<i class='bi bi-check'></i>" : '' !!}</td>
                            <td>{!! $item->good ? "<i class='bi bi-check'></i>" : '' !!}</td>
                            <td>{!! $item->fair ? "<i class='bi bi-check'></i>" : '' !!}</td>
                            <td>{!! $item->poor ? "<i class='bi bi-check'></i>" : '' !!}</td>
                        </tr>
                    @endforeach
                    <tr>
                        <td colspan="5" class="fw-bold content">Comments:</td>
                    </tr>

                </tbody>
            </table>

            <div class="row mb-4">
                <div class="col-lg-6">
                    <div> <strong>Human Resources Representative:</strong> {{ $data->approver?->getFullName() }}</div>
                    <div><strong>Date:</strong> {{ $data->approvedLog?->getCreatedAt() }}</div>
                </div>
                <div class="col-lg-6">
                    <div><strong>Employee</strong>: {{ $data->getEmployeeName() }} </div>
                    <div><strong>Date:</strong> {{ $data->submittedLog?->getCreatedAt() }}</div>
                </div>
            </div>
        </div>
    </section>

@endsection
