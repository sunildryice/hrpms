@extends('layouts.container-report')

@section('title', 'Training Request Print')
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
            width: 25%;
        }

        tbody,
        td,
        tfoot,
        th,
        thead,
        tr {
            border-width: 0.1px;
        }
    </style>
@endsection
@section('page_js')

@endsection

@section('page-content')

    <!-- CSS only -->

    <div class="print-title fw-bold mb-3 translate-middle text-center ">
        <div class="fs-5"> One Heart Worldwide</div>
        <div class="fs-8">{{$requester->getOfficeName()}}</div>
        <div class="fs-8"> Training Request Form </div>
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
        <table class="table mb-5">
            <tbody>
                <tr>
                    <td>Date of request:</td>
                    <td colspan="3">{{$trainingRequest->submittedLog?->getCreatedAt()}}</td>
                </tr>
                <tr>
                    <td>Requesting Employee Name / Designation/ Department:</td>
                    <td colspan="3">{{$trainingRequest->getRequesterName()}} / {{$requester->getDesignationName()}} / {{$requester->getDepartmentName()}} </td>
                </tr>
                <tr>
                    <td>Name of Course(s) or Training requested Training Institution Name</td>
                    <td colspan="3">{{$trainingRequest->title}}</td>
                </tr>
                <tr>
                    <td>Date of Course: Begin</td>
                    <td>{{$trainingRequest->getStartDate()}}</td>
                    <td>Own Time: {{$trainingRequest->own_time}} hours</td>
                    <td>Work Time: {{$trainingRequest->work_time}} hours</td>
                </tr>
                <tr>
                    <td>Date of Course: End</td>
                    <td>{{$trainingRequest->getEndDate()}}</td>
                    <td>Time of Course</td>
                    <td>{{$trainingRequest->duration}}</td>
                </tr>
            </tbody>
        </table>

        <div>
            <div>Course Fee(s) <span> Rs. {{$trainingRequest->course_fee}} </span>(attach receipt for fees) </div>
            <table class="table">
                <tbody>
                    <tr>
                        <td>Activity Code: {{$trainingRequest->activityCode->getActivityCode()}}</td>
                        <td>Account Code: {{$trainingRequest->accountCode->getAccountCode()}}</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div>
            <div>Brief description of course(s) (attach a copy of course(s) description): </div>
            <div class="border" style="min-height: 150px;">{{$trainingRequest->description}}</div>
        </div>
        <table class="table my-4">
            <thead>
                <th>To be filled by Requester</th>
                <th>Response</th>
            </thead>
            <tbody>
                @if($trainingRequestQuestions->count()>0)
                    @foreach($trainingRequestQuestions as $trainingRequestQuestion)
                        @if($trainingRequestQuestion->trainingQuestion->answer_type == 'textarea' && $trainingRequestQuestion->trainingQuestion->type == '1')
                            <tr>
                                <td>{{$trainingRequestQuestion->trainingQuestion->question}}</td>
                                <td>{{$trainingRequestQuestion->answer}}</td>
                            </tr>
                        @endif
                    @endforeach
                @endif
            </tbody>
        </table>
        <table class="table">
            <tbody>
                <tr>
                    <td rowspan="2">I meet the criteria for this application and I have: </td>
                    <td><i class="bi-check-circle-fill"></i> completed all information as requested,</td>
                </tr>

                <tr>
                    <td> @if(file_exists('storage/'.$trainingRequest->attachment) && $trainingRequest->attachment != '') <i class="bi-check-circle-fill"></i> @else _____________ @endif  attached the course description.</td>
                </tr>
                <tr>
                    <td colspan="2">I shall submit Training Report within Seven (7) days after completion of the training
                    </td>
                </tr>
                <tr>
                    <td>Employee Name: {{$trainingRequest->getRequesterName()}}</td>
                    <td>Date: {{ $trainingRequest->submittedLog?->getCreatedAt() }}</td>
                </tr>
            </tbody>
        </table>

        <table class="table">
            <thead>
                <th>To be filled by HR Officer</th>
                <th>Response</th>
            </thead>
            <tbody>
                @if($trainingRequestQuestions->count()>0)
                    @foreach($trainingRequestQuestions as $trainingRequestQuestion)
                        @if($trainingRequestQuestion->trainingQuestion->answer_type == 'textarea' && $trainingRequestQuestion->trainingQuestion->type == '3')
                            <tr>
                                <td>{{$trainingRequestQuestion->trainingQuestion->question}}</td>
                                <td>{{$trainingRequestQuestion->answer}}</td>
                            </tr>
                        @endif
                    @endforeach
                @endif
                <tr>
                    <td>Name of HR </td>
                    <td>{{$trainingRequest->getReviewerName()}}</td>
                </tr>
            </tbody>
        </table>
        <table class="table mb-5">
            <tbody>
                <tr>
                    <td>Department of Recommendation: {{$trainingRequest->recommendedLog?->getDepartmentName()}} </td>
                </tr>
                <tr>
                    <td>Recommended By (Name and Title): {{$trainingRequest->recommendedLog?->getFullName()}} / {{$trainingRequest->recommendedLog?->getDesignationName()}}</td>
                </tr>
                <tr>
                    <td>Date: {{ $trainingRequest->recommendedLog?->getCreatedAt() }} </td>
                </tr>
            </tbody>
        </table>
        <div>
            <strong>Approval:</strong>
            <table class="table">
                <tbody>
                    <tr>
                        <th colspan="2">Approved / Not Approved for this time</th>
                    </tr>
                    <tr>
                        <td rowspan="2">TIME FOR COURSE ATTENDANCE:</td>
                        <td>Approved as time worked.</td>
                    </tr>
                    <tr>
                        <td>Temporary work schedule adjustment.</td>
                    </tr>
                    <tr>
                        <td>Approved Amount for Training:</td>
                        <td>{{$trainingRequest->approved_amount == NULL?0:$trainingRequest->approved_amount}}</td>
                    </tr>
                    <tr>
                        <th colspan="2">Approved By: </th>
                    </tr>
                    <tr>
                        <td colspan="2">Name and Title: {{$trainingRequest->approvedLog?->getFullName()}} / {{$trainingRequest->approvedLog?->getDesignationName()}}</td>
                    </tr>
                    <tr>
                        <td colspan="2">Date: {{ $trainingRequest->approvedLog?->getCreatedAt() }}</td>
                    </tr>
                </tbody>

            </table>
        </div>



    </div>



@endsection
