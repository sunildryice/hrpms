@extends('layouts.container')

@section('title', 'Edit Advance Request')

@section('page_js')
    <script type="text/javascript">
        document.addEventListener('DOMContentLoaded', function(e) {
            $('#navbarVerticalMenu').find('#advance-requests-menu').addClass('active');

            const form = document.getElementById('advanceRequestEditForm');
            const fv = FormValidation.formValidation(form, {
                fields: {
                    // district_id: {
                    //     validators: {
                    //         notEmpty: {
                    //             message: 'District is required',
                    //         },
                    //     },
                    // },
                    request_for_office_id: {
                        validators: {
                            notEmpty: {
                                message: 'Office is required',
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
                                message: 'The start date is required',
                            },
                        },
                    },
                    settlement_date: {
                        validators: {
                            notEmpty: {
                                message: 'The settlement date is required',
                            },
                        },
                    },
                    end_date: {
                        validators: {
                            notEmpty: {
                                message: 'The end date is required',
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

            $(form).on('change', '[name="district_id"]', function(e) {
                fv.revalidateField('district_id');
            });

            $(form).on('change', '[name="activity_code_id"]', function(e) {
                fv.revalidateField('activity_code_id');
            }).on('change', '[name="account_code_id"]', function(e) {
                fv.revalidateField('account_code_id');
            });


            $('[name="required_date"]').datepicker({
                language: 'en-GB',
                autoHide: true,
                format: 'yyyy-mm-dd',
                startDate: '{{ date('Y-m-d', strtotime(date('Y-m-d') . ' + 1 days')) }}',
            }).on('change', function(e) {
                fv.revalidateField('required_date');
            });

            $('[name="start_date"]').datepicker({
                language: 'en-GB',
                autoHide: true,
                format: 'yyyy-mm-dd',
                startDate: '{{ date('Y-m-d', strtotime(date('Y-m-d') . ' + 1 days')) }}',
            }).on('change', function(e) {
                fv.revalidateField('start_date');
            });

            $('[name="end_date"]').datepicker({
                language: 'en-GB',
                autoHide: true,
                format: 'yyyy-mm-dd',
                startDate: '{{ date('Y-m-d', strtotime(date('Y-m-d') . ' + 1 days')) }}',
            }).on('change', function(e) {
                fv.revalidateField('end_date');
            });

            $('[name="settlement_date"]').datepicker({
                language: 'en-GB',
                autoHide: true,
                format: 'yyyy-mm-dd',
                startDate: '{{ date('Y-m-d', strtotime(date('Y-m-d') . ' + 1 days')) }}',
            }).on('change', function(e) {
                fv.revalidateField('settlement_date');
            });
        });

        var oTable = $('#advanceRequestDetailsTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('advance.requests.details.index', $advanceRequest->id) }}",
            bFilter: false,
            bPaginate: false,
            bInfo: false,
            columns: [{
                    data: 'activity',
                    name: 'activity'
                },
                {
                    data: 'account',
                    name: 'account'
                },
                {
                    data: 'donor',
                    name: 'donor'
                },
                {
                    data: 'description',
                    name: 'description'
                },
                {
                    data: 'amount',
                    name: 'amount'
                },
                {
                    data: 'attachment',
                    name: 'attachment'
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false,
                    className: 'sticky-col'
                },
            ]
        });

        $('#advanceRequestDetailsTable').on('click', '.delete-record', function(e) {
            e.preventDefault();
            $object = $(this);
            var $url = $object.attr('data-href');
            var successCallback = function(response) {
                toastr.success(response.message, 'Success', {
                    timeOut: 5000
                });
                if (response.advanceDetailCount) {
                    $('.submit-record').show();
                } else {
                    $('.submit-record').hide();
                }
                oTable.ajax.reload();
            }
            ajaxDeleteSweetAlert($url, successCallback);
        });

        $(document).on('click', '.open-advance-detail-modal-form', function(e) {
            e.preventDefault();
            $('#openModal').find('.modal-content').html('');
            $('#openModal').modal('show').find('.modal-content').load($(this).attr('href'), function (){
                const form = document.getElementById('advanceRequestDetailForm');
                $(form).find(".select2").each(function() {
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
                        // donor_code_id: {
                        //     validators: {
                        //         notEmpty: {
                        //             message: 'Donor code is required',
                        //         },
                        //     },
                        // },
                        description: {
                            validators: {
                                notEmpty: {
                                    message: 'Description is required',
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
                        attachment: {
                            validators: {
                                file: {
                                    extension: 'jpeg,jpg,png,pdf',
                                    type: 'image/jpeg,image/png,application/pdf',
                                    maxSize: '2097152',
                                    message: 'The selected file is not valid image or pdf or must not be greater than 2 MB.',
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
                }).on('core.form.valid', function(event) {
                    $url = fv.form.action;
                    $form = fv.form;
                    data = $($form).serialize();
                    var formData = new FormData();

                    $('#advanceRequestDetailForm input, #advanceRequestDetailForm select, #advanceRequestDetailForm textarea')
                        .each(function(index) {
                            var input = $(this);
                            formData.append(input.attr('name'), input.val());
                        });
                    var attachmentFiles = advanceRequestDetailForm.querySelector('[name="attachment"]').files;
                    if (attachmentFiles.length > 0) {
                        formData.append('attachment', attachmentFiles[0]);
                    }

                    var successCallback = function(response) {
                        $('#openModal').modal('hide');
                        toastr.success(response.message, 'Success', {
                            timeOut: 5000
                        });
                        if (response.advanceDetailCount) {
                            $('.submit-record').show();
                        } else {
                            $('.submit-record').hide();
                        }
                        oTable.ajax.reload();
                    }
                    ajaxSubmitFormData($url, 'POST', formData, successCallback);
                });

                $(form).on('change', '[name="activity_code_id"]', function(e) {
                    $element = $(this);
                    var activityCodeId = $element.val();
                    var htmlToReplace = '<option value="">Select Account Code</option>';
                    if (activityCodeId) {
                        var url = baseUrl + '/api/master/activity-codes/' + activityCodeId;
                        var successCallback = function(response) {
                            response.accountCodes.forEach(function(accountCode) {
                                htmlToReplace += '<option value="' + accountCode.id + '">' +
                                    accountCode.title + ' ' + accountCode.description + '</option>';
                            });
                            $($element).closest('form').find('[name="account_code_id"]').html(htmlToReplace)
                                .trigger('change');
                        }
                        var errorCallback = function(error) {
                            console.log(error);
                        }
                        ajaxNativeSubmit(url, 'GET', {}, 'json', successCallback, errorCallback);
                    } else {
                        $($element).closest('form').find('[name="account_code_id"]').html(htmlToReplace);
                    }
                    fv.revalidateField('activity_code_id');
                    fv.revalidateField('account_code_id');
                }).on('change', '[name="account_code_id"]', function(e) {
                    fv.revalidateField('account_code_id');
                }).on('click', '#delete-attachment', function(e) {
                    e.preventDefault();
                    $object = $(this);
                    var $url = $object.attr('data-href');
                    var successCallback = function(response) {
                        toastr.success(response.message, 'Success', {
                            timeOut: 5000
                        });
                        $('#openModal').modal('hide');
                        oTable.ajax.reload();
                    }
                    ajaxDeleteSweetAlert($url, successCallback);
                });
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
                                        Request</a>
                                </li>
                                <li class="breadcrumb-item" aria-current="page">@yield('title')</li>
                            </ol>
                        </nav>
                        <h4 class="m-0 lh1 mt-1 fs-6 text-uppercase fw-bold text-primary">@yield('title')</h4>
                    </div>
                </div>
            </div>

            <section class="registration">
                    <form action="{{ route('advance.requests.update', $advanceRequest->id) }}"
                        id="advanceRequestEditForm" method="post" enctype="multipart/form-data" autocomplete="off">
                        <div class="card">
                            <div class="card-body">
                                <div class="row mb-2">
                                    <div class="col-lg-3">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="validationpurchasetype"
                                                class="form-label required-label">Project</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-9">
                                        @php $selectedProjectId = $advanceRequest->project_code_id; @endphp
                                        <select class="select2 form-control" name="project_code_id">
                                            <option value="">Select a Project</option>
                                            @foreach ($projects as $project)
                                                <option value="{{ $project->id }}"
                                                    {{ $project->id == $selectedProjectId ? 'selected' : '' }}>
                                                    {{ $project->getProjectCode() }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @if ($errors->has('project_id'))
                                            <div class="fv-plugins-message-container invalid-feedback">
                                                <div data-field="project_id">
                                                    {!! $errors->first('project_id') !!}
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-lg-3">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="validationdd" class="form-label required-label">Required
                                                Date</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-9">
                                        <input type="text"
                                            class="form-control
                                            @if ($errors->has('required_date')) is-invalid @endif"
                                            readonly name="required_date"
                                            value="{{ old('required_date') ?: $advanceRequest->required_date->format('Y-m-d') }}" />
                                        @if ($errors->has('required_date'))
                                            <div class="fv-plugins-message-container invalid-feedback">
                                                <div data-field="required_date">{!! $errors->first('required_date') !!}</div>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <div class="row mb-2">
                                    <div class="col-lg-12">
                                        <label for="validationRemarks" class="form-label">Activity Start and End
                                            Date</label>
                                    </div>
                                </div>

                                <div class="row mb-2">
                                    <div class="col-lg-3">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="validationdd" class="form-label required-label">Start Date</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <input class="form-control @if ($errors->has('start_date')) is-invalid @endif"
                                            type="text" readonly name="start_date"
                                            value="{{ old('start_date') ?: $advanceRequest->start_date->format('Y-m-d') }}" />
                                        @if ($errors->has('start_date'))
                                            <div class="fv-plugins-message-container invalid-feedback">
                                                <div data-field="start_date">
                                                    {!! $errors->first('start_date') !!}
                                                </div>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="col-lg-3">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="validationdd" class="form-label required-label">Tentative
                                                Settlement Date</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <input class="form-control @if ($errors->has('settlement_date')) is-invalid @endif"
                                            type="text" readonly name="settlement_date"
                                            value="{{ old('settlement_date') ?: $advanceRequest->settlement_date->format('Y-m-d') }}" />
                                        @if ($errors->has('settlement_date'))
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
                                        <input class="form-control @if ($errors->has('end_date')) is-invalid @endif"
                                            type="text" readonly name="end_date"
                                            value="{{ old('end_date') ?: $advanceRequest->end_date->format('Y-m-d') }}" />
                                        @if ($errors->has('end_date'))
                                            <div class="fv-plugins-message-container invalid-feedback">
                                                <div data-field="end_date">
                                                    {!! $errors->first('end_date') !!}
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                    {{-- <div class="col-lg-3">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="validationdd" class="form-label required-label">District</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        @php $selectedDistrictId = $advanceRequest->district_id; @endphp
                                        <select name="district_id" class="select2 form-control" data-width="100%">
                                            <option value="">Select a District</option>
                                            @foreach ($districts as $district)
                                                <option value="{{ $district->id }}"
                                                    data-purchase="{{ $district->id }}"
                                                    {{ $district->id == $selectedDistrictId ? 'selected' : '' }}>
                                                    {{ $district->getDistrictName() }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @if ($errors->has('district_id'))
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
                                        @php $selectedOfficeId = $advanceRequest->request_for_office_id; @endphp
                                        <select name="request_for_office_id" class="select2 form-control" data-width="100%">
                                            <option value="">Select an Office</option>
                                            @foreach ($offices as $office)
                                                <option value="{{ $office->id }}"
                                                    data-purchase="{{ $office->id }}"
                                                    {{ $office->id == $selectedOfficeId ? 'selected' : '' }}>
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
                                        <textarea type="text" class="form-control @if ($errors->has('purpose')) is-invalid @endif" name="purpose">{{ old('purpose') ?: $advanceRequest->purpose }}</textarea>
                                        @if ($errors->has('purpose'))
                                            <div class="fv-plugins-message-container invalid-feedback">
                                                <div data-field="purpose">
                                                    {!! $errors->first('purpose') !!}
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <div class="row mb-2">
                                    <div class="col-lg-3">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="validationRemarks" class="form-label required-label">Verifier</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        @php $selectedVerifierId = old('verifier_id') ?: $advanceRequest->verifier_id; @endphp
                                        <select name="verifier_id"
                                            class="select2 form-control
                                            @if ($errors->has('verifier_id')) is-invalid @endif"
                                            data-width="100%">
                                            <option value="">Select Verifier</option>
                                            @foreach ($verifiers as $verifier)
                                                <option value="{{ $verifier->id }}"
                                                    {{ $verifier->id == $selectedVerifierId ? 'selected' : '' }}>
                                                    {{ $verifier->full_name }}</option>
                                            @endforeach
                                        </select>
                                        @if ($errors->has('verifier_id'))
                                            <div class="fv-plugins-message-container invalid-feedback">
                                                <div data-field="verifier_id">
                                                    {!! $errors->first('verifier_id') !!}
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="validationRemarks" class="form-label required-label">Approver</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        @php $selectedApproverId = old('approver_id') ?: $advanceRequest->approver_id; @endphp
                                        <select name="approver_id"
                                                class="select2 form-control
                                            @if ($errors->has('approver_id')) is-invalid @endif"
                                                data-width="100%">
                                            <option value="">Select Approver</option>
                                            @foreach ($approvers as $approver)
                                                <option value="{{ $approver->id }}"
                                                    {{ $approver->id == $selectedApproverId ? 'selected' : '' }}>
                                                    {{ $approver->getFullName() }}</option>
                                            @endforeach
                                        </select>
                                        @if ($errors->has('approver_id'))
                                            <div class="fv-plugins-message-container invalid-feedback">
                                                <div data-field="approver_id">
                                                    {!! $errors->first('approver_id') !!}
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                {!! csrf_field() !!}
                                {!! method_field('PUT') !!}
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header fw-bold">
                                <div class="d-flex align-items-center add-info justify-content-between">
                                    <span> Details</span>
                                    @if ($authUser->can('update', $advanceRequest))
                                        <button data-toggle="modal"
                                            class="btn btn-primary btn-sm open-advance-detail-modal-form"
                                            href="{!! route('advance.requests.details.create', $advanceRequest->id) !!}"><i class="bi-plus"></i> Add
                                            Advance Detail
                                        </button>
                                    @endif
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table" id="advanceRequestDetailsTable">
                                        <thead class="thead-light">
                                            <tr>
                                                <th scope="col">Activity</th>
                                                <th scope="col">Account Code</th>
                                                <th scope="col">{{ __('label.donor') }}</th>
                                                <th scope="col">Description</th>
                                                <th scope="col">Amount</th>
                                                <th scope="col">Attachment</th>
                                                <th style="width: 150px" class="sticky-col">{{ __('label.action') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                        </div>


                        <div class="justify-content-end d-flex gap-2">
                            <button type="submit" name="btn" value="save"
                                class="btn btn-primary btn-sm">Update
                            </button>
                            <button type="submit" name="btn" value="submit"
                                class="btn btn-success btn-sm submit-record"
                                @if (!$authUser->can('submit', $advanceRequest)) style="display:none;" @endif>
                                Submit
                            </button>
                            <a href="{!! route('advance.requests.index') !!}" class="btn btn-danger btn-sm">Cancel</a>
                        </div>
                    </form>
            </section>

@stop
