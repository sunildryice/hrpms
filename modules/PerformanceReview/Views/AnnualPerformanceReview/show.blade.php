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
                url: "{{ route('performance.keygoal.employee.get') }}",
                data: {
                    '_token': "{{ csrf_token() }}",
                    'performance_review_id': "{{ $performanceReview->id }}",
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
                url: "{{ route('performance.keygoal.supervisor.get') }}",
                data: {
                    '_token': "{{ csrf_token() }}",
                    'performance_review_id': "{{ $performanceReview->id }}",
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
                            <a href="{{ route('performance.index') }}" class="text-decoration-none text-dark">Performance
                                Review</a>
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

        <!-- B. Key Goals Review -->
        <div id="keyGoalsReview" class="mb-3">
            <div class="card">
                <div class="card-header fw-bold">
                    <span class="card-title">
                        <span class="fw-bold">B.</span> Key Goals Review
                    </span>
                </div>
                <div class="card-body">
                    <table class="table table-bordered" id="keyGoalTable">
                        <thead>
                            <tr>
                                <th rowspan="2" style="width: 18%">Objective</th>
                                <th rowspan="2" style="width: 15%">Output / Deliverable</th>
                                <th rowspan="2" style="width: 22%">Major Activities</th>
                                <th colspan="2">Achievement against output / deliverable</th>
                            </tr>
                            <tr>
                                <th style="width: 15%">Status</th>
                                <th style="width: 25%">Remarks / Comments</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($keygoals as $keygoal)
                                <tr>
                                    <td>{{ $keygoal->title }}</td>
                                    <td>{{ $keygoal->output_deliverables }}</td>
                                    <td>{{ $keygoal->major_activities_employee ?? '—' }}</td>
                                    <td>
                                        <span class="badge {{ $keygoal->status?->colorClass() ?? 'bg-secondary' }}">
                                            {{ $keygoal->status?->label() ?? 'Not Set' }}
                                        </span>
                                    </td>
                                    <td>{{ $keygoal->remarks_employee ?? '—' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- C. Professional Development Plan -->
        <div id="professionalDevelopmentPlan" class="mb-3">
            <div class="card">
                <div class="card-header fw-bold">
                    <span class="card-title">
                        <span class="fw-bold">C.</span> Professional Development Plan
                    </span>
                </div>
                <div class="card-body">
                    @php $devPlans = $keyGoalReview->developmentPlans ?? collect(); @endphp

                    @if ($devPlans->isEmpty())
                        <div class="text-center text-muted py-4">
                            No professional development plan has been added yet.
                        </div>
                    @else
                        <table class="table table-bordered" id="devplan-table">
                            <thead>
                                <tr>
                                    <th style="width: 5%">SN</th>
                                    <th style="width: 45%">Development Plan Objective</th>
                                    <th style="width: 45%">Activity</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($devPlans as $index => $plan)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $plan->objective }}</td>
                                        <td>{{ $plan->activity ?? '—' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>
        </div>

        <!-- D. Core Competencies -->
        <div id="coreCompetenciesSection" class="mb-3">
            <div class="card">
                <div class="card-header fw-bold">
                    <span class="card-title">
                        <span class="fw-bold">D.</span> Core Competencies
                    </span>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th style="width: 35%">Competency</th>
                                <th style="width: 15%">Rating (1-5)</th>
                                <th style="width: 50%">Examples</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($coreCompetencies ?? collect() as $comp)
                                <tr>
                                    <td>{{ $comp->competency }}</td>
                                    <td>
                                        @php
                                            $ratings = [
                                                1 => '1 - Poor',
                                                2 => '2 - Fair',
                                                3 => '3 - Good',
                                                4 => '4 - Very Good',
                                                5 => '5 - Excellent',
                                            ];
                                        @endphp

                                        @if ($comp->rating)
                                            <span class="badge bg-primary">
                                                {{ $ratings[$comp->rating] ?? $comp->rating }}
                                            </span>
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td>{{ $comp->example ?? '—' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted">No core competencies recorded.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- E. Challenges / Difficulties -->
        <div id="challengesSection" class="mb-3">
            <div class="card">
                <div class="card-header fw-bold">
                    <span class="card-title">
                        <span class="fw-bold">E.</span> Challenges / Difficulties
                    </span>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th style="width: 45%">Challenge / Difficulty Faced</th>
                                <th style="width: 45%">Result / Outcome</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($challenges ?? collect() as $challenge)
                                <tr>
                                    <td>{{ $challenge->challenge }}</td>
                                    <td>{{ $challenge->result }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="text-center text-muted">No challenges recorded.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- F. Employee Comments -->
        <div id="employeeComments" class="mb-3">
            <div class="card mb-3">
                <div class="card-header fw-bold">
                    <span class="card-title">
                        <span class="fw-bold">F.</span>
                        Employee Comments
                    </span>
                </div>
                <div class="card-body">
                    <div class="col-md-12' }}">
                        <p class="mb-0">{{ $performanceReview->employee_comments ?: '—' }}</p>
                    </div>
                </div>
            </div>
        </div>

    </section>


    <section>
        <div class="card">
            <div class="card-header fw-bold">
                Performance Review Process
            </div>
            <div class="card-body">
                <div class="c-b">
                    @foreach ($performanceReview->logs as $log)
                        <div class="d-flex py-2 flex-row gap-2 mb-2 border-bottom ">
                            <div width="40" height="40" class="rounded-circle mr-3 user-icon">
                                <i class="bi-person-circle fs-5"></i>
                            </div>
                            <div class="w-100">
                                <div
                                    class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                                    <div
                                        class="d-flex flex-column flex-md-row align-items-start align-items-md-center gap-md-2 mb-2 mb-md-0">
                                        <label class="form-label mb-0">{{ $log->createdBy->getFullName() }}</label>
                                        <span class="badge bg-primary c-badge">
                                            {!! $log->createdBy->employee->latestTenure->getDesignationName() !!}
                                        </span>
                                    </div>
                                    <small
                                        title="{{ $log->created_at }}">{{ $log->created_at->format('M d, Y h:i A') }}</small>
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
