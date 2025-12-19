<div class="modal-header bg-primary text-white">
    <div class="modal-title fw-bold text-uppercase" id="openModalLabel">Add New Maintenance Item</div>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form action="{!! route('maintenance.requests.items.store', $maintenanceRequest->id) !!}" method="post" enctype="multipart/form-data" id="maintenanceRequestItemForm"
    autocomplete="off">
    <div class="modal-body">
        {{-- <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="atvcde" class="form-label required-label">{{ __('label.activity-code') }}
                    </label>
                </div>
            </div>
            <div class="col-lg-9">
                <select name="activity_code_id"
                        class="form-select select2"
                        data-width="100%">
                    <option value="">Select an Activity Code</option>
                    @foreach ($activityCodes as $activityCode)
                        <option value="{{ $activityCode->id }}">
                            {{ $activityCode->getActivityCodeWithDescription() }}
                        </option>
                    @endforeach
                </select>
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
                        class="form-select select2"
                        data-width="100%">
                    <option value="">Select an Account Code</option>
                </select>
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
                        class="form-select select2"
                        data-width="100%">
                    <option value="">Select a Donor Code</option>
                    @foreach ($donorCodes as $donorCode)
                        <option value="{{ $donorCode->id }}">
                            {{ $donorCode->getDonorCodeWithDescription() }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
         --}}
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="itmname" class="form-label required-label">{{ __('label.item-name') }}
                    </label>
                </div>
            </div>
            <div class="col-lg-9">
                <select name="item_id" class="form-select select2" data-width="100%">
                    <option value="">Select a Item Name</option>
                    @foreach ($items as $item)
                        <option value="{{ $item->id }}">
                            {{ $item->title }}
                        </option>
                    @endforeach
                </select>
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
                <textarea name="problem" id="prblmdsc" cols="30" rows="5" class="form-control"></textarea>
            </div>
        </div>
        {{-- <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="estcst"
                        class="form-label required-label">{{ __('label.estimation-cost-for') }}</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="number" class="form-control" id="estcst" name="estimated_cost">
            </div>
        </div> --}}
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label class="form-label required-label">
                        Replacement Goods Needed for Maintenance
                    </label>
                </div>
            </div>

            <div class="col-lg-9">
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="replacement_good_needed"
                        id="replacement_good_needed_yes" value="1">
                    <label class="form-check-label" for="replacement_good_needed_yes">
                        Yes
                    </label>
                </div>

                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="replacement_good_needed"
                        id="replacement_good_needed_no" value="0">
                    <label class="form-check-label" for="replacement_good_needed_no">
                        No
                    </label>
                </div>
            </div>
        </div>

        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="rmsks" class="form-label required-label">{{ __('label.remarks') }}</label>
                </div>
            </div>
            <div class="col-lg-9">
                <textarea name="remarks" id="rmsks" cols="30" rows="5" class="form-control"></textarea>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Save</button>
    </div>
    {!! csrf_field() !!}
</form>
