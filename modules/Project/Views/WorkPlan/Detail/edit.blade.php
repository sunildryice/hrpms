<div class="modal-header bg-primary text-white">
    <h5 class="modal-title mb-0 fs-6" id="editPlanModalLabel">Edit Work Plan</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
@php
    $selectedMemberIds = $workPlanDetail->members->pluck('id')->all();
    $currentProjectMembers = optional($workPlanDetail->project)->allMembers() ?? collect();
@endphp
<form id="addPlanForm" action="{{ route('work-plan.update', $workPlanDetail->id) }}" method="POST">
    @csrf
    @method('PUT')
    <input type="hidden" name="from_date" value="{{ $week['start_date']->format('Y-m-d') }}">
    <input type="hidden" name="to_date" value="{{ $week['end_date']->format('Y-m-d') }}">
    <div class="modal-body">
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label class="form-label required-label m-0">Date</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="text" name="work_plan_date" class="form-control wp-date" placeholder="yyyy-mm-dd"
                    readonly value="{{ $workPlanDetail->work_plan_date }}" required />
            </div>
        </div>

        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label class="form-label required-label m-0">Project</label>
                </div>
            </div>
            <div class="col-lg-9">
                <select class="form-select project-select" name="project_id">
                    <option value="">Select Project</option>
                    @foreach ($projects as $project)
                        @php
                            $activitiesPayload = $project->activities
                                ->map(function ($activity) {
                                    return [
                                        'id' => $activity->id,
                                        'title' => $activity->title,
                                    ];
                                })
                                ->values();
                            $membersPayload = $project
                                ->allMembers()
                                ->map(function ($member) {
                                    return [
                                        'id' => $member->id,
                                        'name' => $member->full_name,
                                    ];
                                })
                                ->values();
                        @endphp
                        <option value="{{ $project->id }}" data-activities='@json($activitiesPayload)'
                            data-members='@json($membersPayload)'
                            {{ $project->id == $workPlanDetail->project_id ? 'selected' : '' }}>
                            {{ $project->short_name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label class="form-label required-label m-0">Activity</label>
                </div>
            </div>
            <div class="col-lg-9">
                <select class="form-select activity-select" name="activity_id"
                    data-selected-activity="{{ $workPlanDetail->project_activity_id }}">
                    <option value="">Select Activity</option>
                </select>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label class="form-label required-label m-0">Planned Task</label>
                </div>
            </div>
            <div class="col-lg-9">
                <textarea class="form-control" name="planned_task" rows="3">{{ $workPlanDetail->plan_tasks }}</textarea>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex flex-column align-items-start h-100">
                    <label class="form-label  m-0">Involved Members</label>

                </div>
            </div>
            <div class="col-lg-9">
                <select class="form-select select2 members-select" name="members[]" multiple
                    data-selected-members='@json($selectedMemberIds)'
                    {{ $currentProjectMembers->isEmpty() ? 'disabled' : '' }}>
                    @foreach ($currentProjectMembers as $member)
                        <option value="{{ $member->id }}"
                            {{ in_array($member->id, $selectedMemberIds) ? 'selected' : '' }}>
                            {{ $member->full_name }}</option>
                    @endforeach
                </select>
                <small class="text-muted">Includes project members, team lead, and focal person.</small>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Update Plan</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
    </div>
</form>
