<div class="modal-header bg-primary text-white">
    <h5 class="modal-title mb-0 fs-6" id="openModal1Label">Add New Document</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form action="{{route('document.exit.handover.note.store',[$exitHandOverNote->id])}}" method="post"
      enctype="multipart/form-data" id="documentForm" autocomplete="off">
    <div class="modal-body">
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">Attachment Name</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="text" class="form-control" name="attachment_name" value="" placeholder="Attachment Name">
            </div>
        </div>

        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">Attachment</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="file" class="form-control" name="attachment">
            </div>
        </div>

    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Save</button>
    </div>
    {!! csrf_field() !!}
</form>

