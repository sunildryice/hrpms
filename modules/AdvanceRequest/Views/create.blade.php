@extends('layouts.container')

@section('title', 'Add New Advance Request')

@section('page_js')
    <script type="text/javascript">
        document.addEventListener('DOMContentLoaded', function (e) {
            $('#navbarVerticalMenu').find('#advance-requests-menu').addClass('active');


            const form = document.getElementById('advanceRequestAddForm');
            const fv = FormValidation.formValidation(form, {
                fields: {
                    project_code_id: {
                        validators: {
                            notEmpty: {
                                message: 'Project is required',
                            },
                        },
                    },
                    required_date: {
                        validators: {
                            notEmpty: {
                                message: 'The required date is required',
                            },
                        },
                    },
                     start_date: {
                        validators: {
                            notEmpty: {
                                message: 'The Start date is required',
                            },
                        },
                    },
                    settlement_date: {
                        validators: {
                            notEmpty: {
                                message: 'The Settlement Date is required',
                            },
                        },
                    },
                    end_date: {
                        validators: {
                            notEmpty: {
                                message: 'The End Date is required',
                            },
                        },
                    },
                    purpose: {
                        validators: {
                            notEmpty: {
                                message: 'The purpose is required',
                            },
                        },
                    },
                    request_for_office_id: {
                        validators: {
                            notEmpty: {
                                message: 'The office is required',
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

                     startEndDate: new FormValidation.plugins.StartEndDate({
                        format: 'YYYY-MM-DD',
                        startDate: {
                            field: 'start_date',
                            message: 'Start date must be a valid date and earlier than End date.',
                        },
                        endDate: {
                            field: 'end_date',
                            message: 'End date must be a valid date and later than Start date.',
                        },
                    }),


                },
            }).on('change', '[name="project_code_id"]', function(e){
               fv.revalidateField('project_code_id');
            });

            $('[name="required_date"]').datepicker({
                language: 'en-GB',
                autoHide: true,
                format: 'yyyy-mm-dd',
                startDate: '{{ date('Y-m-d', strtotime(date('Y-m-d'). ' + 1 days')) }}',
            }).on('change', function (e) {
                fv.revalidateField('required_date');
            });

            $('[name="start_date"]').datepicker({
                language: 'en-GB',
                autoHide: true,
                format: 'yyyy-mm-dd',
                startDate: '{{ date('Y-m-d', strtotime(date('Y-m-d'). ' + 1 days')) }}',
            }).on('change', function (e) {
                fv.revalidateField('start_date');
            });

            $('[name="end_date"]').datepicker({
                language: 'en-GB',
                autoHide: true,
                format: 'yyyy-mm-dd',
                startDate: '{{ date('Y-m-d', strtotime(date('Y-m-d'). ' + 1 days')) }}',
            }).on('change', function (e) {
                fv.revalidateField('end_date');
            });

            $('[name="settlement_date"]').datepicker({
                language: 'en-GB',
                autoHide: true,
                format: 'yyyy-mm-dd',
                startDate: '{{ date('Y-m-d', strtotime(date('Y-m-d'). ' + 1 days')) }}',
            }).on('change', function (e) {
                fv.revalidateField('settlement_date');
            });

        });

        $(document).on('shown.bs.modal', '#openModal', function (e) {
            const form = document.getElementById('advanceRequestDetailForm');
            $(form).find(".select2").each(function () {
                    $(this)
                        .wrap("<div class=\"position-relative\"></div>")
                        .select2({
                            dropdownParent: $(this).parent(),
                            width: '100%',
                            dropdownAutoWidth: true
                        });
                });
            const fv = FormValidation.formValidation(form, {
                fields: {
                    activity_code_id: {
                        validators: {
                            notEmpty: {
                                message: 'Activity code is required',
                            },
                        },
                    },
                    account_code_id: {
                        validators: {
                            notEmpty: {
                                message: 'Account code is required',
                            },
                        },
                    },
                    donor_code_id: {
                        validators: {
                            notEmpty: {
                                message: 'Donor code is required',
                            },
                        },
                    },
                    description: {
                        validators: {
                            notEmpty: {
                                message: 'Description code is required',
                            },
                        },
                    },
                    amount: {
                        validators: {
                            notEmpty: {
                                message: 'Amount is required',
                            },
                            greaterThan: {
                                message: 'The value must be greater than or equal to 0.01',
                                min: 0.01,
                            },
                        },
                    },
                },
                plugins: {
                    trigger: new FormValidation.plugins.Trigger(),
                    bootstrap5: new FormValidation.plugins.Bootstrap5(),
                    submitButton: new FormValidation.plugins.SubmitButton(),
                    icon: new FormValidation.plugins.Icon({
                        valid: 'bi bi-check2-square',
                        invalid: 'bi bi-x-lg',
                        validating: 'bi bi-arrow-repeat',
                    }),
                },
            }).on('core.form.valid', function (event) {
                $url = fv.form.action;
                $form = fv.form;
                data = $($form).serialize();
                var successCallback = function (response) {
                    $('#openModal').modal('hide');
                    toastr.success(response.message, 'Success', {timeOut: 5000});
                    oTable.ajax.reload();
                }
                ajaxSubmit($url, 'POST', data, successCallback);
            });
        });
    </script>
@endsection
@section('page-content')


            <div class="page-header pb-3 mb-3 border-bottom">
                <div class="d-flex align-items-center">
                    <div class="brd-crms flex-grow-1">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item">
                                    <a href="{!! route('dashboard.index') !!}" class="text-decoration-none text-dark">Home</a>
                                </li>
                                <li class="breadcrumb-item">
                                    <a href="{{ route('advance.requests.index') }}" class="text-decoration-none text-dark">Advance
                                        Requests</a>
                                </li>
                                <li class="breadcrumb-item" aria-current="page">@yield('title')</li>
                            </ol>
                        </nav>
                        <h4 class="m-0 lh1 mt-1 fs-6 text-uppercase fw-bold text-primary">@yield('title')</h4>
                    </div>
                </div>
            </div>

            <section class="registration">
                <div class="card">
                    <form action="{{ route('advance.requests.store') }}" id="advanceRequestAddForm" method="post"
                          enctype="multipart/form-data" autocomplete="off">
                        <div class="card-body">
                            <div class="row mb-2">
                                <div class="col-lg-3">
                                    <div class="d-flex align-items-start h-100">
                                        <label for="validationpurchasetype" class="form-label required-label">Project</label>
                                    </div>
                                </div>
                                <div class="col-lg-9">
                                    <select name="project_code_id" class="select2 form-control" data-width="100%">
                                        <option value="">Select a Project</option>
                                        @foreach($projects as $project)
                                            <option value="{{ $project->id }}" data-advance="{{ $project->id }}"
                                                {{ $project->id == old('project_code_id')? "selected":"" }}>
                                                {{ $project->getProjectCode() }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @if($errors->has('project_code_id'))
                                        <div class="fv-plugins-message-container invalid-feedback">
                                            <div data-field="project_code_id">
                                                {!! $errors->first('project_code_id') !!}
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="row mb-2">
                                <div class="col-lg-3">
                                    <div class="d-flex align-items-start h-100">
                                        <label for="validationdd" class="form-label required-label">Required Date</label>
                                    </div>
                                </div>
                                <div class="col-lg-9">
                                    <input class="form-control @if($errors->has('required_date')) is-invalid @endif"
                                           type="text" readonly name="required_date" value="{{ old('required_date') }}"/>
                                    @if($errors->has('required_date'))
                                        <div class="fv-plugins-message-container invalid-feedback">
                                            <div data-field="required_date">
                                                {!! $errors->first('required_date') !!}
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="row mb-2">
                                <div class="col-lg-12">
                                        <label for="validationRemarks" class="form-label">Activity Start and End Date</label>
                                </div>
                            </div>

                              <div class="row mb-2">
                                <div class="col-lg-3">
                                    <div class="d-flex align-items-start h-100">
                                        <label for="validationdd" class="form-label required-label">Start  Date</label>
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <input class="form-control @if($errors->has('start_date')) is-invalid @endif"
                                           type="text" readonly name="start_date" value="{{ old('start_date') }}"/>
                                    @if($errors->has('start_date'))
                                        <div class="fv-plugins-message-container invalid-feedback">
                                            <div data-field="start_date">
                                                {!! $errors->first('start_date') !!}
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                 <div class="col-lg-3">
                                    <div class="d-flex align-items-start h-100">
                                        <label for="validationdd" class="form-label required-label">Tentative Settlement Date</label>
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <input class="form-control @if($errors->has('settlement_date')) is-invalid @endif"
                                           type="text" readonly name="settlement_date" value="{{ old('settlement_date') }}"/>
                                    @if($errors->has('settlement_date'))
                                        <div class="fv-plugins-message-container invalid-feedback">
                                            <div data-field="settlement_date">
                                                {!! $errors->first('settlement_date') !!}
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>



                              <div class="row mb-2">
                                <div class="col-lg-3">
                                    <div class="d-flex align-items-start h-100">
                                        <label for="validationdd" class="form-label required-label">End Date</label>
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <input class="form-control @if($errors->has('end_date')) is-invalid @endif"
                                           type="text" readonly name="end_date" value="{{ old('end_date') }}"/>
                                    @if($errors->has('end_date'))
                                        <div class="fv-plugins-message-container invalid-feedback">
                                            <div data-field="end_date">
                                                {!! $errors->first('end_date') !!}
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                {{-- <div class="col-lg-3">
                                    <div class="d-flex align-items-start h-100">
                                        <label for="validationdd" class="m-0">District</label>
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <select name="district_id" class="select2 form-control" data-width="100%">
                                        <option value="">Select a District</option>
                                        @foreach($districts as $district)
                                            <option value="{{ $district->id }}" data-purchase="{{ $district->id }}"
                                                {{ $district->id == old('district_id')? "selected":"" }}>
                                                {{ $district->getDistrictName() }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @if($errors->has('district_id'))
                                        <div class="fv-plugins-message-container invalid-feedback">
                                            <div data-field="district_id">
                                                {!! $errors->first('district_id') !!}
                                            </div>
                                        </div>
                                    @endif
                                </div> --}}

                                <div class="col-lg-3">
                                    <div class="d-flex align-items-start h-100">
                                        <label for="validationdd" class="form-label required-label">Office</label>
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <select name="request_for_office_id" class="select2 form-control" data-width="100%">
                                        <option value="">Select an Office</option>
                                        @foreach ($offices as $office)
                                            <option value="{{ $office->id }}"
                                                data-purchase="{{ $office->id }}"
                                                {{ $office->id == old('request_for_office_id') ? 'selected' : '' }}>
                                                {{ $office->getOfficeName() }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('request_for_office_id'))
                                        <div class="fv-plugins-message-container invalid-feedback">
                                            <div data-field="request_for_office_id">
                                                {!! $errors->first('request_for_office_id') !!}
                                            </div>
                                        </div>
                                    @endif
                                </div>

                            </div>


                            <div class="row mb-2">
                                <div class="col-lg-3">
                                    <div class="d-flex align-items-start h-100">
                                        <label for="validationdd" class="form-label required-label">Purpose</label>
                                    </div>
                                </div>
                                <div class="col-lg-9">
                                    <textarea type="text"
                                              class="form-control @if($errors->has('purpose')) is-invalid @endif"
                                              name="purpose">{{ old('purpose') }}</textarea>
                                    @if($errors->has('purpose'))
                                        <div class="fv-plugins-message-container invalid-feedback">
                                            <div data-field="purpose">
                                                {!! $errors->first('purpose') !!}
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>


                            {!! csrf_field() !!}
                        </div>
                        <div class="card-footer border-0 justify-content-end d-flex gap-2">
                            <button type="submit" name="btn" value="save" class="btn btn-primary btn-sm">Save
                            </button>
                            <a href="{!! route('advance.requests.index') !!}"
                               class="btn btn-danger btn-sm">Cancel</a>
                        </div>
                    </form>
                </div>
            </section>

@stop
