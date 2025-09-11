<div class="modal-header bg-primary text-white">
    <h5 class="modal-title mb-0 fs-6" id="openModalLabel">Add Supplier</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form action="{!! route('suppliers.store') !!}" method="post"
      enctype="multipart/form-data" id="supplierAddForm" autocomplete="off">
    <div class="modal-body">
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">Supplier Type </label>
                </div>
            </div>
            <div class="col-lg-9">
                <select class="form-control select2" data-width="100%" name="supplier_type">
                    <option value="1">Organization</option>
                    <option value="2">Individual</option>
                </select>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">Supplier Name </label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="text" class="form-control" name="supplier_name" value="" placeholder="Supplier Name">
            </div>
        </div>

        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="m-0">{{ __('label.vat-pan-no') }}</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="text" class="form-control" name="vat_pan_number" value="" placeholder="{{ __('label.vat-pan-no') }}">
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
                    <label for="" class="m-0">Email Address</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="email" class="form-control" name="email_address" value="" placeholder="Email Address">
            </div>
        </div>

        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="m-0">Contact Name</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="text" class="form-control" name="contact_person_name" value="" placeholder="Contact Person Name">
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="m-0">Contact Email</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="email" class="form-control" name="contact_person_email_address" value="" placeholder="Contact Person Email">
            </div>
        </div>

        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="m-0">Address1</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="text" class="form-control" name="address1" value="" placeholder="Address1">
            </div>
        </div>

        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="m-0">Address2</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="text" class="form-control" name="address2" value="" placeholder="Address2">
            </div>
        </div>

        <div>
            <span class="text-primary fw-bold">Bank Detail</span>
            <hr style="margin-top: 0px;">
            <div class="row mb-2">
                <div class="col-lg-3">
                    <div class="d-flex align-items-start h-100">
                        <label for="account_number" class="m-0">Account Number</label>
                    </div>
                </div>
                <div class="col-lg-9">
                    <input type="text" class="form-control" name="account_number" id="account_number" value="" placeholder="Account number">
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-lg-3">
                    <div class="d-flex align-items-start h-100">
                        <label for="account_name" class="m-0">Account Name</label>
                    </div>
                </div>
                <div class="col-lg-9">
                    <input type="text" class="form-control" name="account_name" id="account_name" value="" placeholder="Account name">
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-lg-3">
                    <div class="d-flex align-items-start h-100">
                        <label for="bank_name" class="m-0">Bank Name</label>
                    </div>
                </div>
                <div class="col-lg-9">
                    <input type="text" class="form-control" name="bank_name" id="bank_name" value="" placeholder="Bank name">
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-lg-3">
                    <div class="d-flex align-items-start h-100">
                        <label for="branch_name" class="m-0">Branch Name</label>
                    </div>
                </div>
                <div class="col-lg-9">
                    <input type="text" class="form-control" name="branch_name" id="branch_name" value="" placeholder="Branch name">
                </div>
            </div>
            {{-- <div class="row mb-2">
                <div class="col-lg-3">
                    <div class="d-flex align-items-start h-100">
                        <label for="swift_code" class="m-0">Swift Code</label>
                    </div>
                </div>
                <div class="col-lg-9">
                    <input type="text" class="form-control" name="swift_code" id="swift_code" value="" placeholder="Swift code">
                </div>
            </div> --}}
            <hr>
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
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Save</button>
    </div>
    {!! csrf_field() !!}
</form>
