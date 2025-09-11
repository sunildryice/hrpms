<div class="modal-header bg-primary text-white">
    <div class="modal-title fw-bold text-uppercase" id="openModalLabel">Edit Maintenance Item</div>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form
    action="{!! route('maintenance.requests.items.update', [$maintenanceRequestItem->maintenance_id, $maintenanceRequestItem->id]) !!}"
    method="post"
    enctype="multipart/form-data" id="maintenanceRequestItemForm" autocomplete="off">
    <div class="modal-body">
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="atvcde" class="form-label required-label">{{ __('label.activity-code') }}
                    </label>
                </div>
            </div>
            <div class="col-lg-9">
                <select name="activity_code_id"
                        class="form-select select2 @if($errors->has('activity_code_id')) is-invalid @endif"
                        data-width="100%">
                    <option value="">Select an Activity Code</option>
                    @foreach($activityCodes as $activityCode)
                        <option value="{{ $activityCode->id }}"
                                @if($activityCode->id == $maintenanceRequestItem->activity_code_id)selected @endif>
                            {{ $activityCode->getActivityCodeWithDescription() }}
                        </option>
                    @endforeach
                </select>
                @if($errors->has('activity_code_id'))
                    <div class="fv-plugins-message-container invalid-feedback">
                        <div data-field="activity_code_id">
                            {!! $errors->first('activity_code_id') !!}
                        </div>
                    </div>
                @endif
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="acccde" class="form-label required-label">{{ __('label.account-code') }}
                    </label>
                </div>
            </div>
            <div class="col-lg-9">
                <select name="account_code_id"
                        class="form-select select2 @if($errors->has('account_code_id')) is-invalid @endif"
                        data-width="100%">
                    <option value="">Select an Account Code</option>
                    @foreach($accountCodes as $accountCode)
                        <option value="{{ $accountCode->id }}"
                                @if($accountCode->id == $maintenanceRequestItem->account_code_id)selected @endif>
                            {{ $accountCode->getAccountCodeWithDescription() }}
                        </option>
                    @endforeach
                </select>
                @if($errors->has('account_code_id'))
                    <div class="fv-plugins-message-container invalid-feedback">
                        <div data-field="account_code_id">
                            {!! $errors->first('account_code_id') !!}
                        </div>
                    </div>
                @endif
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="donor" class="form-label">{{ __('label.donor-grant') }}
                    </label>
                </div>
            </div>
            <div class="col-lg-9">
                <select name="donor_code_id"
                        class="form-select select2 @if($errors->has('donor_code_id')) is-invalid @endif"
                        data-width="100%">
                    <option value="">Select a Donor Code</option>
                    @foreach($donorCodes as $donorCode)
                        <option value="{{ $donorCode->id }}"
                                @if($donorCode->id == $maintenanceRequestItem->donor_code_id)selected @endif>
                            {{ $donorCode->getDonorCodeWithDescription() }}
                        </option>
                    @endforeach
                </select>
                @if($errors->has('donor_code_id'))
                    <div class="fv-plugins-message-container invalid-feedback">
                        <div data-field="donor_code_id">
                            {!! $errors->first('donor_code_id') !!}
                        </div>
                    </div>
                @endif
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="itmname" class="form-label required-label">{{ __('label.item-name') }}
                    </label>
                </div>
            </div>
            <div class="col-lg-9">
                <select name="item_id" class="form-select select2 @if($errors->has('item_id')) is-invalid @endif"
                        data-width="100%">
                    <option value="">Select a Item Name</option>
                    @foreach($items as $item)
                        <option value="{{ $item->id }}"
                                @if($item->id == $maintenanceRequestItem->item_id)selected @endif>
                            {{ $item->title }}
                        </option>
                    @endforeach
                </select>
                @if($errors->has('item_id'))
                    <div class="fv-plugins-message-container invalid-feedback">
                        <div data-field="item_id">
                            {!! $errors->first('item_id') !!}
                        </div>
                    </div>
                @endif
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="prblmdsc" class="form-label required-label">{{ __('label.service-request-for') }}
                    </label>
                </div>
            </div>
            <div class="col-lg-9">
                <textarea name="problem" id="prblmdsc" cols="30" rows="5"
                          class="form-control @if($errors->has('problem')) is-invalid @endif">@if($maintenanceRequestItem->problem){{$maintenanceRequestItem->problem}}@endif</textarea>
                @if($errors->has('problem'))
                    <div class="fv-plugins-message-container invalid-feedback">
                        <div data-field="problem">
                            {!! $errors->first('problem') !!}
                        </div>
                    </div>
                @endif
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="estcst" class="form-label required-label">{{ __('label.estimation-cost-for') }}</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="number" class="form-control @if($errors->has('estimated_cost')) is-invalid @endif"
                       id="estcst" name="estimated_cost"
                       value="@if($maintenanceRequestItem->estimated_cost){{$maintenanceRequestItem->estimated_cost}}@endif">
                @if($errors->has('estimated_cost'))
                    <div class="fv-plugins-message-container invalid-feedback">
                        <div data-field="estimated_cost">
                            {!! $errors->first('estimated_cost') !!}
                        </div>
                    </div>
                @endif
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="rmsks" class="form-label required-label">{{ __('label.remarks') }}</label>
                </div>
            </div>
            <div class="col-lg-9">
                <textarea name="remarks" id="rmsks" cols="30" rows="5"
                          class="form-control @if($errors->has('remarks')) is-invalid @endif">@if($maintenanceRequestItem->remarks){{$maintenanceRequestItem->remarks}}@endif</textarea>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary btn-sm" name="btn">Update</button>
        <button type="button" class="btn btn-danger btn-sm" data-bs-dismiss="modal">Close</button>
    </div>
    {!! csrf_field() !!}
    {!! method_field('PUT') !!}

</form>
