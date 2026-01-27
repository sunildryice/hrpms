<div class="modal-header bg-primary text-white">
    <h5 class="modal-title mb-0 fs-6" id="openModalLabel">Show TimeSheet</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
    <div class="row mb-2">
        <div class="col-lg-3">
            <div class="d-flex align-items-start h-100">
                <label class="form-label m-0">Date</label>
            </div>
        </div>
        <div class="col-lg-9">
            <div class="span-mimic-text-input">{{ $timesheet->timesheet_date->format('Y-m-d') }}</div>
        </div>
    </div>
    <div class="row mb-2">
        <div class="col-lg-3">
            <div class="d-flex align-items-start h-100">
                <label class="form-label m-0">Hours Spent</label>
            </div>
        </div>
        <div class="col-lg-9">
            <div class="span-mimic-text-input">{{ $timesheet->hours_spent }}</div>
        </div>
    </div>
    <div class="row mb-2">
        <div class="col-lg-3">
            <div class="d-flex align-items-start h-100">
                <label class="form-label m-0">Description</label>
            </div>
        </div>
        <div class="col-lg-9">
            <div class="span-mimic-text-input">{{ $timesheet->description }}</div>
        </div>
    </div>

    <div class="row mb-2">
        <div class="col-lg-3">
            <div class="d-flex align-items-start h-100">
                <label for="" class="m-0">Attachment </label>
            </div>
        </div>
        <div class="col-lg-9">
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
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
</div>
