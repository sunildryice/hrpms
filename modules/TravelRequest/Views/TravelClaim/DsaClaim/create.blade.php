<div class="modal-header bg-primary text-white">
    <h5 class="modal-title mb-0 fs-6" id="openModalLabel">Add TADA Claim</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form action="{!! route('travel.claims.dsa.store', $travelClaim->id) !!}" method="post" enctype="multipart/form-data" id="claimItineraryForm"
    autocomplete="off">
    <div class="modal-body">
        <div class="row mb-3">
            <div class="col-lg-9 offset-lg-3">
                <small>
                    <em>DSA Rates per day → Breakfast Rs.400 | Lunch Rs.500 | Dinner Rs.600 | Incidental Rs.300</em>
                </small>
            </div>
        </div>
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
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">Activities/Tasks</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="text" class="form-control" name="activities" value="" placeholder="Activities">
            </div>
        </div>
        <div class="mb-2 row">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">From Date</label>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="row">
                    <div class="input-group has-validation">
                        <input type="text" class="form-control @if ($errors->has('departure_date')) is-invalid @endif"
                            name="departure_date" value="{{ old('departure_date') }}" onfocus="this.blur()"
                            placeholder="yyyy-mm-dd" />
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">From Place</label>
                </div>
            </div>
            <div class="col-lg-3">
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
                    <label for="" class="form-label required-label">To Date</label>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="row">
                    <div class="input-group has-validation">
                        <input type="text" class="form-control @if ($errors->has('arrival_date')) is-invalid @endif"
                            name="arrival_date" value="{{ old('arrival_date') }}" onfocus="this.blur()"
                            placeholder="yyyy-mm-dd" />
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">To Place</label>
                </div>
            </div>
            <div class="col-lg-3">
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
                    <label class="form-label">Days Spent</label>
                </div>
            </div>
            <div class="col-lg-3">
                <input type="number" class="form-control" name="days_spent" readonly>
            </div>
        </div>
        <div class="mb-2 row">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label">Breakfast</label>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="row">
                    <div class="input-group has-validation">
                        <input type="number" class="form-control" name="breakfast" id="breakfast" value=""
                            min="0">
                    </div>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label">Lunch</label>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="row">
                    <div class="input-group has-validation">
                        <input type="number" class="form-control" name="lunch" id="lunch" value=""
                            min="0">
                    </div>
                </div>
            </div>
        </div>
        <div class="mb-2 row">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label">Dinner</label>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="row">
                    <div class="input-group has-validation">
                        <input type="number" class="form-control" name="dinner" id="dinner" value=""
                            min="0">
                    </div>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label">Incidental Cost </label>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="row">
                    <div class="input-group has-validation">
                        <input type="number" class="form-control" name="incident_cost" id="incident_cost"
                            value="" min="0">
                    </div>
                </div>
            </div>
        </div>
        <div class="mb-2 row">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label">Total DSA per day</label>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="row">
                    <div class="input-group has-validation">
                        <input type="number" class="form-control" name="total_dsa" id="total_dsa" value=""
                            min="0" readonly>
                    </div>
                </div>
            </div>
        </div>


        <div class="mb-2 row">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label">Daily Allowance</label>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="row">
                    <div class="input-group has-validation">
                        <input type="number" class="form-control" name="daily_allowance" id="daily_allowance"
                            value="" min="0" readonly>
                    </div>
                </div>
            </div>
        </div>

        <div class="mb-2 row">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label">Lodging expenses</label>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="row">
                    <div class="input-group has-validation">
                        <input type="number" class="form-control" name="lodging_expense" id="lodging_expense"
                            value="" min="0">
                    </div>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label">Other expenses</label>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="row">
                    <div class="input-group has-validation">
                        <input type="number" class="form-control" name="other_expense" id="other_expense"
                            value="" min="0">
                    </div>
                </div>
            </div>
        </div>

        <div class="mb-2 row">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label">Total Amount</label>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="row">
                    <div class="input-group has-validation">
                        <input type="number" class="form-control" name="total_amount" id="total_amount"
                            value="" min="0" readonly>
                    </div>
                </div>
            </div>
        </div>

        <div class="mb-2 row">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="m-0">{{ __('label.mode-of-travel') }} </label>
                </div>
            </div>
            <div class="col-lg-9">
                <select name="travel_modes[]" class="select2 form-control travel-mode" data-width="100%" multiple>
                    @foreach ($travelModes as $travelMode)
                        <option value="{{ $travelMode->id }}">
                            {{ $travelMode->title }}
                        </option>
                    @endforeach
                </select>
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
