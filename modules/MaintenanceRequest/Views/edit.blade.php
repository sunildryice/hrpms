@extends('layouts.container')

@section('title', 'Edit Maintenance Request')

@section('page_css')
    <style>
        .wrap-text {
            white-space: normal !important;
            word-wrap: break-word;
            word-break: break-word;
        }
    </style>
@endsection

@section('page_js')
    <script type="text/javascript">
        document.addEventListener('DOMContentLoaded', function(e) {
            $('#navbarVerticalMenu').find('#maintenance-requests-menu').addClass('active');
            const form = document.getElementById('maintenanceRequestEditForm');
            $('[name="request_date"]').datepicker({
                language: 'en-GB',
                autoHide: true,
                format: 'yyyy-mm-dd',
                endDate: '{!! date('Y-m-d') !!}',
                zIndex: 2048,
            }).on('change', function(e) {
                fv.revalidateField('request_date');
            });

            const fv = FormValidation.formValidation(form, {
                fields: {
                    request_date: {
                        validators: {
                            notEmpty: {
                                message: 'The request date is required.',
                            },
                            date: {
                                format: 'YYYY-MM-DD',
                                message: 'The value is not a valid date.',
                            },
                        },
                    },
                    approver_id: {
                        validators: {
                            notEmpty: {
                                message: 'Approver is required.',
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

            $(form).on('change', '[name="reviewer_id"]', function(e) {
                fv.revalidateField('reviewer_id');
            });
        });

        var oTable = $('#maintenanceRequestItemTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('maintenance.requests.items.index', $maintenanceRequest->id) }}",
            bFilter: false,
            bPaginate: false,
            bInfo: false,
            columns: [{
                    data: 'item_name',
                    name: 'item_name',
                    className: 'wrap-text'
                },
                {
                    data: 'problem',
                    name: 'problem'
                },
                // {
                //     data: 'activity',
                //     name: 'activity'
                // },
                // {
                //     data: 'account',
                //     name: 'account'
                // },
                // {
                //     data: 'donor',
                //     name: 'donor'
                // },
                // {
                //     data: 'estimated_cost',
                //     name: 'estimated_cost'
                // },
                {
                    data: 'replacement_good_needed',
                    name: 'replacement_good_needed',
                },
                {
                    data: 'remarks',
                    name: 'remarks',
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

        $('#maintenanceRequestItemTable').on('click', '.delete-record', function(e) {
            e.preventDefault();
            $object = $(this);
            var $url = $object.attr('data-href');
            var successCallback = function(response) {
                toastr.success(response.message, 'Success', {
                    timeOut: 5000
                });
                $('#maintenanceRequestItemTable').find('#total_amount').text(response.maintenanceRequest
                    .estimated_cost);
                oTable.ajax.reload();
            }
            ajaxDeleteSweetAlert($url, successCallback);
        });

        $(document).on('click', '.open-item-modal-form', function(e) {
            e.preventDefault();
            $('#openModal').find('.modal-content').html('');
            $('#openModal').modal('show').find('.modal-content').load($(this).attr('href'), function() {
                const form = document.getElementById('maintenanceRequestItemForm');
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
                        // activity_code_id: {
                        //     validators: {
                        //         notEmpty: {
                        //             message: 'Activity code is required',
                        //         },
                        //     },
                        // },
                        // account_code_id: {
                        //     validators: {
                        //         notEmpty: {
                        //             message: 'Account code is required',
                        //         },
                        //     },
                        // },
                        problem: {
                            validators: {
                                notEmpty: {
                                    message: 'Problem field is required.',
                                },
                            },
                        },

                        // estimated_cost: {
                        //     validators: {
                        //         notEmpty: {
                        //             message: 'Estimated Cost field is required.',
                        //         },
                        //         numeric: {
                        //             message: 'The Estimated Cost should be number.',
                        //         },
                        //         between: {
                        //             inclusive: true,
                        //             min: 1,
                        //             max: 99999999,
                        //             message: 'The value must be between 1 to 99999999',
                        //         },
                        //     },
                        // },
                        replacement_good_needed: {
                            validators: {
                                notEmpty: {
                                    message: 'Please select if replacement goods are needed.',
                                },
                            },
                        },
                        item_id: {
                            validators: {
                                notEmpty: {
                                    message: 'Item Name field is required.',
                                },
                            },
                        },
                        remarks: {
                            validators: {
                                notEmpty: {
                                    message: 'Remarks field is required.',
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
                    var successCallback = function(response) {
                        $('#openModal').modal('hide');
                        toastr.success(response.message, 'Success', {
                            timeOut: 5000
                        });
                        $('#maintenanceRequestItemTable').find('#total_amount').text(response
                            .estimatedCost);
                        oTable.ajax.reload();
                    }
                    ajaxSubmit($url, 'POST', data, successCallback);
                });

                $(form).on('change', '[name="activity_code_id"]', function(e) {
                    $element = $(this);
                    var activityCodeId = $element.val();
                    var htmlToReplace = '<option value="">Select Account Code</option>';
                    if (activityCodeId) {
                        var url = baseUrl + '/api/master/activity-codes/' + activityCodeId;
                        var successCallback = function(response) {
                            response.accountCodes.forEach(function(accountCode) {
                                htmlToReplace += '<option value="' + accountCode.id +
                                    '">' + accountCode.title + ' ' + accountCode
                                    .description + '</option>';
                            });
                            $($element).closest('form').find('[name="account_code_id"]').html(
                                htmlToReplace).trigger('change');

                        }
                        var errorCallback = function(error) {
                            console.log(error);
                        }
                        ajaxNativeSubmit(url, 'GET', {}, 'json', successCallback, errorCallback);
                    } else {
                        $($element).closest('form').find('[name="account_code_id"]').html(
                            htmlToReplace);
                    }
                    fv.revalidateField('activity_code_id');
                }).on('change', '[name="account_code_id"]', function(e) {
                    fv.revalidateField('account_code_id');
                });
            });
        });
    </script>
@endsection
@section('page-content')

    <div class="pb-3 mb-3 page-header border-bottom">
        <div class="gap-2 d-flex flex-column flex-lg-row align-items-start align-items-lg-center">
            <div class="brd-crms flex-grow-1">
                <nav aria-label="breadcrumb">
                    <ol class="m-0 breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{!! route('dashboard.index') !!}" class="text-decoration-none text-dark">Home</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('maintenance.requests.index') }}"
                                class="text-decoration-none text-dark">Maintenance
                                Request</a>
                        </li>
                        <li class="breadcrumb-item" aria-current="page">@yield('title')</li>
                    </ol>
                </nav>
                <h4 class="m-0 mt-1 lh1 fs-6 text-uppercase fw-bold text-primary">@yield('title')</h4>
            </div>
        </div>
    </div>

    <section class="registration">
        <form action="{{ route('maintenance.requests.update', $maintenanceRequest->id) }}" id="maintenanceRequestEditForm"
            method="post" enctype="multipart/form-data" autocomplete="off">
            <div class="card">
                <div class="card-body">
                    <div class="mb-2 row">
                        <div class="col-lg-2">
                            <div class="d-flex align-items-start h-100">
                                <label for="validationHealthFacility" class="form-label required-label">Request
                                    Date</label>
                            </div>
                        </div>
                        <div class="col-lg-10">
                            <input type="text" class="form-control @if ($errors->has('request_date')) is-invalid @endif"
                                name="request_date"
                                value="{{ old('request_date') ?: $maintenanceRequest->request_date }}" />
                            @if ($errors->has('request_date'))
                                <div class="fv-plugins-message-container invalid-feedback">
                                    <div data-field="request_date">{!! $errors->first('request_date') !!}</div>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="mb-2 row">
                        <div class="col-lg-2">
                            <div class="d-flex align-items-start h-100">
                                <label for="validationHealthFacility" class="form-label">Reason For Maintenance
                                    Request</label>
                            </div>
                        </div>
                        <div class="col-lg-10">
                            <textarea type="text" class="form-control @if ($errors->has('remarks')) is-invalid @endif" name="remarks">{{ old('remarks') ?? $maintenanceRequest->remarks }}</textarea>
                            @if ($errors->has('remarks'))
                                <div class="fv-plugins-message-container invalid-feedback">
                                    <div data-field="remarks">{!! $errors->first('remarks') !!}</div>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="mb-2 row">
                        <div class="col-lg-2">
                            <div class="d-flex align-items-start h-100">
                                <label for="validationRemarks" class="form-label">Reviewer</label>
                            </div>
                        </div>
                        <div class="col-lg-10">
                            @php $selectedReviewerId = old('reviewer_id') ?: $maintenanceRequest->reviewer_id; @endphp
                            <select name="reviewer_id"
                                class="select2 form-control
                                            @if ($errors->has('reviewer_id')) is-invalid @endif"
                                data-width="100%">
                                <option value="">Select Reviewer</option>
                                @foreach ($reviewers as $reviewer)
                                    <option value="{{ $reviewer->id }}" @if ($reviewer->id == $selectedReviewerId) selected @endif>
                                        {{ $reviewer->getFullName() }}
                                    </option>
                                @endforeach
                            </select>
                            @if ($errors->has('reviewer_id'))
                                <div class="fv-plugins-message-container invalid-feedback">
                                    <div data-field="reviewer_id">
                                        {!! $errors->first('reviewer_id') !!}
                                    </div>
                                </div>
                            @endif
                        </div>

                    </div>
                    <div class="row">
                        <div class="col-lg-2">
                            <div class="d-flex align-items-start h-100">
                                <label for="validationRemarks" class="form-label required-label">Approver</label>
                            </div>
                        </div>
                        <div class="col-lg-10">
                            @php $selectedApproverId = old('approver_id') ?: $maintenanceRequest->approver_id; @endphp
                            <select name="approver_id"
                                class="select2 form-control
                                            @if ($errors->has('approver_id')) is-invalid @endif"
                                data-width="100%">
                                <option value="">Select Approver</option>
                                @foreach ($approvers as $approver)
                                    <option value="{{ $approver->id }}" @if ($approver->id == $selectedApproverId) selected @endif>
                                        {{ $approver->getFullName() }}
                                    </option>
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

                </div>
                {!! csrf_field() !!}
                {!! method_field('PUT') !!}
                <div class="gap-2 border-0 card-footer justify-content-end d-flex">
                    <button type="submit" name="btn" value="save" class="btn btn-primary btn-sm">
                        Update
                    </button>
                </div>
            </div>

            @if ($maintenanceRequest->returnLog()->exists())
                <div class="card">
                    <div class="card-header text-danger">
                        Return Remarks
                    </div>
                    <div class="card-body">
                        {{ $maintenanceRequest->returnLog->log_remarks }}
                    </div>
                </div>
            @endif

            <div class="card">
                <div class="card-header fw-bold">
                    <div class="d-flex align-items-center add-info justify-content-between">
                        <span> Maintenance Request Items</span>
                        @if ($authUser->can('update', $maintenanceRequest))
                            <button data-toggle="modal" class="btn btn-primary btn-sm open-item-modal-form"
                                href="{!! route('maintenance.requests.items.create', $maintenanceRequest->id) !!}"><i class="bi-plus"></i> Add New Item
                            </button>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="maintenanceRequestItemTable">
                            <thead class="thead-light">
                                <tr>
                                    <th scope="col">{{ __('label.item-name') }}</th>
                                    <th scope="col">{{ __('label.problem') }}</th>
                                    {{-- <th scope="col">{{ __('label.activity') }}</th>
                                    <th scope="col">{{ __('label.account') }}</th>
                                    <th scope="col">{{ __('label.donor') }}</th>
                                    <th scope="col">{{ __('label.estimate') }}</th> --}}
                                    <th scope="col">{{ __('label.replacement-good-needed') }}</th>
                                    <th scope="col">{{ __('label.remarks') }}</th>
                                    <th style="width: 150px" class="sticky-col">{{ __('label.action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                            {{-- <tfoot>
                                <tr>
                                    <td colspan="3">Total Amount</td>
                                    <td id="total_amount">{{ $maintenanceRequest->estimated_cost }}</td>
                                    <td colspan="1"></td>
                                </tr>
                            </tfoot> --}}
                        </table>
                    </div>
                </div>

            </div>
            <div class="gap-2 justify-content-end d-flex">
                <button type="submit" name="btn" value="submit" class="btn btn-success btn-sm">
                    Submit
                </button>
                <a href="{!! route('maintenance.requests.index') !!}" class="btn btn-danger btn-sm">Cancel</a>
            </div>
        </form>
    </section>
@stop
