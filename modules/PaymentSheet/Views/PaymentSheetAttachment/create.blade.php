<div class="modal-header bg-primary text-white">
    <h5 class="modal-title mb-0 fs-6" id="openModalLabel">Add Attachment</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form action="{{ route('payment.sheets.attachment.store', $paymentSheetId) }}" method="POST"
      enctype="multipart/form-data" id="attachmentCreateForm" autocomplete="off">
      @csrf

    <div class="modal-body">
          <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="title" class="form-label required-label">Title</label>
                </div>
            </div>
            <div class="col-lg-9">
               <input type="text" class="form-control" name="title" value="" placeholder="Attachment title">
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="attachment" class="m-0">Attachment</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input class="form-control" type="file" name="attachment" id="attachment">
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="attachment_link" class="m-0">Link</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input class="form-control" type="url" name="attachment_link" id="attachment_link" placeholder="Attachment Link">
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Save</button>
    </div>
</form>
