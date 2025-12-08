<div class="modal-header bg-primary text-white">
    <h5 class="modal-title mb-0 fs-6" id="openModalLabel">Edit Expense</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form action="{!! route('travel.claims.dsa.update', [$travelDsaClaim->travel_claim_id, $travelDsaClaim->id]) !!}" method="post" enctype="multipart/form-data" id="claimItineraryForm"
    autocomplete="off">
    <div class="modal-body">
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">From Date</label>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="row">
                    <div class="input-group has-validation">
                        <input type="text" class="form-control datetime-picker" name="departure_date"
                            placeholder="YYYY-MM-DD HH:mm" value="{!! $travelRequestItinerary->departure_date ? $travelRequestItinerary->departure_date->format('Y-m-d H:i') : '' !!}" onfocus="this.blur()">
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
                        <input type="text" class="form-control" name="departure_place" autofocus=""
                            value="{!! $travelRequestItinerary->departure_place !!}">
                    </div>
                </div>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">To Date</label>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="row">
                    <div class="input-group has-validation">
                        <input type="text" class="form-control datetime-picker" name="arrival_date"
                            placeholder="YYYY-MM-DD HH:mm" value="{!! $travelRequestItinerary->arrival_date ? $travelRequestItinerary->arrival_date->format('Y-m-d H:i') : '' !!}" onfocus="this.blur()">
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
                        <input type="text" class="form-control" name="arrival_place" autofocus=""
                            value="{!! $travelRequestItinerary->arrival_place !!}">
                    </div>
                </div>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="m-0">{{ __('label.mode-of-travel') }} </label>
                </div>
            </div>
            @php $selectedTravelModes = $travelRequestItinerary->travelModes->pluck('id')->toArray(); @endphp
            <div class="col-lg-9">
                <select name="travel_modes[]" class="select2 form-control travel-mode" data-width="100%" multiple>
                    @foreach ($travelModes as $travelMode)
                        <option value="{{ $travelMode->id }}" @if (in_array($travelMode->id, $selectedTravelModes)) selected @endif>
                            {{ $travelMode->title }}
                        </option>
                    @endforeach
                </select>
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
