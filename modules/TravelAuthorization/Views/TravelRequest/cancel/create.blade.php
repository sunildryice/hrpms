<div class="modal-header bg-primary text-white">
    <h5 class="modal-title mb-0 fs-6" id="openModalLabel">Cancel Travel Request 
        {{ $travel->getTravelAuthorizationNumber() }}</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form action="{!! route('ta.requests.cancel.create', $travel->id) !!}" method="post" enctype="multipart/form-data" id="cancelForm" autocomplete="off">
    <div class="modal-body">
        <div class="card-body">
            <div class="row mb-2">
                <div class="col-lg-3">
                    <div class="d-flex align-items-start h-100">
                        <label for="validationRemarks" class="form-label required-label">Cancellation Remarks</label>
                    </div>
                </div>
                <div class="col-lg-9">
                    <div class="input-group has-validation">
                        <textarea type="text" class="form-control" name="cancel_remarks"></textarea>
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
