<div class="modal-header bg-primary text-white">
    <h5 class="modal-title mb-0 fs-6" id="openModalLabel">Edit Itinerary</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form action="{!! route('ta.requests.itinerary.update', [$itinerary->travel_authorization_id, $itinerary->id]) !!}" method="post" enctype="multipart/form-data" id="itineraryForm" autocomplete="off">
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
                            <input type="text" class="form-control" name="travel_date" readonly autofocus="" value="{{$itinerary->travel_date->format('Y-m-d')}}">
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
                            <input type="text" class="form-control" name="place_from" autofocus="" value="{{$itinerary->place_from}}">
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
                            <input type="text" class="form-control" name="place_to" autofocus="" value="{{$itinerary->place_to}}">
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
                    <textarea name="activities" rows="10" class="form-control">{{$itinerary->activities}}</textarea>
                </div>
            </div>

        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Update</button>
    </div>
    {!! csrf_field() !!}
    {!! method_field('PUT') !!}
</form>
