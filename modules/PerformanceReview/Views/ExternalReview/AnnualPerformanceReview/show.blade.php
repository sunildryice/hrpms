@extends('layouts.container')

@section('title', '360 Feedback - ' . $performanceReview->getReviewType())

@section('page_js')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#navbarVerticalMenu').find('#performance-external-review-index').addClass('active');

            // Save Draft
            $('#save-external-comments').on('click', function() {
                saveExternalComments(false);
            });

            // Submit & Close
            window.submitExternalReview = function() {
                // if (confirm('Are you sure you want to submit this review? This will close the 360 feedback and change status to Closed.')) {
                //     saveExternalComments(true);
                // }
                saveExternalComments(true);
            };

            function saveExternalComments(isSubmit) {
                let comments = $('#external_reviewer_comments').val().trim();

                if (!comments) {
                    toastr.error('Please provide Reviewer Comments before saving.', 'Validation Error');
                    return;
                }

                $.ajax({
                    type: 'POST',
                    url: "{{ route('performance.external-review.store') }}",
                    data: {
                        _token: "{{ csrf_token() }}",
                        performance_review_id: "{{ $performanceReview->id }}",
                        external_reviewer_comments: comments,
                        is_submit: isSubmit ? 1 : 0
                    },
                    success: function(response) {
                        if (response.type === 'success') {
                            toastr.success(response.message, 'Success');

                            if (isSubmit) {
                                setTimeout(() => {
                                    window.location.href =
                                        "{{ route('performance.external-review.index') }}";
                                }, 1500);
                            }
                        } else {
                            toastr.error(response.message || 'Failed to save comments.');
                        }
                    },
                    error: function(xhr) {
                        console.error(xhr);
                        toastr.error('Something went wrong. Please try again.');
                    }
                });
            }

        });
    </script>
@endsection

@section('page-content')

    <style>
        td,
        th {
            border: 1px solid grey;
            padding: 8px;
            text-align: left;
        }
    </style>

    <div class="pb-3 mb-3 page-header border-bottom">
        <div class="gap-2 d-flex flex-column flex-lg-row align-items-start align-items-lg-center">
            <div class="brd-crms flex-grow-1">
                <nav aria-label="breadcrumb">
                    <ol class="m-0 breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{!! route('dashboard.index') !!}" class="text-decoration-none text-dark">Home</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('performance.external-review.index') }}"
                                class="text-decoration-none text-dark">360 Feedback</a>
                        </li>
                        <li class="breadcrumb-item" aria-current="page">@yield('title')</li>
                    </ol>
                </nav>
                <h4 class="m-0 mt-1 lh1 fs-6 text-uppercase fw-bold text-primary">@yield('title')</h4>
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
                                <th rowspan="2" style="width: 10%">Objective</th>
                                <th rowspan="2" style="width: 15%">Output / Deliverable</th>
                                <th rowspan="2" style="width: 15%">Major Activities</th>
                                <th colspan="2">Achievement against output / deliverable</th>
                                <th rowspan="2" style="width: 22%">Line Manager Comments</th>
                            </tr>
                            <tr>
                                <th style="width: 10%">Status</th>
                                <th style="width: 15%">Remarks / Comments</th>
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
                                    <td>{{ $keygoal->description_supervisor ?? '—' }}</td>
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

        <!-- G. Line Manager Result and Comments -->
        <div id="managerResultComments" class="mb-3">
            <div class="card mb-3">
                <div class="card-header fw-bold">
                    <span class="card-title">
                        <span class="fw-bold">G.</span> Result and Comments
                    </span>
                </div>
                <div class="card-body">
                    <div class="col-md-12 mb-2">
                        <label class="form-label fw-bold">Result</label>
                        <p class="mb-0">{{ $performanceReview->result ?: '—' }}</p>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label fw-bold">Comments</label>
                        <p class="mb-0">{{ $performanceReview->comments ?: '—' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- H. External Reviewer Comments -->
        <div id="externalReviewerComments" class="mb-3">
            @if ($performanceReview->status_id != config('constant.CLOSED_STATUS'))
                <form id="groupHForm" method="POST">
                    @csrf
                    <input type="hidden" name="performance_review_id" value="{{ $performanceReview->id }}">

                    <div class="card">
                        <div class="card-header fw-bold">
                            <span class="card-title">
                                <span class="fw-bold">H.</span> Reviewer Comments
                            </span>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <textarea name="external_reviewer_comments" id="external_reviewer_comments" class="form-control" rows="4"
                                    placeholder="Provide detailed comments and feedback...">{{ old('external_reviewer_comments', $performanceReview->external_reviewer_comments ?? '') }}</textarea>
                            </div>
                        </div>
                        <div class="card-footer text-end">
                            <button type="button" class="btn btn-sm btn-outline-primary me-2"
                                id="save-external-comments">Save</button>
                            <button type="button" class="btn btn-sm btn-success" id="submit-external-review"
                                onclick="submitExternalReview()">Submit</button>
                        </div>
                    </div>
                </form>
            @else
                <div class="card border-success">
                    <div class="card-header fw-bold bg-light">
                        <span class="card-title">
                            <span class="fw-bold">H.</span> Reviewer Comments
                        </span>
                    </div>
                    <div class="card-body">
                        <div class="col-md-12' }}">
                            <p class="mb-0">{{ $performanceReview->external_reviewer_comments ?: '—' }}</p>
                        </div>
                    </div>
                </div>
            @endif
        </div>

    </section>

    <!-- Process Logs -->
    <section>
        <div class="card">
            <div class="card-header fw-bold">Performance Review Process</div>
            <div class="card-body">
                @foreach ($performanceReview->logs as $log)
                    <div class="d-flex py-2 flex-row gap-2 mb-2 border-bottom">
                        <div class="rounded-circle mr-3 user-icon">
                            <i class="bi-person-circle fs-5"></i>
                        </div>
                        <div class="w-100">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <strong>{{ $log->createdBy->getFullName() }}</strong>
                                    <span class="badge bg-primary ms-2">
                                        {!! $log->createdBy->employee?->latestTenure?->getDesignationName() !!}
                                    </span>
                                </div>
                                <small>{{ $log->created_at->format('M d, Y h:i A') }}</small>
                            </div>
                            <p class="mb-0 mt-1">{{ $log->log_remarks }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endsection
