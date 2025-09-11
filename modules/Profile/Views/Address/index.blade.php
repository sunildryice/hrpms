<div class="card-header fw-bold">
    <h3 class="m-0 fs-6">Address</h3>
</div>
@php
    $action = $employee->address->created_at ? route('profile.address.update', [$employee->address->id]) :
            route('profile.address.store');
@endphp
<form class="g-3 needs-validation" action="{{ $action }}" method="POST" id="addressAddForm" autocomplete="off" enctype="multipart/form-data">
    <div class="card-body">
        {!! csrf_field() !!}
        <div class="row mb-3 ">
            <div class="col-lg-12">
                <div class="d-flex align-items-center h-100 border-bottom p-1 mb-2">
                    <label for="validationprovince" class="m-0">Current Address</label>
                </div>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="validationprovince" class="form-label required-label">Province </label>
                </div>
            </div>
            <div class="col-lg-9">
                <select id="validationprovince" data-width="100%" class="select2 form-control @if($errors->has('temporary_province_id')) is-invalid @endif" name="temporary_province_id">
                    <option value="">Select a Province</option>
                    @foreach($provinces as $province)
                        {{$p_selected = old('temporary_province_id')?old('temporary_province_id'):$employee->address->temporary_province_id}}
                        <option value="{{ $province->id }}" {{$province->id == $p_selected ? "selected":""}}>
                            {{ $province->province_name }}
                        </option>
                    @endforeach

                </select>
                @if($errors->has('temporary_province_id'))
                    <div class="fv-plugins-message-container invalid-feedback">
                        <div data-field="temporary_province_id">{!! $errors->first('temporary_province_id') !!}</div>
                    </div>
                @endif
            </div>
        </div>

        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="validationdistrict" class="form-label required-label">District </label>
                </div>
            </div>
            <div class="col-lg-9">
                <select id="validationdistrict" data-width="100%" class="select2 form-control @if($errors->has('temporary_district_id')) is-invalid @endif" name="temporary_district_id">
                    <option value="">Select a District</option>
                    @foreach($districts as $district)
                        {{$d_selected = old('temporary_district_id')?old('temporary_district_id'):$employee->address->temporary_district_id}}
                        <option value="{{ $district->id }}" {{$district->id == $d_selected ? "selected":""}}>
                            {{ $district->district_name }}
                        </option>
                    @endforeach
                </select>
                @if($errors->has('temporary_district_id'))
                    <div class="fv-plugins-message-container invalid-feedback">
                        <div data-field="temporary_district_id">{!! $errors->first('temporary_district_id') !!}</div>
                    </div>
                @endif
            </div>
        </div>

        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="validationmunicipality" class="form-label required-label">Local Level Government </label>
                </div>
            </div>
            <div class="col-lg-9">
                <select id="validationmunicipality" data-width="100%" class="select2 form-control
                @if($errors->has('temporary_local_level_id')) is-invalid @endif" name="temporary_local_level_id">
                    <option value="">Select a Local Level Government</option>
                    @foreach($localLevels as $locallevel)
                        {{$l_selected = old('temporary_local_level_id')?old('temporary_local_level_id'):$employee->address->temporary_local_level_id}}
                        <option value="{{ $locallevel->id }}" {{$locallevel->id == $l_selected ? "selected":""}}>
                            {{ $locallevel->local_level_name }}
                        </option>
                    @endforeach
                </select>
                @if($errors->has('temporary_local_level_id'))
                    <div class="fv-plugins-message-container invalid-feedback">
                        <div data-field="temporary_local_level_id">{!! $errors->first('temporary_local_level_id') !!}</div>
                    </div>
                @endif
            </div>
        </div>

        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="validationward" class="form-label required-label">Ward Number </label>
                </div>

            </div>
            <div class="col-lg-9">
                <input type="number" class="form-control @if($errors->has('temporary_ward')) is-invalid @endif" id="validationward"
                       value="{{ old('temporary_ward') ?: $employee->address->temporary_ward }}" name="temporary_ward"
                       placeholder="Ward Number">
                @if($errors->has('temporary_ward'))
                    <div class="fv-plugins-message-container invalid-feedback">
                        <div data-field="temporary_ward">{!! $errors->first('temporary_ward') !!}</div>
                    </div>
                @endif
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="validationtole" class="form-label required-label">Tole </label>
                </div>

            </div>
            <div class="col-lg-9">
                <input type="text" class="form-control @if($errors->has('temporary_tole')) is-invalid @endif" id="validationtole"
                       value="{{ old('temporary_tole') ?: $employee->address->temporary_tole }}"
                       placeholder="Tole" name="temporary_tole">
                @if($errors->has('temporary_tole'))
                    <div class="fv-plugins-message-container invalid-feedback">
                        <div data-field="temporary_tole">{!! $errors->first('temporary_tole') !!}</div>
                    </div>
                @endif
            </div>
        </div>
        <div class="mb-3 row">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="validationtole" class="form-label">Current Location</label>
                </div>

            </div>
            <div class="col-lg-9">
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-geo-alt"></i></span>
                    <textarea class="form-control" name="current_location" aria-label="Location">{{ $employee->address->current_location }}</textarea>
                </div>
                @if ($errors->has('current_location'))
                    <div class="fv-plugins-message-container invalid-feedback">
                        <div data-field="current_location">{!! $errors->first('current_location') !!}</div>
                    </div>
                @endif
            </div>
        </div>
        {{-- permanent address --}}
        <div class="row mb-3 mt-3 border-bottom">
            <div class="col-lg-3">
                <div class="d-flex align-items-center h-100 p-1 mb-2">
                    <label for="validationprovince" class="m-0">Permanent Address
                    </label>
                </div>
            </div>
            <div class="col-lg-9">
                <div class="d-flex align-items-center h-100 p-1 mb-2">
                    <label for="validationcurrent" class="m-0">
                        <input type="checkbox"name="validationcurrent" id="validationcurrent" value="checked">
                        Same as Current Address
                    </label>
                </div>
            </div>
        </div>
        <div class="currentchecked">
            <div class="row mb-2">
                <div class="col-lg-3">
                    <div class="d-flex align-items-start h-100">
                        <label for="validationprovince2" class="form-label required-label">Province </label>
                    </div>
                </div>
                <div class="col-lg-9">
                    <select id="validationprovince2" data-width="100%" class="select2 form-control @if($errors->has('permanent_province_id')) is-invalid @endif"
                            name="permanent_province_id">
                        <option value="">Select a Province...</option>
                        @foreach($provinces as $province)
                            {{$p_select = old('permanent_province_id')?old('permanent_province_id'):$employee->address->permanent_province_id}}
                            <option value="{{ $province->id }}" {{$province->id == $p_select ? "selected":""}}>
                                {{ $province->province_name }}
                            </option>
                        @endforeach
                    </select>
                    @if($errors->has('permanent_province_id'))
                        <div class="fv-plugins-message-container invalid-feedback">
                            <div data-field="permanent_province_id">{!! $errors->first('permanent_province_id') !!}</div>
                        </div>
                    @endif
                </div>
            </div>

            <div class="row mb-2">
                <div class="col-lg-3">
                    <div class="d-flex align-items-start h-100">
                        <label for="validationdistrict2" class="form-label required-label">District </label>
                    </div>
                </div>
                <div class="col-lg-9">
                    <select id="validationdistrict2" data-width="100%" class="select2 form-control @if($errors->has('permanent_district_id')) is-invalid @endif"
                            name="permanent_district_id">
                        <option value="">Select a District...</option>
                        @foreach($districts as $district)
                        {{$d_selected = old('permanent_district_id')?old('permanent_district_id'):$employee->address->permanent_district_id}}
                        <option value="{{ $district->id }}" {{$district->id == $d_selected ? "selected":""}}>
                            {{ $district->district_name }}
                        </option>
                    @endforeach
                    </select>
                    @if($errors->has('permanent_district_id'))
                        <div class="fv-plugins-message-container invalid-feedback">
                            <div data-field="permanent_district_id">{!! $errors->first('permanent_district_id') !!}</div>
                        </div>
                    @endif
                </div>
            </div>

            <div class="row mb-2">
                <div class="col-lg-3">
                    <div class="d-flex align-items-start h-100">
                        <label for="validationmunicipality2" class="form-label required-label">Local Level Government </label>
                    </div>
                </div>
                <div class="col-lg-9">
                    <select id="validationmunicipality2" data-width="100%" class="select2 form-control @if($errors->has('permanent_local_level_id')) is-invalid @endif"
                            name="permanent_local_level_id">
                        <option value="">Select a Local Level Government...</option>
                        @foreach($localLevels as $locallevel)
                        {{$l_selected = old('permanent_local_level_id')?old('permanent_local_level_id'):$employee->address->permanent_local_level_id}}
                        <option value="{{ $locallevel->id }}" {{$locallevel->id == $l_selected ? "selected":""}}>
                            {{ $locallevel->local_level_name }}
                        </option>
                    @endforeach
                    </select>
                    @if($errors->has('permanent_local_level_id'))
                        <div class="fv-plugins-message-container invalid-feedback">
                            <div data-field="permanent_local_level_id">{!! $errors->first('permanent_local_level_id') !!}</div>
                        </div>
                    @endif
                </div>
            </div>

            <div class="row mb-2">
                <div class="col-lg-3">
                    <div class="d-flex align-items-start h-100">
                        <label for="validationwardno2" class="form-label required-label">Ward Number </label>
                    </div>

                </div>
                <div class="col-lg-9">
                    <input type="number" class="form-control @if($errors->has('permanent_ward')) is-invalid @endif" id="validationwardno2"
                           value="{{ old('permanent_ward') ?: $employee->address->permanent_ward }}"
                           placeholder="Ward Number" name="permanent_ward">
                    @if($errors->has('permanent_ward'))
                        <div class="fv-plugins-message-container invalid-feedback">
                            <div data-field="permanent_ward">{!! $errors->first('permanent_ward') !!}</div>
                        </div>
                    @endif
                </div>
            </div>

            <div class="row mb-2">
                <div class="col-lg-3">
                    <div class="d-flex align-items-start h-100">
                        <label for="validationtole2" class="form-label required-label">Tole </label>
                    </div>

                </div>
                <div class="col-lg-9">
                    <input type="text" class="form-control @if($errors->has('permanent_tole')) is-invalid @endif" id="validationtole2"
                           value="{{ old('permanent_tole') ?: $employee->address->permanent_tole }}"
                           placeholder="Tole" name="permanent_tole">
                    @if($errors->has('permanent_tole'))
                        <div class="fv-plugins-message-container invalid-feedback">
                            <div data-field="permanent_tole">{!! $errors->first('permanent_tole') !!}</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div class="append_input"></div>
    <div class="card-footer border-0 justify-content-end d-flex gap-2">
        <button type="submit" class="btn btn-primary btn-sm">
            @if($employee->address->created_at) Update @else Save @endif
        </button>
    </div>
    @if($employee->address->created_at)
    {!! method_field('PUT') !!}
    @endif
</form>
@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function (e) {
            var checked = $('#validationcurrent').is(':checked');
            const fv = FormValidation.formValidation(document.getElementById('addressAddForm'), {
                fields: {
                    temporary_province_id: {
                        validators: {
                            notEmpty: {
                                message: 'Current Province is required',
                            },
                        },
                    },
                    temporary_district_id: {
                        validators: {
                            notEmpty: {
                                message: 'Current District is required',
                            },
                        },
                    },
                    temporary_local_level_id: {
                        validators: {
                            notEmpty: {
                                message: 'Current Local Level Government is required',
                            },
                        },
                    },
                    temporary_ward: {
                        validators: {
                            notEmpty: {
                                message: 'Current Ward is required',
                            },
                            numeric: {
                                message: 'Current Ward No should be integer',
                            },
                            between: {
                                min: 1,
                                max: 33,
                                message: 'The ward number must be between 1 and 33',
                            },
                        },
                    },
                    temporary_tole: {
                        validators: {
                            notEmpty: {
                                message: 'Current Tole is required',
                            },
                        },
                    },
                    permanent_province_id: {
                        validators: {
                            notEmpty: {
                                message: 'Permanent Province is required',
                            },

                            callback: {
                                message: 'Please select a permanent province',
                                callback: function (value, validator, $field) {
                                    if ($('[name="validationcurrent"]').is(':checked')) {
                                        validator.updateStatus('permanent_province_id', validator.STATUS_VALID);
                                        return true;
                                    } else {
                                        return true;
                                    }
                                }
                            }
                        },
                    },
                    permanent_district_id: {
                        validators: {
                            notEmpty: {
                                message: 'Permanent District is required',
                            },
                            callback: {
                                message: 'Please select a permanent district',
                                callback: function (value, validator, $field) {
                                    if ($('[name="validationcurrent"]').is(':checked')) {
                                        validator.updateStatus('permanent_district_id', validator.STATUS_VALID);
                                        return true;
                                    } else {
                                        return true;
                                    }
                                }
                            }
                        },
                    },
                    permanent_local_level_id: {
                        validators: {
                            notEmpty: {
                                message: 'Permanent local level is required',
                            },
                            callback: {
                                message: 'Please select a local level',
                                callback: function (value, validator, $field) {
                                    if ($('[name="validationcurrent"]').is(':checked')) {
                                        validator.updateStatus('permanent_local_level_id', validator.STATUS_VALID);
                                        return true;
                                    } else {
                                        return true;
                                    }
                                }
                            }
                        },
                    },
                    permanent_ward: {
                        validators: {
                            notEmpty: {
                                message: 'Permanent Ward is required',
                            },
                            between: {
                                min: 1,
                                max: 33,
                                message: 'The ward number must be between 1 and 33',
                            },
                            callback: {
                                message: 'Please select a permanent ward',
                                callback: function (value, validator, $field) {
                                    if ($('[name="validationcurrent"]').is(':checked')) {
                                        validator.updateStatus('permanent_ward', validator.STATUS_VALID);
                                        return true;
                                    } else {
                                        return true;
                                    }
                                }
                            }
                        },
                    },
                    permanent_tole: {
                        validators: {
                            notEmpty: {
                                message: 'Permanent Tole is required',
                            },
                            callback: {
                                message: 'Please select a permanent ward',
                                callback: function (value, validator, $field) {
                                    if ($('[name="validationcurrent"]').is(':checked')) {
                                        validator.updateStatus('permanent_tole', validator.STATUS_VALID);
                                        return true;
                                    } else {
                                        return true;
                                    }
                                }
                            }
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

            $('#addressAddForm').on('change', '[name="temporary_province_id"]', function (e) {
                $element = $(this);
                var provinceId = $element.val();
                var htmlToReplace = '<option value="">Select a District</option>';
                if (provinceId) {
                    var url = baseUrl + '/api/master/provinces/' + provinceId;
                    var successCallback = function (response) {
                        response.districts.forEach(function (district) {
                            htmlToReplace += '<option value="' + district.id + '">' + district.district_name + '</option>';
                        });
                         $($element).closest('form').find('[name="temporary_district_id"]').html(htmlToReplace).trigger('change');
                         $($element).closest('form').find('[name="temporary_district_id"]').select2("destroy").select2();
                    }
                    var errorCallback = function (error) {
                        console.log(error);
                    }
                    ajaxNativeSubmit(url, 'GET', {}, 'json', successCallback, errorCallback);
                } else {
                    $($element).closest('form').find('[name="temporary_district_id"]').html(htmlToReplace);
                }
                fv.revalidateField('temporary_province_id');
            }).on('change', '[name="temporary_district_id"]', function (e) {
                $element = $(this);
                var districtId = $element.val();
                var htmlToReplace = '<option value="">Select a Local Level</option>';
                if (districtId) {
                    var url = baseUrl + '/api/master/districts/' + districtId;
                    var successCallback = function (response) {
                        response.localLevels.forEach(function (localLevel) {
                            htmlToReplace += '<option value="' + localLevel.id + '">' + localLevel.local_level_name + '</option>';
                        });
                        $($element).closest('form').find('[name="temporary_local_level_id"]').html(htmlToReplace).trigger('change');
                        $($element).closest('form').find('[name="temporary_local_level_id"]').select2("destroy").select2();
                    }
                    var errorCallback = function (error) {
                        console.log(error);
                    }
                    ajaxNativeSubmit(url, 'GET', {}, 'json', successCallback, errorCallback);
                } else {
                    $($element).closest('form').find('[name="temporary_local_level_id"]').html(htmlToReplace);
                }
                fv.revalidateField('temporary_district_id');
            }).on('change', '[name="temporary_local_level_id"]', function (e) {
                fv.revalidateField('temporary_local_level_id');
            }).on('change', '[name="permanent_province_id"]', function (e) {
                $element = $(this);
                var provinceId = $element.val();
                var htmlToReplace = '<option value="">Select a District</option>';
                if (provinceId) {
                    var url = baseUrl + '/api/master/provinces/' + provinceId;
                    var successCallback = function (response) {
                        response.districts.forEach(function (district) {
                            htmlToReplace += '<option value="' + district.id + '">' + district.district_name + '</option>';
                        });
                        $($element).closest('form').find('[name="permanent_district_id"]').html(htmlToReplace).trigger('change');
                        $($element).closest('form').find('[name="permanent_district_id"]').select2('destroy').select2();
                    }
                    var errorCallback = function (error) {
                        console.log(error);
                    }
                    ajaxNativeSubmit(url, 'GET', {}, 'json', successCallback, errorCallback);
                } else {
                    $($element).closest('form').find('[name="permanent_district_id"]').html(htmlToReplace);
                }
                fv.revalidateField('permanent_province_id');
            }).on('change', '[name="permanent_district_id"]', function (e) {
                $element = $(this);
                var districtId = $element.val();
                var htmlToReplace = '<option value="">Select a Local Level</option>';
                if (districtId) {
                    var url = baseUrl + '/api/master/districts/' + districtId;
                    var successCallback = function (response) {
                        response.localLevels.forEach(function (localLevel) {
                            htmlToReplace += '<option value="' + localLevel.id + '">' + localLevel.local_level_name + '</option>';
                        });
                        $($element).closest('form').find('[name="permanent_local_level_id"]').html(htmlToReplace).trigger('change');
                        $($element).closest('form').find('[name="permanent_local_level_id"]').select2('destroy').select2();
                    }
                    var errorCallback = function (error) {
                        console.log(error);
                    }
                    ajaxNativeSubmit(url, 'GET', {}, 'json', successCallback, errorCallback);
                } else {
                    $($element).closest('form').find('[name="permanent_local_level_id"]').html(htmlToReplace);
                }
                fv.revalidateField('permanent_district_id');
            }).on('change', '[name="permanent_local_level_id"]', function (e) {
                fv.revalidateField('permanent_local_level_id');
            });

            $('#validationcurrent').on('change', function (e) {
                fv.revalidateField('temporary_province_id');
                fv.revalidateField('temporary_district_id');
                fv.revalidateField('temporary_local_level_id');
                fv.revalidateField('temporary_ward');
                fv.revalidateField('temporary_tole');
                $element = $(this);
                var checked = $(this).is(':checked');
                var province2 = $("#validationprovince").val();
                var district2 = $('#validationdistrict').val();
                var locallevel2 = $('#validationmunicipality').val();
                var ward2 = $('#validationward').val();
                var tole2 = $('#validationtole').val();
                if (checked) {
                    var htmltoappend = '<input name="permanent_province_id" id="permanent_province_id" class="inputs" value="' + province2 + '" hidden>' +
                        '<input name="permanent_district_id" id="permanent_district_id" class="inputs" value="' + district2 + '" hidden>' +
                        '<input name="permanent_local_level_id" id="permanent_local_level_id" class="inputs" value="' + locallevel2 + '" hidden>' +
                        '<input name="permanent_ward" id="permanent_ward" class="inputs" value="' + ward2 + '" hidden>' +
                        '<input name="permanent_tole" id="permanent_tole" class="inputs" value="' + tole2 + '" hidden>';
                    $('.currentchecked').hide();
                    $('.append_input').append(htmltoappend);
                } else {
                    fv.revalidateField('permanent_province_id');
                    fv.revalidateField('permanent_district_id');
                    fv.revalidateField('permanent_local_level_id');
                    fv.revalidateField('permanent_ward');
                    fv.revalidateField('permanent_tole');
                    $('.append_input').remove();
                    $('.currentchecked').show();
                }
            });
        });
    </script>
@endpush
