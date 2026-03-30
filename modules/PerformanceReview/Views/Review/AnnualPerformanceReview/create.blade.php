@extends('layouts.container')

@section('title', 'Annual Performance Review Form')

@section('page_js')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#navbarVerticalMenu').find('#performance-review-index').addClass('active');

            $('#receiver').hide();
            let errors = {!! $errors !!};
            if (errors.receiver_id != undefined) {
                $('#receiver').show();
            }
            if ($('#status_id').val() == {{ config('constant.VERIFIED_STATUS') }}) {
                $('#receiver').show();
            }

            let previousLength = 0;
            const handleInput = (event) => {
                const bullet = "\u2022";
                const newLength = event.target.value.length;
                const characterCode = event.target.value.substr(-1).charCodeAt(0);

                if (newLength > previousLength) {
                    if (characterCode === 10) {
                        event.target.value = `${event.target.value}${bullet} `;
                    } else if (newLength === 1) {
                        event.target.value = `${bullet} ${event.target.value}`;
                    }
                }

                previousLength = newLength;
            }

            $('input[type="checkbox"]').on('change', function() {
                $('input[type="checkbox"]').not(this).prop('checked', false);
            });


            $('#status_id').on('change', function() {
                let status = $('#status_id').val();
                if (status == {{ config('constant.VERIFIED_STATUS') }}) {
                    $('#receiver').show();
                } else {
                    $('#receiver').hide();
                }
            });

            // GROUP B - KEY GOALS (Line Manager Comments)
            $('#groupBForm').on('submit', function(e) {
                e.preventDefault();

                let isValid = true;

                $('#keyGoalTable tbody tr').each(function() {
                    const supervisorComment = $(this).find('.description-supervisor').val().trim();

                    if (!supervisorComment) {
                        isValid = false;
                        $(this).addClass('table-danger');
                    } else {
                        $(this).removeClass('table-danger');
                    }
                });

                if (!isValid) {
                    toastr.error('Please fill Line Manager Comments for all key goals.',
                        'Validation Error');
                    return;
                }

                // Save each row via AJAX
                $('#keyGoalTable tbody tr').each(function() {
                    let row = $(this);
                    let keyGoalId = row.data('keygoal-id');

                    if (!keyGoalId) return;

                    let descriptionSupervisor = row.find('.description-supervisor').val().trim();

                    updateKeyGoalSupervisor(keyGoalId, descriptionSupervisor);
                });

                toastr.success('Line Manager Comments saved successfully!', 'Success', {
                    timeOut: 1500
                });
            });

            function updateKeyGoalSupervisor(keyGoalId, descriptionSupervisor) {
                $.ajax({
                    type: 'POST',
                    url: "{{ route('performance.keygoal.update') }}",
                    data: {
                        _token: "{{ csrf_token() }}",
                        key_goal_id: keyGoalId,
                        performance_review_id: "{{ $performanceReview->id }}",
                        description_supervisor: descriptionSupervisor,
                        type: 'current'
                    },
                    success: function(res) {
                        // toastr.success('Comment updated');
                    },
                    error: function(err) {
                        console.error(err);
                        toastr.error('Failed to update one of the comments.');
                    }
                });
            }

            // GROUP G - LINE MANAGER RESULT AND COMMENTS
            $('#groupGForm').on('submit', function(e) {
                e.preventDefault();

                let result = $('#result').val().trim();
                let comments = $('#comments').val().trim();

                if (!result || !comments) {
                    toastr.error('Please provide both Result and Comments before saving.',
                        'Validation Error');
                    return;
                }

                $.ajax({
                    type: 'POST',
                    url: "{{ route('performance.manager.result.store') }}",
                    data: {
                        _token: "{{ csrf_token() }}",
                        performance_review_id: "{{ $performanceReview->id }}",
                        result: result,
                        comments: comments
                    },
                    success: function(response) {
                        if (response.type === 'success') {
                            toastr.success('Result and Comments saved successfully!',
                                'Success');
                        } else {
                            toastr.error(response.message ||
                                'Failed to save result and comments.');
                        }
                    },
                    error: function(xhr) {
                        console.error(xhr);
                        toastr.error('Something went wrong while saving result and comments.');
                    }
                });
            });

            // validateForm()
            const managerResult = $('#result').val().trim();
            const managerComments = $('#comments').val().trim();

            if (!managerResult && !managerComments) {
                isGroupGFormSaved = false;
                $('#result, #comments').addClass('is-invalid');
                toastr.error('Please fill Result and Comments (Section G) before submitting.', 'Validation Error');
            } else {
                $('#result, #comments').removeClass('is-invalid');
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
                            <a href="{{ route('performance.review.index') }}"
                                class="text-decoration-none text-dark">Performance Review</a>
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
            <form action="{{ route('performance.keygoal.update') }}" method="POST" id="groupBForm">
                @csrf
                <input type="hidden" name="performance_review_id" value="{{ $performanceReview->id }}">

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
                            <tbody id="keygoal-body">
                                @foreach ($keygoals as $keygoal)
                                    <tr data-keygoal-id="{{ $keygoal->id }}">
                                        <td>{{ $keygoal->title }}</td>
                                        <td>{{ $keygoal->output_deliverables }}</td>
                                        <td>{{ $keygoal->major_activities_employee ?? '—' }}</td>
                                        <td>
                                            <span class="badge {{ $keygoal->status?->colorClass() ?? 'bg-secondary' }}">
                                                {{ $keygoal->status?->label() ?? 'Not Set' }}
                                            </span>
                                        </td>
                                        <td>{{ $keygoal->remarks_employee ?? '—' }}</td>
                                        <td>
                                            <textarea name="description_supervisor_{{ $keygoal->id }}" class="form-control description-supervisor" rows="1">{{ $keygoal->description_supervisor ?? '' }}</textarea>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-sm btn-outline-primary float-end">Save</button>
                    </div>
                </div>
            </form>
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
            <form id="groupGForm" method="POST">
                @csrf
                <input type="hidden" name="performance_review_id" value="{{ $performanceReview->id }}">

                <div class="card">
                    <div class="card-header fw-bold">
                        <span class="card-title">
                            <span class="fw-bold">G.</span> Result and Comments
                        </span>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Result</label>
                            <textarea name="result" id="result" class="form-control" rows="4"
                                placeholder="Summarize the overall performance result...">{{ old('result', $performanceReview->result ?? '') }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Comments</label>
                            <textarea name="comments" id="comments" class="form-control" rows="4"
                                placeholder="Provide detailed comments and feedback...">{{ old('comments', $performanceReview->comments ?? '') }}</textarea>
                        </div>
                    </div>
                    <div class="card-footer text-end">
                        <button type="submit" class="btn btn-sm btn-outline-primary" id="save-result-comments">
                            Save
                        </button>
                    </div>
                </div>
            </form>
        </div>

    </section>


    <section>
        <div class="card">
            <div class="card-header fw-bold">
                Performance Review Process
            </div>
            <form action="{{ route('performance.review.store') }}" id="performanceReviewProcessForm" method="post"
                enctype="multipart/form-data" autocomplete="off"
                onsubmit="return confirm('Have you saved all the forms? Are you sure to submit?');">
                <input type="hidden" name="performance_review_id" value="{{ $performanceReview->id }}">
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-6">
                            @foreach ($performanceReview->logs as $log)
                                <div class="flex-row gap-2 py-2 mb-2 d-flex border-bottom">
                                    <div width="40" height="40" class="mr-3 rounded-circle user-icon">
                                        <i class="bi-person"></i>
                                    </div>
                                    <div class="w-100">
                                        <div
                                            class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                                            <div
                                                class="mb-2 d-flex flex-column flex-md-row align-items-start align-items-md-center gap-md-2 mb-md-0">
                                                <span class="me-2">{{ $log->createdBy->getFullName() }}</span>
                                                <span class="badge bg-primary c-badge">
                                                    {!! $log->createdBy->employee->latestTenure->getDesignationName() !!}
                                                </span>
                                            </div>
                                            <small
                                                title="{{ $log->created_at }}">{{ $log->created_at->format('M d, Y h:i A') }}</small>
                                        </div>
                                        <p class="mt-1 mb-0 text-justify comment-text">
                                            {{ $log->log_remarks }}
                                        </p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-2 row">
                                <div class="col-lg-3">
                                    <div class="d-flex align-items-start h-100">
                                        <label for="status_id" class="form-label required-label">Status </label>
                                    </div>
                                </div>
                                <div class="col-lg-9">
                                    <select name="status_id" id="status_id" class="select2 form-control"
                                        data-width="100%">
                                        <option value="">Select a Status</option>
                                        <option value="{{ config('constant.RETURNED_STATUS') }}"
                                            {{ old('status_id') == config('constant.RETURNED_STATUS') ? 'selected' : '' }}>
                                            Return to Employee</option>
                                        <option value="{{ config('constant.VERIFIED_STATUS') }}"
                                            {{ old('status_id') == config('constant.VERIFIED_STATUS') ? 'selected' : '' }}>
                                            Verify</option>
                                        @if ($authUser->employee->designation_id == 9)
                                            <option value="{{ config('constant.APPROVED_STATUS') }}"
                                                @if (old('status_id') == config('constant.APPROVED_STATUS')) selected @endif>
                                                Approve
                                            </option>
                                        @elseif(!$nextLineManagerExists && $authUser->can('approve-performance-review'))
                                            <option value="{{ config('constant.APPROVED_STATUS') }}"
                                                @if (old('status_id') == config('constant.APPROVED_STATUS')) selected @endif>
                                                Approve
                                            </option>
                                        @endif
                                    </select>
                                    @if ($errors->has('status_id'))
                                        <div class="fv-plugins-message-container invalid-feedback">
                                            <div data-field="status_id">
                                                {!! $errors->first('status_id') !!}
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="mb-2 row" id="receiver">
                                <div class="col-lg-3">
                                    <div class="d-flex align-items-start h-100">
                                        <label for="receiver_id" class="form-label required-label">Send To </label>
                                    </div>
                                </div>
                                <div class="col-lg-9">
                                    <select name="receiver_id" id="receiver_id" class="select2 form-control"
                                        data-width="100%">
                                        @foreach ($receivers as $receiver)
                                            @if ($loop->first)
                                                <option value="{{ $receiver->id }}"
                                                    {{ old('receiver_id') == $receiver->id ? 'selected' : (empty(old('receiver_id')) ? 'selected' : '') }}>
                                                    {{ $receiver->getFullName() }}</option>
                                            @else
                                                <option value="{{ $receiver->id }}">{{ $receiver->getFullName() }}
                                                </option>
                                            @endif
                                        @endforeach
                                    </select>
                                    @if ($errors->has('receiver_id'))
                                        <div class="fv-plugins-message-container invalid-feedback">
                                            <div data-field="receiver_id">
                                                {!! $errors->first('receiver_id') !!}
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="mb-2 row">
                                <div class="col-lg-3">
                                    <div class="d-flex align-items-start h-100">
                                        <label for="log_remarks" class="form-label required-label">Remarks </label>
                                    </div>
                                </div>
                                <div class="col-lg-9">
                                    <textarea type="text" class="form-control @if ($errors->has('log_remarks')) is-invalid @endif" name="log_remarks">{{ old('log_remarks') }}</textarea>
                                    @if ($errors->has('log_remarks'))
                                        <div class="fv-plugins-message-container invalid-feedback">
                                            <div data-field="log_remarks">{!! $errors->first('log_remarks') !!}</div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            {!! csrf_field() !!}
                        </div>
                    </div>
                </div>
                <div class="gap-2 border-0 card-footer justify-content-end d-flex">
                    <button type="submit" name="btn" value="submit" class="btn btn-success btn-sm">
                        Submit
                    </button>
                    <a href="{!! route('performance.review.index') !!}" class="btn btn-danger btn-sm">Cancel</a>
                </div>
            </form>
        </div>
    </section>

@stop
