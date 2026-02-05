<div class="card-header fw-bold">Edit Tenure</div>
<form class="needs-validation" action="{{ route('employees.tenures.update', [$tenure->employee_id, $tenure->id]) }}"
    method="post" id="tenureEditForm" enctype="multipart/form-data" autocomplete="off">
    <div class="card-body">
        @php
            $selectedDesignationId = old('designation_id') ?: $tenure->designation_id;
            $selectedDepartmentId = old('department_id') ?: $tenure->department_id;
            $selectedSupervisorId = old('supervisor_id') ?: $tenure->supervisor_id;
            $selectedCrossSupervisorId = old('cross_supervisor_id') ?: $tenure->cross_supervisor_id;
            $selectedNextLineManagerId = old('next_line_manager_id') ?: $tenure->next_line_manager_id;
            $selectedDutyStationId = old('duty_station_id') ?: $tenure->duty_station_id;
            $selectedOfficeId = old('office_id') ?: $tenure->office_id;
        @endphp

        <div class="mb-2 row">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="office_id" class="form-label required-label">Office</label>
                </div>
            </div>
            <div class="col-lg-9">
                <select name="office_id"
                    class="select2 form-control @if ($errors->has('office_id')) is-invalid @endif" data-width="100%">
                    <option value="">Select a Office</option>
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
                    <label for="designation_id" class="form-label required-label">Designation</label>
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
                    <label for="department_id" class="form-label required-label">Department</label>
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
                    <label for="joined_date" class="form-label required-label">Joined Date</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="text" class="form-control @if ($errors->has('joined_date')) is-invalid @endif"
                    name="joined_date" placeholder="Joined Date"
                    value="{{ old('joined_date') ?: ($tenure->joined_date ? $tenure->joined_date->format('Y-m-d') : '') }}"
                    onfocus="this.blur()" />
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
                    <label for="to_date" class="form-label">To Date</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="text" class="form-control @if ($errors->has('to_date')) is-invalid @endif"
                    name="to_date" placeholder="To Date"
                    value="{{ old('to_date') ?: ($tenure->to_date ? $tenure->to_date->format('Y-m-d') : '') }}"
                    onfocus="this.blur()" />
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
                    <label for="contract_end_date" class="form-label">Contract End Date</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="text" class="form-control @if ($errors->has('contract_end_date')) is-invalid @endif"
                    name="contract_end_date" placeholder="Contract End Date"
                    value="{{ old('contract_end_date') ?: ($tenure->contract_end_date ? $tenure->contract_end_date->format('Y-m-d') : '') }}"
                    onfocus="this.blur()" />
                @if ($errors->has('contract_end_date'))
                    <div class="fv-plugins-message-container invalid-feedback">
                        <div data-field="contract_end_date">{!! $errors->first('contract_end_date') !!}</div>
                    </div>
                @endif
            </div>
        </div>

        <div class="mb-2 row">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="supervisor_id" class="form-label">Line Manager</label>
                </div>
            </div>
            <div class="col-lg-9">
                <select name="supervisor_id"
                    class="select2 form-control @if ($errors->has('supervisor_id')) is-invalid @endif"
                    data-width="100%">
                    <option value="">Select a Line Manager</option>
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
                    <label for="next_line_manager_id" class="form-label">Reviewer</label>
                </div>
            </div>
            <div class="col-lg-9">
                <select name="next_line_manager_id"
                    class="select2 form-control @if ($errors->has('next_line_manager_id')) is-invalid @endif"
                    data-width="100%">
                    <option value="">Select a Reviewer</option>
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
                    id="validationinstitution" value="{{ old('duty_station') ?? $tenure->duty_station }}"
                    placeholder="" name="duty_station" autofocus>
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
                    <label for="duty_station_id" class="form-label">District</label>
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
                <textarea name="remarks" class="form-control" placeholder="Remarks">{!! old('remarks') ?: $tenure->remarks !!}</textarea>
            </div>
        </div>
    </div>
    <div class="gap-2 border-0 card-footer justify-content-end d-flex">
        <button type="submit" class="btn btn-primary btn-sm">Update</button>
    </div>
    {!! csrf_field() !!}
    {!! method_field('PUT') !!}
</form>
@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function(e) {
            const form = document.getElementById('tenureEditForm');
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
                    to_date: {
                        validators: {
                            date: {
                                format: 'YYYY-MM-DD',
                                message: 'The value is not a valid date',
                            },
                            callback: {
                                message: 'To Date cannot be earlier than Joined Date',
                                callback: function(input) {
                                    if (!input.value.trim()) {
                                        return true;
                                    }
                                    const joinedDate = form.querySelector('[name="joined_date"]').value;
                                    if (!joinedDate) {
                                        return true;
                                    }
                                    return new Date(input.value) >= new Date(joinedDate);
                                }
                            }
                        }
                    },
                    contract_end_date: {
                        validators: {
                            date: {
                                format: 'YYYY-MM-DD',
                                message: 'The value is not a valid date',
                            },
                            callback: {
                                message: 'Contract End Date cannot be earlier than Joined Date',
                                callback: function(input) {
                                    if (!input.value.trim()) {
                                        return true;
                                    }
                                    const joinedDate = form.querySelector('[name="joined_date"]').value;
                                    if (!joinedDate) return true;
                                    return new Date(input.value) >= new Date(joinedDate);
                                }
                            }
                        }
                    },
                    supervisor_id: {
                        validators: {
                            different: {
                                compare: function() {
                                    return form.querySelector('[name="next_line_manager_id"]').value;
                                },
                                message: 'The line manager and reviewer cannot be the same.',
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
                endDate: '{!! date('Y-m-d') !!}',
            }).on('change', function(e) {
                fv.revalidateField('joined_date');
                fv.revalidateField('to_date');
                fv.revalidateField('contract_end_date');
            });

            $('[name="to_date"]').datepicker({
                language: 'en-GB',
                autoHide: true,
                format: 'yyyy-mm-dd',
            }).on('change', function(e) {
                fv.revalidateField('to_date');
            });

            $('[name="contract_end_date"]').datepicker({
                language: 'en-GB',
                autoHide: true,
                format: 'yyyy-mm-dd',
            }).on('change', function(e) {
                fv.revalidateField('contract_end_date');
            });

            $('#tenureEditForm').on('change', '[name="department_id"]', function(e) {
                fv.revalidateField('department_id');
            }).on('change', '[name="designation_id"]', function(e) {
                fv.revalidateField('designation_id');
            }).on('change', '[name="supervisor_id"]', function(e) {
                fv.revalidateField('supervisor_id');
                fv.revalidateField('next_line_manager_id');
            }).on('change', '[name="cross_supervisor_id"]', function(e) {
                fv.revalidateField('supervisor_id');
                fv.revalidateField('next_line_manager_id');
            }).on('change', '[name="next_line_manager_id"]', function(e) {
                fv.revalidateField('supervisor_id');
                fv.revalidateField('next_line_manager_id');
            }).on('change', '[name="office_id"]', function(e) {
                fv.revalidateField('office_id');
            });
        });
    </script>
@endpush
