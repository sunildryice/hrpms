<div class="text-white modal-header bg-primary">
    <h5 class="mb-0 modal-title fs-6" id="openModalLabel">Add Direct Dispatch</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form action="{!! route('good.requests.direct.dispatch.store', $inventoryItem->id) !!}" method="post" enctype="multipart/form-data" id="directDispatchForm"
    autocomplete="off">
    @csrf
    <div class="modal-body">
        <div class="mb-2 row">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">Item Name</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input class="form-control" data-width="100%" name="item_name"
                    value="{{ $inventoryItem->getItemName() }}" disabled />
            </div>
        </div>
        <div class="mb-2 row">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="m-0">Unit</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input class="form-control" type="text" disabled value="{{ $inventoryItem->getUnitName() }}">
            </div>
        </div>
        <div class="mb-2 row">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="m-0">Available Quantity</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input class="form-control" type="text" disabled
                    value="{{ $inventoryItem->getAvailableQuantity() }}">
            </div>
        </div>
        <div class="mb-2 row">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="purpose" class="form-label required-label">Purpose</label>
                </div>
            </div>
            <div class="col-lg-9">
                <textarea class="form-control" name="purpose" id="purpose" rows="2" placeholder="Purpose"></textarea>
            </div>
            @if ($errors->has('purpose'))
                <div class="fv-plugins-message-container invalid-feedback">
                    <div data-field="purpose">
                        {!! $errors->first('purpose') !!}
                    </div>
                </div>
            @endif
        </div>
        <div class="mb-2 row">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="quantity" class="form-label required-label">Quantity</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input class="form-control" data-width="100%" name="quantity" id="quantity">
            </div>
            @if ($errors->has('quantity'))
                <div class="fv-plugins-message-container invalid-feedback">
                    <div data-field="quantity">
                        {!! $errors->first('quantity') !!}
                    </div>
                </div>
            @endif
        </div>
        <div class="mb-2 row">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="office_id" class="form-label required-label">Office</label>
                </div>
            </div>
            <div class="col-lg-9">
                <select name="office_id" id="office_id" class="form-control select2">
                    <option value="">Select office</option>
                    @foreach ($offices as $office)
                        <option value="{{ $office->id }}">{{ $office->getOfficeName() }}</option>
                    @endforeach
                </select>
            </div>
            @if ($errors->has('office_id'))
                <div class="fv-plugins-message-container invalid-feedback">
                    <div data-field="office_id">
                        {!! $errors->first('office_id') !!}
                    </div>
                </div>
            @endif
        </div>
        <div class="mb-2 row">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="receiver_id" class="form-label required-label">Send To</label>
                </div>
            </div>
            <div class="col-lg-9">
                <select name="receiver_id" id="receiver_id" class="form-control select2">
                    <option value="">Select Receiver</option>
                </select>
            </div>
        </div>
        <div class="mb-2 row">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="approver_id" class="form-label required-label">Approver</label>
                </div>
            </div>
            <div class="col-lg-9">
                <select name="approver_id" id="approver_id" class="form-control select2">
                    <option value="">Select approver</option>
                    @foreach ($approvers as $approver)
                        <option value="{{ $approver->id }}">{{ $approver->getFullName() }}</option>
                    @endforeach
                </select>
            </div>
            @if ($errors->has('approver_id'))
                <div class="fv-plugins-message-container invalid-feedback">
                    <div data-field="approver_id">
                        {!! $errors->first('approver_id') !!}
                    </div>
                </div>
            @endif
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Save</button>
    </div>
</form>
