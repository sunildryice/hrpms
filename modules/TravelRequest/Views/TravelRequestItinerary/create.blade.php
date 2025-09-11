<div class="text-white modal-header bg-primary">
    <h5 class="mb-0 modal-title fs-6" id="openModalLabel">Add Travel Request Itinerary</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form action="{!! route('travel.requests.itinerary.store', $travelRequest->id) !!}" method="post" enctype="multipart/form-data" id="itineraryForm" autocomplete="off">
    {{--    @dump($errors) --}}
    <div class="modal-body">
        <div class="card-body">
            <div class="mb-2 row">
                <div class="col-lg-3">
                    <div class="d-flex align-items-start h-100">
                        <label for="" class="form-label required-label">From Date</label>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="row">
                        <div class="input-group has-validation">
                            <input type="text" class="form-control" name="departure_date" autofocus="">
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
                            <input type="text" class="form-control" name="arrival_date" autofocus="">
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
            <div class="mb-2 row other-travel-mode" style="display: none;">
                <div class="col-lg-3">
                    <div class="d-flex align-items-start h-100">
                        <label for="travel_mode" class="m-0">Other Travel Modes</label>
                    </div>
                </div>
                <div class="col-lg-9">
                    <input type="text" class="form-control" name="travel_mode" autofocus="">
                </div>
            </div>
            <div class="mb-2 row">
                <div class="col-lg-3">
                    <div class="d-flex align-items-start h-100">
                        <label for="validationProject" class="form-label required-label">Activity Code
                        </label>
                    </div>
                </div>
                <div class="col-lg-9">
                    <select name="activity_code_id" class="select2 form-control" data-width="100%">
                        <option value="">Select Activity Code</option>
                        @foreach ($activityCodes as $activity)
                            <option value="{{ $activity->id }}"
                                {{ $activity->id == old('activity_code_id') ? 'selected' : '' }}>
                                {{ $activity->getActivityCodeWithDescription() }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="mb-2 row">
                <div class="col-lg-3">
                    <div class="d-flex align-items-start h-100">
                        <label for="validationProject" class="form-label required-label">Account Code
                        </label>
                    </div>
                </div>
                <div class="col-lg-9">
                    <select name="account_code_id" class="select2 form-control" data-width="100%">
                        <option value="">Select Account Code</option>
                    </select>
                </div>
            </div>
            <div class="mb-2 row">
                <div class="col-lg-3">
                    <div class="d-flex align-items-start h-100">
                        <label for="validationProject" class="m-0">Donor Code
                        </label>
                    </div>
                </div>
                <div class="col-lg-9">
                    <select name="donor_code_id" class="select2 form-control" data-width="100%">
                        <option value="">Select Donor Code</option>
                        @foreach ($donorCodes as $donor)
                            <option value="{{ $donor->id }}"
                                {{ $donor->id == old('donor_code_id') ? 'selected' : '' }}>
                                {{ $donor->getDonorCodeWithDescription() }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="mb-2 row">
                <div class="col-lg-3">
                    <div class="d-flex align-items-start h-100">
                        <label for=""
                            class="form-label required-label">{{ __('label.dsa-category') }}</label>
                    </div>
                </div>
                <div class="col-lg-9">
                    <select name="dsa_category_id" class="select2 form-control" data-width="100%">
                        <option value="">Select {{ __('label.dsa-category') }}</option>
                        @foreach ($dsaCategories as $dsaCategory)
                            <option value="{{ $dsaCategory->id }}">
                                {{ $dsaCategory->title }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="mb-2 row">
                <div class="col-lg-3">
                    <div class="d-flex align-items-start h-100">
                        <label for="" class="form-label required-label">{{ __('label.dsa-rate') }}</label>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="row">
                        <div class="input-group has-validation">
                            <input type="number" class="form-control" name="dsa_unit_price" autofocus="">
                        </div>
                    </div>
                </div>
            </div>

            <div class="mb-2 row">
                <div class="col-lg-3">
                    <div class="d-flex align-items-start h-100">
                        <label for="" class="form-label required-label">Charging Office</label>
                    </div>
                </div>
                <div class="col-lg-9">
                    <select class="form-control select2" data-width="100%" name="charging_office_id">
                        <option value="">Select Charging Office</option>
                        @foreach ($offices as $office)
                            <option value="{!! $office->id !!}">{{ $office->getOfficeName() }}</option>
                        @endforeach
                    </select>
                </div>
            </div>


            <div class="mb-2 row">
                <div class="col-lg-3">
                    <div class="d-flex align-items-start h-100">
                        <label for="" class="form-label required-label">{{ __('label.description') }}
                        </label>
                    </div>
                </div>
                <div class="col-lg-9">
                    <textarea name="description" class="form-control"></textarea>
                </div>
            </div>

        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Save</button>
    </div>
    {!! csrf_field() !!}
</form>
