<div class="card-header fw-bold">
    <h3 class="m-0 fs-6">General Information</h3>
</div>
<div class="card-body">
    <div class="row mb-2">
        <div class="col-lg-3">
            <div class="d-flex align-items-start h-100">
                <label for="" class="form-label required-label">{{ __('label.consultant-code') }}</label>
            </div>
        </div>
        <div class="col-lg-9">
            <input type="text" class="form-control @if ($errors->has('employee_code')) is-invalid @endif"
                   name="employee_code" disabled value="{{ old('employee_code') ?: $employee->employee_code }}"/>
            @if ($errors->has('employee_code'))
                <div class="fv-plugins-message-container invalid-feedback">
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
                   name="full_name" disabled value="{{ old('full_name') ?: $employee->full_name }}" placeholder="Full name"/>
            @if ($errors->has('full_name'))
                <div class="fv-plugins-message-container invalid-feedback">
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
                   name="official_email_address" disabled
                   value="{{ old('official_email_address') ?: $employee->official_email_address }}"
                   placeholder="example@example.com">
            @if ($errors->has('official_email_address'))
                <div class="fv-plugins-message-container invalid-feedback">
                    <div data-field="official_email_address">{!! $errors->first('official_email_address') !!}</div>
                </div>
            @endif
        </div>
    </div>
    <div class="row mb-2">
        <div class="col-lg-3">
            <div class="d-flex align-items-start h-100">
                <label for="" class="m-0">Personal Email </label>
            </div>
        </div>
        <div class="col-lg-9">
            <input type="email" class="form-control @if ($errors->has('personal_email_address')) is-invalid @endif"
                   name="personal_email_address" disabled
                   value="{{ old('personal_email_address') ?: $employee->personal_email_address }}"
                   placeholder="example@example.com">
            @if ($errors->has('personal_email_address'))
                <div class="fv-plugins-message-container invalid-feedback">
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
                <div class="col-lg-6">
                    <div class="input-group">
                        <input type="text" class="form-control" name="telephone_number"
                               placeholder="Telephone Number" disabled
                               value="{{ old('telephone_number') ?: $employee->telephone_number }}"
                               aria-label="Recipient's username" aria-describedby="basic-addon2"/>
                        <div class="input-group-append">
                            <span class="input-group-text" id="basic-addon2">Telephone</span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="input-group">
                        <input type="text" class="form-control" name="mobile_number" placeholder="Mobile Number"
                               value="{{ old('mobile_number') ?: $employee->mobile_number }}" disabled
                               aria-label="Recipient's username" aria-describedby="basic-addon2"/>
                        <div class="input-group-append">
                            <span class="input-group-text  required-label" id="basic-addon2">Mobile</span>
                        </div>
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
            <input type="text" class="form-control" name="date_of_birth" disabled
                   value="{{ old('date_of_birth') ?: $employee->date_of_birth }}"/>
            <input type="hidden" value="{{ date('Y-m-d') }}" name="today" class="form-control"/>
        </div>
    </div>
    <div class="row mb-2">
        <div class="col-lg-3">
            <div class="d-flex align-items-start h-100">
                <label for="validationcitizenship" class="m-0">Citizenship No.
                </label>
            </div>
        </div>
        <div class="col-lg-9">
            <div class="row">
                <div class="col-lg-6">
                    <input type="text" class="form-control" name="citizenship_number" disabled
                           value="{{ old('citizenship_number') ?: $employee->citizenship_number }}"
                           placeholder="Citizenship No">
                </div>
                <div class="col-lg-6">
                    <input type="file" class="form-control" name="citizenship_attachment" disabled/>
                </div>
            </div>
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
                <div class="col-lg-6">
                    <input type="number" class="form-control" name="pan_number" disabled
                           value="{{ old('pan_number') ?: $employee->pan_number }}" placeholder="Pan No">
                </div>
                <div class="col-lg-6">
                    <input type="file" class="form-control" name="pan_attachment" disabled/>
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
            <select name="gender" disabled class="select2 form-control" data-width="100%">
                <option value="">Select a Gender</option>
                @foreach ($genders as $gender)
                    <option value="{{ $gender->id }}" @if ($gender->id == $employee->gender) selected @endif>
                        {{ $gender->title }}</option>
                @endforeach
            </select>
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
            <select name="marital_status" class="select2 form-control" data-width="100%" disabled>
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
                <label for="Fdname" class="m-0">Physically Abled?</label>
            </div>
        </div>
        <div class="col-lg-9">
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" role="switch" id="physicallyabled"
                       name="physically_abled" @if($employee->physically_abled) checked @endif>
                <label class="form-check-label" for="physicallyabled"></label>
            </div>
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
                       name="active" @if($employee->activated_at) checked @endif>
                <label class="form-check-label" for="flexSwitchCheckChecked"></label>
            </div>
        </div>
    </div>
</div>
