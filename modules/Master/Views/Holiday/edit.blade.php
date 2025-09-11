<div class="modal-header bg-primary text-white">
    <h5 class="modal-title mb-0 fs-6" id="openModalLabel">Edit Holiday</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form action="{!! route('master.holidays.update',$holiday->id) !!}" method="post"
      enctype="multipart/form-data" id="holidayForm" autocomplete="off">
    <div class="modal-body">
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">Holiday Name </label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="text" class="form-control" name="title" value="{!! $holiday->title !!}" placeholder="Holiday Name">
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">Holiday Date </label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="text" readonly class="form-control" name="holiday_date" value="{!! $holiday->holiday_date->format('Y-m-d') !!}" placeholder="Holiday Date">
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="m-0">Description </label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="text" class="form-control" name="description" value="{!! $holiday->description !!}" placeholder="Description">
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="m-0">Offices </label>
                </div>
            </div>
            @php $selectedOfficeIds = $holiday->offices->pluck('id')->toArray() @endphp
            <div class="col-lg-9">
                <select class="select2 form-control" name="office_ids[]" id="office_ids" multiple>
                    @foreach($offices as $office)
                        <option value="{{ $office->id }}" @if(in_array($office->id, $selectedOfficeIds)) selected @endif>{{ $office->getOfficeName() }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3"></div>
            <div class="col">
                <input type="checkbox" id="select_all" name="select_all"> Applicable to all offices
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3"></div>
            <div class="col">
                <input type="checkbox" id="only_female" name="only_female" {{$holiday->only_female == '1' ? 'checked' : ''}}> Applicable only for female employees
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Update</button>
    </div>
    {!! csrf_field() !!}
    {!! method_field('PUT') !!}
</form>
