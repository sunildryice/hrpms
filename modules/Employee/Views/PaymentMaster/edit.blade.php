<div class="modal-header bg-primary text-white">
    <h5 class="modal-title mb-0 fs-6" id="openModalLabel">Edit Payment Master</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form action="{!! route('employees.payments.masters.update',[$paymentMaster->employee_id, $paymentMaster->id]) !!}" method="post"
      enctype="multipart/form-data" id="paymentMasterForm" autocomplete="off">
    <div class="modal-body">
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">Start Date</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="text" readonly class="form-control" name="start_date" placeholder="Start Date"
                       value="{{ $paymentMaster->start_date ? $paymentMaster->start_date->format('Y-m-d'): '' }}">
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">End Date</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="text"  readonly class="form-control" name="end_date" placeholder="End Date"
                       value="{{ $paymentMaster->end_date ? $paymentMaster->end_date->format('Y-m-d'): '' }}" >
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Update</button>
    </div>
    {!! csrf_field() !!}
    {!! method_field('PUT') !!}
</form>
