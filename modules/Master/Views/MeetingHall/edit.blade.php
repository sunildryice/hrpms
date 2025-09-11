<div class="modal-header bg-primary text-white">
    <h5 class="modal-title mb-0 fs-6" id="openModalLabel">Edit Meeting Hall</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form action="{!! route('master.meeting.hall.update',$meetingHall->id) !!}" method="post"
      enctype="multipart/form-data" id="meetingHallEditForm" autocomplete="off">
    <div class="modal-body">
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">Office</label>
                </div>
            </div>
            <div class="col-lg-9">
                <select class="form-control select2" name="office_id">
                    @foreach($offices as $office)
                        <option value="{{ $office->id }}" @if($office->id == $meetingHall->office_id) selected @endif>{{ $office->office_name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">Meeting Hall Name </label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="text" class="form-control" name="title" value="{!! $meetingHall->title !!}" placeholder="Account Code" />
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Update</button>
    </div>
    {!! csrf_field() !!}
    {!! method_field('PUT') !!}
</form>
