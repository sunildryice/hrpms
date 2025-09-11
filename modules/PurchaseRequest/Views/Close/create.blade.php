<div class="modal-header bg-primary text-white">
    <h5 class="modal-title mb-0 fs-6" id="openModalLabel">Close
        {{ $purchaseRequest->getPurchaseRequestNumber() }}</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form action="{!! route('close.purchase.requests.store', $purchaseRequest->id) !!}" method="post" enctype="multipart/form-data" id="closeForm" autocomplete="off">
    <div class="modal-body">
        <div class="card-body">
            <div class="row mb-2">
                <div class="col-lg-3">
                    <div class="d-flex align-items-start h-100">
                        <label for="validationRemarks" class="form-label required-label">Remarks</label>
                    </div>
                </div>
                <div class="col-lg-9">
                    <div class="input-group has-validation">
                        <textarea type="text" class="form-control" name="close_remarks"></textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary" name="btn" value="save">Save</button>
    </div>
    {!! csrf_field() !!}
</form>
