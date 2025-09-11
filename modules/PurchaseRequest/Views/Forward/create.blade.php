<div class="modal-header bg-primary text-white">
    <h5 class="modal-title mb-0 fs-6" id="openModalLabel">Submit Purchase Request</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form action="{!! route('purchase.requests.forward.store', $purchaseRequest->id) !!}" method="post" enctype="multipart/form-data" id="forwardForm" autocomplete="off">
    <div class="modal-body">
        {{-- @if ($purchaseRequest->verificationRequired()) --}}
            <div class="row mb-2">
                <div class="col-lg-3">
                    <div class="d-flex align-items-start h-100">
                        <label for="" class="form-label required-label">Budget Holder</label>
                    </div>
                </div>
                <div class="col-lg-9">
                    <select class="form-control select2" data-width="100%" name="budget_verifier_id">
                        <option value="">Select Verifier</option>
                        @foreach ($verifiers as $reviewer)
                            <option value="{!! $reviewer->id !!}"
                                {{ $purchaseRequest->verifier_id == $reviewer->id ? 'selected' : '' }}>
                                {{ $reviewer->getFullName() }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            {{-- <div class="row mb-2">
                <div class="col-lg-3">
                    <div class="d-flex align-items-start h-100">
                        <label for="" class="form-label required-label">Finance Reviewer </label>
                    </div>
                </div>
                <div class="col-lg-9">
                    <select class="form-control select2" data-width="100%" name="reviewer_id">
                        <option value="">Select Reviewer</option>
                        @foreach ($reviewers as $reviewer)
                            <option value="{!! $reviewer->id !!}"
                                {{ $purchaseRequest->reviewer_id == $reviewer->id ? 'selected' : '' }}>
                                {{ $reviewer->getFullName() }}</option>
                        @endforeach
                    </select>
                </div>
            </div> --}}

        {{-- @endif --}}
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">Approver </label>
                </div>
            </div>
            <div class="col-lg-9">
                <select class="form-control select2" data-width="100%" name="approver_id">
                    <option value="">Select Approver</option>
                    @foreach ($approvers as $approver)
                        <option value="{!! $approver->id !!}"
                            {{ $purchaseRequest->approver_id == $approver->id ? 'selected' : '' }}>
                            {{ $approver->getFullName() }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Submit</button>
    </div>
    {!! csrf_field() !!}
</form>
