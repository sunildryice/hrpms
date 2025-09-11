<div class="modal-header bg-primary text-white">
    <h5 class="modal-title mb-0 fs-6" id="openModalLabel">Edit Local Level</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form action="{!! route('master.local.levels.update',$localLevel->id) !!}" method="post"
      enctype="multipart/form-data" id="districtForm" autocomplete="off">
    <div class="modal-body">
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">District </label>
                </div>
            </div>
            <div class="col-lg-9">
                <select class="form-control" name="district_id">
                    <option value="">Select District</option>
                    @foreach($districts as $district)
                        <option value="{{ $district->id }}" @if($district->id == $localLevel->province_id) selected @endif>{{ $district->getDistrictName() }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">Local Level Name </label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="text" class="form-control" name="local_level_name" value="{!! $localLevel->local_level_name !!}" placeholder="Local Level Name" />
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Update</button>
    </div>
    {!! csrf_field() !!}
    {!! method_field('PUT') !!}
</form>
