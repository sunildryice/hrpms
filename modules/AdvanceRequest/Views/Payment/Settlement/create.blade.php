<div class="modal-header bg-primary text-white">
    <h5 class="modal-title mb-0 fs-6" id="openModalLabel">Payment Completion for
        {{ $advancedSettlementRequest->advanceRequest->getAdvanceRequestNumber() }}</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form action="{!! route('approved.settlement.pay.store', $advancedSettlementRequest->id) !!}" method="post" enctype="multipart/form-data" id="paymentForm" autocomplete="off">
    <div class="modal-body">
        <div class="card-body">
            <div class="row mb-2">
                <div class="col-lg-3">
                    <div class="d-flex align-items-start h-100">
                        <label for="validationPayDate" class="form-label required-label">Pay Date</label>
                    </div>
                </div>
                <div class="col-lg-9">
                    <div class="input-group has-validation">
                        <input type="text" class="form-control" name="pay_date" readonly>
                    </div>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-lg-3">
                    <div class="d-flex align-items-start h-100">
                        <label for="validationRemarks" class="form-label required-label">Remarks</label>
                    </div>
                </div>
                <div class="col-lg-9">
                    <div class="input-group has-validation">
                        <textarea type="text" class="form-control" name="payment_remarks"></textarea>
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
