<div class="modal-header bg-primary text-white">
    <h5 class="modal-title mb-0 fs-6" id="openModalLabel">Edit Project Activity</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form id="ProjectActivityCreateForm" method="post"
    action="{{ route('project-activity.update', ['projectActivity' => $projectActivity->id]) }}" autocomplete="off">
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
                        <option @if (old('activity_level', $projectActivity->activity_level) == $level->value) selected @endif value="{{ $level->value }}">
                            {{ ucfirst(strtolower($level->name)) }}</option>
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
                        <option @if (old('activity_stage_id', $projectActivity->activity_stage_id) == $stage->id) selected @endif value="{{ $stage->id }}">
                            {{ $stage->title }}</option>
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
                <input type="text" name="title" value="{{ old('title', $projectActivity->title) }}"
                    class="form-control" />
            </div>
        </div>

        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label class="form-label m-0">Parent Activity</label>
                </div>
            </div>
            <div class="col-lg-9">
                <select name="parent_id" class="select2 form-control" data-width="100%">
                    <option value="">Select Parent Activity</option>
                    @foreach ($parentActivities as $activity)
                        <option @if (old('parent_id', $projectActivity->parent_id) == $activity->id) selected @endif value="{{ $activity->id }}">
                            {{ $activity->title }}</option>
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
                <input type="text" name="start_date"
                    value="{{ old('start_date', $projectActivity->start_date->format('Y-m-d')) }}" class="form-control"
                    placeholder="yyyy-mm-dd" onfocus="this.blur()" autocomplete="off" />
            </div>
        </div>

        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label class="form-label m-0">Completion Date</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="text" name="completion_date"
                    value="{{ old('completion_date', $projectActivity->completion_date->format('Y-m-d')) }}"
                    class="form-control" placeholder="yyyy-mm-dd" onfocus="this.blur()" autocomplete="off" />
            </div>
        </div>

        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label class="form-label required-label m-0">Members</label>
                </div>
            </div>
            <div class="col-lg-9">
                <select name="members[]" class="select2 form-control" data-width="100%" multiple>
                    @foreach ($allProjectMembers as $id => $fullName)
                        <option @if (in_array($id, old('members', $projectActivity->members->pluck('id')->toArray() ?? []))) selected @endif value="{{ $id }}">
                            {{ $fullName }}</option>
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
