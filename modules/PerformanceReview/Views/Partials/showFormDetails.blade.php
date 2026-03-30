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