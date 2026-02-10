<div class="card-header fw-bold">General Information</div>
<form action="{{ route('employees.store') }}" id="employeeAddForm" method="post" enctype="multipart/form-data"
    autocomplete="off">
    <div class="card-body">
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label class="form-label required-label">{{ __('label.staff-code') }}</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="number" min="1"
                    class="form-control @if ($errors->has('employee_code')) is-invalid @endif" name="employee_code"
                    value="{!! old('employee_code') !!}" autofocus />
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
                    <label for="validationfullname" class="form-label required-label">Full Name
                    </label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="text" class="form-control @if ($errors->has('full_name')) is-invalid @endif"
                    name="full_name" value="{{ old('full_name') }}" placeholder="Full name" />
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
                    <label class="form-label required-label">Official Email </label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="email" class="form-control @if ($errors->has('official_email_address')) is-invalid @endif"
                    name="official_email_address" value="{{ old('official_email_address') }}"
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
                    <label class="form-label required-label">Personal Email </label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="email" class="form-control @if ($errors->has('personal_email_address')) is-invalid @endif"
                    name="personal_email_address" value="{{ old('personal_email_address') }}"
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
                    <label for="validationphone" class="form-label">Contact Number
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
                            <input type="text"
                                class="form-control  @if ($errors->has('mobile_number')) is-invalid @endif"
                                name="mobile_number" placeholder="Mobile Number" value="{{ old('mobile_number') }}"
                                aria-label="Recipient's username" aria-describedby="basic-addon2" />

                            @if ($errors->has('mobile_number'))
                                <div class="fv-plugins-message-container invalid-feedback">
                                    <div data-field="mobile_number">{!! $errors->first('mobile_number') !!}
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="validationdob" class="form-label required-label">Joined Date
                    </label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="text" class="form-control" name="joined_date" value="{{ old('joined_date') }}"
                    onfocus="this.blur()" placeholder="yyyy-mm-dd" />
                <input type="hidden" value="{{ date('Y-m-d') }}" name="today" class="form-control" />
            </div>
        </div>

        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="validationdob" class="form-label">Date of Birth
                    </label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="text" class="form-control" name="date_of_birth" value="{{ old('date_of_birth') }}"
                    onfocus="this.blur()" placeholder="yyyy-mm-dd" />
                <input type="hidden" value="{{ date('Y-m-d') }}" name="today" class="form-control" />
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="validationcitizenship" class="form-label required-label">Citizenship
                        No.</label>
                </div>
            </div>
            <div class="col-lg-9">
                <div class="row">
                    <div class="col-lg-4">
                        <input type="text"
                            class="form-control @if ($errors->has('citizenship_number')) is-invalid @endif"
                            name="citizenship_number" value="{{ old('citizenship_number') }}"
                            placeholder="Citizenship No">
                        @if ($errors->has('citizenship_number'))
                            <div class="fv-plugins-message-container invalid-feedback">
                                <div data-field="citizenship_number">{!! $errors->first('citizenship_number') !!}
                                </div>
                            </div>
                        @endif
                    </div>
                    <div class="col-lg-8">
                        <input type="file" class="form-control" name="citizenship_attachment" />
                        <small>Supported file types jpeg/jpg/png/pdf and file size of upto 2MB.</small>
                        @if ($errors->has('citizenship_attachment'))
                            <div class="fv-plugins-message-container invalid-feedback">
                                <div data-field="citizenship_attachment">{!! $errors->first('citizenship_attachment') !!}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3"><label class="form-label">NID Number</label></div>
            <div class="col-lg-9">
                <input type="text" class="form-control" name="nid_number" value="{{ old('nid_number') }}"
                    placeholder="National ID Number (optional)" />
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="validationpan" class="form-label">PAN No.
                    </label>
                </div>
            </div>
            <div class="col-lg-9">
                <div class="row">
                    <div class="col-lg-4">
                        <input type="number"
                            class="form-control @if ($errors->has('pan_number')) is-invalid @endif"
                            name="pan_number" value="{{ old('pan_number') }}" placeholder="Pan No">
                        @if ($errors->has('pan_number'))
                            <div class="fv-plugins-message-container invalid-feedback">
                                <div data-field="pan_number">{!! $errors->first('pan_number') !!}
                                </div>
                            </div>
                        @endif
                    </div>
                    <div class="col-lg-8">
                        <input type="file" class="form-control" name="pan_attachment" />
                        <small>Supported file types jpeg/jpg/png/pdf and file size of upto 2MB.</small>
                        @if ($errors->has('pan_attachment'))
                            <div class="fv-plugins-message-container invalid-feedback">
                                <div data-field="pan_attachment">{!! $errors->first('pan_attachment') !!}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3"><label class="form-label">Passport No.</label></div>
            <div class="col-lg-9">
                <div class="row">
                    <div class="col-lg-4">
                        <input type="text" class="form-control" name="passport_number"
                            value="{{ old('passport_number') }}" placeholder="e.g. 12345678 (optional)" />
                    </div>
                    <div class="col-lg-8">
                        <input type="file" class="form-control" name="passport_attachment"
                            accept=".jpg,.jpeg,.png,.pdf" />
                        <small class="text-muted">Scan/PDF of passport (optional)</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-lg-3"><label class="form-label">Driving License No.</label></div>
            <div class="col-lg-9">
                <div class="row g-3">
                    <div class="col-lg-4">
                        <input type="text" class="form-control" name="vehicle_license_number"
                            value="{{ old('vehicle_license_number') }}" placeholder="License Number (optional)" />
                    </div>
                    <div class="col-lg-8">
                        <select name="vehicle_license_category[]" class="select2 form-control" multiple="multiple"
                            data-placeholder="Select categories (optional)" style="width: 100%">
                            @foreach ($vehicleLicenseCategories as $cat)
                                <option value="{{ $cat->code }}"
                                    {{ is_array(old('vehicle_license_category')) && in_array($cat->code, old('vehicle_license_category')) ? 'selected' : '' }}>
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
                    <label for="validationGender" class="form-label">Gender
                    </label>
                </div>
            </div>
            <div class="col-lg-9">
                <select name="gender" class="select2 form-control" data-width="100%">
                    <option value="">Select a Gender</option>
                    @foreach ($genders as $gender)
                        <option value="{{ $gender->id }}" @if ($gender->id == old('gender')) selected @endif>
                            {{ $gender->title }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="validationMaritalstaatus" class="form-label">Marital Status
                    </label>
                </div>
            </div>
            <div class="col-lg-9">
                <select name="marital_status" class="select2 form-control" data-width="100%">
                    <option value="">Select a Marital Status</option>
                    @foreach ($maritalStatus as $status)
                        <option value="{{ $status->id }}" @if ($status->id == old('marital_status')) selected @endif>
                            {{ $status->title }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        {!! csrf_field() !!}

    </div>
    <div class="card-footer border-0 justify-content-end d-flex gap-2">
        <button type="submit" class="btn btn-primary btn-sm">Save</button>
        {{-- <button class="btn btn-success btn-sm">Update</button> --}}
        <a href="{!! route('employees.index') !!}" class="btn btn-danger btn-sm">Cancel</a>
    </div>
</form>
