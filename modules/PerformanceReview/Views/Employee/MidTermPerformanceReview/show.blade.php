@extends('layouts.container')

@section('title', 'View Mid-Term Performance Review')

@section('page_js')
    <script type="text/javascript">
        $(function() {
            $('#navbarVerticalMenu').find('#performance-employee-index').addClass('active');
        });
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
                            <a href="{{ route('performance.employee.index') }}"
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

        <!-- A. Employee and Line Manager Details -->
        @include('PerformanceReview::Partials.employeeDetails')

        <div id="employeeFeedbackForThisReviewPeriod" class="mb-3">
            <form action="{{ route('performance.answer.store') }}" method="POST" id="groupBForm">
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
                        @foreach ($groupBQuestions as $question)
                            @if (!$loop->first)
                                <hr>
                            @endif
                            <div class="row">
                                <label class="fw-bold">{{ $question->question }}</label>
                                <span class="mt-2">{{ $performanceReview->getAnswer($question->id) }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </form>
        </div>

        <div id="keyGoalsReview" class="mb-3">
            <form action="{{ route('performance.keygoal.update') }}" method="POST" id="groupCForm">
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
                        <table class="table table-sm table-bordered text-wrap" id="keyGoalTable">
                            <tr>
                                <th style="width: 24%">Key goals agreed upon during previous performance review</th>
                                <th style="width: 38%">Completed by Employee</th>
                                <th style="width: 38%">Completed by Supervisor</th>
                            </tr>

                            @foreach ($keygoals as $keygoal)
                                <tr>
                                    <td>
                                        <span class="text-black">{{ $keygoal->title }}</span>
                                    </td>
                                    <td>
                                        <span class="text-black">{{ $keygoal->description_employee }}</span>
                                    </td>
                                    <td>
                                        <span class="text-black">{{ $keygoal->description_supervisor }}</span>
                                    </td>
                                </tr>
                            @endforeach
                        </table>
                    </div>
                </div>
            </form>
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
                            <span>{{ $professionalDevelopmentPlan }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div id="strengthsAndAreasForGrowth" class="mb-3">
            <form action="{{ route('performance.answer.store') }}" method="POST" id="groupEForm">
                <div class="card">
                    <div class="card-header fw-bold">
                        <span class="card-title">
                            <span class="fw-bold">D.</span>
                            <span>
                                Strengths and Areas for Growth (to be completed by supervisor)
                            </span>
                        </span>
                    </div>
                    <div class="card-body">
                        @foreach ($groupEQuestions as $question)
                            @if (!$loop->last)
                                @if (!$loop->first)
                                    <hr>
                                @endif
                                <div class="row">
                                    <label class="fw-bold">{{ $question->question }}</label>
                                    <span class="mt-2">{{ $performanceReview->getAnswer($question->id) }}</span>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </form>
        </div>

        <div id="employeeComments" class="mb-3">
            <form action="{{ route('performance.answer.store') }}" method="POST" id="groupHForm">
                @foreach ($groupHQuestions as $question)
                    <div class="card">
                        <div class="card-header fw-bold">
                            <span class="card-title">
                                <span class="fw-bold">E.</span>
                                <span>
                                    {{ $question->question }}
                                </span>
                            </span>
                        </div>
                        <div class="card-body">
                            <div>
                                <span>{{ $performanceReview->getAnswer($question->id) }}</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </form>
        </div>

        <div id="supervisorComments" class="mb-3">
            <form action="{{ route('performance.answer.store') }}" method="POST" id="groupIForm">
                @foreach ($groupIQuestions as $question)
                    <div class="card">
                        <div class="card-header fw-bold">
                            <span class="card-title">
                                <span class="fw-bold">F.</span>
                                <span>
                                    {{ $question->question }}
                                </span>
                            </span>
                        </div>
                        <div class="card-body">
                            <div>
                                <span>{{ $performanceReview->getAnswer($question->id) }}</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </form>
        </div>

        <div id="acknowledgements" class="mb-3">
            <form action="{{ route('performance.answer.store') }}" method="POST" id="groupJForm">
                @foreach ($groupJQuestions as $question)
                    <div class="card">
                        <div class="card-header fw-bold">
                            <span class="card-title">
                                <span class="fw-bold">G.</span>
                                <span>
                                    {{ $question->question }}
                                </span>
                            </span>
                        </div>
                        <div class="card-body">
                            <div>
                                <span>{{ $performanceReview->getAnswer($question->id) }}</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </form>
        </div>

    </section>

    @if ($performanceReview->status_id == config('constant.RETURNED_STATUS'))
        <section>
            <div class="col-lg-6 m-2">
                <div class="row mb-2 border p-3">
                    <div>
                        <div class="d-flex align-items-start h-100">
                            <span class="fw-bold" style="text-decoration: underline">Remarks:</span>
                        </div>
                    </div>
                    <div class="mt-2">
                        <span>{{ $performanceReview->getLatestRemark() }}</span>
                    </div>
                </div>
            </div>
        </section>
    @endif
@endsection
