<div class="modal fade" id="statusReasonModal" tabindex="-1" aria-labelledby="statusReasonModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="statusReasonForm" method="post" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title mb-0 fs-6" id="statusReasonModalLabel">Mark As Completed</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="status_detail_id" name="id">
                    <input type="hidden" id="status_value" name="status">

                    <div class="row mb-2">
                        <div class="col-lg-3">
                            <div class="d-flex align-items-start h-100">
                                <label class="form-label required-label m-0">Output/Deliverables documents</label>
                            </div>
                        </div>
                        <div class="col-lg-9">
                            <div class="output-documents-wrapper">
                                <div id="outputDocumentRows">
                                    <div
                                        class="output-doc-row d-flex flex-column flex-md-row align-items-start align-items-md-center mb-2">
                                        <div
                                            class="flex-grow-1 d-flex flex-column flex-md-row align-items-start align-items-md-center">
                                            <input type="text"
                                                class="form-control document-name-input mb-2 mb-md-0 me-md-2"
                                                name="documents[0][name]" placeholder="Document name" required>
                                            <input type="file"
                                                class="form-control document-file-input output-doc-input"
                                                name="documents[0][file]" accept="application/pdf,image/jpeg,image/png"
                                                required>
                                        </div>
                                        <div class="delete-placeholder ms-md-2 mt-2 mt-md-0" style="width: 44px;">
                                            &nbsp;
                                        </div>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-outline-primary btn-sm" id="addOutputDocumentRow">
                                    <i class="bi bi-plus-lg"></i>
                                </button>
                                <small class="text-muted d-block mt-2">Attach supporting files (PDF, JPG, PNG) with a
                                    short document name.</small>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-lg-3">
                            <div class="d-flex align-items-start h-100">
                                <label for="status_reason" class="form-label required-label m-0">Reason</label>
                            </div>
                        </div>
                        <div class="col-lg-9">
                            <textarea class="form-control" id="status_reason" name="reason" rows="3" required
                                placeholder="Please provide a reason for completion..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
