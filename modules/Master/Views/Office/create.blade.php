<div class="modal-header bg-primary text-white">
    <h5 class="modal-title mb-0 fs-6" id="openModalLabel">Add Office</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form action="{!! route('master.offices.store') !!}" method="post"
      enctype="multipart/form-data" id="officeForm" autocomplete="off">
    <div class="modal-body">
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">Office Name </label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="text" class="form-control" name="office_name" value="" placeholder="Office Name">
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">Office Code </label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="text" class="form-control" name="office_code" value="" placeholder="Office Code">
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="office_type_id" class="form-label required-label">Office Type </label>
                </div>
            </div>
            <div class="col-lg-9">
                <select class="form-control" name="office_type_id" id="office_type_id">
                    <option value="">Select office type</option>
                    @foreach ($officeTypes as $type)
                        <option value="{{$type->id}}">{{$type->getTitle()}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="parent_id" class="m-0">Parent Office </label>
                </div>
            </div>
            <div class="col-lg-9">
                <select class="form-control" name="parent_id" id="parent_id">
                </select>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">Phone Number </label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="text" class="form-control" name="phone_number" value="" placeholder="Phone Number">
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="m-0">Fax Number</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="text" class="form-control" name="fax_number" value="" placeholder="Fax Number">
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="m-0">Email Address </label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="email" class="form-control" name="email_address" value="" placeholder="Email Address">
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">District </label>
                </div>
            </div>
            <div class="col-lg-9">
                <select class="select2 form-control" name="district_id">
                    <option value="">Select District</option>
                    @foreach($districts as $district)
                        <option value="{{ $district->id }}">{{ $district->getDistrictName() }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="m-0">Bank Account Number </label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="text" class="form-control" name="account_number" value="" placeholder="Bank Account Number">
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="m-0">Bank Name </label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="text" class="form-control" name="bank_name" value="" placeholder="Bank Name">
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="m-0">Bank Branch Name</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="text" class="form-control" name="branch_name" value="" placeholder="Bank Branch Name">
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="m-0">Weekend Type</label>
                </div>
            </div>
            <div class="col-lg-9">
                <select class="select2 form-control" name="weekend_type">
                    <option value="1">Saturday</option>
                    <option value="2">Saturday+Sunday</option>
                </select>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Save</button>
    </div>
    {!! csrf_field() !!}
</form>
