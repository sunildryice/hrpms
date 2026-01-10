<div class="modal-header bg-primary text-white">
    <h5 class="modal-title mb-0 fs-6" id="openModalLabel">Activity Stage Details</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
    <div class="row mb-2">
        <div class="col-lg-3">
            <div class="d-flex align-items-start h-100">
                <label class="form-label">Title</label>
            </div>
        </div>
        <div class="col-lg-9">
            <div class="span-mimic-text-input">{{ $activityStage->title }}</div>
        </div>
    </div>
    <div class="row mb-2">
        <div class="col-lg-3">
            <div class="d-flex align-items-start h-100">
                <label class="form-label">Description</label>
            </div>
        </div>
        <div class="col-lg-9">
            <div class="span-mimic-text-input">{{ $activityStage->description }}</div>
        </div>
    </div>
    <div class="row mb-2">
        <div class="col-lg-3">
            <div class="d-flex align-items-start h-100">
                <label class="form-label">Activated</label>
            </div>
        </div>
        <div class="col-lg-9">
            <div class="span-mimic-text-input">{{ $activityStage->activated_at ? $activityStage->activated_at : 'No' }}
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
</div>
