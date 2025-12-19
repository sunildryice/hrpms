@extends('layouts.container-report')

@section('title', 'Annual Performance Review Form')
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


    <section class="p-3 bg-white print-info" id="print-info">
        <div class="mb-3 text-center print-title fw-bold translate-middle">
            <div class="fs-5"> HERD International</div>
            <div class="fs-8"> Annual Performance Review Form</div>
        </div>

        <div class="print-header">
            <div class="row">
                <div class="col-lg-8">

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

        <div class="mb-5 print-body">

            <div class="row">
                <div class="col-lg-12">
                    <table class="table mb-4 border">
                        <thead>
                            <tr>
                                <th scope="col" colspan="4">A. EMPLOYEE AND SUPERVISOR DETAILS</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <th scope="row">Employee Name:</th>
                                <td>{{ $performanceReview->getEmployeeName() }}</td>
                                <th scope="row">Employee Title:</th>
                                <td>{{ $performanceReview->getEmployeeTitle() }}</td>
                            </tr>
                            <tr>
                                <th scope="row">Supervisor Name:</th>
                                <td>{{ $performanceReview->getSupervisorName() }}</td>
                                <th scope="row">Supervisor Title: </th>
                                <td>{{ $performanceReview->getSupervisorTitle() }}</td>
                            </tr>
                            <tr>
                                <th scope="row">Technical Supervisor Name:</th>
                                <td>{{ $performanceReview->getTechnicalSupervisorName() }}</td>
                                <th scope="row">Technical Supervisor Title: </th>
                                <td>{{ $performanceReview->getTechnicalSupervisorTitle() }}</td>
                            </tr>
                            <tr>
                                <th scope="row">Date of joining:</th>
                                <td>{{ $performanceReview->employee->getFirstJoinedDate() }}</td>
                                <th scope="row">In current position since: </th>
                                <td>{{ $performanceReview->getJoinedDate() }}</td>
                            </tr>
                            <tr>
                                <th scope="row">Duty Station:</th>
                                <td colspan="3">{{ $performanceReview->getDutyStation() }}</td>
                            </tr>
                            <tr>
                                <th scope="row">Review period from:</th>
                                <td>{{ $performanceReview->getReviewFromDate() }}</td>
                                <th scope="row">Review period to: </th>
                                <td>{{ $performanceReview->getReviewToDate() }}</td>
                            </tr>
                        </tbody>
                    </table>

                    <table class="table border">
                        <thead>
                            <tr>
                                <th scope="col" colspan="4">B.EMPLOYEE FEEDBACK FOR THIS REVIEW PERIOD</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if ($midTermReview)
                                <th colspan="2">Mid-Term Review</th>
                                @foreach ($groupBQuestions as $question)
                                    <tr>
                                        <th scope="row">{{ $question->question }}</th>
                                        <td>{{ $midTermReview->getAnswer($question->id) }}</td>
                                    </tr>
                                @endforeach

                            @endif
                            <th colspan="2">Annual Review</th>
                            @foreach ($groupBQuestions as $question)
                                <tr>
                                    <th scope="row">{{ $question->question }}</th>
                                    <td>{{ $performanceReview->getAnswer($question->id) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>


                    <table class="table border">
                        <thead>
                            <tr>
                                <th scope="col" colspan="4">C. KEY GOALS REVIEW </th>
                            </tr>
                        </thead>
                        <tbody>
                            @if ($midTermReview)
                                <th colspan="3">Mid-Term Review</th>
                                <tr>
                                    <th scope="col">Key goals agreed upon during previous performance review</th>
                                    <th scope="col">To be completed by Employee</th>
                                    <th scope="col">To be completed by Supervisor</th>
                                </tr>
                                @foreach ($keygoals as $key => $keygoal)
                                    <tr>
                                        <td>{{ ++$key . '. ' . $keygoal->title }}</td>
                                        <td>{{ $keygoal->description_employee }}</td>
                                        <td>{{ $keygoal->description_supervisor }}</td>
                                    </tr>
                                @endforeach
                            @endif

                            <th colspan="3">Annual Review</th>
                            <tr>
                                <th scope="col">Key goals agreed upon during previous performance review</th>
                                <th scope="col">To be completed by Employee</th>
                                <th scope="col">To be completed by Supervisor</th>
                            </tr>
                            @foreach ($keygoals as $key => $keygoal)
                                <tr>
                                    <td>{{ ++$key . '. ' . $keygoal->title }}</td>
                                    <td>{{ $keygoal->description_employee_annual }}</td>
                                    <td>{{ $keygoal->description_supervisor_annual }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <table class="table border">
                        <thead>
                            <tr>
                                <th scope="col" colspan="4">PROFESSIONAL DEVELOPMENT PLAN</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>{{ $professionalDevelopmentPlan }}</td>
                            </tr>
                        </tbody>
                    </table>


                    <table class="table border">
                        <thead>
                            <tr>
                                <th scope="col" colspan="4">D. KEY SKILLS EVALUATION (to be completed by supervisor)
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($groupDQuestions as $question)
                                <tr>
                                    <th scope="row">{{ $question->question }}</th>
                                    <td>{{ $performanceReview->getAnswer($question->id) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>


                    <table class="table border">
                        <thead>
                            <tr>
                                <th scope="col" colspan="4">E. STRENGTHS AND AREAS FOR GROWTH (to be completed by
                                    supervisor)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if ($midTermReview)
                                <th colspan="2">Mid-Term Review</th>
                                @foreach ($groupEQuestions as $question)
                                    @if (!$loop->last)
                                        <tr>
                                            <th scope="row">{{ $question->question }}</th>
                                            <td>{{ $midTermReview->getAnswer($question->id) }}</td>
                                        </tr>
                                    @endif
                                @endforeach
                            @endif

                            <th colspan="2">Annual Review</th>
                            @foreach ($groupEQuestions as $question)
                                @if (!$loop->last)
                                    <tr>
                                        <th scope="row">{{ $question->question }}</th>
                                        <td>{{ $performanceReview->getAnswer($question->id) }}</td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>


                    <table class="table border">
                        <thead>
                            <tr>
                                <th scope="col" colspan="4">F. OVERALL PERFORMANCE EVALUATION (to be completed by
                                    supervisor)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                @foreach ($groupFQuestions as $question)
                                    <th style="width: 20%">{{ $question->question }}</th>
                                @endforeach
                            </tr>
                            <tr>
                                @foreach ($groupFQuestions as $question)
                                    <td style="font-style: italic">{{ $question->description }}</td>
                                @endforeach
                            </tr>
                            <tr>
                                @php
                                    $counter = 5;
                                @endphp
                                @foreach ($groupFQuestions as $question)
                                    <td>
                                        <input disabled type="checkbox" name="{{ 'question_' . $question->id }}"
                                            id="{{ 'question_' . $question->id }}"
                                            {{ $performanceReview->getAnswer($question->id) == 'true' ? 'checked' : '' }}>
                                        <label for="{{ 'question_' . $question->id }}">{{ $counter-- }}</label>
                                    </td>
                                @endforeach
                            </tr>
                        </tbody>
                    </table>


                    <table class="table border">
                        <thead>
                            <tr>
                                <th scope="col" colspan="4">G. IDENTIFY AT LEAST 3 KEY GOALS FOR THE NEXT REVIEW
                                    PERIOD: (To be completed jointly)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $counter = 1;
                            @endphp
                            @foreach ($futureKeyGoals as $item)
                                <tr>
                                    <td>{{ $counter++ . '. ' . $item->title }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <table class="table border">
                        <thead>
                            <tr>
                                <th scope="col" colspan="4">H. EMPLOYEE COMMENTS (optional)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($groupHQuestions as $question)
                                @if ($midTermReview)
                                    <tr>
                                        <th>Mid-Term Review</th>
                                        <td>{{ $midTermReview->getAnswer($question->id) }}</td>
                                    </tr>
                                @endif
                                <tr>
                                    <th>Annual Review</th>
                                    <td>{{ $performanceReview->getAnswer($question->id) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>


                    <table class="table border">
                        <thead>
                            <tr>
                                <th scope="col" colspan="4">I. SUPERVISOR /NEXT LINE MANAGER COMMENTS (OPTIONAL)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($groupIQuestions as $question)
                                @if ($midTermReview)
                                    <tr>
                                        <th>Mid-Term Review</th>
                                        <td>{{ $midTermReview->getAnswer($question->id) }}</td>
                                    </tr>
                                @endif
                                <tr>
                                    <th>Annual Review</th>
                                    <td>{{ $performanceReview->getAnswer($question->id) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <table class="table border">
                        <thead>
                            <tr>
                                <th scope="col" colspan="4">J. ACKNOWLEDGEMENTS</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($groupJQuestions as $question)
                                @if ($midTermReview)
                                    <tr>
                                        <th>Mid-Term Review</th>
                                        <td>{{ $midTermReview->getAnswer($question->id) }}</td>
                                    </tr>
                                @endif
                                <tr>
                                    <th>Annual Review</th>
                                    <td>{{ $performanceReview->getAnswer($question->id) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>


                    <table class="table mb-4 border">
                        <tbody>
                            <tr>
                                <th scope="row">Employee Name:</th>
                                <td>{{ $performanceReview->getEmployeeName() }}</td>
                                <th scope="row">Date:</th>
                                <td>{{ $performanceReview->logs->where('status_id', config('constant.SUBMITTED_STATUS'))->last()?->created_at }}
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">Supervisor:</th>
                                <td>{{ $performanceReview->getSupervisorName() }}</td>
                                <th scope="row">Date:</th>
                                <td>{{ $performanceReview->logs->where('status_id', config('constant.VERIFIED_STATUS'))->last()?->created_at }}
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">Next Line Manager:</th>
                                <td>{{ $performanceReview->getRecommenderName() }}</td>
                                <th scope="row">Date:</th>
                                <td>{{ $performanceReview->logs->where('status_id', config('constant.RECOMMENDED_STATUS'))->last()?->created_at }}
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">Executive Director:</th>
                                <td>{{ $performanceReview->getApproverName() }}</td>
                                <th scope="row">Date:</th>
                                <td>{{ $performanceReview->logs->where('status_id', config('constant.APPROVED_STATUS'))->last()?->created_at }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>

    <script>
        window.onload = print;
    </script>

@endsection
