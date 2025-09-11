<div class="modal-header bg-primary text-white">
    <h5 class="modal-title mb-0 fs-6" id="openModalLabel">Edit Partner Organization</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form action="{!! route('master.partner.org.update',$partner->id) !!}" method="post"
      enctype="multipart/form-data" id="projectCodeEditForm" autocomplete="off">
    <div class="modal-body">
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">Organization Name</label>
                </div>
            </div>
            <div class="col-lg-9">
              <input type="text" class="form-control" name="name" value="{{$partner->name}}" >
            </div>
        </div>

        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="m-0">District</label>
                </div>
            </div>
            <div class="col-lg-9">
                <select class="form-control select2" data-width="100%" name="district_id">
                    <option value="">Select district</option>
                    @foreach($districts as $district)
                        <option value="{!! $district->id !!}" @if($partner->district_id == $district->id) selected @endif>{{ $district->getDistrictName() }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Update</button>
    </div>
    {!! csrf_field() !!}
    {!! method_field('PUT') !!}
</form>
