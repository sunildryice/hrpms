<div class="modal-header bg-primary text-white">
    <h5 class="modal-title mb-0 fs-6" id="openModalLabel">Add New Distribution Item</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form action="{!! route('distribution.requests.items.store', $distributionRequest->id) !!}" method="post"
      enctype="multipart/form-data" id="distributionRequestItemForm" autocomplete="off">
    <div class="modal-body">
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">Item Name</label>
                </div>
            </div>
            <div class="col-lg-9">
                <select class="form-control select2" data-width="100%" name="inventory_item_id">
                    <option value="">Select Item</option>
                    @foreach($inventoryItems as $item)
                        <option value="{!! $item->id !!}">{{ $item->getItemName() }} {{ $item->grn->getGrnNumber() == '-' ? '' : ' || '.$item->grn->getGrnNumber() . ' ' }}{{'| ' . $item->quantity - $item->assigned_quantity }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="m-0">Specification</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="text" class="form-control" name="specification" readonly="readonly" placeholder="Specification">
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="m-0">Unit</label>
                </div>
            </div>
            <div class="col-lg-3">
                <input class="form-control" data-width="100%" readonly name="unit">
                <input class="form-control" value="" type="hidden" name="unit_id">
            </div>
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="m-0">Expiry Date</label>
                </div>
            </div>
            <div class="col-lg-3">
                <input class="form-control" data-width="100%" readonly name="expiry_date">
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">Activity Code</label>
                </div>
            </div>
            <div class="col-lg-9">
                <select class="form-control select2" data-width="100%" name="activity_code_id">
                    <option value="">Select Activity Code</option>
                    @foreach($activityCodes as $activityCode)
                        <option value="{!! $activityCode->id !!}">{{ $activityCode->getActivityCodeWithDescription() }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">Account Code</label>
                </div>
            </div>
            <div class="col-lg-9">
                <select class="form-control select2" data-width="100%" name="account_code_id">
                    <option value="">Select Account Code</option>
                </select>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="m-0">Donor Code</label>
                </div>
            </div>
            <div class="col-lg-9">
                <select class="form-control select2" data-width="100%" name="donor_code_id">
                    <option value="">Select Donor Code</option>
                    @foreach($donorCodes as $donorCode)
                        <option value="{!! $donorCode->id !!}">{{ $donorCode->getDonorCodeWithDescription() }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">Available Quantity</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="number" class="form-control" name="available_quantity" placeholder="Available Quantity"
                       readonly="readonly">
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">Quantity</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="number" class="form-control" name="quantity" value="" placeholder="Quantity">
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">Unit Price</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="number" class="form-control" name="unit_price" readonly="readonly" placeholder="Unit Price">
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="m-0">Total Price</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="number" class="form-control total_price" readonly="readonly" value="">
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Save</button>
    </div>
    {!! csrf_field() !!}
</form>
