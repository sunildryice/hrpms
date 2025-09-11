<div class="card-header fw-bold">Add New Family Member</div>
<form action="{{ route('employees.family.details.store', $employee->id) }}" method="post" id="familyDetailForm"
    enctype="multipart/form-data" autocomplete="off" style="width: 100%;">
    <div class="card-body">
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="Fdname" class="form-label required-label">Full Name</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="text" class="form-control" name="full_name" placeholder="Full name"
                    value="{{ old('full_name') }}">
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="validationrelation" class="form-label required-label">Relation</label>
                </div>
            </div>
            <div class="col-lg-9">
                <select name="family_relation_id" class="select2 form-control" data-width="100%">
                    <option value="">Select a Relation</option>
                    @foreach ($familyRelations as $relation)
                        <option value="{{ $relation->id }}">{{ $relation->title }}</option>
                    @endforeach
                </select>
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
                <input type="text" class="form-control" name="date_of_birth" placeholder="Date of Birth"
                    value="{{ old('date_of_birth') }}" readonly>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="Fdname" class="form-label">Remarks</label>
                </div>
            </div>
            <div class="col-lg-9">
                <textarea name="remarks" class="form-control" placeholder="Remarks">{!! old('remarks') !!}</textarea>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="Fdname" class="form-label">Emergency Contact</label>
                </div>
            </div>
            <div class="col-lg-9">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" role="switch" id="flexSwitchCheckChecked"
                        name="emergency_contact">
                    <label class="form-check-label" for="flexSwitchCheckChecked"></label>
                </div>
            </div>
        </div>
        <div style="display: none" id="emergencyContactBlock">
            <div class="row mb-2">
                <div class="col-lg-3">
                    <div class="d-flex align-items-start h-100">
                        <label for="validationProvince" class="form-label">Province *
                        </label>
                    </div>
                </div>
                <div class="col-lg-9">
                    <select name="province_id" class="select2 form-control" placeholder="Select a Province..."
                        data-width="100%">
                        <option value="">Select a Province</option>
                        @foreach ($provinces as $province)
                            <option value="{{ $province->id }}">{{ $province->getProvinceName() }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="row mb-2">
                <div class="col-lg-3">
                    <div class="d-flex align-items-start h-100">
                        <label for="validationDistrict" class="form-label">District *
                        </label>
                    </div>
                </div>
                <div class="col-lg-9">
                    <select name="district_id" class="select2 form-control" placeholder="Select a District..."
                        data-width="100%">
                        <option value="">Select a District</option>
                    </select>
                </div>
            </div>

            <div class="row mb-2">
                <div class="col-lg-3">
                    <div class="d-flex align-items-start h-100">
                        <label for="validationMunicipality" class="form-label">Local Level *
                        </label>
                    </div>
                </div>
                <div class="col-lg-9">
                    <select name="local_level_id" class="select2 form-control" data-width="100%">
                        <option value="">Select a Local Level</option>
                    </select>
                </div>
            </div>

            <div class="row mb-2">
                <div class="col-lg-3">
                    <div class="d-flex align-items-start h-100">
                        <label for="validationWardNumber" class="form-label">Ward Number *</label>
                    </div>
                </div>
                <div class="col-lg-9">
                    <input type="number" min="1" class="form-control" name="ward"
                        value="{{ old('ward') }}" placeholder="Ward Number">
                </div>
            </div>

            <div class="row mb-2">
                <div class="col-lg-3">
                    <div class="d-flex align-items-start h-100">
                        <label for="validationTole" class="form-label">Tole</label>
                    </div>
                </div>
                <div class="col-lg-9">
                    <input type="text" class="form-control" name="tole" value="{{ old('tole') }}"
                        placeholder="Tole">
                </div>
            </div>

            <div class="row mb-2">
                <div class="col-lg-3">
                    <div class="d-flex align-items-start h-100">
                        <label for="validationTole" class="form-label">Contact Number</label>
                    </div>
                </div>
                <div class="col-lg-9">
                    <input type="text" class="form-control" name="contact_number"
                        value="{{ old('contact_number') }}" placeholder="Contact Number">
                </div>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="Fdname" class="form-label">Is Nominee</label>
                </div>
            </div>
            <div class="col-lg-9">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" role="switch" id="nomineeSwitchCheckChecked"
                        name="nominee" />
                    <label class="form-check-label" for="nomineeSwitchCheckChecked"></label>
                </div>
            </div>
        </div>

    </div>
    <div class="card-footer text-end">
        <button type="submit" class="btn btn-primary btn-sm">Save</button>
    </div>
    {!! csrf_field() !!}
</form>
@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function(e) {
            const form = document.getElementById('familyDetailForm');
            const fv = FormValidation.formValidation(form, {
                fields: {
                    family_relation_id: {
                        validators: {
                            notEmpty: {
                                message: 'Relation is required',
                            },
                        },
                    },
                    full_name: {
                        validators: {
                            notEmpty: {
                                message: 'Full name is required',
                            },
                        },
                    },
                    date_of_birth: {
                        validators: {
                            date: {
                                format: 'YYYY-MM-DD',
                                message: 'The value is not a valid date',
                            },
                        },
                    },
                    province_id: {
                        validators: {
                            notEmpty: {
                                message: 'The province is required.'
                            },
                            callback: {
                                message: 'Please select a province',
                                callback: function(value, validator, $field) {
                                    if ($('[name="emergency_contact"]').is(':checked')) {
                                        return true;
                                    } else {
                                        validator.updateStatus('province_id', validator.STATUS_VALID);
                                        return true;
                                    }
                                }
                            }
                        }
                    },
                    district_id: {
                        validators: {
                            notEmpty: {
                                message: 'The district is required.'
                            },
                            callback: {
                                message: 'Please select a district',
                                callback: function(value, validator, $field) {
                                    if ($('[name="emergency_contact"]').is(':checked')) {
                                        return true;
                                    } else {
                                        validator.updateStatus('district_id', validator.STATUS_VALID);
                                        return true;
                                    }
                                }
                            },
                        }
                    },
                    local_level_id: {
                        validators: {
                            notEmpty: {
                                message: 'The local level is required.'
                            },
                            callback: {
                                message: 'Please select a district',
                                callback: function(value, validator, $field) {
                                    if ($('[name="emergency_contact"]').is(':checked')) {
                                        return true;
                                    } else {
                                        validator.updateStatus('local_level_id', validator
                                        .STATUS_VALID);
                                        return true;
                                    }
                                }
                            },
                        }
                    },
                    ward: {
                        validators: {
                            notEmpty: {
                                message: 'Ward number is required',
                            },
                            between: {
                                min: 1,
                                max: 33,
                                message: 'The ward number must be between 1 and 33',
                            },
                        },
                    },
                    contact_number: {
                        validators: {
                            stringLength: {
                                max: 17,
                                message: 'The contact number must be less than 17 characters',
                            },
                            callback: {
                                message: 'Please enter a contact number',
                                callback: function(value, validator, $field) {
                                    if ($('[name="emergency_contact"]').is(':checked')) {
                                        return true;
                                    } else {
                                        validator.updateStatus('contact_number', validator
                                        .STATUS_VALID);
                                        return true;
                                    }
                                }
                            },
                        }
                    },
                },
                plugins: {
                    trigger: new FormValidation.plugins.Trigger(),
                    bootstrap5: new FormValidation.plugins.Bootstrap5(),
                    excluded: new FormValidation.plugins.Excluded(),
                    submitButton: new FormValidation.plugins.SubmitButton(),
                    defaultSubmit: new FormValidation.plugins.DefaultSubmit(),

                    icon: new FormValidation.plugins.Icon({
                        valid: 'bi bi-check2-square',
                        invalid: 'bi bi-x-lg',
                        validating: 'bi bi-arrow-repeat',
                    }),
                },
            });

            $('[name="date_of_birth"]').datepicker({
                language: 'en-GB',
                autoHide: true,
                format: 'yyyy-mm-dd',
                endDate: '{!! date('Y-m-d') !!}',
            }).on('change', function(e) {
                fv.revalidateField('date_of_birth');
            });
            @if ($employee->nominee->nominee_at)

                $('#familyDetailForm').on('change', '[name="nominee"]', function(e) {
                    $object = $(this);
                    if (this.checked) {
                        Swal.fire({
                            title: 'Do want to change a nominee?',
                            text: "You have already selected a nominee. ",
                            type: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Yes, change it!'
                        }).then((result) => {
                            console.log(result);
                            if (!result.value) {
                                console.log($object);
                                $($object).closest('form').find('[name="nominee"]').prop('checked',
                                    false);
                            }
                        });
                    }
                })
            @endif

            $('#familyDetailForm').on('change', '[name="emergency_contact"]', function(e) {
                $('#emergencyContactBlock').hide();
                if (this.checked) {
                    $('#emergencyContactBlock').show();
                }
                fv.revalidateField('province_id');
                fv.revalidateField('district_id');
                fv.revalidateField('local_level_id');
            }).on('change', '[name="province_id"]', function(e) {
                $element = $(this);
                var provinceId = $element.val();
                var htmlToReplace = '<option value="">Select a District</option>';
                if (provinceId) {
                    var url = baseUrl + '/api/master/provinces/' + provinceId;
                    var successCallback = function(response) {
                        console.log(response);
                        response.districts.forEach(function(district) {
                            htmlToReplace += '<option value="' + district.id + '">' + district
                                .district_name + '</option>';
                        });
                        $($element).closest('form').find('[name="district_id"]').html(htmlToReplace)
                            .trigger('change');
                        $($element).closest('form').find('[name="district_id"]').select2("destroy")
                            .select2();
                    }
                    var errorCallback = function(error) {
                        console.log(error);
                    }
                    // ajaxNativeSubmit(url, 'GET', {}, 'json');
                    ajaxNativeSubmit(url, 'GET', {}, 'json', successCallback, errorCallback);
                } else {
                    $($element).closest('form').find('[name="district_id"]').html(htmlToReplace);
                }
                fv.revalidateField('province_id');
            }).on('change', '[name="district_id"]', function(e) {
                $element = $(this);
                var districtId = $element.val();
                var htmlToReplace = '<option value="">Select a Local Level</option>';
                if (districtId) {
                    var url = baseUrl + '/api/master/districts/' + districtId;
                    var successCallback = function(response) {
                        response.localLevels.forEach(function(localLevel) {
                            htmlToReplace += '<option value="' + localLevel.id + '">' +
                                localLevel.local_level_name + '</option>';
                        });
                        $($element).closest('form').find('[name="local_level_id"]').html(htmlToReplace);
                        $($element).closest('form').find('[name="local_level_id"]').select2("destroy")
                            .select2();
                    }
                    var errorCallback = function(error) {
                        console.log(error);
                    }
                    ajaxNativeSubmit(url, 'GET', {}, 'json', successCallback, errorCallback);
                } else {
                    $($element).closest('form').find('[name="local_level_id"]').html(htmlToReplace);
                }
                fv.revalidateField('district_id');
                fv.revalidateField('local_level_id');
            }).on('change', '[name="local_level_id"]', function(e) {
                fv.revalidateField('local_level_id');
            }).on('change', '[name="family_relation_id"]', function(e) {
                fv.revalidateField('family_relation_id');
            });
        });
    </script>
@endpush
