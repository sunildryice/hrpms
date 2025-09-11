<div class="modal-header bg-primary text-white">
    <h5 class="modal-title mb-0 fs-6" id="openModalLabel">Add Health Facility</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form action="{!! route('master.health.facilities.update', $healthFacility->id) !!}" method="post"
      enctype="multipart/form-data" id="healthFacilityForm" autocomplete="off">
      @csrf
      @method('PUT')
    <div class="modal-body">
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="title" class="form-label required-label">Health Facility Name </label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="text" class="form-control" name="title" id="title" value="{{ $healthFacility->title }}" placeholder="Health Facility Name">
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="validationProject" class="form-label required-label">Province
                    </label>
                </div>
            </div>
            <div class="col-lg-9">
                @php
                    $selected = old('province_id') ?? $healthFacility->province_id;
                @endphp
                <select name="province_id" class="select2 form-control"
                        data-width="100%">
                    <option value="">Select Province</option>
                    @foreach ($provinces as $province)
                        <option value="{{$province->id}}"  @if($selected == $province->id) selected @endif>{{ $province->province_name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="validationProject" class="form-label required-label">District
                    </label>
                </div>
            </div>
            <div class="col-lg-9">
                @php
                    $selected = old('district_id') ?? $healthFacility->district_id;
                @endphp
                <select name="district_id" class="select2 form-control"
                        data-width="100%">
                    <option value="">Select District</option>
                    @foreach ($districts as $district)
                        <option value="{{$district->id}}" @if($selected == $district->id) selected @endif> {{$district->district_name}}  </option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="validationProject" class="form-label required-label">Palika
                    </label>
                </div>
            </div>
            <div class="col-lg-9">
                @php
                    $selected = old('local_level_id') ?? $healthFacility->local_level_id;
                @endphp
                <select name="local_level_id" class="select2 form-control"
                        data-width="100%">
                    <option value="">Select Palika</option>
                    @foreach ($localLevels as $localLevel)
                        <option value="{{$localLevel->id}}"  @if($selected == $localLevel->id) selected @endif>{{ $localLevel->local_level_name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="ward" class="form-label required-label">Ward</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="number" class="form-control" name="ward" id="ward" value="">
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Save</button>
    </div>
</form>
