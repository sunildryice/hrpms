<div class="modal-header bg-primary text-white">
    <h5 class="modal-title mb-0 fs-6" id="openModalLabel">Edit Vehicle</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form action="{!! route('master.vehicles.update',$vehicle->id) !!}" method="post"
      enctype="multipart/form-data" id="vehicleForm" autocomplete="off">
    <div class="modal-body">
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">Office </label>
                </div>
            </div>
            <div class="col-lg-9">
                <select class="select2 form-control" name="office_id">
                    <option value="">Select Office</option>
                    @foreach($offices as $office)
                        <option value="{{ $office->id }}" @if($office->id == $vehicle->office_id) selected @endif>{{ $office->getOfficeName() }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">Vehicle Type </label>
                </div>
            </div>
            <div class="col-lg-9">
                <select class="select2 form-control" name="vehicle_type_id">
                    <option value="">Select Vehicle Type</option>
                    @foreach($vehicleTypes as $vehicleType)
                        <option value="{{ $vehicleType->id }}" @if($vehicleType->id == $vehicle->vehicle_type_id) selected @endif>{{ $vehicleType->getVehicleType() }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">Vehicle Number </label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="text" class="form-control" name="vehicle_number" value="{!! $vehicle->vehicle_number !!}" placeholder="Vehicle Number" />
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">Capacity </label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="number" class="form-control" name="passenger_capacity" value="{!! $vehicle->passenger_capacity !!}" placeholder="Passenger Capacity" />
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Update</button>
    </div>
    {!! csrf_field() !!}
    {!! method_field('PUT') !!}
</form>
