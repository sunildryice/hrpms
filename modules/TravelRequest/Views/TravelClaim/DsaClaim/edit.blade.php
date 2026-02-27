<div class="modal-header bg-primary text-white">
    <h5 class="modal-title mb-0 fs-6" id="openModalLabel">Edit TADA Claim</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form action="{!! route('travel.claims.dsa.update', [$travelDsaClaim->travel_claim_id, $travelDsaClaim->id]) !!}" method="post" enctype="multipart/form-data" id="claimItineraryForm"
    autocomplete="off">
    <div class="modal-body">
        <div class="row mb-3">
            <div class="col-lg-9 offset-lg-3">
                <small>
                    <em>DSA Rates per day → Breakfast Rs.400 | Lunch Rs.500 | Dinner Rs.600 | Incidental Rs.300</em>
                </small>
            </div>
        </div>

        <div class="mb-2 row">
            <div class="col-lg-3"><label class="form-label required-label">Date</label></div>
            <div class="col-lg-3">
                <input type="text" class="form-control datepicker" name="departure_date"
                    value="{{ old('departure_date', $travelDsaClaim->departure_date?->format('Y-m-d')) }}"
                    onfocus="this.blur()" placeholder="yyyy-mm-dd" />
            </div>
        </div>

        <div class="mb-2 row">
            <div class="col-lg-3"><label class="form-label">Breakfast</label></div>
            <div class="col-lg-3">
                <input type="number" class="form-control meal-input" name="breakfast" id="edit_breakfast"
                    value="{{ old('breakfast', $travelDsaClaim->breakfast) }}" min="0">
            </div>

            <div class="col-lg-3"><label class="form-label">Lunch</label></div>
            <div class="col-lg-3">
                <input type="number" class="form-control meal-input" name="lunch" id="edit_lunch"
                    value="{{ old('lunch', $travelDsaClaim->lunch) }}" min="0">
            </div>
        </div>

        <div class="mb-2 row">
            <div class="col-lg-3"><label class="form-label">Dinner</label></div>
            <div class="col-lg-3">
                <input type="number" class="form-control meal-input" name="dinner" id="edit_dinner"
                    value="{{ old('dinner', $travelDsaClaim->dinner) }}" min="0">
            </div>

            <div class="col-lg-3"><label class="form-label">Incidental Cost</label></div>
            <div class="col-lg-3">
                <input type="number" class="form-control" name="incident_cost" id="edit_incident_cost"
                    value="{{ old('incident_cost', $travelDsaClaim->incident_cost) }}" min="0">
            </div>
        </div>

        <div class="mb-2 row">
            <div class="col-lg-3"><label class="form-label">Daily Allowance</label></div>
            <div class="col-lg-3">
                <input type="number" class="form-control" name="total_dsa" id="edit_total_dsa" readonly
                    value="{{ old('total_dsa', $travelDsaClaim->total_dsa) }}">
            </div>
        </div>

        <div class="mb-2 row">
            <div class="col-lg-3"><label class="form-label">Lodging expenses</label></div>
            <div class="col-lg-3">
                <input type="number" class="form-control" name="lodging_expense" id="edit_lodging_expense"
                    value="{{ old('lodging_expense', $travelDsaClaim->lodging_expense) }}" min="0">
            </div>

            <div class="col-lg-3"><label class="form-label">Other expenses</label></div>
            <div class="col-lg-3">
                <input type="number" class="form-control" name="other_expense" id="edit_other_expense"
                    value="{{ old('other_expense', $travelDsaClaim->other_expense) }}" min="0">
            </div>
        </div>

        <div class="mb-2 row">
            <div class="col-lg-3"><label class="form-label">Total Amount</label></div>
            <div class="col-lg-3">
                <input type="number" class="form-control" name="total_amount" id="edit_total_amount" readonly
                    value="{{ old('total_amount', $travelDsaClaim->total_amount) }}">
            </div>
        </div>

        <div class="row mb-2">
            <div class="col-lg-3"><label class="form-label">Remarks</label></div>
            <div class="col-lg-9">
                <textarea name="remarks" class="form-control">{{ old('remarks', $travelDsaClaim->remarks) }}</textarea>
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
                @if (file_exists('storage/' . $travelDsaClaim->attachment) && $travelDsaClaim->attachment != '')
                    <div class="media">
                        <a href="{!! asset('storage/' . $travelDsaClaim->attachment) !!}" target="_blank" class="fs-5" title="View Attachment">
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
