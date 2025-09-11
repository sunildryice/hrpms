<div class="modal-header bg-primary text-white">
    <h5 class="modal-title mb-0 fs-6" id="openModalLabel">Add Contract</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form action="{!! route('contracts.store') !!}" method="post" enctype="multipart/form-data" id="contractForm"
    autocomplete="off">
    <div class="modal-body">
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">Organization / Individual</label>
                </div>
            </div>
            <div class="col-lg-9">
                <select class="form-control select2" data-width="100%" name="supplier_id">
                    <option value="">Select Organization / Individual</option>
                    @foreach ($suppliers as $supplier)
                        <option value="{!! $supplier->id !!}">{{ $supplier->getSupplierNameandVAT() }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">{{ __('label.contract-number') }}</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="text" class="form-control" name="contract_number" value=""
                    placeholder="{{ __('label.contract-number') }}">
            </div>
        </div>

        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="m-0">Description</label>
                </div>
            </div>
            <div class="col-lg-9">
                <textarea rows="5" class="form-control" name="description"></textarea>
            </div>
        </div>

        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="m-0">{{ __('label.contact-name') }}</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="text" class="form-control" name="contact_name" value=""
                    placeholder="{{ __('label.contact-name') }}">
            </div>
        </div>

        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="m-0">Contact Number</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="text" class="form-control" name="contact_number" value="" placeholder="Contact Number">
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="m-0">Address</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="text" class="form-control" name="address" value="" placeholder="Address">
            </div>
        </div>

        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">Contract Date</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="text" class="form-control datepicker" readonly name="contract_date" value=""
                    placeholder="Contact Date">
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">Effective Date</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="text" class="form-control" readonly name="effective_date" value=""
                    placeholder="Effective Date">
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">Expiry Date</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="text" class="form-control" readonly name="expiry_date" value=""
                    placeholder="Expiry Date">
            </div>
        </div>

        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="m-0">Reminder Days</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="number" class="form-control" name="reminder_days" value="" placeholder="Reminder Days">
            </div>
        </div>

        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="m-0">Termination Days</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="number" class="form-control" name="termination_days" value=""
                    placeholder="Termination Days">
            </div>
        </div>

        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">Contract Amount</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="number" class="form-control" name="contract_amount" value=""
                    placeholder="Contract Amount">
            </div>
        </div>

        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="m-0">Focal Person</label>
                </div>
            </div>
            <div class="col-lg-9">
                    <select class="form-control select2" data-width="100%" name="focal_person_id">
                        <option value="">Select Focal Person</option>
                        @foreach ($employees as $employee)
                            <option value="{!! $employee->id !!}">{{ $employee->getFullName() }}</option>
                        @endforeach
                    </select>
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
                <input type="file" class="form-control" name="attachment" />
                <small>Supported file types jpeg/jpg/png/pdf and file size of upto 2MB.</small>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Save</button>
    </div>
    {!! csrf_field() !!}
</form>
