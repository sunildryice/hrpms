<div class="card-header fw-bold">Add New Tenure</div>
<form class="needs-validation" action="{{ route('employees.tenures.store', $employee) }}" method="post" id="tenureAddForm"
    enctype="multipart/form-data" autocomplete="off">
    <div class="card-body">
        @php
            $selectedDesignationId = old('designation_id') ?: $employee->latestTenure->designation_id;
            $selectedDepartmentId = old('department_id') ?: $employee->latestTenure->department_id;
            $selectedSupervisorId = old('supervisor_id') ?: $employee->latestTenure->supervisor_id;
            $selectedCrossSupervisorId = old('cross_supervisor_id') ?: $employee->latestTenure->cross_supervisor_id;
            $selectedNextLineManagerId = old('next_line_manager_id') ?: $employee->latestTenure->next_line_manager_id;
            $selectedDutyStationId = old('duty_station_id') ?: $employee->latestTenure->duty_station_id;
            $selectedOfficeId = old('office_id') ?: $employee->latestTenure->office_id;
        @endphp

        <div class="mb-2 row">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="validationcitizenship" class="form-label required-label">Office</label>
                </div>
            </div>
            <div class="col-lg-9">
                <select name="office_id"
                    class="select2 form-control @if ($errors->has('office_id')) is-invalid @endif" data-width="100%">
                    <option value="">Select an Office</option>
                    @foreach ($offices as $office)
                        <option value="{{ $office->id }}" @if ($selectedOfficeId == $office->id) selected @endif>
                            {{ $office->getOfficeName() }}</option>
                    @endforeach
                </select>
                @if ($errors->has('office_id'))
                    <div class="fv-plugins-message-container invalid-feedback">
                        <div data-field="office_id">{!! $errors->first('office_id') !!}</div>
                    </div>
                @endif
            </div>
        </div>

        <div class="mb-2 row">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="Fdname" class="form-label required-label">Designation</label>
                </div>
            </div>
            <div class="col-lg-9">
                <select name="designation_id"
                    class="select2 form-control @if ($errors->has('designation_id')) is-invalid @endif" data-width="100%">
                    <option value="">Select a Designation</option>
                    @foreach ($designations as $designation)
                        <option value="{{ $designation->id }}" @if ($selectedDesignationId == $designation->id) selected @endif>
                            {{ $designation->getDesignationName() }}</option>
                    @endforeach
                </select>
                @if ($errors->has('designation_id'))
                    <div class="fv-plugins-message-container invalid-feedback">
                        <div data-field="designation_id">{!! $errors->first('designation_id') !!}</div>
                    </div>
                @endif
            </div>
        </div>

        <div class="mb-2 row">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="Fdname" class="form-label required-label">Department</label>
                </div>
            </div>
            <div class="col-lg-9">
                <select name="department_id"
                    class="select2 form-control @if ($errors->has('department_id')) is-invalid @endif" data-width="100%">
                    <option value="">Select a Department</option>
                    @foreach ($departments as $department)
                        <option value="{{ $department->id }}" @if ($selectedDepartmentId == $department->id) selected @endif>
                            {{ $department->getDepartmentName() }}</option>
                    @endforeach
                </select>
                @if ($errors->has('department_id'))
                    <div class="fv-plugins-message-container invalid-feedback">
                        <div data-field="department_id">{!! $errors->first('department_id') !!}</div>
                    </div>
                @endif
            </div>
        </div>

        <div class="mb-2 row">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="validationdob" class="form-label required-label">Joined Date</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="text" class="form-control @if ($errors->has('joined_date')) is-invalid @endif"
                    name="joined_date" placeholder="Joined Date"
                    value="{{ old('joined_date') ?: ($employee->latestTenure?->to_date?->format('Y-m-d') ?: $employee->latestTenure?->joined_date?->format('Y-m-d') ?: '') }}"
                    readonly />
                @if ($errors->has('joined_date'))
                    <div class="fv-plugins-message-container invalid-feedback">
                        <div data-field="joined_date">{!! $errors->first('joined_date') !!}</div>
                    </div>
                @endif
            </div>
        </div>

        <div class="mb-2 row">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="validationdob" class="form-label">To Date</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="text" class="form-control @if ($errors->has('to_date')) is-invalid @endif"
                    name="to_date" placeholder="To Date" value="{{ old('to_date') }}" readonly />
                @if ($errors->has('to_date'))
                    <div class="fv-plugins-message-container invalid-feedback">
                        <div data-field="to_date">{!! $errors->first('to_date') !!}</div>
                    </div>
                @endif
            </div>
        </div>

        <div class="mb-2 row">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="validationdob" class="form-label">Supervisor</label>
                </div>
            </div>
            <div class="col-lg-9">
                <select name="supervisor_id"
                    class="select2 form-control @if ($errors->has('supervisor_id')) is-invalid @endif" data-width="100%">
                    <option value="">Select a Supervisor</option>
                    @foreach ($supervisors as $supervisor)
                        <option value="{{ $supervisor->id }}" @if ($selectedSupervisorId == $supervisor->id) selected @endif>
                            {{ $supervisor->getFullName() }}</option>
                    @endforeach
                </select>
                @if ($errors->has('supervisor_id'))
                    <div class="fv-plugins-message-container invalid-feedback">
                        <div data-field="supervisor_id">{!! $errors->first('supervisor_id') !!}</div>
                    </div>
                @endif
            </div>
        </div>
        <div class="mb-2 row">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="validationdob" class="form-label">Cross Supervisor</label>
                </div>
            </div>
            <div class="col-lg-9">
                <select name="cross_supervisor_id"
                    class="select2 form-control @if ($errors->has('cross_supervisor_id')) is-invalid @endif"
                    data-width="100%">
                    <option value="">Select a Cross Supervisor</option>
                    @foreach ($supervisors as $supervisor)
                        <option value="{{ $supervisor->id }}" @if ($selectedCrossSupervisorId == $supervisor->id) selected @endif>
                            {{ $supervisor->getFullName() }}</option>
                    @endforeach
                </select>
                @if ($errors->has('cross_supervisor_id'))
                    <div class="fv-plugins-message-container invalid-feedback">
                        <div data-field="cross_supervisor_id">{!! $errors->first('cross_supervisor_id') !!}</div>
                    </div>
                @endif
            </div>
        </div>
        <div class="mb-2 row">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="validationcitizenship" class="form-label">Next Line Manager</label>
                </div>
            </div>
            <div class="col-lg-9">
                <select name="next_line_manager_id"
                    class="select2 form-control @if ($errors->has('next_line_manager_id')) is-invalid @endif"
                    data-width="100%">
                    <option value="">Select a Next Line Manager</option>
                    @foreach ($supervisors as $supervisor)
                        <option value="{{ $supervisor->id }}" @if ($selectedNextLineManagerId == $supervisor->id) selected @endif>
                            {{ $supervisor->getFullName() }}</option>
                    @endforeach
                </select>
                @if ($errors->has('next_line_manager_id'))
                    <div class="fv-plugins-message-container invalid-feedback">
                        <div data-field="next_line_manager_id">{!! $errors->first('next_line_manager_id') !!}</div>
                    </div>
                @endif
            </div>
        </div>
        <div class="mb-2 row">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="validationinstitution" class="form-label">Duty Station</label>
                </div>

            </div>
            <div class="col-lg-9">
                <input type="text" class="form-control @if ($errors->has('duty_station')) is-invalid @endif"
                    id="validationinstitution" value="{{ old('duty_station') }}" placeholder="" name="duty_station"
                    autofocus>
                @if ($errors->has('duty_station'))
                    <div class="fv-plugins-message-container invalid-feedback">
                        <div data-field="duty_station">{!! $errors->first('duty_station') !!}</div>
                    </div>
                @endif
            </div>
        </div>

        <div class="mb-2 row">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="validationcitizenship" class="form-label">District</label>
                </div>
            </div>
            <div class="col-lg-9">
                <select name="duty_station_id"
                    class="select2 form-control @if ($errors->has('duty_station_id')) is-invalid @endif"
                    data-width="100%">
                    <option value="">Select a Duty Station</option>
                    @foreach ($dutyStations as $district)
                        <option value="{{ $district->id }}" @if ($selectedDutyStationId == $district->id) selected @endif>
                            {{ $district->getDistrictName() }}</option>
                    @endforeach
                </select>
                @if ($errors->has('duty_station_id'))
                    <div class="fv-plugins-message-container invalid-feedback">
                        <div data-field="duty_station_id">{!! $errors->first('duty_station_id') !!}</div>
                    </div>
                @endif
            </div>
        </div>

        <div class="mb-2 row">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="Fdname" class="form-label">Remarks</label>
                </div>
            </div>
            <div class="col-lg-9">
                <textarea name="remarks" class="form-control" placeholder="Remarks">{!! old('remarks') ?: $employee->latestTenure->remarks !!}</textarea>
            </div>
        </div>
    </div>
    <div class="gap-2 border-0 card-footer justify-content-end d-flex">
        <button type="submit" class="btn btn-primary btn-sm">Save</button>
    </div>
    {!! csrf_field() !!}
</form>
@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function(e) {
            const form = document.getElementById('tenureAddForm');
            const fv = FormValidation.formValidation(form, {
                fields: {
                    office_id: {
                        validators: {
                            notEmpty: {
                                message: 'Office is required',
                            },
                        },
                    },
                    designation_id: {
                        validators: {
                            notEmpty: {
                                message: 'Designation is required',
                            },
                        },
                    },
                    department_id: {
                        validators: {
                            notEmpty: {
                                message: 'Department is required',
                            },
                        },
                    },
                    joined_date: {
                        validators: {
                            notEmpty: {
                                message: 'Joined date is required',
                            },
                            date: {
                                format: 'YYYY-MM-DD',
                                message: 'The value is not a valid date',
                            },
                        },
                    },
                    supervisor_id: {
                        validators: {
                            different: {
                                compare: function() {
                                    return form.querySelector('[name="next_line_manager_id"]').value;
                                },
                                message: 'The supervisor and next line manager cannot be the same.',
                            },
                        },
                    },
                    cross_supervisor_id: {
                        validators: {
                            different: {
                                compare: function() {
                                    return form.querySelector('[name="supervisor_id"]').value;
                                },
                                message: 'The supervisor and cross supervisor cannot be the same.',
                            },
                        },
                    },
                    next_line_manager_id: {
                        validators: {
                            different: {
                                compare: function() {
                                    return form.querySelector('[name="cross_supervisor_id"]').value;
                                },
                                message: 'The next line manager and cross supervisor cannot be the same.',
                            },
                        },
                    },
                },
                plugins: {
                    trigger: new FormValidation.plugins.Trigger(),
                    bootstrap5: new FormValidation.plugins.Bootstrap5(),
                    submitButton: new FormValidation.plugins.SubmitButton(),
                    defaultSubmit: new FormValidation.plugins.DefaultSubmit(),

                    icon: new FormValidation.plugins.Icon({
                        valid: 'bi bi-check2-square',
                        invalid: 'bi bi-x-lg',
                        validating: 'bi bi-arrow-repeat',
                    }),
                },
            });

            $('[name="joined_date"]').datepicker({
                language: 'en-GB',
                autoHide: true,
                format: 'yyyy-mm-dd',
            }).on('change', function(e) {
                fv.revalidateField('joined_date');
            });

            $('[name="to_date"]').datepicker({
                language: 'en-GB',
                autoHide: true,
                format: 'yyyy-mm-dd',
            });

            $('#tenureAddForm').on('change', '[name="department_id"]', function(e) {
                fv.revalidateField('department_id');
            }).on('change', '[name="designation_id"]', function(e) {
                fv.revalidateField('designation_id');
            }).on('change', '[name="supervisor_id"]', function(e) {
                fv.revalidateField('supervisor_id');
                fv.revalidateField('cross_supervisor_id');
                fv.revalidateField('next_line_manager_id');
            }).on('change', '[name="cross_supervisor_id"]', function(e) {
                fv.revalidateField('supervisor_id');
                fv.revalidateField('cross_supervisor_id');
                fv.revalidateField('next_line_manager_id');
            }).on('change', '[name="next_line_manager_id"]', function(e) {
                fv.revalidateField('supervisor_id');
                fv.revalidateField('cross_supervisor_id');
                fv.revalidateField('next_line_manager_id');
            }).on('change', '[name="office_id"]', function(e) {
                fv.revalidateField('office_id');
            });
        });
    </script>
@endpush
