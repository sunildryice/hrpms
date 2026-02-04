<div class="modal-header bg-primary text-white">
    <h5 class="modal-title mb-0 fs-6" id="editPlanModalLabel">Edit Weekly Plan</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form id="addPlanForm" action="{{ route('work-plan.update', $workPlanDetail->id) }}" method="POST">
    @csrf
    @method('PUT')
    <input type="hidden" name="from_date" value="{{ $week['start_date']->format('Y-m-d') }}">
    <input type="hidden" name="to_date" value="{{ $week['end_date']->format('Y-m-d') }}">
    <div class="modal-body">
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
                        <option value="{{ $project->id }}" data-activities="{{ json_encode($project->activities) }}"
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
                <select class="form-select activity-select" name="activity_id">
                    <option value="">Select Activity</option>
                    @foreach ($workPlanDetail->project->activities as $activity)
                        <option value="{{ $activity->id }}"
                            {{ $activity->id == $workPlanDetail->project_activity_id ? 'selected' : '' }}>
                            {{ $activity->title }}</option>
                    @endforeach
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
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Update Plan</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
    </div>
</form>
