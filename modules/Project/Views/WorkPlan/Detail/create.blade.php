<div class="modal-header bg-primary text-white">
    <h5 class="modal-title mb-0 fs-6" id="addPlanModalLabel">Add Weekly Plan</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form id="addPlanForm" action="{{ route('work-plan.store') }}" method="POST">
    @csrf
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
                        <option value="{{ $project->id }}" data-activities="{{ json_encode($project->activities) }}">
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
                <textarea class="form-control" name="planned_task" rows="3"></textarea>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Save Plan</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
    </div>
</form>
