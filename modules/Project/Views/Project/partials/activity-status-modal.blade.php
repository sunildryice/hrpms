<div class="modal fade" id="activityStatusModal" tabindex="-1" aria-labelledby="activityStatusModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" style="max-width: 900px; margin: 1.5rem auto;">
        <div class="modal-content" id="activityStatusModalContent">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title mb-0 fs-6" id="activityStatusModalLabel">Update Activity Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="activityStatusForm" autocomplete="off" enctype="multipart/form-data">
                @csrf
                <input type="hidden" id="activityStatusValue" name="status">
                <div class="modal-body">
                    <div class="row g-3 fv-row" id="status-date-row">
                        <div class="col-12 col-md-4 col-lg-3">
                            <label class="form-label required-label mb-0" id="activityStatusDateLabel"
                                for="activityStatusDate">
                                Date <span class="text-danger">*</span>
                            </label>
                        </div>
                        <div class="col-12 col-md-8 col-lg-9">
                            <input type="text" class="form-control" id="activityStatusDate" name="status_date"
                                readonly />
                        </div>
                    </div>
                    <div class="row g-3 fv-row" id="activityStatusMessageContainer">
                        <div class="col-12 col-md-4 col-lg-3">
                            <label class="form-label required-label mb-0" id="activityStatusMessageLabel"
                                for="activityStatusMessage">
                                Reason <span class="text-danger">*</span>
                            </label>
                        </div>
                        <div class="col-12 col-md-8 col-lg-9">
                            <textarea class="form-control" id="activityStatusMessage" name="remarks" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="row g-3 fv-row" id="activityOutputDocumentsSection" style="display: none;">
                        <div class="col-12 col-md-4 col-lg-3">
                            <label class="form-label required-label mb-0">
                                Output/Deliverables Documents
                            </label>
                        </div>
                        <div class="col-12 col-md-8 col-lg-9">
                            <div class="output-documents-wrapper">
                                <div id="activityOutputDocumentRows">
                                    <div
                                        class="activity-doc-row d-flex flex-column flex-md-row align-items-start align-items-md-center gap-2 mb-2">
                                        <div
                                            class="d-flex flex-column flex-md-row align-items-start align-items-md-center gap-2 w-100">
                                            <div class="fv-row flex-grow-1 w-100">
                                                <input type="text" class="form-control document-name-input"
                                                    placeholder="Document name">
                                            </div>
                                            <div class="fv-row flex-grow-1 w-100">
                                                <input type="file" class="form-control document-file-input"
                                                    accept="application/pdf,image/jpeg,image/png">
                                            </div>
                                        </div>
                                        <button type="button"
                                            class="btn btn-outline-danger btn-sm remove-activity-output-doc mt-2 mt-md-0"
                                            disabled>
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-outline-primary btn-sm"
                                    id="addActivityDocumentRow">
                                    <i class="bi bi-plus-lg"></i>
                                </button>
                                <small class="text-muted d-block mt-2">Attach supporting files (PDF, JPG, PNG) with a
                                    short document name.</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary btn-status-save">Save</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>
