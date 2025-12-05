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
                        <label for="estimated_dsa" class="form-label">{{ __('label.estimated-dsa') }} </label>
                    </div>
                </div>
                <div class="col-lg-9">
                    <input type="number" class="form-control" name="estimated_dsa" id="estimated_dsa"
                        value="">
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
                        <label for="estimated_hotel_accommodation"
                            class="m-0">{{ __('label.estimated-hotel-accommodation') }}
                        </label>
                    </div>
                </div>
                <div class="col-lg-9">
                    <input type="number" class="form-control" name="estimated_hotel_accommodation" id="estimated_hotel_accommodation"
                        value="" min="0">
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-lg-3">
                    <div class="d-flex align-items-start h-100">
                        <label for="estimated_airport_taxi"
                            class="m-0">{{ __('label.estimated-airport-taxi') }}
                        </label>
                    </div>
                </div>
                <div class="col-lg-9">
                    <input type="number" class="form-control" name="estimated_airport_taxi" id="estimated_airport_taxi"
                        value="" min="0">
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-lg-3">
                    <div class="d-flex align-items-start h-100">
                        <label for="miscellaneous_amount"
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
                        <label for="estimated_event_activities_cost"
                            class="m-0">{{ __('label.estimated-event-activities-cost') }}
                        </label>
                    </div>
                </div>
                <div class="col-lg-9">
                    <input type="number" class="form-control" name="estimated_event_activities_cost" id="estimated_event_activities_cost"
                        value="" min="0">
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-lg-3">
                    <div class="d-flex align-items-start h-100">
                        <label for="miscellaneous_remarks"
                            class="m-0">{{ __('label.miscellaneous-remarks') }}
                        </label>
                    </div>
                </div>
                <div class="col-lg-9">
                    <textarea class="form-control" name="miscellaneous_remarks" id="miscellaneous_remarks"></textarea>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-lg-3">
                    <div class="d-flex align-items-start h-100">
                        <label for="total_amount"
                            class="m-0">{{ __('label.total-advance-amount') }}
                        </label>
                    </div>
                </div>
                <div class="col-lg-9">
                    <input type="number" class="form-control" name="total_amount" id="total_amount"
                        value="" min="0" readonly>
                </div>
            </div>

        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Save</button>
    </div>
    {!! csrf_field() !!}
</form>
