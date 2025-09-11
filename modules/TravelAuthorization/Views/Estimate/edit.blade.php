<div class="modal-header bg-primary text-white">
    <h5 class="modal-title mb-0 fs-6">Edit Travel Request Estimate</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form action="{!! route('ta.requests.estimate.update', $estimate->id) !!}" method="post"
      enctype="multipart/form-data" id="estimateForm" autocomplete="off">
      @method('PUT')
    <div class="modal-body">
        <div class="card-body">
            <div class="row mb-2">
                <div class="col-lg-3">
                    <div class="d-flex align-items-start h-100">
                        <label for="estimated_air_fare" class="m-0">{{ __('label.particulars') }}
                        </label>
                    </div>
                </div>
                <div class="col-lg-9">
                    <input type="text" class="form-control" name="particulars" id="particulars" value="{{$estimate->particulars}}"
                        min="0">
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-lg-3">
                    <div class="d-flex align-items-start h-100">
                        <label for="" class="form-label required-label">Quantity</label>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="row">
                        <div class="input-group has-validation">
                            <input type="number" class="form-control" name="quantity" autofocus="" value="{{$estimate->quantity}}">
                        </div>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="d-flex align-items-start h-100">
                        <label for="" class="form-label required-label">Days</label>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="row">
                        <div class="input-group has-validation">
                            <input type="number" class="form-control" name="days" autofocus="" value="{{$estimate->days}}">
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-lg-3">
                    <div class="d-flex align-items-start h-100">
                        <label for="" class="form-label required-label">Rate</label>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="row">
                        <div class="input-group has-validation">
                            <input type="number" class="form-control" name="unit_price" autofocus="" value="{{$estimate->unit_price}}">
                        </div>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="d-flex align-items-start h-100">
                        <label for="" class="form-label required-label">Total</label>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="row">
                        <div class="input-group has-validation">
                            <input type="number" class="form-control" name="total_amount" autofocus="" readonly value="{{$estimate->total_price}}">
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-lg-3">
                    <div class="d-flex align-items-start h-100">
                        <label for="validationProject" class="form-label required-label">Activity Code
                        </label>
                    </div>
                </div>
                <div class="col-lg-9">
                    <select name="activity_code_id" class="select2 form-control" data-width="100%">
                        <option value="">Select Activity Code</option>
                        @foreach($activityCodes as $activity)
                            <option
                                value="{{ $activity->id }}" {{$activity->id == $estimate->activity_code_id ? "selected":""}}>
                                {{ $activity->getActivityCodeWithDescription() }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-lg-3">
                    <div class="d-flex align-items-start h-100">
                        <label for="validationProject" class="form-label required-label">Account Code
                        </label>
                    </div>
                </div>
                <div class="col-lg-9">
                    <select name="account_code_id" class="select2 form-control" data-width="100%">
                        <option value="">Select Account Code</option>
                        @foreach($accountCodes as $accountCode)
                            <option
                                value="{{ $accountCode->id }}" {{ $accountCode->id == $estimate->account_code_id ? "selected":"" }}>
                                {{ $accountCode->getAccountCodeWithDescription() }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-lg-3">
                    <div class="d-flex align-items-start h-100">
                        <label for="validationProject" class="m-0 required-label">Donor Code
                        </label>
                    </div>
                </div>
                <div class="col-lg-9">
                    <select name="donor_code_id" class="select2 form-control" data-width="100%">
                        <option value="">Select Donor Code</option>
                        @foreach($donorCodes as $donor)
                            <option
                                value="{{ $donor->id }}" {{ $donor->id == $estimate->donor_code_id ? "selected":"" }}>
                                {{ $donor->getDonorCodeWithDescription() }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Update</button>
    </div>
    {!! csrf_field() !!}
</form>
