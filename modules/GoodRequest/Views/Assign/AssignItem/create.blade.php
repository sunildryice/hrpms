<div class="modal-header bg-primary text-white">
    <h5 class="modal-title mb-0 fs-6" id="openModalLabel">Assign Item</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form
    action="{!! route('assign.good.requests.items.store', [$goodRequestItem->good_request_id, $goodRequestItem->id]) !!}"
    method="post" enctype="multipart/form-data" id="goodRequestAssignItemForm" autocomplete="off">
    <div class="modal-body">
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="m-0">Item Name</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input class="form-control" data-width="100%" name="item_name" readonly="readonly"
                value="{!! $goodRequestItem->item_name !!}"/>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="m-0">Unit</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input class="form-control" data-width="100%" name="item_name" readonly="readonly"
                       value="{!! $goodRequestItem->getUnit() !!}"/>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="m-0">Specification</label>
                </div>
            </div>
            <div class="col-lg-9">
                <textarea class="form-control" name="specification" readonly="readonly" placeholder="Specification">{!! $goodRequestItem->specification !!}</textarea>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="m-0">Quantity</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input class="form-control" name="quantity" readonly="readonly" value="{{ $goodRequestItem->quantity }}">
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">Item</label>
                </div>
            </div>
            <div class="col-lg-9">
                <select class="select2 form-control" name="assigned_inventory_item_id">
                    <option value="">Select Item</option>
                    @foreach($inventoryItems as $inventoryItem)
                        <option value="{{ $inventoryItem->id }}" data-consumable="{{ $inventoryItem->getConsumableFlag() }}">{{ $inventoryItem->getItemName() }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div id="consumableBlock" style="display: none;">
            <div class="row mb-2">
                <div class="col-lg-3">
                    <div class="d-flex align-items-start h-100">
                        <label for="validationAssignedQuantity"
                               class="form-label required-label">Assigned
                            Quantity</label>
                    </div>
                </div>
                <div class="col-lg-9">
                    <input name="assigned_quantity" class="form-control"
                           value="{{ old('assigned_quantity') }}">
                    @if ($errors->has('assigned_quantity'))
                        {!! $errors->first('assigned_quantity') !!}
                    @endif
                </div>
            </div>
        </div>
        <div id="nonConsumableBlock" style="display: none;">
            <div class="row mb-2">
                <div class="col-lg-3">
                    <div class="d-flex align-items-start h-100">
                        <label for="validationLeaveType"
                               class="m-0">Assigned Assets</label>
                    </div>
                </div>
                <div class="col-lg-9">
                    <select name="assigned_asset_ids[]"
                            class="select2 form-control assigned_assets" multiple>
                    </select>
                    @if($errors->has('assigned_asset_id'))
                        {!! $errors->first('assigned_asset_id') !!}
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Update</button>
    </div>
    {!! csrf_field() !!}
</form>
