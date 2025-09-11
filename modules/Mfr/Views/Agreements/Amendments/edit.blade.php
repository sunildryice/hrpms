<div class="modal-header bg-primary text-white">
    <h5 class="modal-title mb-0 fs-6" id="openModalLabel">Edit Amendment</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form action="{{ route('mfr.agreement.amendment.update', $amendment->id) }}" method="POST"
      enctype="multipart/form-data" id="amendmentForm" autocomplete="off">
    @csrf
    @method('PUT')
    <div class="modal-body">
          <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="effective_date" class="form-label required-label">Effective Date</label>
                </div>
            </div>
            <div class="col-lg-9">
               <input type="text" class="form-control" readonly name="effective_date" id="effective_date" value="{{$amendment->effective_date->format('Y-m-d')}}" placeholder="Effective date">
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="extension_to_date" class="m-0">Extension To Date</label>
                </div>
            </div>
            <div class="col-lg-9">
               <input type="text" class="form-control" readonly name="extension_to_date" id="extension_to_date" value="{{$amendment->extension_to_date?->format('Y-m-d')}}" placeholder="Extension to date">
            </div>
        </div>
          <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="total_estimate_cost" class="form-label required-label">Approved Budget</label>
                </div>
            </div>
            <div class="col-lg-9">
               <input type="text" class="form-control" name="approved_budget" id="approved_budget" value="{{$amendment->approved_budget}}">
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="attachment" class="form-label required-label">Attachment</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="file" name="attachment" id="attachment">
                @if ($amendment->attachment)
                    <a class="btn btn-sm btn-outline-primary" href="{{asset('storage/' . $amendment->attachment)}}" target="_blank" rel="tooltip" title="View attachment">
                        <i class="bi bi-file-earmark-text"></i>
                    </a>
                @endif
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Save</button>
    </div>
</form>
