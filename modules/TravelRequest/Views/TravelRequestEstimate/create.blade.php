<div class="modal-header bg-primary text-white">
    <h5 class="modal-title mb-0 fs-6">Add Travel Advanced Request</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form action="{!! route('travel.requests.estimate.store', $travelRequest->id) !!}" method="post"
      enctype="multipart/form-data" id="estimateForm" autocomplete="off">
    <div class="modal-body">
        <div class="card-body">
            <div class="row mb-2">
                <div class="col-lg-3">
                    <div class="d-flex align-items-start h-100">
                        <label for="estimatedDSA" class="form-label required-label">{{ __('label.estimated-dsa') }} </label>
                    </div>
                </div>
                <div class="col-lg-9">
                    <input type="number" class="form-control" name="estimated_dsa" id="estimatedDSA"
                        value="{{ $estimatedDsaAmount }}">
                </div>
            </div>

            <div class="row mb-2">
                <div class="col-lg-3">
                    <div class="d-flex align-items-start h-100">
                        <label for="estimated_air_fare" class="m-0">{{ __('label.estimated-air-fare') }}
                        </label>
                    </div>
                </div>
                <div class="col-lg-9">
                    <input type="number" class="form-control" name="estimated_air_fare" id="estimated_air_fare" value=""
                        min="0">
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-lg-3">
                    <div class="d-flex align-items-start h-100">
                        <label for="estimated_vehicle_fare"
                            class="m-0">{{ __('label.estimated-vehicle-fare') }}
                        </label>
                    </div>
                </div>
                <div class="col-lg-9">
                    <input type="number" class="form-control" name="estimated_vehicle_fare" id="estimated_vehicle_fare"
                        value="" min="0">
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-lg-3">
                    <div class="d-flex align-items-start h-100">
                        <label for="estimated_vehicle_fare" class="m-0">{{ __('label.advance-amount') }}
                        </label>
                    </div>
                </div>
                <div class="col-lg-9">
                    <input type="number" class="form-control" name="advance_amount" id="advance_amount" value=""
                        min="0">
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-lg-3">
                    <div class="d-flex align-items-start h-100">
                        <label for="estimated_vehicle_fare"
                            class="m-0">{{ __('label.miscellaneous-amount') }}
                        </label>
                    </div>
                </div>
                <div class="col-lg-9">
                    <input type="number" class="form-control" name="miscellaneous_amount" id="miscellaneous_amount"
                        value="" min="0">
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-lg-3">
                    <div class="d-flex align-items-start h-100">
                        <label for="estimated_vehicle_fare"
                            class="m-0">{{ __('label.miscellaneous-remarks') }}
                        </label>
                    </div>
                </div>
                <div class="col-lg-9">
                    <textarea class="form-control" name="miscellaneous_remarks" id="miscellaneous_remarks"></textarea>
                </div>
            </div>

        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Save</button>
    </div>
    {!! csrf_field() !!}
</form>
