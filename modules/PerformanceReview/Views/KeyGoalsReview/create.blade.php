@extends('layouts.container')

@section('title', 'Set Key Goals')

@section('page_css')
    <style>
        #keygoals-table th,
        #keygoals-table td,
        #devplan-table th,
        #devplan-table td {
            border: 1px solid #dee2e6;
            padding: 10px;
            vertical-align: middle;
        }

        #keygoals-table,
        #devplan-table {
            table-layout: fixed;
            width: 100%;
        }

        .col-objective {
            width: 45%;
        }

        .col-output {
            width: 45%;
        }

        .col-plan {
            width: 90%;
        }

        .col-action {
            width: 10%;
            text-align: center;
        }

        .btn-sm {
            padding: 0.25rem 0.5rem;
            margin: 0 2px;
        }

        .table thead th {
            background-color: #f8f9fa;
            font-weight: 600;
        }
    </style>
@endsection

@section('page_js')
    <script type="text/javascript">
        let keyGoalRowIndex = {{ $currentKeyGoals->count() ?: 0 }};
        let devPlanRowIndex = {{ count($existingDevPlans ?? []) ?: 0 }};

        $(function() {
            $('#navbarVerticalMenu').find('#performance-employee-index').addClass('active');

            updateKeyGoalButtons();
            updateDevPlanButtons();

            // Save draft
            $('#btn-save-draft').on('click', function() {
                const formData = $('#performance-keygoals-form').serialize();

                $.ajax({
                    url: "{{ route('performance.keygoals.save-draft', $performanceReview->id) }}",
                    type: 'POST',
                    data: formData,
                    success: function(res) {
                        toastr.success(res.message || 'Draft saved successfully');
                    },
                    error: function(xhr) {
                        toastr.error(xhr.responseJSON?.message ||
                            'Could not save. Please check the fields.');
                        console.log(xhr.responseJSON);
                    }
                });
            });

            // Submit with validation
            $('#btn-submit').on('click', function(e) {
                e.preventDefault();

                // Client validation first
                let valid = true;

                $('#keygoals-body .keygoal-row').each(function() {
                    const title = $(this).find('input[name*="\\[title\\]"]').val()?.trim();
                    const deliverables = $(this).find('input[name*="\\[output_deliverables\\]"]')
                        .val()?.trim();
                    if (!title || !deliverables) valid = false;
                });

                $('#devplan-body .devplan-row').each(function() {
                    const plan = $(this).find('input[name*="\\[plan\\]"]').val()?.trim();
                    if (!plan) valid = false;
                });

                if (!valid) {
                    toastr.warning('Please complete all required fields.');
                    return;
                }

                // Auto-save before submit
                const formData = $('#performance-keygoals-form').serialize();

                $.ajax({
                    url: "{{ route('performance.keygoals.save-draft', $performanceReview->id) }}",
                    type: 'POST',
                    data: formData,
                    beforeSend: function() {
                        $('#btn-submit').prop('disabled', true).text('Saving & Submitting...');
                    },
                    success: function(res) {
                        toastr.success('Saved! Submitting now...');
                        window.location.href =
                            "{{ route('performance.submit', $performanceReview->id) }}";
                    },
                    error: function(xhr) {
                        toastr.error('Failed to save. Please try again.');
                        $('#btn-submit').prop('disabled', false).text('Submit');
                    }
                });
            });
        });

        // KEY GOALS (B) 

        function buildKeyGoalRow(idx, title = '', output_deliverables = '', id = null) {
            const isExisting = id !== null;
            return `
            <tr class="keygoal-row" data-row-index="${idx}" ${isExisting ? `data-id="${id}"` : ''}>
                <td class="col-objective">
                    <input type="text" class="form-control" 
                           name="keygoals[${idx}][title]" 
                           value="${title.replace(/"/g, '&quot;')}" 
                           placeholder="Enter objective" required>
                </td>
                <td class="col-output">
                    <input type="text" class="form-control" 
                           name="keygoals[${idx}][output_deliverables]" 
                           value="${output_deliverables.replace(/"/g, '&quot;')}" 
                           placeholder="Output / Deliverable" required>
                </td>
                <td class="col-action">
                    <button type="button" class="btn btn-outline-primary btn-sm add-keygoal-row" title="Add new row">
                        <i class="bi bi-plus"></i>
                    </button>
                    <button type="button" class="btn btn-outline-danger btn-sm remove-keygoal-row" title="Remove this row">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            </tr>`;
        }

        function updateKeyGoalButtons() {
            const $rows = $('#keygoals-body .keygoal-row');
            $rows.find('.add-keygoal-row').hide();
            $rows.find('.remove-keygoal-row').show();

            if ($rows.length === 1) $rows.find('.remove-keygoal-row').hide();
            $rows.last().find('.add-keygoal-row').show();
        }

        $(document).on('click', '.add-keygoal-row', function() {
            keyGoalRowIndex++;
            const $newRow = $(buildKeyGoalRow(keyGoalRowIndex));
            $('#keygoals-body').append($newRow);
            updateKeyGoalButtons();
        });

        $(document).on('click', '.remove-keygoal-row', function() {
            const $row = $(this).closest('tr');
            const goalId = $row.data('id');

            if (goalId) {

                $.ajax({
                    url: "{{ route('performance.keygoal.destroy') }}",
                    type: 'POST',
                    data: {
                        _token: "{{ csrf_token() }}",
                        keyGoalId: goalId
                    },
                    success: function(res) {
                        if (res.type === 'success') {
                            $row.remove();
                            updateKeyGoalButtons();
                            toastr.success('Key goal removed');
                        } else {
                            toastr.error('Failed to delete');
                        }
                    },
                    error: function() {
                        toastr.error('Server error');
                    }
                });
            } else {
                $row.remove();
                updateKeyGoalButtons();
            }
        });

        // PROFESSIONAL DEVELOPMENT PLAN (C) 

        function buildDevPlanRow(idx, plan = '', id = null) {
            const isExisting = id !== null;
            return `
            <tr class="devplan-row" data-row-index="${idx}" ${isExisting ? `data-id="${id}"` : ''}>
                <td class="col-plan">
                    <input type="text" class="form-control" 
                           name="devplans[${idx}][plan]" 
                           value="${plan.replace(/"/g, '&quot;')}" 
                           placeholder="Enter development plan / need" required>
                </td>
                <td class="col-action">
                    <button type="button" class="btn btn-outline-primary btn-sm add-devplan-row" title="Add new plan">
                        <i class="bi bi-plus"></i>
                    </button>
                    <button type="button" class="btn btn-outline-danger btn-sm remove-devplan-row" title="Remove this plan">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            </tr>`;
        }

        function updateDevPlanButtons() {
            const $rows = $('#devplan-body .devplan-row');
            $rows.find('.add-devplan-row').hide();
            $rows.find('.remove-devplan-row').show();

            if ($rows.length === 1) $rows.find('.remove-devplan-row').hide();
            $rows.last().find('.add-devplan-row').show();
        }

        $(document).on('click', '.add-devplan-row', function() {
            devPlanRowIndex++;
            const $newRow = $(buildDevPlanRow(devPlanRowIndex));
            $('#devplan-body').append($newRow);
            updateDevPlanButtons();
        });

        $(document).on('click', '.remove-devplan-row', function() {
            const $row = $(this).closest('tr');
            const planId = $row.data('id');

            if (planId) {
                $row.remove();
                updateDevPlanButtons();
            } else {
                $row.remove();
                updateDevPlanButtons();
            }
        });
    </script>
@endsection

@section('page-content')

    <form id="performance-keygoals-form" method="POST">
        @csrf

        <!-- A. Employee & Supervisor Details -->
        <div id="employeeAndSupervisorDetails" class="mb-3">
            <div class="card">
                <div class="card-header fw-bold">
                    <span class="card-title">
                        <span class="fw-bold">A.</span> Employee and Line Manager Details
                    </span>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="row">
                                <div class="col-lg-4"><span class="fw-bold">Employee Name</span></div>
                                <div class="col-lg-6">{{ $performanceReview->getEmployeeName() }}</div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="row">
                                <div class="col-lg-4"><span class="fw-bold">Employee Title</span></div>
                                <div class="col-lg-6">{{ $performanceReview->getEmployeeTitle() }}</div>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="row">
                                <div class="col-lg-4"><span class="fw-bold">Line Manager Name</span></div>
                                <div class="col-lg-6">{{ $performanceReview->getSupervisorName() }}</div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="row">
                                <div class="col-lg-4"><span class="fw-bold">Line Manager Title</span></div>
                                <div class="col-lg-6">{{ $performanceReview->getSupervisorTitle() }}</div>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="row">
                                <div class="col-lg-4"><span class="fw-bold">Date of Joining</span></div>
                                <div class="col-lg-6">{{ $performanceReview->employee->getFirstJoinedDate() }}</div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="row">
                                <div class="col-lg-4"><span class="fw-bold">In Current Position Since</span></div>
                                <div class="col-lg-6">{{ $performanceReview->getJoinedDate() }}</div>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row mb-2">
                        <div class="col-lg-6">
                            <div class="row">
                                <div class="col-lg-4"><span class="fw-bold">Review period from:</span></div>
                                <div class="col-lg-6">{{ $performanceReview->getReviewFromDate() }}</div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="row">
                                <div class="col-lg-4"><span class="fw-bold">Review period to:</span></div>
                                <div class="col-lg-6">{{ $performanceReview->getReviewToDate() }}</div>
                            </div>
                        </div>
                        <div class="col-lg-6 mt-3">
                            <div class="row">
                                <div class="col-lg-4"><span class="fw-bold">Deadline:</span></div>
                                <div class="col-lg-6">{{ $performanceReview->getDeadlineDate() }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- B. Set Key Goals -->
        <div id="setKeyGoals" class="mb-3">
            <div class="card">
                <div class="card-header fw-bold">
                    <span class="fw-bold">B.</span> Set Key Goals
                </div>
                <div class="card-body">
                    <table class="table table-bordered" id="keygoals-table">
                        <thead>
                            <tr>
                                <th class="col-objective">Objective</th>
                                <th class="col-output">Output / Deliverable</th>
                                <th class="col-action">Action</th>
                            </tr>
                        </thead>
                        <tbody id="keygoals-body">
                            @forelse ($currentKeyGoals as $index => $kg)
                                <tr class="keygoal-row" data-row-index="{{ $index }}"
                                    data-id="{{ $kg->id }}">
                                    <td class="col-objective">
                                        <input type="text" class="form-control"
                                            name="keygoals[{{ $index }}][title]"
                                            value="{{ old('keygoals.' . $index . '.title', $kg->title) }}"
                                            placeholder="Enter objective" required>
                                    </td>
                                    <td class="col-output">
                                        <input type="text" class="form-control"
                                            name="keygoals[{{ $index }}][output_deliverables]"
                                            value="{{ old('keygoals.' . $index . '.output_deliverables', $kg->output_deliverables ?? '') }}"
                                            placeholder="Output / Deliverable" required>
                                    </td>
                                    <td class="col-action">
                                        <button type="button" class="btn btn-outline-primary btn-sm add-keygoal-row">
                                            <i class="bi bi-plus"></i>
                                        </button>
                                        <button type="button" class="btn btn-outline-danger btn-sm remove-keygoal-row">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr class="keygoal-row" data-row-index="0">
                                    <td class="col-objective">
                                        <input type="text" class="form-control" name="keygoals[0][title]"
                                            placeholder="Enter objective" required>
                                    </td>
                                    <td class="col-output">
                                        <input type="text" class="form-control"
                                            name="keygoals[0][output_deliverables]" placeholder="Output / Deliverable"
                                            required>
                                    </td>
                                    <td class="col-action">
                                        <button type="button" class="btn btn-outline-primary btn-sm add-keygoal-row">
                                            <i class="bi bi-plus"></i>
                                        </button>
                                        <button type="button" class="btn btn-outline-danger btn-sm remove-keygoal-row"
                                            style="display:none">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div class="mt-3">
                        <a class="text-decoration-none"
                            href="{{ route('performance.previous.show', $performanceReview->id) }}" target="_blank">
                            View previous Key Goals <i class="bi bi-arrow-up-right-square"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- C. Professional Development Plan - now same style as B -->
        <div id="professionalDevelopmentPlan" class="mb-3">
            <div class="card">
                <div class="card-header fw-bold">
                    <span class="fw-bold">C.</span> Professional Development Plan
                </div>
                <div class="card-body">
                    <table class="table table-bordered" id="devplan-table">
                        <thead>
                            <tr>
                                <th class="col-plan">Development Plan</th>
                                <th class="col-action">Action</th>
                            </tr>
                        </thead>
                        <tbody id="devplan-body">
                            @forelse ($existingDevPlans ?? [] as $index => $plan)
                                <tr class="devplan-row" data-row-index="{{ $index }}"
                                    data-id="{{ $plan->id ?? '' }}">
                                    <td class="col-plan">
                                        <input type="text" class="form-control"
                                            name="devplans[{{ $index }}][plan]"
                                            value="{{ old('devplans.' . $index . '.plan', $plan->answer ?? '') }}"
                                            placeholder="Enter development plan / need" required>
                                    </td>
                                    <td class="col-action">
                                        <button type="button" class="btn btn-outline-primary btn-sm add-devplan-row">
                                            <i class="bi bi-plus"></i>
                                        </button>
                                        <button type="button" class="btn btn-outline-danger btn-sm remove-devplan-row">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr class="devplan-row" data-row-index="0">
                                    <td class="col-plan">
                                        <input type="text" class="form-control" name="devplans[0][plan]"
                                            placeholder="Enter development plan / need" required>
                                    </td>
                                    <td class="col-action">
                                        <button type="button" class="btn btn-outline-primary btn-sm add-devplan-row">
                                            <i class="bi bi-plus"></i>
                                        </button>
                                        <button type="button" class="btn btn-outline-danger btn-sm remove-devplan-row"
                                            style="display:none">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="text-end mt-4">
            <button type="button" id="btn-save-draft" class="btn btn-sm btn-primary px-4">Save</button>
            <button type="button" id="btn-submit" class="btn btn-sm btn-success px-4">Submit</button>
            <a href="{{ route('performance.employee.index') }}" class="btn btn-sm btn-danger px-4">Cancel</a>
        </div>

        @if ($performanceReview->status_id == config('constant.RETURNED_STATUS'))
            <div class="mt-4 border p-3 bg-light">
                <strong style="text-decoration: underline">Remarks from Supervisor:</strong><br>
                {{ $performanceReview->getLatestRemark() ?? 'No remarks' }}
            </div>
        @endif
    </form>

@endsection
