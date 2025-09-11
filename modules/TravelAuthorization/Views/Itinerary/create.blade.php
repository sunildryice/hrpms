<div class="modal-header bg-primary text-white">
    <h5 class="modal-title mb-0 fs-6" id="openModalLabel">Add Travel Request Itinerary</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form action="{!! route('ta.requests.itinerary.store', $travel->id) !!}" method="post" enctype="multipart/form-data" id="itineraryForm" autocomplete="off">
    {{--    @dump($errors) --}}
    <div class="modal-body">
        <div class="card-body">
            <div class="row mb-2">
                <div class="col-lg-3">
                    <div class="d-flex align-items-start h-100">
                        <label for="" class="form-label required-label">Travel Date</label>
                    </div>
                </div>
                <div class="col-lg-9">
                    <div class="row">
                        <div class="input-group has-validation">
                            <input type="text" class="form-control" name="travel_date" readonly autofocus="">
                        </div>
                    </div>
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
                            <input type="text" class="form-control" name="place_from" autofocus="">
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
                            <input type="text" class="form-control" name="place_to" autofocus="">
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mb-2">
                <div class="col-lg-3">
                    <div class="d-flex align-items-start h-100">
                        <label for="" class="form-label required-label">{{ __('label.activities') }} </label>
                    </div>
                </div>
                <div class="col-lg-9">
                    <textarea name="activities" rows="10" class="form-control"></textarea>
                </div>
            </div>

        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Save</button>
    </div>
    {!! csrf_field() !!}
</form>
