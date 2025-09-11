<div class="modal-header bg-primary text-white">
    <h5 class="modal-title mb-0 fs-6" id="openModalLabel">Create Inventory In Bulk</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form action="{!! route('approved.grns.items.inventory.bulk.store', $grn->id) !!}"
    method="post" enctype="multipart/form-data" id="grnInventoryBulkForm" autocomplete="off">
    <div class="modal-body">
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">Distribution Type</label>
                </div>
            </div>
            <div class="col-lg-9">
                <select class="form-control" name="distribution_type_id">
                    <option value="">Select Type</option>
                    @foreach($distributionTypes as $distributionType)
                        <option value="{{ $distributionType->id }}">{{ $distributionType->title }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="m-0">Execution Type</label>
                </div>
            </div>
            <div class="col-lg-9">
                <select class="form-control" name="execution_id">
                    <option value="">Select Type</option>
                    @foreach($executionTypes as $executionType)
                        <option value="{{ $executionType->id }}">{{ $executionType->title }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="specification" class="m-0">Description</label>
                </div>
            </div>
            <div class="col-lg-9">
                <textarea class="form-control" name="specification" id="specification" rows="2"></textarea>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="purchase_date" class="m-0">Purchase Date</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="text" class="form-control" name="purchase_date" id="purchase_date" value="{{$grn->received_date->format('Y-m-d')}}">
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="m-0">Expiry Date</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="text" class="form-control" readonly="readonly" name="expiry_date">
            </div>
        </div>
        <div class="mb-2 row">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">Notify</label>
                </div>
            </div>
            <div class="col-lg-9">
                <select class="form-control select2" multiple name="interpreter_ids[]">
                    <option value="">Select Finance/Logistic officers</option>
                    @foreach ($interpreters as $user)
                        <option value="{{ $user->id }}">{{ $user->getFullName() }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Submit</button>
    </div>
    {!! csrf_field() !!}
</form>
