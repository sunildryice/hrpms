@extends('layouts.container')

@section('title', 'View Annual Performance Review')

@section('page_js')
<script type="text/javascript">
    $(function() {
        $('#navbarVerticalMenu').find('#performance-index').addClass('active');

        getKeyGoalsEmployee();
        getKeyGoalsSupervisor();
    });

    function getKeyGoalsEmployee() {
        $.ajax({
            type: 'POST',
            url: "{{route('performance.keygoal.employee.get')}}",
            data: {
                '_token': "{{csrf_token()}}",
                'performance_review_id': "{{$performanceReview->id}}",
            },
            success: function(data) {
                $('#keygoal_employee').html.empty;
                $('#keygoal_employee').html(data.goal);
            },
            error: function(data) {
                // toastr.error('Key goal could not be added.', 'Failed', {timeOut: 2000});
            }
        });
    }

    function getKeyGoalsSupervisor() {
        $.ajax({
            type: 'POST',
            url: "{{route('performance.keygoal.supervisor.get')}}",
            data: {
                '_token': "{{csrf_token()}}",
                'performance_review_id': "{{$performanceReview->id}}",
            },
            success: function(data) {
                $('#keygoal_supervisor').html.empty;
                $('#keygoal_supervisor').html(data.goal);
            },
            error: function(data) {
                // toastr.error('Key goal could not be added.', 'Failed', {timeOut: 2000});
            }
        });
    }
</script>
@endsection

@section('page-content')

        <div class="page-header pb-3 mb-3 border-bottom">
            <div class="d-flex flex-column flex-lg-row align-items-start align-items-lg-center gap-2">
            <div class="brd-crms flex-grow-1">
                <nav aria-label="breadcrumb">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item">
                                <a href="{!! route('dashboard.index') !!}" class="text-decoration-none text-dark">Home</a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="{{ route('performance.index') }}"
                                    class="text-decoration-none text-dark">Performance Review</a>
                            </li>
                            <li class="breadcrumb-item" aria-current="page">@yield('title')</li>
                        </ol>
                    </nav>
                    <h4 class="m-0 lh1 mt-1 fs-6 text-uppercase fw-bold text-primary">@yield('title')</h4>
                </div>
            </div>
        </div>
        <section>

            <div id="employeeAndSupervisorDetails" class="mb-3">
                <div class="card">
                    <div class="card-header fw-bold">
                        <span class="card-title">
                            <span class="fw-bold">A.</span>
                            <span>
                                Employee and Supervisor Details
                            </span>
                        </span>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="row">
                                    <div class="col-lg-4">
                                        <span class="fw-bold">Employee Name</span>
                                    </div>
                                    <div class="col-lg-6">
                                        <span>{{$performanceReview->getEmployeeName()}}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="row">
                                    <div class="col-lg-4">
                                        <span class="fw-bold">Employee Title</span>
                                    </div>
                                    <div class="col-lg-6">
                                        <span>{{$performanceReview->getEmployeeTitle()}}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="row">
                                    <div class="col-lg-4">
                                        <span class="fw-bold">Supervisor Name</span>
                                    </div>
                                    <div class="col-lg-6">
                                        <span>{{$performanceReview->getSupervisorName()}}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="row">
                                    <div class="col-lg-4">
                                        <span class="fw-bold">Supervisor Title</span>
                                    </div>
                                    <div class="col-lg-6">
                                        <span>{{$performanceReview->getSupervisorTitle()}}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="row">
                                    <div class="col-lg-4">
                                        <span class="fw-bold">Technical Supervisor's Name</span>
                                    </div>
                                    <div class="col-lg-6">
                                        <span>{{$performanceReview->getTechnicalSupervisorName()}}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="row">
                                    <div class="col-lg-4">
                                        <span class="fw-bold">Technical Supervisor's Title</span>
                                    </div>
                                    <div class="col-lg-6">
                                        <span>{{$performanceReview->getTechnicalSupervisorTitle()}}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="row">
                                    <div class="col-lg-4">
                                        <span class="fw-bold">Date of Joining</span>
                                    </div>
                                    <div class="col-lg-6">
                                        <span>{{$performanceReview->employee->getFirstJoinedDate()}}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="row">
                                    <div class="col-lg-4">
                                        <span class="fw-bold">In Current Position Since</span>
                                    </div>
                                    <div class="col-lg-6">
                                        <span>{{$performanceReview->getJoinedDate()}}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="row mb-2">
                            <div class="col-lg-6">
                                <div class="row">
                                    <div class="col-lg-4">
                                        <span class="fw-bold">Review period from:</span>
                                    </div>
                                    <div class="col-lg-6">
                                        <span>{{$performanceReview->getReviewFromDate()}}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="row">
                                    <div class="col-lg-4">
                                        <span class="fw-bold">Review period to:</span>
                                    </div>
                                    <div class="col-lg-6">
                                        <span>{{$performanceReview->getReviewToDate()}}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6 mt-3">
                                <div class="row">
                                    <div class="col-lg-4">
                                        <span class="fw-bold">Deadline:</span>
                                    </div>
                                    <div class="col-lg-6">
                                        <span>{{$performanceReview->getDeadlineDate()}}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div id="employeeFeedbackForThisReviewPeriod" class="mb-3">
                <div>
                    <div class="card">
                        <div class="card-header fw-bold">
                            <span class="card-title">
                                <span class="fw-bold">B.</span>
                                <span>
                                    Employee Feedback For This Review Period
                                </span>
                            </span>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                @if($midTermReview)
                                    <div class="col-md-6">
                                        <div class="card">
                                            <div class="card-header fw-bold">
                                                Mid-Term Review
                                            </div>
                                            <div class="card-body">
                                                @foreach ($groupBQuestions as $question)
                                                    @if (!$loop->first)
                                                        <hr>
                                                    @endif
                                                    <div class="row">
                                                        <label class="fw-bold">{{$question->question}}</label>
                                                        <span class="mt-2">{{$midTermReview->getAnswer($question->id)}}</span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-header fw-bold">
                                            Annual Review
                                        </div>
                                        <div class="card-body">
                                            @foreach ($groupBQuestions as $question)
                                                @if (!$loop->first)
                                                    <hr>
                                                @endif
                                                <div class="row">
                                                    <label class="fw-bold">{{$question->question}}</label>
                                                    <span class="mt-2">{{$performanceReview->getAnswer($question->id)}}</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div id="keyGoalsReview" class="mb-3">
                <div>
                    <div class="card">
                        <div class="card-header fw-bold">
                            <span class="card-title">
                                <span class="fw-bold">C.</span>
                                <span>
                                    Key Goals Review
                                </span>
                            </span>
                        </div>
                        <div class="card-body">

                                @if($midTermReview)
                                    <div class="card">
                                        <div class="card-header fw-bold">
                                            Mid-Term Review
                                        </div>
                                        <div class="card-body">
                                            <table class="table table-sm table-bordered text-wrap" id="keyGoalTable" style="width: 100%">
                                                <tr>
                                                    <th class="text-black" style="width: 20%">Key Goals</th>
                                                    <th class="text-black" style="width: 40%">To be completed by Employee</th>
                                                    <th class="text-black" style="width: 40%">To be completed by Supervisor</th>
                                                </tr>

                                                @foreach ($keygoals as $keygoal)
                                                    <tr>
                                                        <td class="text-black">{{$keygoal->title}}</td>
                                                        <td class="text-black">{{$keygoal->description_employee}}</td>
                                                        <td class="text-black">{{$keygoal->description_supervisor}}</td>
                                                    </tr>
                                                @endforeach
                                            </table>
                                        </div>
                                    </div>
                                @endif

                            <div class="card">
                                <div class="card-header fw-bold">
                                    Annual Review
                                </div>
                                <div class="card-body">
                                    <table class="table table-sm table-bordered text-wrap" id="keyGoalTable" style="width: 100%">
                                        <tr>
                                            <th class="text-black" style="width: 20%">Key Goals</th>
                                            <th class="text-black" style="width: 40%">To be completed by Employee</th>
                                            <th class="text-black" style="width: 40%">To be completed by Supervisor</th>
                                        </tr>

                                        @foreach ($keygoals as $keygoal)
                                            <tr>
                                                <td class="text-black">{{$keygoal->title}}</td>
                                                <td class="text-black">{{$keygoal->description_employee_annual}}</td>
                                                <td class="text-black">{{$keygoal->description_supervisor_annual}}</td>
                                            </tr>
                                        @endforeach
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div id="professionalDevelopmentPlan" class="mb-3">
                <div class="card">
                    <div class="card-header fw-bold">
                        <span class="card-title">
                            <span class="fw-bold"></span>
                            <span>
                                Professional Development Plan
                            </span>
                        </span>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-6">
                                <span>{{$professionalDevelopmentPlan}}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div id="keySkillsEvaluation" class="mb-3">
                <div>
                    <div class="card">
                        <div class="card-header fw-bold">
                            <span class="card-title">
                                <span class="fw-bold">D.</span>
                                <span>
                                    Key Skills Evaluation (to be completed by supervisor)
                                </span>
                            </span>
                        </div>
                        <div class="card-body">
                            @foreach ($groupDQuestions as $question)
                                @if (!$loop->first)
                                    <hr>
                                @endif
                                <div class="row">
                                    <label class="fw-bold">{{$question->question}}</label>
                                    <span class="mb-2">{{$performanceReview->getAnswer($question->id)}}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <div id="strengthsAndAreasForGrowth" class="mb-3">
                <div>
                    <div class="card">
                        <div class="card-header fw-bold">
                            <span class="card-title">
                                <span class="fw-bold">E.</span>
                                <span>
                                    Strengths and Areas for Growth (to be completed by supervisor)
                                </span>
                            </span>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                @if($midTermReview)
                                    <div class="col-md-6">
                                        <div class="card">
                                            <div class="card-header fw-bold">
                                                Mid-Term Review
                                            </div>
                                            <div class="card-body">
                                                @foreach ($groupEQuestions as $question)
                                                    @if (!$loop->last)
                                                        @if (!$loop->first)
                                                            <hr>
                                                        @endif
                                                        <div class="row">
                                                            <label class="fw-bold">{{$question->question}}</label>
                                                            <span class="mt-2">{{$midTermReview->getAnswer($question->id)}}</span>
                                                        </div>
                                                    @endif
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-header fw-bold">
                                            Annual Review
                                        </div>
                                        <div class="card-body">
                                            @foreach ($groupEQuestions as $question)
                                                @if (!$loop->last)
                                                    @if (!$loop->first)
                                                        <hr>
                                                    @endif
                                                    <div class="row">
                                                        <label class="fw-bold">{{$question->question}}</label>
                                                        <span class="mt-2">{{$performanceReview->getAnswer($question->id)}}</span>
                                                    </div>
                                                    {{-- <label for="{{'question_'.$question->id}}" class="fw-bold">{{$question->question}}</label>
                                                    <textarea disabled name="{{'question_'.$question->id}}" id="{{'question_'.$question->id}}" data-question-id="{{$question->id}}" style="width: 100%;" rows="5">{{$performanceReview->getAnswer($question->id)}}</textarea> --}}
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div id="overallPerformanceEvaluation" class="mb-3">
                <form action="{{route('performance.answer.store')}}" method="POST" id="groupFForm">
                    <div class="card">
                        <div class="card-header fw-bold">
                            <span class="card-title">
                                <span class="fw-bold">F.</span>
                                <span>
                                    Overall Performance Evaluation (to be completed by supervisor)
                                </span>
                            </span>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm table-bordered text-wrap">
                                <tr>
                                    @foreach ($groupFQuestions as $question)
                                        <th class="text-black" style="width: 20%">{{$question->question}}</th>
                                    @endforeach
                                </tr>
                                <tr>
                                    @foreach ($groupFQuestions as $question)
                                        <td class="text-black" style="font-style: italic">{{$question->description}}</td>
                                    @endforeach
                                </tr>
                                <tr>
                                    @php
                                        $counter = 5;
                                    @endphp
                                    @foreach ($groupFQuestions as $question)
                                        <td class="text-black" style="text-align: center">
                                            <input disabled type="checkbox" name="{{'question_'.$question->id}}" id="{{'question_'.$question->id}}" {{$performanceReview->getAnswer($question->id) == 'true' ? 'checked' : ''}}>
                                            <label for="{{'question_'.$question->id}}">{{$counter--}}</label>
                                        </td>
                                    @endforeach
                                </tr>
                            </table>
                        </div>
                    </div>
                </form>
            </div>

            <div id="identifyKeyGoals" class="mb-3">
                <form action="{{route('performance.answer.store')}}" method="POST" id="groupGForm">
                    @foreach ($groupGQuestions as $question)
                        <div class="card">
                            <div class="card-header fw-bold">
                                <span class="card-title">
                                    <span class="fw-bold">G.</span>
                                    <span>{{$question->question}}</span>
                                </span>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="row mb-2">
                                            <span class="fw-bold">Filled by Employee:</span>
                                        </div>
                                        <div class="row">
                                            @foreach ($futureKeyGoals->where('created_by', $performanceReview->requester_id) as $key => $item)
                                                <span>{{++$key.'. '.$item->title}}</span>
                                            @endforeach
                                        </div>
                                    </div>

                                    <div class="col-lg-6">
                                        <div class="row mb-2">
                                            <span class="fw-bold">Filled by Supervisor:</span>
                                        </div>
                                        <div class="row">
                                            @foreach ($futureKeyGoals->where('created_by', '!=', $performanceReview->requester_id) as $key => $item)
                                                <span>{{++$key.'. '.$item->title}}</span>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </form>
            </div>

            <div id="employeeComments" class="mb-3">
                <form action="{{route('performance.answer.store')}}" method="POST" id="groupHForm">
                    @foreach ($groupHQuestions as $question)
                        <div class="card">
                            <div class="card-header fw-bold">
                                <span class="card-title">
                                    <span class="fw-bold">H.</span>
                                    <span>
                                        {{$question->question}}
                                    </span>
                                </span>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                @if($midTermReview)
                                    <div class="col-md-6">
                                        <div class="card">
                                            <div class="card-header fw-bold">Mid-Term Review</div>
                                            <div class="card-body">
                                                <span>{{$midTermReview->getAnswer($question->id)}}</span>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                    <div class="col-md-6">
                                        <div class="card">
                                            <div class="card-header fw-bold">Annual Review</div>
                                            <div class="card-body">
                                                <span>{{$performanceReview->getAnswer($question->id)}}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </form>
            </div>

            <div id="supervisorComments" class="mb-3">
                <form action="{{route('performance.answer.store')}}" method="POST" id="groupIForm">
                    @foreach ($groupIQuestions as $question)
                        <div class="card">
                            <div class="card-header fw-bold">
                                <span class="card-title">
                                    <span class="fw-bold">I.</span>
                                    <span>
                                        {{$question->question}}
                                    </span>
                                </span>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                @if($midTermReview)
                                    <div class="col-md-6">
                                        <div class="card">
                                            <div class="card-header fw-bold">Mid-Term Review</div>
                                            <div class="card-body">
                                                <span>{{$midTermReview->getAnswer($question->id)}}</span>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                    <div class="col-md-6">
                                        <div class="card">
                                            <div class="card-header fw-bold">Annual Review</div>
                                            <div class="card-body">
                                                <span>{{$performanceReview->getAnswer($question->id)}}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </form>
            </div>

            <div id="acknowledgements" class="mb-3">
                <form action="{{route('performance.answer.store')}}" method="POST" id="groupJForm">
                    @foreach ($groupJQuestions as $question)
                        <div class="card">
                            <div class="card-header fw-bold">
                                <span class="card-title">
                                    <span class="fw-bold">J.</span>
                                    <span>
                                        {{$question->question}}
                                    </span>
                                </span>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                @if($midTermReview)
                                    <div class="col-md-6">
                                        <div class="card">
                                            <div class="card-header fw-bold">Mid-Term Review</div>
                                            <div class="card-body">
                                                <span>{{$midTermReview->getAnswer($question->id)}}</span>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                    <div class="col-md-6">
                                        <div class="card">
                                            <div class="card-header fw-bold">Annual Review</div>
                                            <div class="card-body">
                                                <span>{{$performanceReview->getAnswer($question->id)}}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </form>
            </div>

        </section>


        <section>
            <div class="card">
                <div class="card-header fw-bold">
                    Performance Review Process
                </div>
                <div class="card-body">
                    <div class="c-b">
                        @foreach($performanceReview->logs as $log)
                            <div class="d-flex py-2 flex-row gap-2 mb-2 border-bottom ">
                                <div width="40" height="40"
                                    class="rounded-circle mr-3 user-icon">
                                    <i class="bi-person-circle fs-5"></i>
                                </div>
                                <div class="w-100">
                                   <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                                   <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center gap-md-2 mb-2 mb-md-0">
                                            <label class="form-label mb-0">{{ $log->createdBy->getFullName() }}</label>
                                            <span class="badge bg-primary c-badge">
                                                {!! $log->createdBy->employee->latestTenure->getDesignationName() !!}
                                            </span>
                                        </div>
                                        <small title="{{$log->created_at}}">{{ $log->created_at->format('M d, Y h:i A') }}</small>
                                    </div>
                                    <p class="text-justify comment-text mb-0 mt-1">
                                        {{ $log->log_remarks }}
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>
@endsection
