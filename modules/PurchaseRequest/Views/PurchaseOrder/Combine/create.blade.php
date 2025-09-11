<div class="modal-header bg-primary text-white">
    <h5 class="modal-title mb-0 fs-6" id="openModalLabel">Add to Exisisting Purchase Order</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form action="{!! route('purchase.requests.orders.combine.store', $purchaseRequest->id) !!}" method="post" enctype="multipart/form-data" id="purchaseOrderCombineForm"
    autocomplete="off">
    @csrf
    <div class="modal-body">
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">Purchase Order</label>
                </div>
            </div>
            <div class="col-lg-9">
                <select class="form-control select2" data-width="100%" name="purchase_order_id">
                    <option value="">Select Purchase Order</option>
                    @foreach ($purchaseOrders as $po)
                        <option value="{!! $po->id !!}">{{ $po->getSupplierNameTotalandDate() }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="modal-footer">
            <button type="submit" class="btn btn-primary">Proceed</button>
        </div>
</form>
