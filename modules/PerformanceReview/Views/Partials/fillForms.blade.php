<!-- B. Key Goals Review -->
<div id="keyGoalsReview" class="mb-3">
    <form action="{{ route('performance.keygoal.update') }}" method="POST" id="groupBForm">
        <div class="card">
            <div class="card-header fw-bold">
                <span class="card-title d-flex justify-content-between">
                    <span class="fw-bold">B.
                        Key Goals Review
                    </span>
                    <button class="btn btn-sm btn-primary" id="add-key-goal"
                        data-href="{{ route('performance.keygoal.store') }}"><i class="bi bi-plus"></i>Add
                        New</button>
                </span>
            </div>
            <div class="card-body">
                <table class="table" id="keyGoalTable">
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
                            <tr data-keygoal-id="{{ $keygoal->id }}">
                                <td>{{ $keygoal->title }}</td>
                                <td>{{ $keygoal->output_deliverables }}</td>
                                <td>
                                    <textarea name="major_activities_employee_{{ $keygoal->id }}" class="form-control major-activities" rows="2">{{ $keygoal->major_activities_employee }}</textarea>
                                </td>
                                <td>
                                    <select name="status_{{ $keygoal->id }}" class="form-select status-dropdown">
                                        <option value="">Select Status</option>
                                        @foreach (\Modules\PerformanceReview\Models\Enums\KeyGoalStatus::cases() as $status)
                                            <option value="{{ $status->value }}"
                                                {{ $keygoal->status?->value === $status->value ? 'selected' : '' }}>
                                                {{ $status->label() }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <textarea name="remarks_employee_{{ $keygoal->id }}" class="form-control remarks-employee" rows="2">{{ $keygoal->remarks_employee }}</textarea>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <table id="new-keyGoalTable" style="width: 100%;@if (!$newKeyGoals->count()) display:none @endif">
                    <thead>
                        <tr>
                            <th rowspan="2" style="width: 18%">(Additional Objective)</th>
                            <th rowspan="2" style="width: 15%">(Additional Output / Deliverable)</th>
                            <th rowspan="2" style="width: 22%">Major Activities</th>
                            <th colspan="2">Achievement against output / deliverable</th>
                            <th rowspan="2" style="width: 22%">Action</th>
                        </tr>
                        <tr>
                            <th style="width: 15%">Status</th>
                            <th style="width: 25%">Remarks / Comments</th>
                        </tr>
                    </thead>
                    <tbody id="keygoal-body">
                        @foreach ($newKeyGoals as $keygoal)
                            <tr>
                                <td>
                                    <span style="width: 100%">{{ $keygoal->title }}
                                    </span>
                                </td>
                                <td>{{ $keygoal->output_deliverables }}</td>
                                <td>
                                    <textarea name="major_activities_employee_{{ $keygoal->id }}" class="form-control major-activities" rows="2">{{ $keygoal->major_activities_employee }}</textarea>
                                </td>
                                <td>
                                    <select name="status_{{ $keygoal->id }}" class="form-select status-dropdown">
                                        <option value="">Select Status</option>
                                        @foreach (\Modules\PerformanceReview\Models\Enums\KeyGoalStatus::cases() as $status)
                                            <option value="{{ $status->value }}"
                                                {{ $keygoal->status?->value === $status->value ? 'selected' : '' }}>
                                                {{ $status->label() }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <textarea name="remarks_employee_{{ $keygoal->id }}" class="form-control remarks-employee" rows="2">{{ $keygoal->remarks_employee }}</textarea>
                                </td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <a class="edit-key-goal btn btn-outline-primary btn-sm" href="#"
                                            data-href="{{ route('performance.keygoal.update') }}"
                                            data-title="{{ $keygoal->title }}" data-id="{{ $keygoal->id }}"u><i
                                                class="bi bi-pencil-square"></i></a>
                                        <a class="delete-key-goal btn btn-outline-danger btn-sm" href="#"
                                            data-href="{{ route('performance.keygoal.destroy') }}"
                                            data-id="{{ $keygoal->id }}"><i class="bi bi-trash"></i></a>
                                    </div>
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
    <form action="{{ route('performance.devplan.update') }}" method="POST" id="groupCForm">
        @csrf
        <input type="hidden" name="performance_review_id" value="{{ $performanceReview->id }}">

        <div class="card">
            <div class="card-header fw-bold">
                <span class="card-title">
                    <span class="fw-bold">C.</span> Professional Development Plan
                </span>
            </div>
            <div class="card-body">
                @php
                    $devPlans = $keyGoalReview->developmentPlans ?? collect();
                @endphp

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
                                    <td class="readonly-cell">
                                        {{ $plan->objective }}
                                    </td>
                                    <td>
                                        <textarea name="devplans[{{ $index }}][activity]" class="form-control devplan-activity" rows="1"
                                            data-id="{{ $plan->id }}" placeholder="Enter activities...">{{ $plan->activity ?? '' }}</textarea>
                                        <input type="hidden" name="devplans[{{ $index }}][id]"
                                            value="{{ $plan->id }}">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-sm btn-outline-primary float-end">Save</button>
            </div>
        </div>
    </form>
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
            <form id="groupDForm" method="POST" action="{{ route('performance.corecompetency.store') }}">
                @csrf
                <input type="hidden" name="performance_review_id" value="{{ $performanceReview->id }}">

                <table class="table table-bordered" id="competencies-table">
                    <thead>
                        <tr>
                            <th style="width: 35%">Competency</th>
                            <th style="width: 15%">Rating (1-5)</th>
                            <th style="width: 45%">Provide examples that reflect your roles</th>
                            <th style="width: 5%" class="text-center">Action</th>
                        </tr>
                    </thead>

                    <tbody id="competencies-body">
                        @php
                            $existingCompetencies = $coreCompetencies ?? collect();
                        @endphp

                        @forelse ($existingCompetencies as $index => $comp)
                            <tr class="competency-row" data-row-index="{{ $index }}"
                                data-id="{{ $comp->id }}">
                                <td>
                                    <input type="text" name="competencies[{{ $index }}][competency]"
                                        class="form-control competency-name" value="{{ $comp->competency ?? '' }}"
                                        placeholder="Competency">
                                    <input type="hidden" name="competencies[{{ $index }}][id]"
                                        value="{{ $comp->id }}">
                                </td>
                                <td>
                                    <select name="competencies[{{ $index }}][rating]"
                                        class="form-select competency-rating">
                                        <option value="">Select Rating</option>
                                        <option value="1" {{ $comp->rating == 1 ? 'selected' : '' }}>1 - Poor
                                        </option>
                                        <option value="2" {{ $comp->rating == 2 ? 'selected' : '' }}>2 - Fair
                                        </option>
                                        <option value="3" {{ $comp->rating == 3 ? 'selected' : '' }}>3 - Good
                                        </option>
                                        <option value="4" {{ $comp->rating == 4 ? 'selected' : '' }}>4 - Very
                                            Good</option>
                                        <option value="5" {{ $comp->rating == 5 ? 'selected' : '' }}>5 -
                                            Excellent</option>
                                    </select>
                                </td>
                                <td>
                                    <textarea name="competencies[{{ $index }}][example]" class="form-control competency-example" rows="1"
                                        placeholder="Provide examples that reflect your roles...">{{ $comp->example ?? '' }}</textarea>
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-outline-primary btn-sm add-competency-row">
                                        <i class="bi bi-plus"></i>
                                    </button>
                                    <button type="button"
                                        class="btn btn-outline-danger btn-sm remove-competency-row">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr class="competency-row" data-row-index="0">
                                <td>
                                    <input type="text" name="competencies[0][competency]"
                                        class="form-control competency-name" placeholder="Competency">
                                </td>
                                <td>
                                    <select name="competencies[0][rating]" class="form-select competency-rating">
                                        <option value="">Select Rating</option>
                                        <option value="1">1 - Poor</option>
                                        <option value="2">2 - Fair</option>
                                        <option value="3">3 - Good</option>
                                        <option value="4">4 - Very Good</option>
                                        <option value="5">5 - Excellent</option>
                                    </select>
                                </td>
                                <td>
                                    <textarea name="competencies[0][example]" class="form-control competency-example" rows="1"
                                        placeholder="Provide examples that reflect your roles..."></textarea>
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-outline-primary btn-sm add-competency-row">
                                        <i class="bi bi-plus"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-danger btn-sm remove-competency-row"
                                        style="display: none;">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="text-end mt-3">
                    <button type="submit" class="btn btn-sm btn-outline-primary">Save</button>
                </div>
            </form>
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
            <form id="groupEForm" method="POST" action="{{ route('performance.challenge.store') }}">
                @csrf
                <input type="hidden" name="performance_review_id" value="{{ $performanceReview->id }}">

                <table class="table table-bordered" id="challenges-table">
                    <thead>
                        <tr>
                            <th style="width: 45%">Challenge / Difficulty Faced</th>
                            <th style="width: 45%">Result / Outcome</th>
                            <th style="width: 10%" class="text-center">Action</th>
                        </tr>
                    </thead>

                    <tbody id="challenges-body">
                        @forelse ($challenges as $index => $challenge)
                            <tr class="challenge-row" data-row-index="{{ $index }}"
                                data-id="{{ $challenge->id }}">
                                <td>
                                    <textarea name="challenges[{{ $index }}][challenge]" class="form-control" rows="2">{{ $challenge->challenge }}</textarea>
                                    <input type="hidden" name="challenges[{{ $index }}][id]"
                                        value="{{ $challenge->id }}">
                                </td>

                                <td>
                                    <textarea name="challenges[{{ $index }}][result]" class="form-control" rows="2">{{ $challenge->result }}</textarea>
                                </td>

                                <td class="text-center">
                                    <button type="button" class="btn btn-outline-primary btn-sm add-challenge-row">
                                        <i class="bi bi-plus"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-danger btn-sm remove-challenge-row">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr class="challenge-row" data-row-index="0">
                                <td>
                                    <textarea name="challenges[0][challenge]" class="form-control" rows="2"></textarea>
                                </td>
                                <td>
                                    <textarea name="challenges[0][result]" class="form-control" rows="2"></textarea>
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-outline-primary btn-sm add-challenge-row">
                                        <i class="bi bi-plus"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-danger btn-sm remove-challenge-row"
                                        style="display:none;">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="text-end mt-2">
                    <button type="submit" class="btn btn-sm btn-outline-primary">
                        Save
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- F. Employee Comments -->
<div id="employeeComments" class="mb-3">
    <form id="groupFForm" method="POST">
        @csrf
        <input type="hidden" name="performance_review_id" value="{{ $performanceReview->id }}">

        <div class="card">
            <div class="card-header fw-bold">
                <span class="card-title">
                    <span class="fw-bold">F.</span>
                    Employee Comments
                </span>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <textarea name="employee_comments" id="employee_comments" class="form-control" rows="8">{{ old('employee_comments', $performanceReview->employee_comments ?? '') }}</textarea>
                </div>
            </div>
            <div class="card-footer text-end">
                <button type="submit" class="btn btn-sm btn-outline-primary">Save</button>
            </div>
        </div>
    </form>
</div>
