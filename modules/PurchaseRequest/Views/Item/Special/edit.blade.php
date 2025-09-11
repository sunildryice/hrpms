<div class="modal-header bg-primary text-white">
    <h5 class="modal-title mb-0 fs-6" id="openModalLabel">Edit Purchase Item</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form action="{!! route('purchase.requests.items.special.update', [$purchaseRequestItem->purchase_request_id, $purchaseRequestItem->id]) !!}" method="post"
      enctype="multipart/form-data" id="purchaseRequestItemForm" autocomplete="off">
    <div class="modal-body">
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">Item </label>
                </div>
            </div>
            <div class="col-lg-9">
                <select class="form-control select2" data-width="100%" name="item_id" disabled>
                    <option value="">Select Item</option>
                    @foreach($items as $item)
                        <option value="{!! $item->id !!}" @if($purchaseRequestItem->item_id == $item->id) selected @endif>{{ $item->getItemName() }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">Unit </label>
                </div>
            </div>
            <div class="col-lg-9">
                <select class="form-control select2" data-width="100%" name="unit_id" disabled>
                    <option value="">Select Unit</option>
                    @foreach($units as $unit)
                        <option value="{!! $unit->id !!}" @if($purchaseRequestItem->unit_id == $unit->id) selected @endif>{{ $unit->getUnitName() }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">Specification </label>
                </div>
            </div>
            <div class="col-lg-9">
                <textarea rows="5" class="form-control" name="specification" disabled>{{ $purchaseRequestItem->specification }}</textarea>
            </div>
        </div>

        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">Quantity </label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="number" class="form-control" name="quantity" value="{{ $purchaseRequestItem->quantity }}" placeholder="Quantity" disabled>
            </div>
        </div>

        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">Unit Price </label>
                </div>
            </div>
            <div class="col-lg-6">
                <input type="number" class="form-control" name="unit_price" value="{{ $purchaseRequestItem->unit_price }}" placeholder="Unit Price" disabled>
            </div>
            <div class="col-lg-3">
                <input type="number" class="form-control total_price" value="{{ $purchaseRequestItem->total_price }}" readonly>
            </div>
        </div>



        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">Activity Code </label>
                </div>
            </div>
            <div class="col-lg-9">
                <select class="form-control select2" data-width="100%" name="activity_code_id" disabled>
                    <option value="">Select Activity Code</option>
                    @foreach($activityCodes as $activityCode)
                        <option value="{!! $activityCode->id !!}" @if($purchaseRequestItem->activity_code_id == $activityCode->id) selected @endif>{{ $activityCode->getActivityCodeWithDescription() }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">Account Code </label>
                </div>
            </div>
            <div class="col-lg-9">
                <select class="form-control select2" data-width="100%" name="account_code_id" disabled>
                    <option value="">Select Account Code</option>
                    @foreach($accountCodes as $accountCode)
                        <option value="{!! $accountCode->id !!}" @if($purchaseRequestItem->account_code_id == $accountCode->id) selected @endif>{{ $accountCode->getAccountCodeWithDescription() }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="m-0">Donor Code </label>
                </div>
            </div>
            <div class="col-lg-9">
                <select class="form-control select2" data-width="100%" name="donor_code_id" disabled>
                    <option value="">Select Donor Code</option>
                    @foreach($donorCodes as $donorCode)
                        <option value="{!! $donorCode->id !!}" @if($purchaseRequestItem->donor_code_id == $donorCode->id) selected @endif>{{ $donorCode->getDonorCodeWithDescription() }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="district_id" class="form-label required-label">District</label>
                </div>
            </div>
            <div class="col-lg-6">
                <select class="form-control select2" name="district_id" id="district_id">
                    <option value="">Select district</option>
                    @foreach ($districts as $district)
                        <option value="{{$district->id}}" {{ $purchaseRequestItem->district_id == $district->id ? 'selected' : '' }}>{{ $district->getDistrictName() }}</option>
                    @endforeach
                </select>
            </div>
        </div> --}}

        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="office_id" class="form-label required-label">Office</label>
                </div>
            </div>
            <div class="col-lg-6">
                <select class="form-control select2" name="office_id" id="office_id">
                    <option value="">Select office</option>
                    @foreach ($offices as $office)
                        <option value="{{$office->id}}" {{ $purchaseRequestItem->office_id == $office->id ? 'selected' : '' }}>{{ $office->getOfficeName() }}</option>
                    @endforeach
                </select>
            </div>
        </div>

    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Update</button>
    </div>
    {!! method_field('PUT') !!}
    {!! csrf_field() !!}
</form>
