<div class="modal-header bg-primary text-white">
    <h5 class="modal-title mb-0 fs-6" id="openModalLabel">Edit TimeSheet</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form id="ProjectActivityTimeSheetForm" method="post"
    action="{{ route('project-activity.timesheet.update', ['timesheet' => $timesheet->id]) }}" autocomplete="off">
    <div class="modal-body">
        {!! csrf_field() !!}
        @method('PUT')
        <div class="row mb-3">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label class="form-label m-0">{{ __('label.activity-title') }}</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="text" name="project_activity" class="form-control" value="{{ $projectActivity->title }}"
                    readonly />
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
                <small>Supported file types jpeg/jpg/png/pdf and file size of upto 2MB.</small>
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
        <button type="submit" class="btn btn-primary">Save</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
    </div>
</form>
