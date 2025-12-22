<div class="modal-header bg-primary text-white">
    <h5 class="modal-title mb-0 fs-6" id="openModalLabel">Add Local Travel Claim</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form action="{!! route('travel.claims.local.travel.store', $travelClaim->id) !!}" method="post" enctype="multipart/form-data" id="claimLocalTravelForm"
    autocomplete="off">
    <div class="modal-body">
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">Activity</label>
                </div>
            </div>
            <div class="col-lg-9">
                <select class="form-control select2" data-width="100%" name="activity_code_id">
                    <option value="">Select Activity</option>
                    @foreach ($activityCodes as $activityCode)
                        <option value="{!! $activityCode->id !!}">{{ $activityCode->getActivityCodeDescription() }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="mb-2 row">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">Date</label>
                </div>
            </div>
            <div class="col-lg-9">
                <div class="row">
                    <div class="input-group has-validation">
                        <input type="text" class="form-control @if ($errors->has('travel_date')) is-invalid @endif"
                            name="travel_date" value="{{ old('travel_date') }}" onfocus="this.blur()"
                            placeholder="yyyy-mm-dd" />
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">Purpose</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="text" class="form-control" name="purpose" value="" placeholder="Purpose">
            </div>
        </div>

        <div class="mb-2 row">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">From Place</label>
                </div>
            </div>
            <div class="col-lg-9">
                <div class="row">
                    <div class="input-group has-validation">
                        <input type="text" class="form-control" name="departure_place" autofocus="">
                    </div>
                </div>
            </div>
        </div>
        <div class="mb-2 row">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">To Place</label>
                </div>
            </div>
            <div class="col-lg-9">
                <div class="row">
                    <div class="input-group has-validation">
                        <input type="text" class="form-control" name="arrival_place" autofocus="">
                    </div>
                </div>
            </div>
        </div>
        <div class="mb-2 row">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">Travel Fare</label>
                </div>
            </div>
            <div class="col-lg-9">
                <div class="row">
                    <div class="input-group has-validation">
                        <input type="number" class="form-control" name="travel_fare" id="travel_fare" value=""
                            min="0">
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label">Remarks</label>
                </div>
            </div>
            <div class="col-lg-9">
                <textarea name="remarks" class="form-control"></textarea>
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
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Save</button>
    </div>
    {!! csrf_field() !!}
</form>
