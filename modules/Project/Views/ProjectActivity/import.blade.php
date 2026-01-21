<div class="modal-header bg-primary text-white">
    <h5 class="modal-title mb-0 fs-6 fw-bold" id="openModalLabel">Import</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form action="{!! route('project-activity.import.store', $project) !!}" method="post" enctype="multipart/form-data" id="activityImportForm"
    autocomplete="off">
    <div class="modal-body">
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-center h-100">
                    <label for="" class="m-0 required-label">{!! __('label.attachment') !!}</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="file" name="attachment" placeholder="Import File" class="form-control form-control-sm">
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-center h-100">
                    <label for="" class="m-0"></label>
                </div>
            </div>
            <div class="col-lg-9">
                <a href="{{ route('project-activity.export', $project) }}" target="_blank">Download</a> a file with all activities to import.<br />
                Make sure the columns in the file match the format of the file you downloaded.
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Import</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
    </div>
    {!! csrf_field() !!}
</form>
