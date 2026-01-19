<div class="modal-header bg-primary text-white">
    <h5 class="modal-title mb-0 fs-6" id="openModalLabel">Edit GRN Item</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form action="{!! route('grns.items.update', [$grnItem->grn_id, $grnItem->id]) !!}" method="post"
    enctype="multipart/form-data" id="grnItemForm" autocomplete="off">
    <div class="modal-body">
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">Item</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="text" class="form-control" disabled value="{{ $grnItem->getItemName() }}">
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">Unit</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="text" class="form-control" disabled value="{{ $grnItem->getUnitName() }}">
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">{{ __('label.quantity-received') }}</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="number" class="form-control" name="quantity" value="{{ $grnItem->quantity }}"
                    placeholder="{{ __('label.quantity-received') }}">
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">{{ __('label.unit-price') }}</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="number" class="form-control" name="unit_price" value="{{ $grnItem->unit_price }}"
                    placeholder="{{ __('label.unit-price') }}">
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="m-0">{{ __('label.sub-total') }}</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input readonly class="form-control" name="sub_total"
                    value="{{ $grnItem->quantity * $grnItem->unit_price }}" placeholder="{{ __('label.sub-total') }}">
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="m-0">{{ __('label.discount-amount') }}</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input class="form-control" name="discount_amount" value="{{ $grnItem->discount_amount }}"
                    placeholder="{{ __('label.discount-amount') }}">
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="validationdd" class="m-0">{{ __('label.vat-applicable') }}</label>
                </div>
            </div>
            <div class="col-lg-9">
                <div class=" form-switch">
                    <input class="form-check-input" type="checkbox" role="switch" id="flexSwitchCheckChecked"
                        name="vat_applicable" @if($grnItem->vat_amount > 0) checked @endif>
                    <label class="form-check-label" for="flexSwitchCheckChecked"></label>
                </div>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="m-0">{{ __('label.vat-amount') }}</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input readonly class="form-control" name="vat_amount" value="{{ $grnItem->vat_amount }}"
                    placeholder="{{ __('label.vat-amount') }}">
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="m-0">{{ __('label.total-amount') }}</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input readonly class="form-control" name="total_amount" value="{{ $grnItem->total_amount }}"
                    placeholder="{{ __('label.total-amount') }}">
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="m-0">{{ __('label.activity-code') }}</label>
                </div>
            </div>
            <div class="col-lg-9">
                <select class="form-control select2" data-width="100%" name="activity_code_id">
                    <option value="">Select Activity Code</option>
                    @foreach($activityCodes as $activityCode)
                        <option value="{!! $activityCode->id !!}" @if($activityCode->id == $grnItem->activity_code_id)
                        selected @endif>{{ $activityCode->getActivityCodeWithDescription() }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="m-0">{{ __('label.account-code') }}</label>
                </div>
            </div>
            <div class="col-lg-9">
                <select class="form-control select2" data-width="100%" name="account_code_id">
                    <option value="">Select Account Code</option>
                    @foreach($accountCodes as $accountCode)
                        <option value="{!! $accountCode->id !!}" @if($accountCode->id == $grnItem->account_code_id) selected
                        @endif>{{ $accountCode->getAccountCodeWithDescription() }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="m-0">{{ __('label.donor-code') }}</label>
                </div>
            </div>
            <div class="col-lg-9">
                <select class="form-control select2" data-width="100%" name="donor_code_id">
                    <option value="">Select Donor Code</option>
                    @foreach($donorCodes as $donorCode)
                        <option value="{!! $donorCode->id !!}" @if($donorCode->id == $grnItem->donor_code_id) selected @endif>
                            {{ $donorCode->getDonorCodeWithDescription() }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="specification" class="m-0">{{ __('label.specification') }}</label>
                </div>
            </div>
            <div class="col-lg-9">
                <textarea class="form-control" name="specification" id="specification"
                    rows="2">{{ $grnItem->specification }}</textarea>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" value="grn" name="btn" class="btn btn-primary">Update</button>
    </div>
    {!! method_field('PUT') !!}
    {!! csrf_field() !!}
</form>