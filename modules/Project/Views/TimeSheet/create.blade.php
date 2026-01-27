<div class="modal-header bg-primary text-white">
    <h5 class="modal-title mb-0 fs-6" id="openModalLabel">Add TimeSheet</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form action="{!! route('timesheet.store') !!}" method="post" enctype="multipart/form-data" id="TimeSheetForm" autocomplete="off">
    <div class="modal-body">
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label class="form-label required-label m-0">Projects</label>
                </div>
            </div>
            <div class="col-lg-9">
                <select name="project_id" class="select2 form-control" data-width="100%">
                    <option value="">Select Project</option>
                    @foreach ($projects as $project)
                        <option value="{{ $project->id }}">{{ $project->title }}</option>
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
                        <option value="{{ $activity->id }}">{{ $activity->title }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label class="form-label required-label m-0">Hours Spent</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="number" step="0.1" name="hours_spent" class="form-control" />
            </div>
        </div>

        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label class="form-label m-0">Description</label>
                </div>
            </div>
            <div class="col-lg-9">
                <textarea name="description" class="form-control" rows="4"></textarea>
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
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Save</button>
    </div>
    {!! csrf_field() !!}
</form>
