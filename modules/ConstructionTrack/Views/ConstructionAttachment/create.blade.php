<div class="modal-header bg-primary text-white">
    <h5 class="modal-title mb-0 fs-6" id="openModalLabel">Add Attachment</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form action="{{ route('construction.attachment.store', $constructionId) }}" method="POST"
      enctype="multipart/form-data" id="constructionAttachmentCreateForm" autocomplete="off">
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
                    <label for="attachment" class="form-label">Attachment</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="file" name="attachment" id="attachment">
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="attachment" class="form-label">Link</label>
                </div>
            </div>
            <div class="col-lg-9">
                <textarea name="link" class="form-control" id=""></textarea>
            </div>
        </div>
        <small><span class="text-danger">***</span>Please provide at least one of Attachment or Link</small>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Save</button>
    </div>
</form>
