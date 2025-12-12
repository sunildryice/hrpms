<div class="modal-header bg-primary text-white">
    <h5 class="modal-title mb-0 fs-6" id="openModalLabel">Edit Local Travel Claim</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form action="{!! route('travel.claims.local.travel.update', [$localTravelClaim->travel_claim_id, $localTravelClaim->id]) !!}" method="post" enctype="multipart/form-data" id="claimLocalTravelForm"
    autocomplete="off">
    <div class="modal-body">
        <div class="mb-2 row">
            <div class="col-lg-3"><label class="form-label required-label">Date</label></div>
            <div class="col-lg-9">
                <input type="text" class="form-control datepicker" name="travel_date"
                    value="{{ old('travel_date', $localTravelClaim->travel_date?->format('Y-m-d')) }}"
                    onfocus="this.blur()" placeholder="yyyy-mm-dd" />
            </div>
        </div>

        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label class="form-label required-label">Purpose</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="text" class="form-control" name="purpose"
                    value="{{ old('purpose', $localTravelClaim->purpose) }}" placeholder="Purpose">
            </div>
        </div>

        <div class="mb-2 row">
            <div class="col-lg-3"><label class="form-label required-label">From Place</label></div>
            <div class="col-lg-9">
                <input type="text" class="form-control" name="departure_place"
                    value="{{ old('departure_place', $localTravelClaim->departure_place) }}">
            </div>
        </div>

        <div class="mb-2 row">
            <div class="col-lg-3"><label class="form-label required-label">To Place</label></div>
            <div class="col-lg-9">
                <input type="text" class="form-control" name="arrival_place"
                    value="{{ old('arrival_place', $localTravelClaim->arrival_place) }}">
            </div>
        </div>

        <div class="mb-2 row">
            <div class="col-lg-3"><label class="form-label">Travel Fare</label></div>
            <div class="col-lg-9">
                <input type="number" class="form-control" name="travel_fare" id="edit_travel_fare"
                    value="{{ old('travel_fare', $localTravelClaim->travel_fare) }}">
            </div>
        </div>

        <div class="row mb-2">
            <div class="col-lg-3"><label class="form-label">Remarks</label></div>
            <div class="col-lg-9">
                <textarea name="remarks" class="form-control">{{ old('remarks', $localTravelClaim->remarks) }}</textarea>
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
                @if (file_exists('storage/' . $localTravelClaim->attachment) && $localTravelClaim->attachment != '')
                    <div class="media">
                        <a href="{!! asset('storage/' . $localTravelClaim->attachment) !!}" target="_blank" class="fs-5" title="View Attachment">
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
