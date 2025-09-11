<div class="modal-header bg-primary text-white">
    <h5 class="modal-title mb-0 fs-6" id="openModalLabel">Edit Advance Request Detail</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form action="{!! route('advance.requests.details.update', [$advanceRequestDetails->advance_request_id, $advanceRequestDetails->id]) !!}" method="post"
      enctype="multipart/form-data" id="advanceRequestDetailForm" autocomplete="off">
    <div class="modal-body">

        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">Activity Code</label>
                </div>
            </div>
            <div class="col-lg-9">
                <select class="form-control select2" data-width="100%" name="activity_code_id">
                    <option value="">Select Activity Code</option>
                    @foreach($activityCodes as $activityCode)
                        <option value="{!! $activityCode->id !!}" @if($advanceRequestDetails->activity_code_id == $activityCode->id) selected @endif>{{ $activityCode->getActivityCodeWithDescription() }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">Account Code</label>
                </div>
            </div>
            <div class="col-lg-9">
                <select class="form-control select2" data-width="100%" name="account_code_id">
                    <option value="">Select Account Code</option>
                    @foreach($accountCodes as $accountCode)
                        <option value="{!! $accountCode->id !!}" @if($advanceRequestDetails->account_code_id == $accountCode->id) selected @endif>{{ $accountCode->getAccountCodeWithDescription() }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="m-0">Donor Code</label>
                </div>
            </div>
            <div class="col-lg-9">
                <select class="form-control select2" data-width="100%" name="donor_code_id">
                    <option value="">Select Donor Code</option>
                    @foreach($donorCodes as $donorCode)
                        <option value="{!! $donorCode->id !!}" @if($advanceRequestDetails->donor_code_id == $donorCode->id) selected @endif>{{ $donorCode->getDonorCodeWithDescription() }}</option>
                    @endforeach
                </select>
            </div>
        </div>

          <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">Description</label>
                </div>
            </div>
            <div class="col-lg-9">
                <textarea rows="5" class="form-control" name="description">{{ $advanceRequestDetails->description }}</textarea>
            </div>
        </div>

        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">Amount</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="number" class="form-control"  name="amount" value="{{ $advanceRequestDetails->amount }}" placeholder="Amount">
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="m-0">Attachment</label>
                </div>
            </div>
            @php
                $col_class = $advanceRequestDetails->attachment != NULL? 'col-lg-7': 'col-lg-9';
            @endphp
            <div class="{{$col_class}}">
                <input type="file" class="form-control @if($errors->has('attachment')) is-invalid @endif" id="validationdocument"
                    placeholder="" name="attachment">
                <small>Supported file types jpeg/jpg/png/pdf and file size of upto 2MB.</small>

                @if($errors->has('attachment'))
                    <div class="fv-plugins-message-container invalid-feedback">
                        <div data-field="attachment">{!! $errors->first('attachment') !!}</div>
                    </div>
                @endif
            </div>
            @if(file_exists('storage/'.$advanceRequestDetails->attachment) && $advanceRequestDetails->attachment != '')
            <div class="col-lg-2">
                <div class="media">
                    <a href="{!! asset('storage/'.$advanceRequestDetails->attachment) !!}" target="_blank" class="fs-5"
                    title="View Attachment"><i class="bi bi-file-earmark-medical"></i></a>
                    <a href = "javascript:;" data-href="{{route('advance.requests.details.attachment.delete', [$advanceRequestDetails->id])}}"
                    id="delete-attachment" class="fs-5" title="Delete Attachment"><i class="bi-trash text-danger"></i></a>
                </div>
            </div>
            @endif
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Update</button>
    </div>
    {{-- {!! method_field('PUT') !!} --}}
    {!! csrf_field() !!}
</form>
