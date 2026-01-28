<div class="modal-header bg-primary text-white">
    <h5 class="modal-title mb-0 fs-6" id="openModalLabel">Add Project Activity</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form id="ProjectActivityCreateForm" method="post"
    action="{{ route('project-activity.store', ['project' => $project->id]) }}" autocomplete="off">

    <div class="modal-body">
        {!! csrf_field() !!}

        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label class="form-label required-label m-0">Activity Level</label>
                </div>
            </div>
            <div class="col-lg-9">
                <select name="activity_level" class="select2 form-control" data-width="100%">
                    <option value="">Select Level</option>
                    @foreach ($activityLevels as $level)
                        <option value="{{ $level->value }}">{{ ucfirst(strtolower($level->name)) }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label class="form-label required-label m-0">Stage</label>
                </div>
            </div>
            <div class="col-lg-9">
                <select name="activity_stage_id" class="select2 form-control" data-width="100%">
                    <option value="">Select Stage</option>
                    @foreach ($stages as $stage)
                        <option value="{{ $stage->id }}">{{ $stage->title }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label class="form-label required-label m-0">Title</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="text" name="title" class="form-control" />
            </div>
        </div>

        <div class="row mb-2" id="parent-activity-row">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label class="form-label required-label m-0">Parent Activity</label>
                </div>
            </div>
            <div class="col-lg-9">
                <select name="parent_id" id="parent_activity_select" class="select2 form-control" data-width="100%">
                    <option value="">Select Parent Activity</option>
                    @foreach ($parentActivities as $activity)
                        <option value="{{ $activity->id }}" data-level="{{ $activity->activity_level }}"
                            data-stage="{{ $activity->activity_stage_id }}"
                            data-start-date="{{ $activity->start_date }}"
                            data-end-date="{{ $activity->completion_date }}" data-title="{{ $activity->title }}">
                            {{ $activity->title }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label class="form-label m-0">Start Date</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="text" name="start_date" class="form-control" placeholder="yyyy-mm-dd"
                    onfocus="this.blur()" autocomplete="off" />
                <div id="start-date-hint" class="form-text small text-muted mt-1"></div>
            </div>
        </div>

        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label class="form-label m-0">End Date</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="text" name="completion_date" class="form-control" placeholder="yyyy-mm-dd"
                    onfocus="this.blur()" autocomplete="off" />
                <div id="end-date-hint" class="form-text small text-muted mt-1"></div>
            </div>
        </div>

        <div class="row mb-2" id="members-row">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label class="form-label required-label m-0">Members</label>
                </div>
            </div>
            <div class="col-lg-9">
                <select name="members[]" class="select2 form-control" id="members_select" data-width="100%" multiple>
                    @foreach ($allProjectMembers as $id => $fullName)
                        <option value="{{ $id }}">{{ $fullName }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label class="form-label m-0">Status</label>
                </div>
            </div>
            <div class="col-lg-9">
                <select name="status" class="select2 form-control" data-width="100%">
                    <option value="">Select Status</option>
                    @foreach ($status as $st)
                        <option value="{{ $st->value }}">{{ $st->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Save</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
    </div>
</form>
