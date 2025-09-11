<div class="modal-header bg-primary text-white">
    <h5 class="modal-title mb-0 fs-6" id="openModalLabel">Edit Activity Code</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form action="{!! route('master.activity.codes.update',$activityCode->id) !!}" method="post"
      enctype="multipart/form-data" id="activityCodeForm" autocomplete="off">
    <div class="modal-body">
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">Activity Code </label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="text" class="form-control" name="title" value="{!! $activityCode->title !!}" placeholder="Activity Code" />
            </div>
        </div>

        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="m-0">Description</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="text" class="form-control" name="description" value="{!! $activityCode->description !!}" placeholder="Description" />
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="m-0">Account Codes</label>
                </div>
            </div>
            @php $selectedAccountCodes = $activityCode->accountCodes->pluck('id')->toArray(); @endphp
            <div class="col-lg-9">
                <select class="form-control select2" name="account_codes[]" multiple="multiple">
                    @foreach($accountCodes as $accountCode)
                        <option value="{{ $accountCode->id }}" @if(in_array($accountCode->id, $selectedAccountCodes)) selected="selected" @endif>{{ $accountCode->title }}</option>
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
