<div class="modal-header bg-primary text-white">
    <h5 class="modal-title mb-0 fs-6" id="openModalLabel">Edit Monthly Work Log</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form action="{!! route('monthly.work.logs.update',$workPlan->id) !!}" method="post"
      enctype="multipart/form-data" id="workLogEditForm" autocomplete="off">
    <div class="modal-body">
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">{{ __('label.year') }}</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="text"
                    name="year"
                    value="{{$workPlan->year}}"
                    class="form-control" readonly>
            </div>
        </div>

        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">{{ __('label.month') }}</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="text"
                    name=""
                    value="{{$workPlan->getMonth()}}"
                    class="form-control" readonly>
                {{-- for validation purpose --}}
                <input type="text"
                    name="month"
                    value="{{$workPlan->month}}"
                    class="form-control" hidden>

            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Save</button>
    </div>
    {!! csrf_field() !!}
    {!! method_field('PUT') !!}
</form>
