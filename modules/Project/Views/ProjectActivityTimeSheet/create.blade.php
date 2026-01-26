<div class="modal-header bg-primary text-white">
    <h5 class="modal-title mb-0 fs-6" id="openModalLabel">TimeSheet</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form id="ProjectActivityTimeSheetForm" method="post"
    action="{{ route('project-activity.timesheet.store', [$projectActivity->id]) }}"
    autocomplete="off">
    <div class="modal-body">
        {!! csrf_field() !!}

        <div class="row mb-3">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label class="form-label m-0">{{ __('label.activity-title') }}</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="text" name="project_activity" class="form-control" value="{{ $projectActivity->title }}" readonly />
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
                    onfocus="this.blur()" autocomplete="off" />
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
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Save</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
    </div>
</form>
