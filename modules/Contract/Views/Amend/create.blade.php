<div class="modal-header bg-primary text-white">
    <h5 class="modal-title mb-0 fs-6" id="openModalLabel">Amend Contract</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<form action="{!! route('contracts.amendments.store', $contract->id) !!}" method="post"
      enctype="multipart/form-data" id="contractAmendForm" autocomplete="off">
    <div class="modal-body">
        <table class="display table table-bordered table-condensed">
            <tr>
                <td class="gray-bg">Contract Attachment</td>
                <td>
                    @if(file_exists('storage/'.$contract->attachment) && $contract->attachment != '')
                        <a href="{!! asset('storage/'.$contract->attachment) !!}" target="_blank" class="fs-5"
                           title="View Attachment">
                            <i class="bi bi-file-earmark-medical"></i>
                        </a>
                    @else
                        File does not exists.
                    @endif
                </td>
            </tr>
            @foreach($contract->amendments as $amendment)
                <tr>
                    <td class="gray-bg">Contract Attachment (Amended)</td>
                    <td>
                        @if(file_exists('storage/'.$amendment->attachment) && $amendment->attachment != '')
                            <a href="{!! asset('storage/'.$amendment->attachment) !!}" target="_blank" class="fs-5"
                               title="View Attachment">
                                <i class="bi bi-file-earmark-medical"></i>
                            </a>
                        @else
                            File does not exists.
                        @endif
                    </td>
                </tr>
            @endforeach
        </table>

        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="m-0">New Expiry Date</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="hidden" class="form-control" name="effective_date"
                       value="{{ $contract->effective_date->format('Y-m-d') }}"/>
                @php $expiryDate = $contract->latestAmendment ? $contract->latestAmendment->expiry_date : $contract->expiry_date @endphp
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
                       value="{{ $contract->latestAmendment ? $contract->latestAmendment->contract_amount : $contract->contract_amount }}"/>
            </div>
        </div>

        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="m-0">Remarks</label>
                </div>
            </div>
            <div class="col-lg-9">
                <textarea rows="5" class="form-control" name="remarks"></textarea>
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
                <small>Supported file types jpeg/jpg/png/pdf and file size of upto 2MB.</small>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Save</button>
    </div>
    {!! csrf_field() !!}
</form>
