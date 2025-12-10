<div class="modal-header bg-primary text-white">
    <h5 class="modal-title mb-0 fs-6" id="openModalLabel">Edit Detail</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form action="{!! route('local.travel.reimbursements.itineraries.update', [$localTravelItinerary->local_travel_reimbursement_id, $localTravelItinerary->id]) !!}" method="post"
      enctype="multipart/form-data" id="localTravelItineraryForm" autocomplete="off">
    <div class="modal-body">
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">Travel Date</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="text" class="form-control" readonly="readonly" value="{{ $localTravelItinerary->travel_date->format('Y-m-d') }}" name="travel_date" placeholder="Travel Date">
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">Purpose</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="text" class="form-control" name="purpose" value="{{ $localTravelItinerary->purpose }}" placeholder="Purpose">
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">Mode</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="text" class="form-control" name="travel_mode" value="{{ $localTravelItinerary->travel_mode }}" placeholder="Travel Mode">
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">Activity Code</label>
                </div>
            </div>
            <div class="col-lg-9">
                <select class="form-control select2" data-width="100%" name="activity_code_id">
                    <option value="">Select Activity Code</option>
                    @foreach($activityCodes as $activityCode)
                        <option value="{!! $activityCode->id !!}" @if($activityCode->id == $localTravelItinerary->activity_code_id) selected @endif>{{ $activityCode->getActivityCodeWithDescription() }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">Account Code</label>
                </div>
            </div>
            <div class="col-lg-9">
                <select class="form-control select2" data-width="100%" name="account_code_id">
                    <option value="">Select Account Code</option>
                    @foreach($accountCodes as $accountCode)
                        <option value="{!! $accountCode->id !!}" @if($accountCode->id == $localTravelItinerary->account_code_id) selected @endif>{{ $accountCode->getAccountCodeWithDescription() }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        {{-- <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">Donor Code</label>
                </div>
            </div>
            <div class="col-lg-9">
                <select class="form-control select2" data-width="100%" name="donor_code_id">
                    <option value="">Select Donor Code</option>
                    @foreach($donorCodes as $donorCode)
                        <option value="{!! $donorCode->id !!}" @if($donorCode->id == $localTravelItinerary->donor_code_id) selected @endif>{{ $donorCode->getDonorCodeWithDescription() }}</option>
                    @endforeach
                </select>
            </div>
        </div> --}}

        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="m-0">Departure Place</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="text" class="form-control" name="departure_place" value="{{ $localTravelItinerary->departure_place }}" placeholder="Departure Place">
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="m-0">Arrival Place</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="text" class="form-control" name="arrival_place" value="{{ $localTravelItinerary->arrival_place }}" placeholder="Arrival Place">
            </div>
        </div>

        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="m-0">Distance (in KM)</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="number" class="form-control" name="total_distance" value="{{ $localTravelItinerary->total_distance }}" placeholder="Distance">
            </div>
        </div>

        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">Fare</label>
                </div>
            </div>
            <div class="col-lg-6">
                <input type="number" class="form-control" name="total_fare" value="{{ $localTravelItinerary->total_fare }}" placeholder="Fare">
            </div>
        </div>

        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="m-0">Remarks</label>
                </div>
            </div>
            <div class="col-lg-9">
                <textarea rows="5" class="form-control" name="remarks">{{ $localTravelItinerary->remarks }}</textarea>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Update</button>
    </div>
    {!! method_field('PUT') !!}
    {!! csrf_field() !!}
</form>
