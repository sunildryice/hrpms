<div class="modal-header bg-primary text-white">
    <h5 class="modal-title mb-0 fs-6" id="openModalLabel">Update Amendment Contract</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<form action="{!! route('contracts.amendments.update', [$contract->id, $contractAmend->id]) !!}" method="post"
      enctype="multipart/form-data" id="contractAmendForm" autocomplete="off">
    <div class="modal-body">
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="m-0">New Expiry Date</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="hidden" class="form-control" name="effective_date"
                       value="{{ $contract->effective_date->format('Y-m-d') }}"/>
                @php $expiryDate = $contractAmend->expiry_date @endphp
                <input type="text" class="form-control" readonly name="expiry_date"
                       value="{{ $expiryDate->format('Y-m-d') }}" placeholder="Expiry Date">
            </div>
        </div>

        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">New Contract Amount</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="number" class="form-control" name="contract_amount" placeholder="Contract Amount"
                       value="{{ $contractAmend->contract_amount }}"/>
            </div>
        </div>

        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="m-0">Remarks</label>
                </div>
            </div>
            <div class="col-lg-9">
                <textarea rows="5" class="form-control" name="remarks">{!! $contractAmend->remarks !!}</textarea>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="validationAttachment" class="m-0">Attachment</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="file" class="form-control" name="attachment"/>
                <span>
                @if(file_exists('storage/'.$contractAmend->attachment) && $contractAmend->attachment != '')
                    <a href="{!! asset('storage/'.$contractAmend->attachment) !!}" target="_blank" class="fs-5"
                       title="View Attachment">
                        <i class="bi bi-file-earmark-medical"></i>
                    </a>
                @else
                    File does not exists.
                @endif
                </span><br />
                <small>Supported file types jpeg/jpg/png/pdf.</small>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Update</button>
    </div>
    {!! csrf_field() !!}
    {!! method_field('PUT') !!}
</form>
