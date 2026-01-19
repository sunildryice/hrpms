<div class="modal-header bg-primary text-white">
    <h5 class="modal-title mb-0 fs-6" id="openModalLabel">Edit Office</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form action="{!! route('master.offices.update', $office->id) !!}" method="post" enctype="multipart/form-data"
    id="officeForm" autocomplete="off">
    <div class="modal-body">
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">Office Name </label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="text" class="form-control" name="office_name" value="{{ $office->office_name }}"
                    placeholder="Office Name">
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">Office Code </label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="text" class="form-control" name="office_code" value="{{ $office->office_code }}"
                    placeholder="Office Code">
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
                        <option value="{{$type->id}}" {{$type->id == $office->office_type_id ? 'selected' : ''}}>
                            {{$type->getTitle()}}</option>
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
                    <option value="">Select parent office</option>
                    @foreach ($parentOffices as $parentOffice)
                        <option value="{{$parentOffice->id}}" {{$office->parent_id == $parentOffice->id ? 'selected' : ''}}>
                            {{$parentOffice->getOfficeName()}}</option>
                    @endforeach
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
                <input type="text" class="form-control" name="phone_number" value="{{ $office->phone_number }}"
                    placeholder="Phone Number">
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="m-0">Fax Number</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="text" class="form-control" name="fax_number" value="{{ $office->fax_number }}"
                    placeholder="Fax Number">
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="m-0">Email Address </label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="email" class="form-control" name="email_address" value="{{ $office->email_address }}"
                    placeholder="Email Address">
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
                        <option value="{{ $district->id }}" @if($district->id == $office->district_id) selected @endif>
                            {{ $district->getDistrictName() }}</option>
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
                <input type="text" class="form-control" name="account_number" value="{{ $office->account_number }}"
                    placeholder="Bank Account Number">
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="m-0">Bank Name </label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="text" class="form-control" name="bank_name" value="{{ $office->bank_name }}"
                    placeholder="Bank Name">
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="m-0">Bank Branch Name</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="text" class="form-control" name="branch_name" value="{{ $office->branch_name }}"
                    placeholder="Bank Branch Name">
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
                    <option value="2" @if($office->weekend_type == 2) selected @endif>Saturday+Sunday</option>
                </select>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="m-0">Active ? </label>
                </div>
            </div>
            <div class="col-lg-9">
                <div class=" form-switch">
                    <input class="form-check-input" type="checkbox" role="switch" id="flexSwitchCheckChecked"
                        name="active" @if($office->activated_at) checked @endif>
                    <label class="form-check-label" for="flexSwitchCheckChecked"></label>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Update</button>
    </div>
    {!! csrf_field() !!}
    {!! method_field('PUT') !!}
</form>