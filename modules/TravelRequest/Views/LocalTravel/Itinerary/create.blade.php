<div class="modal-header bg-primary text-white">
    <h5 class="modal-title mb-0 fs-6" id="openModalLabel">Add New Detail</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form action="{!! route('local.travel.reimbursements.itineraries.store', $localTravel->id) !!}" method="post" enctype="multipart/form-data" id="localTravelItineraryForm"
    autocomplete="off">
    <div class="modal-body">
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">Travel Date</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="text" class="form-control" name="travel_date" onfocus="this.blur()"
                    placeholder="yyyy-mm-dd">
            </div>
        </div>

        <div class="row mb-2">
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

        <div class="row mb-2">
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

        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="travel_mode" class="form-label required-label">Mode</label>
                </div>
            </div>
            <div class="col-lg-9">
                <select name="travel_mode" id="travel_mode"
                    class="form-control select2 @error('travel_mode') is-invalid @enderror" required>
                    <option value="" selected>Select Travel Mode</option>
                    <option value="Taxi">Taxi</option>
                    <option value="Bike">Bike</option>
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
                    class="form-control" value="{{ old('number_of_travelers') }}" placeholder="e.g. 0, 1, 2, 3...">
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
                <input type="text" class="form-control" name="pickup_location" value=""
                    placeholder="Pickup Location">
            </div>
        </div>

        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">Fare</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="number" class="form-control" name="total_fare" value="" placeholder="Fare">
            </div>
        </div>


        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="m-0">Reason</label>
                </div>
            </div>
            <div class="col-lg-9">
                <textarea rows="5" class="form-control" name="remarks"></textarea>
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
    <script>
        window.currentTravelersData = [];
    </script>
</form>
