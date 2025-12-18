@extends('layouts.container-report')

@section('title', 'Mid-Term Performance Review Form')
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

        .table tr th,
        .table tr td {
            padding: 0.25rem 0.75rem;
        }
    </style>
@endsection
@section('page_js')

@endsection

@section('page-content')


    <section class="print-info bg-white p-3" id="print-info">
        <div class="print-title fw-bold mb-3 translate-middle text-center ">
            <div class="fs-5"> HERD International</div>
            <div class="fs-8"> Mid-Term Performance Review Form</div>
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
                    <table class="table border mb-4">
                        <thead>
                            <tr>
                                <th scope="col" colspan="4" >A. EMPLOYEE AND SUPERVISOR DETAILS</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <th  scope="row">Employee Name:</th>
                                <td>{{$performanceReview->getEmployeeName()}}</td>
                                <th  scope="row">Employee Title:</th>
                                <td>{{$performanceReview->getEmployeeTitle()}}</td>
                            </tr>
                            <tr>
                                <th  scope="row">Supervisor Name:</th>
                                <td>{{$performanceReview->getSupervisorName()}}</td>
                                <th  scope="row">Supervisor Title: </th>
                                <td>{{$performanceReview->getSupervisorTitle()}}</td>
                            </tr>
                            <tr>
                                <th  scope="row">Technical Supervisor Name:</th>
                                <td>{{$performanceReview->getTechnicalSupervisorName()}}</td>
                                <th  scope="row">Technical Supervisor Title: </th>
                                <td>{{$performanceReview->getTechnicalSupervisorTitle()}}</td>
                            </tr>
                            <tr>
                                <th  scope="row">Date of joining:</th>
                                <td>{{$performanceReview->employee->getFirstJoinedDate()}}</td>
                                <th  scope="row">In current position since: </th>
                                <td>{{$performanceReview->getJoinedDate()}}</td>
                            </tr>
                            <tr>
                                <th  scope="row">Duty Station:</th>
                                <td colspan="3">{{$performanceReview->getDutyStation()}}</td>
                            </tr>
                            <tr>
                                <th scope="row">Review period from:</th>
                                <td>{{$performanceReview->getReviewFromDate()}}</td>
                                <th scope="row">Review period to: </th>
                                <td>{{$performanceReview->getReviewToDate()}}</td>
                            </tr>
                        </tbody>
                    </table>
                    <table class="table border">
                        <thead>
                            <tr>
                                <th scope="col" colspan="4" >B.EMPLOYEE FEEDBACK FOR THIS REVIEW PERIOD</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($groupBQuestions as $question)
                                <tr>
                                    <th  scope="row">{{$question->question}}</th>
                                    <td>{{$performanceReview->getAnswer($question->id)}}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <table class="table border">
                        <thead>
                            <tr>
                                <th scope="col" colspan="4" >C.	KEY GOALS REVIEW </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <th  scope="col">(Insert key goals agreed upon during previous performance review):</th>
                                <th  scope="col">To be completed by Employee:</th>
                                <th  scope="col">To be completed by Supervisor:</th>
                            </tr>
                            @foreach ($keygoals as $keygoal)
                                <tr>
                                    <td>{{$keygoal->title}}</td>
                                    <td>{{$keygoal->description_employee}}</td>
                                    <td>{{$keygoal->description_supervisor}}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <table class="table border">
                        <thead>
                            <tr>
                                <th scope="col" colspan="4" >Professional Development Plan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>{{$professionalDevelopmentPlan}}</td>
                            </tr>
                        </tbody>
                    </table>
                    <table class="table border">
                        <thead>
                            <tr>
                                <th scope="col" colspan="4" >D.	STRENGHTS AND AREAS FOR GROWTH (to be completed by supervisor)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($groupEQuestions as $question)
                                @if (!$loop->last)
                                    <tr>
                                        <th  scope="row" >{{$question->question}}</th>
                                        <td>{{$performanceReview->getAnswer($question->id)}}</td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                    <table class="table border">
                        <thead>
                            <tr>
                                <th scope="col" colspan="4" >E.	EMPLOYEE COMMENTS (optional)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($groupHQuestions as $question)
                                <tr>
                                    <td>{{$performanceReview->getAnswer($question->id)}}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <table class="table border">
                        <thead>
                            <tr>
                                <th scope="col" colspan="4" >F.	SUPERVISOR /NEXT LINE MANAGER COMMENTS (OPTIONAL)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($groupIQuestions as $question)
                                <tr>
                                    <td>{{$performanceReview->getAnswer($question->id)}}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <table class="table border">
                        <thead>
                            <tr>
                                <th scope="col" colspan="4" >G.	ACKNOWLEDGEMENTS</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($groupJQuestions as $question)
                                <tr>
                                    <td>{{$performanceReview->getAnswer($question->id)}}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <table class="table border mb-4">
                        <thead>
                            {{-- <tr>
                                <th scope="col" colspan="4" >G.	AKNOWLEDGEMENTS</th>
                            </tr> --}}
                        </thead>
                        <tbody>
                            <tr>
                                <th  scope="row">Employee Name:</th>
                                <td>{{$performanceReview->getEmployeeName()}}</td>
                                <th  scope="row">Date:</th>
                                <td>{{$performanceReview->logs->where('status_id', config('constant.SUBMITTED_STATUS'))->last()?->created_at}}</td>
                            </tr>
                            <tr>
                                <th  scope="row">Supervisor:</th>
                                <td>{{$performanceReview->getSupervisorName()}}</td>
                                <th  scope="row">Date:</th>
                                <td>{{$performanceReview->logs->where('status_id', config('constant.APPROVED_STATUS'))->last()?->created_at?->toFormattedDateString()}}</td>
                            </tr>
                            {{-- <tr>
                                <th  scope="row">Next Line Manager:</th>
                                <td>{{$performanceReview->getRecommenderName()}}</td>
                                <th  scope="row">Date:</th>
                                <td>{{$performanceReview->logs->where('status_id', config('constant.RECOMMENDED_STATUS'))->last()?->created_at}}</td>
                            </tr>
                            <tr>
                                <th  scope="row">Executive Director:</th>
                                <td>{{$performanceReview->getApproverName()}}</td>
                                <th  scope="row">Date:</th>
                                <td>{{$performanceReview->logs->where('status_id', config('constant.APPROVED_STATUS'))->last()?->created_at}}</td>
                            </tr> --}}


                        </tbody>
                    </table>
                </div>
            </div>




        </div>
    </section>

    <script>
        window.onload=print;
    </script>

@endsection

