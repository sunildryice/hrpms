@extends('layouts.container')

@section('title', 'Edit Mfr')

@section('page_js')
    <script>
        console.log(@json($errors->all()))
        const agreement = @json($agreement);
        const effectiveTo = @json($agreement->effective_to->format('Y-m-d'));
        const approvedBudget = @json($agreement->approved_budget);
        $(document).ready(function() {
            $('#navbarVerticalMenu').find('#agreement-index').addClass('active');
        });
        document.addEventListener('DOMContentLoaded', function(e) {
            // $("#substitutes").select2({
            //     width: '100%',
            //     dropdownAutoWidth: true
            // });
            const form = document.getElementById('eventCompletionAddForm');
            const fv = FormValidation.formValidation(form, {
                fields: {
                    partner_organization_id: {
                        validators: {
                            notEmpty: {
                                message: 'The Partner organization is required',
                            },
                        },
                    },
                    district_id: {
                        validators: {
                            notEmpty: {
                                message: 'The District is required',
                            },
                        },
                    },
                    project_id: {
                        validators: {
                            notEmpty: {
                                message: 'The proect is required',
                            },
                        },
                    },
                    grant_number: {
                        validators: {
                            notEmpty: {
                                message: 'The Grant Agreement Number is required',
                            },
                        },
                    },
                    effective_from: {
                        validators: {
                            notEmpty: {
                                message: 'Agreement Period is required',
                            },
                            date: {
                                format: 'YYYY-MM-DD',
                                message: 'The value is not a valid date',
                            },
                        },
                    },
                    effective_to: {
                        validators: {
                            notEmpty: {
                                message: 'Agreement Period is required',
                            },
                            date: {
                                format: 'YYYY-MM-DD',
                                message: 'The value is not a valid date',
                            },
                            callback: {
                                message: 'The End date must be greater than Start date',
                                callback: function(value, validator, field) {
                                    const startDate = new Date(form.querySelector(
                                            '[name="effective_from"]')
                                        .value);
                                    return new Date(value.value) >= startDate;
                                },
                            }
                        },
                    },
                    approved_budget: {
                        validators: {
                            notEmpty: {
                                message: 'The approved budget is required',
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

            $(form.querySelector('[name="effective_from"]')).datepicker({
                language: 'en-GB',
                autoHide: true,
                format: 'yyyy-mm-dd',
            }).on('change', function(e) {
                fv.revalidateField('effective_from');
                if (form.querySelector('[name="effective_to"]').value) {
                    fv.revalidateField('effective_to');
                }
            });

            $(form.querySelector('[name="effective_to"]')).datepicker({
                language: 'en-GB',
                autoHide: true,
                format: 'yyyy-mm-dd',
            }).on('change', function(e) {
                fv.revalidateField('effective_to');
            });


            $(form).on('change', '[name="district_id"]', function(e) {
                fv.revalidateField('district_id');
            });
            $(form).on('change', '[name="activity_code_id"]', function(e) {
                fv.revalidateField('activity_code_id');
            });
        });

        var oTable = $('#amendmentTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('mfr.agreement.amendment.index', $agreement->id) }}",
            bFilter: false,
            bPaginate: false,
            bInfo: false,
            columns: [{
                    data: 'effective_date',
                    name: 'effective_date'
                },
                {
                    data: 'extension_to_date',
                    name: 'extension_to_date'
                },
                {
                    data: 'total_estimate_cost',
                    name: 'total_estimate_cost'
                },
                {
                    data: 'attachment',
                    name: 'attachment',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                },
            ]
        });

        function disableAgreementField(flag, amendment = null, extensionDate = null) {
            $('.original-agreement').attr('disabled', flag);
            $('[name="effective_to"]').val(extensionDate ?? effectiveTo);
            $('[name="approved_budget"]').val(amendment ? amendment.approved_budget : approvedBudget);
        }

        $('#amendmentTable').on('click', '.delete-record', function(e) {
            e.preventDefault();
            $object = $(this);
            var $url = $object.attr('data-href');
            var successCallback = function(response) {
                toastr.success(response.message, 'Success', {
                    timeOut: 5000
                });
                disableAgreementField(response.amendmentCount ? true : false, response.amendment, response
                    .extensionDate);
                oTable.ajax.reload();
            }
            ajaxDeleteSweetAlert($url, successCallback);
        });

        $(document).on('click', '.amendment-form', function(e) {
            e.preventDefault();
            $('#openModal').find('.modal-content').html('');
            $('#openModal').modal('show').find('.modal-content').load($(this).attr('href'), function() {
                const form = document.getElementById('amendmentForm');
                const fv = FormValidation.formValidation(form, {
                    fields: {
                        effective_date: {
                            validators: {
                                notEmpty: {
                                    message: 'Effective date is required.',
                                },
                                date: {
                                    format: 'YYYY-MM-DD',
                                    message: 'The value is not a valid date',
                                },
                            },
                        },
                        // extension_to_date: {
                        //     validators: {
                        //         notEmpty: {
                        //             message: 'Extension to date is required.',
                        //         },
                        //         date: {
                        //             format: 'YYYY-MM-DD',
                        //             message: 'The value is not a valid date',
                        //         },
                        //     },
                        // },
                        approved_budget: {
                            validators: {
                                notEmpty: {
                                    message: 'Approved Budget is required',
                                },
                            },
                        },
                        // attachment: {
                        //     validators: {
                        //         notEmpty: {
                        //             message: 'The attachment is required.',
                        //         },
                        //         file: {
                        //             extension: 'jpeg,jpg,png,pdf',
                        //             type: 'image/jpeg,image/png,application/pdf',
                        //             maxSize: '5097152',
                        //             message: 'The selected file is not valid file or must not be greater than 5 MB.',
                        //         },
                        //     },
                        // },
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
                    let form = document.getElementById('amendmentForm');
                    let data = new FormData(form);

                    let url = form.getAttribute('action');
                    var successCallback = function(response) {
                        $('#openModal').modal('hide');
                        toastr.success(response.message, 'Success', {
                            timeOut: 5000
                        });
                        disableAgreementField(response.amendmentCount ? true : false, response
                            .amendment, response.extensionDate);
                        oTable.ajax.reload();
                    }
                    ajaxSubmitFormData(url, 'POST', data, successCallback);

                });
                $('[name="effective_date"]').datepicker({
                    language: 'en-GB',
                    autoHide: true,
                    format: 'yyyy-mm-dd',
                    zIndex: 2048,
                });
                $('[name="extension_to_date"]').datepicker({
                    language: 'en-GB',
                    autoHide: true,
                    format: 'yyyy-mm-dd',
                    zIndex: 2048,
                });
            });

        });
    </script>

@endsection

@section('page-content')



    <div class="pb-3 mb-3 page-header border-bottom">
        <div class="d-flex align-items-center">
            <div class="brd-crms flex-grow-1">
                <nav aria-label="breadcrumb">
                    <ol class="m-0 breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{!! route('dashboard.index') !!}" class="text-decoration-none text-dark">Home</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('mfr.agreement.index') }}" class="text-decoration-none text-dark">MFR</a>
                        </li>
                        <li class="breadcrumb-item" aria-current="page">@yield('title')</li>
                    </ol>
                </nav>
                <h4 class="m-0 mt-1 lh1 fs-6 text-uppercase fw-bold text-primary">@yield('title')</h4>
            </div>
            <div class="add-info justify-content-end">
                <a class="btn btn-primary btn-sm" href="{!! route('mfr.agreement.show.transactions', [$agreement->id]) !!}">
                    <i class="bi-eye"></i> View Transactions
                </a>
            </div>
        </div>
    </div>

    <section class="registration">
        <div class="card">
            <div class="card-header fw-bold">Partner Organization (PO) Fund Release/ MFR Approval</div>
            <form action="{{ route('mfr.agreement.update', $agreement->id) }}" id="eventCompletionAddForm" method="post"
                enctype="multipart/form-data" autocomplete="off">
                @method('PUT')

                <div class="card-body">

                    <div class="mb-2 row">
                        <div class="col-lg-3">
                            <div class="d-flex align-items-start h-100">
                                <label for="validationdistrict" class="form-label required-label">Partner organization
                                </label>
                            </div>
                        </div>
                        <div class="col-lg-9">
                            <select name="partner_organization_id" class="select2 form-control" data-width="100%">
                                <option value="">Select a Partner organization</option>
                                @foreach ($partnerOrganizations as $partner)
                                    <option value="{{ $partner->id }}"
                                        {{ $partner->id == (old('partner_organization_id') ?? $agreement->partner_organization_id) ? 'selected' : '' }}>
                                        {{ $partner->name }}
                                    </option>
                                @endforeach
                            </select>
                            @if ($errors->has('partner_organization_id'))
                                <div class="fv-plugins-message-container invalid-feedback">
                                    <div data-field="partner_organization_id">
                                        {!! $errors->first('partner_organization_id') !!}
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="mb-2 row">
                        <div class="col-lg-3">
                            <div class="d-flex align-items-start h-100">
                                <label for="validationdistrict" class="form-label required-label">District
                                </label>
                            </div>
                        </div>
                        <div class="col-lg-9">
                            <select name="district_id" class="select2 form-control" data-width="100%">
                                <option value="">Select a District</option>
                                @foreach ($districts as $district)
                                    <option value="{{ $district->id }}"
                                        {{ $district->id == (old('district_id') ?? $agreement->district_id) ? 'selected' : '' }}>
                                        {{ $district->district_name }}
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
                        </div>
                    </div>

                    <div class="mb-2 row">
                        <div class="col-lg-3">
                            <div class="d-flex align-items-start h-100">
                                <label for="validationdistrict" class="form-label required-label">Project
                                </label>
                            </div>
                        </div>
                        <div class="col-lg-9">
                            <select name="project_id" class="select2 form-control" data-width="100%">
                                <option value="">Select a Project</option>
                                @foreach ($projectCodes as $projectCode)
                                    <option value="{{ $projectCode->id }}"
                                        {{ $projectCode->id == (old('project_id') ?? $agreement->project_id) ? 'selected' : '' }}>
                                        {{ $projectCode->title }}
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

                    <div class="mb-2 row">
                        <div class="col-lg-3">
                            <div class="d-flex align-items-start h-100">
                                <label for="validationGrant" class="form-label required-label">Grant Agreement
                                    Number</label>
                            </div>
                        </div>
                        <div class="col-lg-9">
                            <input type="text" class="form-control @if ($errors->has('grant_number')) is-invalid @endif"
                                name="grant_number" value="{{ old('grant_number') ?? $agreement->grant_number }}">
                            @if ($errors->has('grant_number'))
                                <div class="fv-plugins-message-container invalid-feedback">
                                    <div data-field="grant_number">{!! $errors->first('grant_number') !!}</div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="mb-2 row">
                        <div class="col-lg-3">
                            <div class="d-flex align-items-start h-100">
                                <label for="validationpd" class="form-label required-label">Agreement Period (from)
                                </label>
                            </div>
                        </div>
                        <div class="col-lg-9">
                            <input type="text"
                                class="form-control
                                        @if ($errors->has('effective_from')) is-invalid @endif"
                                readonly name="effective_from"
                                value="{{ old('effective_from') ?? $agreement->getEffectiveFromDate() }}" />
                            @if ($errors->has('effective_from'))
                                <div class="fv-plugins-message-container invalid-feedback">
                                    <div data-field="effective_from">{!! $errors->first('effective_from') !!}
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="mb-2 row">
                        <div class="col-lg-3">
                            <div class="d-flex align-items-start h-100">
                                <label for="validationpd" class="form-label required-label">Agreement Period (to)
                                </label>
                            </div>
                        </div>
                        <div class="col-lg-9">
                            <input type="text"
                                class="form-control original-agreement
                                        @if ($errors->has('effective_to')) is-invalid @endif"
                                name="effective_to" @if ($hasAmendments) disabled @else readonly @endif
                                value="{{ old('effective_to') ?? $agreement->getEffectiveToDate() }}" />
                            @if ($errors->has('effective_to'))
                                <div class="fv-plugins-message-container invalid-feedback">
                                    <div data-field="effective_to">{!! $errors->first('effective_to') !!}
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="mb-2 row">
                        <div class="col-lg-3">
                            <div class="d-flex align-items-start h-100">
                                <label for="validationGrant" class="form-label required-label">Approved Budget NPR</label>
                            </div>
                        </div>
                        <div class="col-lg-9">
                            <input type="number"
                                class="form-control original-agreement @if ($errors->has('approved_budget')) is-invalid @endif"
                                name="approved_budget" @if ($hasAmendments) disabled @endif
                                value="{{ old('approved_budget') ?? $agreement->approved_budget }}">
                            @if ($errors->has('approved_budget'))
                                <div class="fv-plugins-message-container invalid-feedback">
                                    <div data-field="approved_budget">{!! $errors->first('approved_budget') !!}</div>
                                </div>
                            @endif
                        </div>
                    </div>

                    @include('Attachment::index', [
                        'modelType' => 'Modules\Mfr\Models\Agreement',
                        'modelId' => $agreement->id,
                    ])

                    <div>
                        <div class="card">
                            <div class="card-header fw-bold"
                                style="display: flex; flex-direction: row; align-items: center; justify-content: space-between;">
                                <span>
                                    Amendments
                                </span>
                                <button data-toggle="modal" class="btn btn-primary btn-sm amendment-form"
                                    href="{{ route('mfr.agreement.amendment.create', $agreement->id) }}"><i
                                        class="bi-plus"></i> Add Amendment
                                </button>
                            </div>
                            <div class="container-fluid-s">
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table" id="amendmentTable">
                                            <thead class="bg-light">
                                                <tr>
                                                    <th scope="col">Effective Date</th>
                                                    <th scope="col">Extension To Date</th>
                                                    <th scope="col">Approved Budget</th>
                                                    <th scope="col" style="width: 150px">Attachment</th>
                                                    <th scope="col" style="width: 150px">{{ __('label.action') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>




                    {!! csrf_field() !!}
                    <div class="gap-2 border-0 card-footer justify-content-end d-flex">
                        <button type="submit" class="btn btn-primary btn-sm">Update</button>
                        <a href="{!! route('mfr.agreement.index') !!}" class="btn btn-danger btn-sm">Cancel</a>
                    </div>
            </form>
        </div>
    </section>

@stop
