<div class="modal-header bg-primary text-white">
    <h5 class="modal-title mb-0 fs-6" id="openModalLabel">Add Monthly Work Log</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form action="{!! route('monthly.work.logs.store') !!}" method="post"
      enctype="multipart/form-data" id="workLogAddForm" autocomplete="off">
    <div class="modal-body">
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">{{ __('label.year') }}</label>
                </div>
            </div>
            <div class="col-lg-9">
                <select name="year" class="select2 form-control" data-width="100%">
                    <option value="">Select a Year</option>
                    @foreach($years as $year)
                        <option value="{{ $year }}"
                            {{ $year == date('Y')? "selected":"" }}>
                            {{ $year }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">{{ __('label.month') }}</label>
                </div>
            </div>
            <div class="col-lg-9">
                <select name="month" class="select2 form-control" data-width="100%">
                    <option value="">Select a Month</option>
                    @foreach($months as $key =>$value)
                        <option value="{{ $key }}"
                            {{ $key == date('m')? "selected":"" }}>
                            {{ $value }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Save</button>
    </div>
    {!! csrf_field() !!}
</form>
<script>
$(".select2").select2({
    dropdownParent: $('.modal'),
    width: '100%',
    dropdownAutoWidth: true
});
</script>
