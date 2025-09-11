@extends('layouts.container')

@section('title', 'Exit Staff Clearance')

@section('page_js')
    <script type="text/javascript">
        document.addEventListener("DOMContentLoaded", function() {
            $('#navbarVerticalMenu').find('#staff-clearance-menu').addClass('active');
            const clearanceId = "{{ $staffClearance->id }}";

            function clearanceTable() {
                var formRoute = "{{ route('staff.clearance.form', ['clearance' => ':id']) }}";
                formRoute = formRoute.replace(':id', clearanceId);
                $.ajax({
                    url: formRoute,
                    method: 'GET',
                    success: function(data) {
                        $('#clearance-table tbody').html(data.formTable);
                    },
                    error: function(xhr) {
                        console.error('Failed to fetch updated records:', xhr.responseText);
                    }
                });
            }

            clearanceTable();

            function payableTable() {
                var formRoute = "{{ route('staff.clearance.payable', ['clearance' => ':id']) }}";
                formRoute = formRoute.replace(':id', clearanceId);
                $.ajax({
                    url: formRoute,
                    method: 'GET',
                    success: function(data) {
                        $('#payable-container').html(data.tableHtml);
                    },
                    error: function(xhr) {
                        console.error('Failed to fetch updated records:', xhr.responseText);
                    }
                });
            }

            payableTable();

            var clearanceHistoryTable = $('#clearance-history-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('clearance.record.index', $staffClearance->id) }}",
                bFilter: false,
                ordering: false,
                bPaginate: false,
                bInfo: false,
                columns: [{
                        data: 'department',
                        name: 'department',
                    },
                    {
                        data: 'cleared_by',
                        name: 'cleared_by'
                    },
                    {
                        data: 'cleared_date',
                        name: 'cleared_date'
                    },
                    {
                        data: 'remarks',
                        name: 'remarks'
                    },
                ],
            });

            $('#clearance-form').on('submit', function(e) {
                e.preventDefault();
                let url = this.action;
                let data = $(this).serialize();
                var successCallback = function(response) {
                    toastr.success(response.message, 'Success', {
                        timeOut: 5000
                    });
                    clearanceTable();
                    clearanceHistoryTable.ajax.reload();
                }
                var errorCallBack = function(response) {
                    toastr.error('Error updating records', 'Error', {
                        timeOut: 5000
                    });
                }
                ajaxSubmit(url, 'POST', data, successCallback, errorCallBack);
            }).on('click', '.delete-record', function(e) {
                e.preventDefault();
                $object = $(this);
                var $url = $object.attr('data-href');
                var successCallback = function(response) {
                    toastr.success(response.message, 'Success', {
                        timeOut: 5000
                    });
                    clearanceTable();
                    clearanceHistoryTable.ajax.reload();
                }
                ajaxDeleteSweetAlert($url, successCallback);
            });


            $(document).on('click', '.open-payable-modal-form', function(e) {
                e.preventDefault();
                $('#openModal').find('.modal-content').html('');
                $('#openModal').modal('show').find('.modal-content').load($(this).attr('href'), function() {
                    const form = document.getElementById('employeePayable');
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
                            employee_id: {
                                validators: {
                                    notEmpty: {
                                        message: 'Employee is required.',
                                    },
                                },
                            },
                            // salary_date_from: {
                            //     validators: {
                            //         notEmpty: {
                            //             message: 'Salary Date from is required.',
                            //         },
                            //     },
                            // },
                            // salary_date_to: {
                            //     validators: {
                            //         notEmpty: {
                            //             message: 'Salary Date to is required.',
                            //         },
                            //     },
                            // },
                            leave_balance: {
                                validators: {
                                    notEmpty: {
                                        message: 'Leave Balance is required.',
                                    },
                                },
                            },
                            salary_amount: {
                                validators: {
                                    notEmpty: {
                                        message: 'Salary amount is required.',
                                    },
                                },
                                greaterThan: {
                                    message: 'The value must be greater than or equal to 0',
                                    min: 0,
                                },
                            },
                            festival_bonus: {
                                validators: {
                                    notEmpty: {
                                        message: 'Festival bonus is required.',
                                    },
                                    greaterThan: {
                                        message: 'The value must be greater than or equal to 0',
                                        min: 0,
                                    },
                                },
                            },
                            gratuity_amount: {
                                validators: {
                                    notEmpty: {
                                        message: 'Gratuity amount is required.',
                                    },
                                    greaterThan: {
                                        message: 'The value must be greater than or equal to 0',
                                        min: 0,
                                    },
                                },
                            },
                            other_amount: {
                                validators: {
                                    notEmpty: {
                                        message: 'Other amount is required.',
                                    },
                                    greaterThan: {
                                        message: 'The value must be greater than or equal to 0',
                                        min: 0,
                                    },
                                },
                            },
                            advance_amount: {
                                validators: {
                                    notEmpty: {
                                        message: 'Advance amount is required.',
                                    },
                                    greaterThan: {
                                        message: 'The value must be greater than or equal to 0',
                                        min: 0,
                                    },
                                },
                            },
                            loan_amount: {
                                validators: {
                                    notEmpty: {
                                        message: 'Loan amount is required.',
                                    },
                                    greaterThan: {
                                        message: 'The value must be greater than or equal to 0',
                                        min: 0,
                                    },
                                },
                            },
                            other_payable_amount: {
                                validators: {
                                    notEmpty: {
                                        message: 'Other payable amount is required.',
                                    },
                                    greaterThan: {
                                        message: 'The value must be greater than or equal to 0',
                                        min: 0,
                                    },
                                },
                            },
                            deduction_amount: {
                                validators: {
                                    greaterThan: {
                                        message: 'The deduction amount must be greater than or equal to 0',
                                        min: 0,
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
                            salaryDate: new FormValidation.plugins.StartEndDate({
                                format: 'YYYY-MM-DD',
                                startDate: {
                                    field: 'salary_date_from',
                                    message: 'Start date must be a valid date and earlier than End date.',
                                },
                                endDate: {
                                    field: 'salary_date_to',
                                    message: 'End date must be a valid date and later than Start date.',
                                },
                            }),
                            festivalBonusDate: new FormValidation.plugins.StartEndDate({
                                format: 'YYYY-MM-DD',
                                startDate: {
                                    field: 'festival_bonus_date_from',
                                    message: 'Start date must be a valid date and earlier than End date.',
                                },
                                endDate: {
                                    field: 'festival_bonus_date_to',
                                    message: 'End date must be a valid date and later than Start date.',
                                },
                            }),
                        },
                    }).on('core.form.valid', function(event) {
                        $url = fv.form.action;
                        $form = fv.form;
                        data = $($form).serialize();
                        var successCallback = function(response) {
                            $('#openModal').modal('hide');
                            toastr.success(response.message, 'Success', {
                                timeOut: 5000
                            });
                            payableTable();
                        }
                        ajaxSubmit($url, 'POST', data, successCallback);
                    });

                    $(form).find('[name="salary_date_from"]').datepicker({
                        language: 'en-GB',
                        autoHide: true,
                        format: 'yyyy-mm-dd',
                        zIndex: 2048,
                    }).on('change', function(e) {
                        fv.revalidateField('salary_date_from');
                        fv.revalidateField('salary_date_to');
                    });

                    $(form).find('[name="salary_date_to"]').datepicker({
                        language: 'en-GB',
                        autoHide: true,
                        format: 'yyyy-mm-dd',
                        zIndex: 2048,
                    }).on('change', function(e) {
                        fv.revalidateField('salary_date_from');
                        fv.revalidateField('salary_date_to');
                    });

                    $(form).find('[name="festival_bonus_date_from"]').datepicker({
                        language: 'en-GB',
                        autoHide: true,
                        format: 'yyyy-mm-dd',
                        zIndex: 2048,
                    }).on('change', function(e) {
                        fv.revalidateField('festival_bonus_date_from');
                        fv.revalidateField('festival_bonus_date_to');
                    });

                    $(form).find('[name="festival_bonus_date_to"]').datepicker({
                        language: 'en-GB',
                        autoHide: true,
                        format: 'yyyy-mm-dd',
                        zIndex: 2048,
                    }).on('change', function(e) {
                        fv.revalidateField('festival_bonus_date_from');
                        fv.revalidateField('festival_bonus_date_to');
                    });
                });
            });

        });
    </script>
@endsection

@section('page-content')

    <style>
        td,
        th {
            border: 1px solid grey;
            padding: 8px;
            text-align: left;
        }
    </style>


    <div class="pb-3 mb-3 page-header border-bottom">
        <div class="gap-2 d-flex flex-column flex-lg-row align-items-start align-items-lg-center">
            <div class="brd-crms flex-grow-1">
                <nav aria-label="breadcrumb">
                    <ol class="m-0 breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{!! route('dashboard.index') !!}" class="text-decoration-none text-dark">Home</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('staff.clearance.index') }}" class="text-decoration-none text-dark">Exit Staff
                                Clearance</a>
                        </li>
                        <li class="breadcrumb-item" aria-current="page">@yield('title')</li>
                    </ol>
                </nav>
                <h4 class="m-0 mt-1 lh1 fs-6 text-uppercase fw-bold text-primary">@yield('title')</h4>
            </div>
        </div>
    </div>

    <section>

        <div id="employee-details" class="mb-3">
            @include('ExitStaffClearance::Partials.employee-details')
        </div>

        @can('logistic-staff-clearance')
            <div class="card collapsible-card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <span role="button" data-bs-toggle="collapse" data-bs-target="#collapse-body-assets" aria-expanded="false"
                        aria-controls="collapseCard">
                        <span class="card-title">
                            <span class="fw-bold"></span>
                            <span>
                                Assets
                            </span>
                        </span>
                        <i class="bi bi-caret-down-fill indicator"></i>
                    </span>
                </div>
                <div @class(['card-body', 'collapse', 'show' => true]) id="collapse-body-assets">
                    <div class="row">
                        <div class="col-lg-12">
                            <table id="clearance-asset-table" class="mb-3" style="width: 100%">
                                <thead>
                                    <tr>
                                        <th style="width: 5%">{{ __('label.sn') }} </th>
                                        <th>Asset Number</th>
                                        <th>Item Name</th>
                                        <th>Office</th>
                                        <th>Handover Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($staffClearance->employee?->user?->goodRequestAssets as $index => $asset)
                                        <tr>
                                            <td>{{ ++$index }}</td>
                                            <td>{{ $asset->getAssetNumber() }}</td>
                                            <td>{{ $asset->asset->inventoryItem->getItemName() }}</td>
                                            <td>{{ $asset->getAssignedOffice() }}</td>
                                            <td><span class="{{ $asset->getStatusClass() }}"> {{ $asset->getStatus() }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        @endcan

        <div class="card">
            <div class="card-body">
                <span class="fw-bold">
                    The above mentioned staff member is leaving OHW and under clearance So Please indicate outstanding, if
                    any against his / her name.
                </span>
            </div>
        </div>

        <div id="keyGoalsReview" class="mb-3">
            <div class="card">
                <div class="card-header fw-bold">
                    <span class="card-title">
                        <span class="fw-bold">A.</span>
                        <span>
                            Clearance
                        </span>
                    </span>
                </div>
                <div class="card-body">
                    <form action="{{ route('clearance.record.store', $staffClearance->id) }}" method="POST"
                        id="clearance-form">
                        @csrf
                        <div class="row">
                            <div class="col-lg-12">
                                <table id="clearance-table" class="mb-3" style="width: 100%">
                                    <thead>
                                        <tr>
                                            <th style="width: 20%">Departments </th>
                                            <th style="width: 10%">Clearance</th>
                                            <th style="width: 60%">Remarks</th>
                                            <th style="width: 5%">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-sm btn-outline-primary" style="float: right">Save</button>
                    </form>
                </div>
            </div>
        </div>

    </section>

    <div class="card collapsible-card">
        <div class="card-header d-flex align-items-center justify-content-between">
            <span role="button" data-bs-toggle="collapse" data-bs-target="#collapse-body" aria-expanded="false"
                aria-controls="collapseCard">
                <span class="card-title">
                    <span class="fw-bold"></span>
                    <span>
                        Clearance Records
                    </span>
                </span>
                <i class="bi bi-caret-down-fill indicator"></i>
            </span>
        </div>
        <div @class(['card-body', 'collapse', 'show' => true]) id="collapse-body">
            <div class="row">
                <div class="col-lg-12">
                    <table id="clearance-history-table" class="mb-3" style="width: 100%">
                        <thead>
                            <tr>
                                <th style="width: 20%" rowspan="2">Departments </th>
                                <th style="width: 50%" colspan="2">Cleared By:</th>
                                <th style="width: 30%" rowspan="2">Remarks</th>
                            </tr>
                            <tr>
                                <th style="width: 20%">Name </th>
                                <th style="width: 20%">Cleared Date</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <section>
        @if ($staffClearance->status_id == config('constant.RETURNED_STATUS'))
            <div class="col-lg-6">
                <div class="p-3 mb-2 border row">
                    <div>
                        <div class="d-flex align-items-start h-100">
                            <span class="fw-bold" style="text-decoration: underline">Remarks:</span>
                        </div>
                    </div>
                    <div class="mt-2">
                        <span>{{ $staffClearance->getLatestRemark() }}</span>
                    </div>
                </div>
            </div>
        @endif
    </section>

    @php
        $formUrl = '';
        if ($canVerify = $authUser->can('verify', $staffClearance)) {
            $formUrl = route('staff.clearance.verify.store', $staffClearance->id);
        } elseif ($canCertify = $authUser->can('certify', $staffClearance)) {
            $formUrl = route('staff.clearance.certify.store', $staffClearance->id);
        }
    @endphp

    @if (auth()->user()->can('create-exit-payable'))
        <div class="card">
            <div class="card-header fw-bold">
                <span class="card-title d-flex justify-content-between">
                    <div>
                        <span class="fw-bold">B.</span>
                        <span>
                            Payable
                        </span>
                    </div>
                    <div>
                        @if ($authUser->can('update', $staffClearance->employeeExitPayable))
                            <a data-toggle="modal" class="btn btn-outline-primary btn-sm open-payable-modal-form"
                                href="{{ route('exit.payable.edit', $staffClearance->employeeExitPayable->id) }}"
                                rel="tooltip" title="Edit Employee Payable Request"><i class="bi-pencil-square"></i>
                                Update</a>
                        @endif
                    </div>

                </span>
            </div>
            <div class="card-body" id="payable-container">
            </div>
        </div>
    @endif


    <section>
        <div class="card">
            <div class="card-header fw-bold">
                C. <span
                    class="">{{ @$canCertify ? 'HR Certification' : (@$canVerify ? 'Supervisor Verification' : 'Clearance Process') }}</span>
            </div>
            <div class="card-body">
                <div class="row">
                    <div @class([
                        'col-lg-12' => empty($formUrl),
                        'col-lg-6' => !empty($formUrl),
                    ])>
                        @include('ExitStaffClearance::Partials.logs')
                    </div>
                    <div class="col-lg-6">
                        @if ($formUrl)
                            <form action="{{ $formUrl }}" id="editForm" method="post"
                                enctype="multipart/form-data" autocomplete="off" {{-- onsubmit="return confirm('Have you saved all the forms? Are you sure to submit?');" --}}>
                                {{-- <span class="mb-2 fs-7 badge bg-secondary">{{isset($canCertify)? 'HR Certification' : 'Supervisor Verification'}}</span> --}}
                                <input type="hidden" name="staff_clearance_id" value="{{ $staffClearance->id }}">
                                <div class="mb-2 row">
                                    <div class="col-lg-3">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="status_id" class="form-label required-label">Status </label>
                                        </div>
                                    </div>
                                    <div class="col-lg-9">
                                        <select name="status_id" class="select2 form-control" data-width="100%">
                                            <option value="">Select Status</option>
                                            @if (isset($canCertify))
                                                <option value="{{ config('constant.VERIFIED2_STATUS') }}"
                                                    {{ old('status_id') == config('constant.VERIFIED2_STATUS') ? 'selected' : '' }}>
                                                    Certify</option>
                                            @else
                                                <option value="{{ config('constant.VERIFIED_STATUS') }}"
                                                    {{ old('status_id') == config('constant.VERIFIED_STATUS') ? 'selected' : '' }}>
                                                    Verify</option>
                                            @endif
                                        </select>
                                        @if ($errors->has('status_id'))
                                            <div class="fv-plugins-message-container invalid-feedback">
                                                <div data-field="status_id">
                                                    {!! $errors->first('status_id') !!}
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                @if (isset($canCertify))
                                    <div class="mb-2 row" id="receiver">
                                        <div class="col-lg-3">
                                            <div class="d-flex align-items-start h-100">
                                                <label for="endorser_id" class="form-label required-label">Send To
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-lg-9">
                                            <select name="endorser_id" id="endorser_id" class="select2 form-control"
                                                data-width="100%">
                                                <option value="">Select Endorser</option>
                                                @foreach ($endorsers as $receiver)
                                                    <option value="{{ $receiver->id }}">
                                                        {{ $receiver->getFullName() }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @if ($errors->has('endorser_id'))
                                                <div class="fv-plugins-message-container invalid-feedback">
                                                    <div data-field="endorser_id">
                                                        {!! $errors->first('endorser_id') !!}
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endif

                                <div class="mb-2 row">
                                    <div class="col-lg-3">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="log_remarks" class="form-label required-label">Remarks </label>
                                        </div>
                                    </div>
                                    <div class="col-lg-9">
                                        <textarea type="text" class="form-control @if ($errors->has('log_remarks')) is-invalid @endif" name="log_remarks">{{ old('log_remarks') }}</textarea>
                                        @if ($errors->has('log_remarks'))
                                            <div class="fv-plugins-message-container invalid-feedback">
                                                <div data-field="log_remarks">{!! $errors->first('log_remarks') !!}</div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                {!! csrf_field() !!}
                                <div class="gap-2 border-0 justify-content-end d-flex">
                                    <button type="submit" name="btn" value="submit" class="btn btn-success btn-sm">
                                        Submit
                                    </button>
                                    <a href="{!! route('staff.clearance.index') !!}" class="btn btn-danger btn-sm">Cancel</a>
                                </div>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>

@stop
