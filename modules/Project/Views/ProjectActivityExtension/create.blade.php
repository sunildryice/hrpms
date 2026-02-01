<div class="modal-header bg-primary text-white">
    <h5 class="modal-title mb-0 fs-6" id="openModalLabel">Project Activity Extension</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form id="ProjectActivityExtensionForm" method="post"
    action="{{ route('project-activity.extension.store', [$projectActivity->id]) }}" autocomplete="off">
    <div class="modal-body">
        {!! csrf_field() !!}

        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label class="form-label required-label m-0">Extension Date</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="text" name="extended_completion_date" class="form-control" placeholder="yyyy-mm-dd"
                    onfocus="this.blur()" autocomplete="off" />
            </div>
        </div>

        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label class="form-label required-label m-0">Reason</label>
                </div>
            </div>
            <div class="col-lg-9">
                <textarea name="reason" class="form-control" rows="3"></textarea>
            </div>
        </div>

    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Save</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
    </div>
</form>
