<div class="modal-header bg-primary text-white">
    <h5 class="modal-title mb-0 fs-6" id="openModalLabel">Edit TimeSheet</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form action="{!! route('timesheet.update', $timesheet->id) !!}" method="post" enctype="multipart/form-data" id="TimeSheetForm" autocomplete="off">
    <div class="modal-body">
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label class="form-label required-label m-0">Project</label>
                </div>
            </div>
            <div class="col-lg-9">
                <select name="project_id" class="select2 form-control" data-width="100%">
                    <option value="">Select Project</option>
                    @foreach ($projects as $project)
                        <option @if (old('project_id', $timesheet->project_id) == $project->id) selected @endif value="{{ $project->id }}">
                            {{ $project->title }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label class="form-label required-label m-0">Activity / Sub Activity</label>
                </div>
            </div>
            <div class="col-lg-9">
                <select name="activity_id" class="select2 form-control" data-width="100%">
                    <option value="">Select Activity / Sub Activity</option>
                    @foreach ($activities as $activity)
                        <option @if (old('project_id', $timesheet->activity_id) == $activity->id) selected @endif value="{{ $activity->id }}">
                            {{ $activity->title }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label class="form-label required-label m-0">Date</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="text" name="timesheet_date" class="form-control" placeholder="yyyy-mm-dd"
                    onfocus="this.blur()" autocomplete="off"
                    value="{{ $timesheet->timesheet_date->format('Y-m-d') }}" />
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label class="form-label required-label m-0">Hours Spent</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="number" step="0.1" name="hours_spent" class="form-control"
                    value="{{ $timesheet->hours_spent }}" />
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label class="form-label m-0">Description</label>
                </div>
            </div>
            <div class="col-lg-9">
                <textarea name="description" class="form-control" rows="4">{{ $timesheet->description }}</textarea>
            </div>
        </div>

        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="m-0">Attachment </label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="file" class="form-control" name="attachment">
                <small>Supported file types jpeg/jpg/png/pdf and file size of upto 5MB.</small>
                @if (file_exists('storage/' . $timesheet->attachment) && $timesheet->attachment != '')
                    <div class="media">
                        <a href="{!! asset('storage/' . $timesheet->attachment) !!}" target="_blank" class="fs-5" title="View Attachment">
                            <i class="bi bi-file-earmark-medical"></i>
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Update</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
    </div>
    {!! csrf_field() !!}
    @method('PUT')
</form>
