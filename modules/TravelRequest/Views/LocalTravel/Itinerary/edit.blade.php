<div class="modal-header bg-primary text-white">
    <h5 class="modal-title mb-0 fs-6" id="openModalLabel">Edit Detail</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form action="{!! route('local.travel.reimbursements.itineraries.update', [
    $localTravelItinerary->local_travel_reimbursement_id,
    $localTravelItinerary->id,
]) !!}" method="post" enctype="multipart/form-data" id="localTravelItineraryForm"
    autocomplete="off">
    <div class="modal-body">
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">Travel Date</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="text" class="form-control"
                    value="{{ $localTravelItinerary->travel_date->format('Y-m-d') }}" name="travel_date"
                    onfocus="this.blur()" placeholder="yyyy-mm-dd">
            </div>
        </div>

        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="travel_mode" class="form-label required-label">Mode</label>
                </div>
            </div>
            <div class="col-lg-9">
                <select name="travel_mode" id="travel_mode"
                    class="form-control select2 @error('travel_mode') is-invalid @enderror" required>
                    <option value="">Select Travel Mode</option>
                    <option value="Taxi"
                        {{ old('travel_mode', $localTravelItinerary->travel_mode) == 'Taxi' ? 'selected' : '' }}>Taxi
                    </option>
                    <option value="Bike"
                        {{ old('travel_mode', $localTravelItinerary->travel_mode) == 'Bike' ? 'selected' : '' }}>Bike
                    </option>
                </select>

                @error('travel_mode')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label class="form-label">Number of travelers</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="number" min="0" id="number_of_travelers" name="number_of_travelers"
                    class="form-control"
                    value="{{ old('number_of_travelers', $localTravelItinerary->number_of_travelers ?? 0) }}"
                    placeholder="e.g. 0, 1, 2, 3...">
            </div>
        </div>

        <div id="travelers-names-container" class="mb-3">

        </div>

        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label">Pickup Location</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="text" class="form-control" name="pickup_location"
                    value="{{ $localTravelItinerary->pickup_location }}" placeholder="Pickup Location">
            </div>
        </div>

        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="m-0">Reason</label>
                </div>
            </div>
            <div class="col-lg-9">
                <textarea rows="5" class="form-control" name="remarks">{{ $localTravelItinerary->remarks }}</textarea>
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
                @if (file_exists('storage/' . $localTravelItinerary->attachment) && $localTravelItinerary->attachment != '')
                    <div class="media">
                        <a href="{!! asset('storage/' . $localTravelItinerary->attachment) !!}" target="_blank" class="fs-5" title="View Attachment">
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
    {!! method_field('PUT') !!}
    {!! csrf_field() !!}
    @if ($localTravelItinerary->names_of_travelers && count($localTravelItinerary->names_of_travelers) > 0)
        <script>
            window.currentTravelersData = @json($localTravelItinerary->names_of_travelers ?? []);
        </script>
    @endif
</form>
