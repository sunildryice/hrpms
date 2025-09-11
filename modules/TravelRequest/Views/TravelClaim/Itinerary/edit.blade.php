<div class="modal-header bg-primary text-white">
    <h5 class="modal-title mb-0 fs-6" id="openModalLabel">Edit Claim Itinerary</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form action="{!! route('travel.claims.itineraries.update', [$travelClaimItinerary->travel_claim_id, $travelClaimItinerary->id]) !!}" method="post"
      enctype="multipart/form-data" id="claimItineraryForm" autocomplete="off">
    <div class="modal-body">
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="m-0">{{ __('label.from-date') }}</label>
                </div>
            </div>
            <div class="col-lg-3">
                <input type="text" class="form-control" value="{{ $travelClaimItinerary->travelRequestItinerary->getDepartureDate() }}" disabled>
            </div>
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="m-0">{{ __('label.from-place') }}</label>
                </div>
            </div>
            <div class="col-lg-3">
                <input type="text" class="form-control" value="{{ $travelClaimItinerary->travelRequestItinerary->departure_place }}" disabled>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="m-0">{{ __('label.to-date') }}</label>
                </div>
            </div>
            <div class="col-lg-3">
                <input type="text" class="form-control" value="{{ $travelClaimItinerary->travelRequestItinerary->getArrivalDate() }}" disabled>
            </div>
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="m-0">{{ __('label.to-place') }}</label>
                </div>
            </div>
            <div class="col-lg-3">
                <input type="text" class="form-control" value="{{ $travelClaimItinerary->travelRequestItinerary->arrival_place }}" disabled>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="m-0">{{ __('label.activity') }}</label>
                </div>
            </div>
            <div class="col-lg-3">
                <input type="text" class="form-control" value="{{ $travelClaimItinerary->travelRequestItinerary->getActivityCode() }}" disabled>
            </div>
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="m-0">{{ __('label.account-code') }}</label>
                </div>
            </div>
            <div class="col-lg-3">
                <input type="text" class="form-control" value="{{ $travelClaimItinerary->travelRequestItinerary->getAccountCode() }}" disabled>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="m-0">{{ __('label.donor-code') }}</label>
                </div>
            </div>
            <div class="col-lg-3">
                <input type="text" class="form-control" value="{{ $travelClaimItinerary->travelRequestItinerary->getDonorCode() }}" disabled>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="m-0">{{ __('label.dsa-rate') }}</label>
                </div>
            </div>
            <div class="col-lg-3">
                <input type="text" class="form-control" name="dsa_unit_price" value="{{ $travelClaimItinerary->travelRequestItinerary->dsa_unit_price }}" disabled>
            </div>
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="m-0">{{ __('label.overnights') }}</label>
                </div>
            </div>
            <div class="col-lg-3">
                <input type="text" class="form-control" name="overnights" value="{{ $travelClaimItinerary->travelRequestItinerary->getOvernights() }}" disabled>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">Percentage Charged </label>
                </div>
            </div>
            <div class="col-lg-3">
                <input type="number" class="form-control" name="percentage_charged" value="{{ $travelClaimItinerary->percentage_charged }}" placeholder="Percentage Charged">
            </div>
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">{{ __('label.total-dsa') }} </label>
                </div>
            </div>
            <div class="col-lg-3">
                <input type="number" class="form-control" name="total_amount" value="{{ $travelClaimItinerary->total_amount }}" disabled>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">Charging Office</label>
                </div>
            </div>
            <div class="col-lg-9">
                <select class="form-control select2" data-width="100%" name="office_id">
                    <option value="">Select Charging Office</option>
                    @foreach($offices as $office)
                        <option value="{!! $office->id !!}" @if($travelClaimItinerary->office_id == $office->id) selected @endif>{{ $office->getOfficeName() }}</option>
                    @endforeach
                </select>
            </div>
        </div>
         <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label ">Remarks</label>
                </div>
            </div>
            <div class="col-lg-9">
                <textarea name="description" class="form-control">{!! $travelClaimItinerary->description !!}</textarea>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="m-0">Attachment </label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="file" class="form-control" name="attachment">
                <small>Supported file types jpeg/jpg/png/pdf and file size of upto 2MB.</small>
                @if(file_exists('storage/'.$travelClaimItinerary->attachment) && $travelClaimItinerary->attachment != '')
                    <div class="media">
                        <a href="{!! asset('storage/'.$travelClaimItinerary->attachment) !!}" target="_blank" class="fs-5"
                           title="View Attachment">
                            <i class="bi bi-file-earmark-medical"></i>
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Update</button>
    </div>
    {!! csrf_field() !!}
</form>
