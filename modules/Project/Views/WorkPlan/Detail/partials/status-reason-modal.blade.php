<div class="modal fade" id="statusReasonModal" tabindex="-1" aria-labelledby="statusReasonModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="statusReasonForm" method="post">
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
