<div class="modal-header bg-primary text-white">
    <h5 class="modal-title mb-0 fs-6" id="openModalLabel">Add Amendment</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form action="{{ route('mfr.agreement.amendment.store', $agreementId) }}" method="POST"
      enctype="multipart/form-data" id="amendmentForm" autocomplete="off">
      @csrf

    <div class="modal-body">
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="effective_date" class="form-label required-label">Effective Date</label>
                </div>
            </div>
            <div class="col-lg-9">
               <input type="text" class="form-control" readonly name="effective_date" id="effective_date" value="{{date('Y-m-d')}}" placeholder="Effective date">
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="extension_to_date" class="m-0">Extension To Date</label>
                </div>
            </div>
            <div class="col-lg-9">
               <input type="text" class="form-control" readonly name="extension_to_date" id="extension_to_date" value="" placeholder="Extension to date">
            </div>
        </div>
          <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="approved_budget" class="form-label required-label">Approved Budget</label>
                </div>
            </div>
            <div class="col-lg-9">
               <input type="text" class="form-control" name="approved_budget" id="approved_budget" value="" >
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="attachment" class="form-label">Attachment</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="file" name="attachment" id="attachment">
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Save</button>
    </div>
</form>
