<div class="card-header fw-bold">General Information</div>
<form action="{{ route('employees.update', $employee->id) }}" id="employeeEditForm" method="post"
    enctype="multipart/form-data" autocomplete="off">
    <div class="card-body">
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">{{ __('label.staff-code') }}</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="text" class="form-control @if ($errors->has('employee_code')) is-invalid @endif"
                    name="employee_code" value="{{ old('employee_code') ?: $employee->employee_code }}" />
                @if ($errors->has('employee_code'))
                    <div class="fv-plugins-message-container text-danger">
                        <div data-field="employee_code">{!! $errors->first('employee_code') !!}</div>
                    </div>
                @endif
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="validationfullname" class="form-label required-label">Full Name</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="text" class="form-control @if ($errors->has('full_name')) is-invalid @endif"
                    name="full_name" value="{{ old('full_name') ?: $employee->full_name }}" placeholder="Full name" />
                @if ($errors->has('full_name'))
                    <div class="fv-plugins-message-container text-danger">
                        <div data-field="full_name">{!! $errors->first('full_name') !!}</div>
                    </div>
                @endif
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">Official Email </label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="email" class="form-control @if ($errors->has('official_email_address')) is-invalid @endif"
                    name="official_email_address"
                    value="{{ old('official_email_address') ?: $employee->official_email_address }}"
                    placeholder="example@example.com">
                @if ($errors->has('official_email_address'))
                    <div class="fv-plugins-message-container text-danger">
                        <div data-field="official_email_address">{!! $errors->first('official_email_address') !!}</div>
                    </div>
                @endif
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">Personal Email </label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="email" class="form-control @if ($errors->has('personal_email_address')) is-invalid @endif"
                    name="personal_email_address"
                    value="{{ old('personal_email_address') ?: $employee->personal_email_address }}"
                    placeholder="example@example.com">
                @if ($errors->has('personal_email_address'))
                    <div class="fv-plugins-message-container text-danger">
                        <div data-field="personal_email_address">{!! $errors->first('personal_email_address') !!}</div>
                    </div>
                @endif
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="validationphone" class="m-0">Contact Number
                    </label>
                </div>
            </div>
            <div class="col-lg-9">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="input-group">
                            <div class="input-group-append">
                                <span class="input-group-text required-label" id="basic-addon2">Mobile</span>
                            </div>
                            <input type="text" class="form-control" name="mobile_number" placeholder="Mobile Number"
                                value="{{ old('mobile_number') ?: $employee->mobile_number }}"
                                aria-label="Recipient's username" aria-describedby="basic-addon2" />
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="validationdob" class="m-0">Date of Birth
                    </label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="text" class="form-control" name="date_of_birth"
                    value="{{ old('date_of_birth') ?: $employee->date_of_birth }}" onfocus="this.blur()"
                    placeholder="yyyy-mm-dd" />
                <input type="hidden" value="{{ date('Y-m-d') }}" name="today" class="form-control" />
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="validationcitizenship" class="form-label required-label">Citizenship No.
                    </label>
                </div>
            </div>
            <div class="col-lg-9">
                <div class="row">
                    <div class="col-lg-4">
                        <input type="text" class="form-control" name="citizenship_number"
                            value="{{ old('citizenship_number') ?: $employee->citizenship_number }}"
                            placeholder="Citizenship No">
                    </div>
                    <div class="col-lg-8">
                        <input type="file" class="form-control" name="citizenship_attachment" />
                        <small>Supported file types jpeg/jpg/png and file size of upto 2MB.</small>
                        @if ($errors->has('citizenship_attachment'))
                            <div class="fv-plugins-message-container text-danger">
                                <div data-field="citizenship_attachment">{!! $errors->first('citizenship_attachment') !!}</div>
                            </div>
                        @endif
                        @if (file_exists('storage/' . $employee->citizenship_attachment) && $employee->citizenship_attachment != '')
                            <div class="media">
                                <a href="{!! asset('storage/' . $employee->citizenship_attachment) !!}" target="_blank" class="fs-5"
                                    title="View Attachment">
                                    <i class="bi bi-file-earmark-medical"></i>
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3"><label class="form-label">NID Number</label></div>
            <div class="col-lg-9">
                <input type="text" name="nid_number" class="form-control"
                    value="{{ old('nid_number', $employee->nid_number) }}" placeholder="Optional" />
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="validationpan" class="m-0">Pan No.
                    </label>
                </div>
            </div>
            <div class="col-lg-9">
                <div class="row">
                    <div class="col-lg-4">
                        <input type="number" class="form-control" name="pan_number"
                            value="{{ old('pan_number') ?: $employee->pan_number }}" placeholder="Pan No">
                    </div>
                    <div class="col-lg-8">
                        <input type="file" class="form-control" name="pan_attachment" />
                        <small>Supported file types jpeg/jpg/png and file size of upto 2MB.</small>
                        @if ($errors->has('pan_attachment'))
                            <div class="fv-plugins-message-container text-danger">
                                <div data-field="pan_attachment">{!! $errors->first('pan_attachment') !!}</div>
                            </div>
                        @endif
                        @if (file_exists('storage/' . $employee->pan_attachment) && $employee->pan_attachment != '')
                            <div class="media">
                                <a href="{!! asset('storage/' . $employee->pan_attachment) !!}" target="_blank" class="fs-5"
                                    title="View Attachment">
                                    <i class="bi bi-file-earmark-medical"></i>
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-lg-3"><label class="form-label">Passport</label></div>
            <div class="col-lg-9">
                <div class="row g-3">
                    <div class="col-lg-4">
                        <input type="text" name="passport_number" class="form-control"
                            value="{{ old('passport_number', $employee->passport_number) }}" />
                    </div>
                    <div class="col-lg-8">
                        <input type="file" class="form-control" name="passport_attachment" />
                        <small>Supported file types jpeg/jpg/png and file size of upto 2MB.</small>
                        @if ($errors->has('passport_attachment'))
                            <div class="fv-plugins-message-container text-danger">
                                <div data-field="passport_attachment">{!! $errors->first('passport_attachment') !!}</div>
                            </div>
                        @endif
                        @if (file_exists('storage/' . $employee->passport_attachment) && $employee->passport_attachment != '')
                            <div class="media">
                                <a href="{!! asset('storage/' . $employee->passport_attachment) !!}" target="_blank" class="fs-5"
                                    title="View Attachment">
                                    <i class="bi bi-file-earmark-medical"></i>
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-lg-3"><label class="form-label">Driving License</label></div>
            <div class="col-lg-9">
                <div class="row g-3">
                    <div class="col-lg-4">
                        <input type="text" name="vehicle_license_number" class="form-control"
                            value="{{ old('vehicle_license_number', $employee->vehicle_license_number) }}" />
                    </div>
                    <div class="col-lg-8">
                        <select name="vehicle_license_category[]" class="select2 form-control" multiple="multiple"
                            data-placeholder="Select categories (optional)" style="width: 100%">
                            @foreach ($vehicleLicenseCategories as $cat)
                                <option value="{{ $cat->code }}"
                                    {{ in_array($cat->code, $employee->vehicle_license_category ?? []) ? 'selected' : '' }}>
                                    {{ $cat->code }} — {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="validationGender" class="m-0">Gender
                    </label>
                </div>
            </div>
            <div class="col-lg-9">
                <select name="gender" class="select2 form-control" data-width="100%">
                    <option value="">Select a Gender</option>
                    @foreach ($genders as $gender)
                        <option value="{{ $gender->id }}" @if ($gender->id == $employee->gender) selected @endif>
                            {{ $gender->title }}</option>
                    @endforeach
                </select>
                @if ($errors->has('gender'))
                    <div class="fv-plugins-message-container text-danger">
                        <div data-field="gender">{!! $errors->first('gender') !!}</div>
                    </div>
                @endif
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="validationMaritalstaatus" class="m-0">Marital Status
                    </label>
                </div>
            </div>
            <div class="col-lg-9">
                <select name="marital_status" class="select2 form-control" data-width="100%">
                    <option value="">Select a Marital Status</option>
                    @foreach ($maritalStatus as $status)
                        <option value="{{ $status->id }}" @if ($status->id == $employee->marital_status) selected @endif>
                            {{ $status->title }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="validationMaritalstaatus" class="m-0">Probation Complete Date
                    </label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="text" name="probation_complete_date" class="form-control"
                    value="{{ $employee->probation_complete_date ?: old('probation_complete_date') }}"
                    onfocus="this.blur()" placeholder="yyyy-mm-dd" />
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="Fdname" class="m-0">Active?</label>
                </div>
            </div>
            <div class="col-lg-9">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" role="switch" id="flexSwitchCheckChecked"
                        name="active" @if ($employee->activated_at) checked @endif>
                    <label class="form-check-label" for="flexSwitchCheckChecked"></label>
                </div>
            </div>
        </div>
        {!! csrf_field() !!}
        {!! method_field('PUT') !!}
    </div>
    <div class="card-footer border-0 justify-content-end d-flex gap-2">
        <button type="submit" class="btn btn-primary btn-sm">Update</button>
        <a href="{!! route('employees.index') !!}" class="btn btn-danger btn-sm">Cancel</a>
    </div>
</form>
