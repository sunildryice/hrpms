<div class="modal-header bg-primary text-white">
    <div class="modal-title fw-bold text-uppercase" id="openModalLabel">Add New Transportation Bill Detail</div>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form action="{!! route('transportation.bills.details.store', $transportationBill->id) !!}" method="post"
      enctype="multipart/form-data" id="transportationBillDetailForm" autocomplete="off">
    <div class="modal-body">
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label class="form-label required-label">Item Description </label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="text" class="form-control" name="item_description" value="" placeholder="Item Description">
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label class="form-label required-label">Quantity </label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="number" class="form-control" name="quantity" step="1" placeholder="Quantity">
            </div>
        </div>

        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label class="form-label">Remarks </label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="text" class="form-control" name="remarks" value="" placeholder="Remarks">
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary btn-sm">Save</button>
    </div>
    {!! csrf_field() !!}
</form>
