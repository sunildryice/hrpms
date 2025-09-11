@extends('layouts.container')

@section('title', 'Edit Advance Settlement Request')

@section('page_js')
    <script type="text/javascript">
        function collapse(cell) {
            var row = cell.parentElement;
            var target_row = row.parentElement.children[row.rowIndex + 1];
            if (target_row.style.display == 'table-row') {
                cell.innerHTML = '<i class="bi bi-plus-lg"></i>';
                target_row.style.display = 'none';
            } else {
                cell.innerHTML = '<i class="bi bi-dash-lg"></i>';
                target_row.style.display = 'table-row';
            }
        }

        document.addEventListener('DOMContentLoaded', function(e) {
            $('#navbarVerticalMenu').find('#settlement-advance-requests-menu').addClass('active');

            const form = document.getElementById('advanceSettlementEditForm');
            const fv = FormValidation.formValidation(form, {
                fields: {
                    completion_date: {
                        validators: {
                            notEmpty: {
                                message: 'Completion date is required.',
                            },
                        },
                    },
                    reviewer_id: {
                        validators: {
                            notEmpty: {
                                message: 'Reviewer is required.',
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

            $('[name="completion_date"]').datepicker({
                language: 'en-GB',
                autoHide: true,
                format: 'yyyy-mm-dd',
                {{-- startDate: '{{ date('Y-m-d', strtotime(date('Y-m-d') . ' + 1 days')) }}', --}}
                endDate: '{!! date('Y-m-d') !!}',
            }).on('change', function(e) {
                fv.revalidateField('completion_date');
            });

            $(form).on('change', '[name="reviewer_id"]', function(e) {
                fv.revalidateField('reviewer_id');
            }).on('change', '[name="approver_id"]', function(e) {
                fv.revalidateField('approver_id');
            });
        });

        var activityTable = $('#activityTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('advance.settlement.activities.index', [$advanceRequestSettlement->id]) }}",
            bFilter: false,
            bPaginate: false,
            bInfo: false,
            columns: [{
                    data: 'description',
                    name: 'description'
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

        $('#activityTable').on('click', '.delete-record', function(e) {
            e.preventDefault();
            $object = $(this);
            var $url = $object.attr('data-href');
            var successCallback = function(response) {
                toastr.success(response.message, 'Success', {
                    timeOut: 5000
                });
                activityTable.ajax.reload();
            }
            ajaxDeleteSweetAlert($url, successCallback);
        });

        $(document).on('click', '.open-activity-modal-form', function(e) {
            e.preventDefault();
            $('#activityModal').find('.modal-content').html('');
            $('#activityModal').modal('show').find('.modal-content').load($(this).attr('href'), function() {
                const form = document.getElementById('AddSettlementActivityForm');
                const fv = FormValidation.formValidation(form, {
                    fields: {
                        description: {
                            validators: {
                                notEmpty: {
                                    message: 'Description is required',
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
                        $('#activityModal').modal('hide');
                        toastr.success(response.message, 'Success', {
                            timeOut: 5000
                        });
                        activityTable.ajax.reload();
                    }
                    ajaxSubmit($url, 'POST', data, successCallback);
                });
            });
        });

        var summaryTable = $('#settlementExpenseSummary').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('advance.settlement.expense.summary', $advanceRequestSettlement->id) }}",
            bFilter: false,
            bPaginate: false,
            bInfo: false,
            columns: [
                // {
                //     data: 'expenseCategory',
                //     name: 'expenseCategory'
                // },
                {
                    data: 'expenseType',
                    name: 'expenseType'
                },
                {
                    data: 'gross_amount',
                    name: 'gross_amount'
                },
                {
                    data: 'tax_amount',
                    name: 'tax_amount'
                },
                {
                    data: 'net_amount',
                    name: 'net_amount'
                },
            ]
        });

        $(document).on('click', '.open-expense-modal-form', function(e) {
            e.preventDefault();
            $('#openModal').find('.modal-content').html('');
            $('#openModal').modal('show').find('.modal-content').load($(this).attr('href'), function() {
                const form = document.getElementById('settlementExpenseForm');
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
                        district_id: {
                            validators: {
                                notEmpty: {
                                    message: 'District is required',
                                },
                            },
                        },
                        location: {
                            validators: {
                                notEmpty: {
                                    message: 'Location is required',
                                },
                            },
                        },
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
                        $('#expenseRow-' + response.settlementExpense.id).find('.narration')
                            .text(response.settlementExpense.narration);
                        $('#expenseRow-' + response.settlementExpense.id).find('.location')
                            .text(response.settlementExpense.location);
                        $('#expenseRow-' + response.settlementExpense.id).find('.district')
                            .text(response.district);
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
                                    '">' +
                                    accountCode.title + ' ' + accountCode.description +
                                    '</option>';
                            });
                            $($element).closest('form').find('[name="account_code_id"]').html(
                                    htmlToReplace)
                                .trigger('change');
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
                    fv.revalidateField('account_code_id');
                }).on('change', '[name="account_code_id"]', function(e) {
                    fv.revalidateField('account_code_id');
                })

                $(form).on('change', '[name="district_id"]', function(e) {
                    fv.revalidateField('district_id');
                });
            });
        });

        $(document).on('click', '.open-expense-detail-modal-form', function(e) {
            e.preventDefault();
            $('#expenseDetailModal').find('.modal-content').html('');
            $('#expenseDetailModal').modal('show').find('.modal-content').load($(this).attr('href'), function() {
                const form = document.getElementById('expenseDetailForm');
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
                        expense_date: {
                            validators: {
                                notEmpty: {
                                    message: 'Expense Date is required',
                                },
                            },
                        },
                        // expense_category_id: {
                        //     validators: {
                        //         notEmpty: {
                        //             message: 'Expense Category is required',
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
                        bill_number: {
                            validators: {
                                notEmpty: {
                                    message: 'Invoice Number is required',
                                },
                            },
                        },
                        gross_amount: {
                            validators: {
                                notEmpty: {
                                    message: 'Gross Amount is required',
                                },
                            },
                        },
                        tax_amount: {
                            validators: {
                                notEmpty: {
                                    message: 'Tax Amount is required',
                                },
                            },
                        },
                        expense_type_id: {
                            validators: {
                                notEmpty: {
                                    message: 'Expense Type is required',
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
                    // data = $($form).serialize();
                    data = new FormData($form);
                    var successCallback = function(response) {
                        $('#expenseDetailModal').modal('hide');
                        toastr.success(response.message, 'Success', {
                            timeOut: 5000
                        });
                        $('#expenseRow-' + response.settlementExpense.id).find('.gross_amount')
                            .text(response.settlementExpense.gross_amount);
                        $('#expenseRow-' + response.settlementExpense.id).find('.tax_amount')
                            .text(response.settlementExpense.tax_amount);
                        $('#expenseRow-' + response.settlementExpense.id).find('.net_amount')
                            .text(response.settlementExpense.net_amount);

                        if ($('#detailRow-' + response.expenseDetail.id).length) {
                            $('#detailRow-' + response.expenseDetail.id).find('.expense_date')
                                .text(response.expenseDate);
                            $('#detailRow-' + response.expenseDetail.id).find('.bill_number')
                                .text(response.expenseDetail.bill_number);
                            // $('#detailRow-' + response.expenseDetail.id).find('.expense_category').text(response.expenseCategory);
                            $('#detailRow-' + response.expenseDetail.id).find('.description')
                                .text(response.description);
                            $('#detailRow-' + response.expenseDetail.id).find('.expense_type')
                                .text(response.expenseType);
                            $('#detailRow-' + response.expenseDetail.id).find('.gross_amount')
                                .text(response.expenseDetail.gross_amount);
                            $('#detailRow-' + response.expenseDetail.id).find('.tax_amount')
                                .text(response.expenseDetail.tax_amount);
                            $('#detailRow-' + response.expenseDetail.id).find('.net_amount')
                                .text(response.expenseDetail.net_amount);
                            $('#detailRow-' + response.expenseDetail.id).find('.attachment')
                                .html(response.attachment);
                        } else {
                            var $action =
                                '<a class="btn btn-outline-primary btn-sm open-expense-detail-modal-form"' +
                                'data-toggle="modal" rel="tooltip" title="Edit expense detail"' +
                                'href="' + response.editUrl + '">' +
                                '<i class="bi-pencil"></i></a>' +
                                '&emsp;<a href = "javascript:;" class="btn btn-danger btn-sm delete-detail-record"' +
                                'data-href="' + response.deleteUrl + '">' +
                                '<i class="bi-trash"></i></a>';

                            newRow = '<tr id="detailRow-' + response.expenseDetail.id + '">' +
                                '<td class="expense_date">' + response.expenseDate + '</td>' +
                                '<td class="bill_number">' + response.expenseDetail
                                .bill_number + '</td>' +
                                // '<td class="expense_category">' + response.expenseCategory + '</td>' +
                                '<td class="description">' + response.description + '</td>' +
                                '<td class="gross_amount">' + response.expenseDetail
                                .gross_amount + '</td>' +
                                '<td class="tax_amount">' + response.expenseDetail.tax_amount +
                                '</td>' +
                                '<td class="net_amount">' + response.expenseDetail.net_amount +
                                '</td>' +
                                '<td class="expense_type">' + response.expenseType + '</td>' +
                                '<td class="attachment">' + response.attachment + '</td>' +
                                '<td class="action">' + $action + '</td>' +
                                '</tr>';

                            $("#expenseTable-" + response.expenseDetail.settlement_expense_id +
                                " tr:last").after(newRow);
                        }
                        summaryTable.ajax.reload();
                    }
                    // ajaxSubmit($url, 'POST', data, successCallback);
                    ajaxSubmitFormData($url, 'POST', data, successCallback);
                });

                $(form).find('[name="expense_date"]').datepicker({
                    language: 'en-GB',
                    autoHide: true,
                    format: 'yyyy-mm-dd',
                    endDate: '{!! date('Y-m-d') !!}',
                    zIndex: 2048,
                }).on('change', function(e) {
                    fv.revalidateField('expense_date');
                });

                $(form)
                    // .on('change', '[name="expense_category_id"]', function (e) {
                    //     fv.revalidateField('expense_category_id');
                    // })
                    .on('change', '[name="expense_type_id"]', function(e) {
                        fv.revalidateField('expense_type_id');
                    }).on('change', '[name="district_id"]', function(e) {
                        fv.revalidateField('district_id');
                    }).on('change', '[name="gross_amount"]', function(e) {
                        fv.revalidateField('gross_amount');
                        calculateTotalAmount(this);
                    }).on('change', '[name="tax_amount"]', function(e) {
                        fv.revalidateField('tax_amount');
                        calculateTotalAmount(this);
                    });

                $(form).on('click', '#delete-attachment', function(e) {
                    e.preventDefault();
                    $object = $(this);
                    var $url = $object.attr('data-href');
                    var successCallback = function(response) {
                        toastr.success(response.message, 'Success', {
                            timeOut: 5000
                        });
                        $('#expenseDetailModal').modal('hide');
                        $('#detailRow-' + response.expenseDetail.id).find('.attachment')
                            .empty();
                    }
                    ajaxDeleteSweetAlert($url, successCallback);
                });

                function calculateTotalAmount($element) {
                    grossAmount = parseFloat($($element).closest('form').find('[name="gross_amount"]')
                        .val());
                    lessTax = parseFloat($($element).closest('form').find('[name="tax_amount"]').val());
                    netAmount = grossAmount ? Math.round(grossAmount - lessTax) : 0;
                    $($element).closest('form').find('[name="net_amount"]').val(netAmount);
                }
            });
        });

        $('#expenseTable').on('click', '.delete-detail-record', function(e) {
            e.preventDefault();
            $object = $(this);
            var $url = $object.attr('data-href');
            var successCallback = function(response) {
                toastr.success(response.message, 'Success', {
                    timeOut: 5000
                });
                $($object).closest('tr').remove();
                summaryTable.ajax.reload();
            }
            ajaxDeleteSweetAlert($url, successCallback);
        });



        // Start - Attachment Scripts Section
        var attachmentTable = $('#attachmentTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('advance.settlement.attachment.index', $advanceRequestSettlement->id) }}",
            bFilter: false,
            bPaginate: false,
            bInfo: false,
            columns: [{
                    data: 'title',
                    name: 'title'
                },
                {
                    data: 'attachment',
                    name: 'attachment',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'link',
                    name: 'link',
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

        $('#attachmentTable').on('click', '.delete-record', function(e) {
            e.preventDefault();
            $object = $(this);
            var $url = $object.attr('data-href');
            var successCallback = function(response) {
                toastr.success(response.message, 'Success', {
                    timeOut: 5000
                });
                attachmentTable.ajax.reload();
            }
            ajaxDeleteSweetAlert($url, successCallback);
        });

        $(document).on('click', '.open-attachment-create-modal-form', function(e) {
            e.preventDefault();
            $('#openModal').find('.modal-content').html('');
            $('#openModal').modal('show').find('.modal-content').load($(this).attr('href'), function() {
                const form = document.getElementById('attachmentCreateForm');
                const fv = FormValidation.formValidation(form, {
                    fields: {
                        title: {
                            validators: {
                                notEmpty: {
                                    message: 'The attachment title is required.',
                                },
                            },
                        },
                        attachment: {
                            validators: {
                                notEmpty: {
                                    message: 'The attachment is required.',
                                },
                                file: {
                                    extension: 'jpeg,jpg,png,pdf',
                                    type: 'image/jpeg,image/png,application/pdf',
                                    maxSize: '5097152',
                                    message: 'The selected file is not valid file or must not be greater than 5 MB.',
                                },
                            },
                        },
                        attachment_link: {
                            validators: {
                                uri: {
                                    protocol: 'http, https, ftp',
                                    message: 'Please enter a valid link.'
                                }
                            }
                        }
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
                    let form = document.getElementById('attachmentCreateForm');
                    let data = new FormData(form);
                    let url = form.getAttribute('action');

                    var successCallback = function(response) {
                        $('#openModal').modal('hide');
                        toastr.success(response.message, 'Success', {
                            timeOut: 5000
                        });
                        attachmentTable.ajax.reload();
                    }
                    ajaxSubmitFormData(url, 'POST', data, successCallback);
                });
            });
        });

        $(document).on('click', '.open-attachment-edit-modal-form', function(e) {
            e.preventDefault();
            $('#openModal').find('.modal-content').html('');
            $('#openModal').modal('show').find('.modal-content').load($(this).attr('href'), function() {
                const form = document.getElementById('attachmentEditForm');
                const fv = FormValidation.formValidation(form, {
                    fields: {
                        title: {
                            validators: {
                                notEmpty: {
                                    message: 'The attachment title is required.',
                                },
                            },
                        },
                        attachment: {
                            validators: {
                                file: {
                                    extension: 'jpeg,jpg,png,pdf',
                                    type: 'image/jpeg,image/png,application/pdf',
                                    maxSize: '5097152',
                                    message: 'The selected file is not valid file or must not be greater than 5 MB.',
                                },
                            },
                        },
                        attachment_link: {
                            validators: {
                                uri: {
                                    protocol: 'http, https, ftp',
                                    message: 'Please enter a valid link.'
                                }
                            }
                        }
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
                    let form = document.getElementById('attachmentEditForm');
                    let data = new FormData(form);
                    let url = form.getAttribute('action');

                    var successCallback = function(response) {
                        $('#openModal').modal('hide');
                        toastr.success(response.message, 'Success', {
                            timeOut: 5000
                        });
                        attachmentTable.ajax.reload();
                    }
                    ajaxSubmitFormData(url, 'POST', data, successCallback);
                });
            });
        });

        // Start - Attachment Scripts Section
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
                            <a href="{{ route('advance.settlement.index') }}" class="text-decoration-none text-dark">Advance
                                Settlement
                            </a>
                        </li>
                        <li class="breadcrumb-item" aria-current="page">@yield('title')</li>
                    </ol>
                </nav>
                <h4 class="m-0 mt-1 lh1 fs-6 text-uppercase fw-bold text-primary">@yield('title')</h4>
            </div>
        </div>
    </div>

    <section class="registration">
        <div>
            <form action="{{ route('advance.settlement.update', [$advanceRequestSettlement->id]) }}"
                id="advanceSettlementEditForm" method="post" enctype="multipart/form-data" autocomplete="off">
                <div class="card">
                    <div class="card-body">
                        <div class="mb-2 row">
                            <div class="col-lg-3">
                                <div class="d-flex align-items-start h-100">
                                    <label for="validationdd" class="form-label">{!! __('label.advance-request-number') !!}</label>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div
                                    style="display: flex; flex-direction: row; flex-wrap: nowrap; justify-content: space-between; align-items: stretch;">
                                    <input class="form-control" type="text" readonly
                                        value="{{ $advanceRequestSettlement->advanceRequest->getAdvanceRequestNumber() }}" />

                                    <a class="btn btn-sm btn-outline-primary"
                                        href="{{ route('advance.requests.show', $advanceRequestSettlement->advance_request_id) }}"
                                        rel="tooltip" title="View Advance Request"
                                        style="margin-left: 2px; align-self: center">
                                        <i class="bi bi-box-arrow-in-up-right"></i></a>
                                </div>
                            </div>

                            <div class="col-lg-3">
                                <div class="d-flex align-items-start h-100">
                                    <label for="advance_issue_date" class="form-label">Advance Issue Date</label>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <input class="form-control" type="text" name="advance_issue_date" readonly
                                    value="{{ $advanceRequestSettlement->advanceRequest->approvedLog?->created_at?->format('Y-m-d') }}" />
                            </div>
                        </div>

                        <div class="mb-2 row">
                            <div class="col-lg-3">
                                <div class="d-flex align-items-start h-100">
                                    <label for="validationdd" class="form-label required-label">Completion
                                        Date</label>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <input class="form-control @if ($errors->has('completion_date')) is-invalid @endif"
                                    type="text" readonly name="completion_date"
                                    value="{{ old('completion_date') ?: $advanceRequestSettlement->completion_date->format('Y-m-d') }}" />
                                @if ($errors->has('completion_date'))
                                    <div class="fv-plugins-message-container invalid-feedback">
                                        <div data-field="completion_date">
                                            {!! $errors->first('completion_date') !!}
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <div class="col-lg-3">
                                <div class="d-flex align-items-start h-100">
                                    <label for="validationdd" class="form-label">Advance Amount</label>
                                </div>
                            </div>
                            @php $advanceAmount = old('advance_amount') ?: $advanceRequest->getEstimatedAmount() @endphp
                            <div class="col-lg-3">
                                <input class="form-control" type="text" readonly name="advance_amount"
                                    value="{{ $advanceAmount }}" />
                            </div>
                        </div>


                        <div class="mb-2 row">
                            <div class="col-lg-3">
                                <div class="d-flex align-items-start h-100">
                                    <label for="validationdd" class="form-label">Days From
                                        Settlement From Advance Issue Date</label>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <input class="form-control" type="text" readonly name="settlementDays"
                                    value="{{ $advanceRequestSettlement->advanceRequest->approvedLog->created_at->diffInDays($advanceRequestSettlement->completion_date) }}" />
                            </div>
                            <div class="col-lg-3">
                                <div class="d-flex align-items-start h-100">
                                    <label for="validationdd" class="form-label">Expenditure Paid</label>
                                </div>
                            </div>
                            @php $expenditurePaid = $advanceRequestSettlement->settlementExpenses->sum('net_amount');  @endphp
                            <div class="col-lg-3">
                                <input class="form-control" type="text" readonly name="expenditurePaid"
                                    value="{{ $expenditurePaid }}" />
                            </div>
                        </div>

                        <div class="mb-2 row">
                            <div class="col-lg-3">
                                <div class="d-flex align-items-start h-100">
                                    <label for="validationdd" class="form-label required-label">Project</label>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                @php $selectedProjectId = $advanceRequestSettlement->project_code_id; @endphp
                                <select class="select2 form-control" disabled>
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
                            <div class="col-lg-3">
                                <div class="d-flex align-items-start h-100">
                                    <label for="validationdd" class="form-label">Cash Surplus or
                                        deficit</label>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <input class="form-control cashSurplusOrDeficit" type="text" readonly
                                    name="cashSurplusOrDeficit" value="{{ $advanceAmount - $expenditurePaid }}" />
                            </div>
                        </div>


                        <div class="mb-2 row">
                            <div class="col-lg-3">
                                <div class="d-flex align-items-start h-100">
                                    <label for="reason_for_over_or_under_spending" class="form-label">Reason for
                                        over/underspending</label>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <input type="text" class="form-control reason_for_over_or_under_spending"
                                    name="reason_for_over_or_under_spending"
                                    value="{{ $advanceRequestSettlement->reason_for_over_or_under_spending }}">
                                @if ($errors->has('reason_for_over_or_under_spending'))
                                    <div class="fv-plugins-message-container invalid-feedback">
                                        <div data-field="reason_for_over_or_under_spending">
                                            {!! $errors->first('reason_for_over_or_under_spending') !!}
                                        </div>
                                    </div>
                                @endif
                            </div>
                            <div class="col-lg-3">
                                <div class="d-flex align-items-start h-100">
                                    <label for="remarks" class="form-label">Remarks</label>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <input class="form-control remarks" type="text" name="remarks"
                                    value="{{ $advanceRequestSettlement->remarks }}" />
                            </div>
                        </div>


                        <div class="mb-2 row">
                            <div class="col-lg-3">
                                <div class="d-flex align-items-start h-100">
                                    <label for="validationRemarks" class="form-label required-label">Send
                                        To</label>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                @php $selectedReviewerId = old('reviewer_id') ?: $advanceRequestSettlement->reviewer_id; @endphp
                                <select name="reviewer_id"
                                    class="select2 form-control
                                            @if ($errors->has('reviewer_id')) is-invalid @endif"
                                    data-width="100%">
                                    <option value="">Select Verifier</option>
                                    @foreach ($reviewers as $reviewer)
                                        <option value="{{ $reviewer->id }}"
                                            {{ $reviewer->id == $selectedReviewerId ? 'selected' : '' }}>
                                            {{ $reviewer->getFullName() }}</option>
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
                            <div class="col-lg-3">
                                <div class="d-flex align-items-start h-100">
                                    <label for="validationRemarks" class="form-label required-label">Approver</label>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                @php $selectedApproverId = old('approver_id') ?: $advanceRequestSettlement->approver_id; @endphp
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
                        <div class="d-flex justify-content-between align-items-center">
                            <span> Activity details</span>
                            <button data-toggle="modal" class="m-2 btn btn-primary btn-sm open-activity-modal-form"
                                href="{{ route('advance.settlement.activities.create', $advanceRequestSettlement->id) }}"><i
                                    class="bi-plus"></i> Add Activity Detail
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table" id="activityTable">
                                <thead class="thead-light">
                                    <tr>
                                        <th scope="col">Activity</th>

                                        <th>{{ __('label.action') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>


                <div class="card">
                    <div class="card-header fw-bold">
                        <div class="d-flex justify-content-between align-items-center">
                            <span>Attachments </span>
                            <button data-toggle="modal" class="btn btn-primary btn-sm open-attachment-create-modal-form"
                                href="{{ route('advance.settlement.attachment.create', $advanceRequestSettlement->id) }}"><i
                                    class="bi-plus"></i> Add Attachment
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table" id="attachmentTable">
                                <thead class="bg-light">
                                    <tr>
                                        <th scope="col">Title</th>
                                        <th scope="col" style="width: 150px">Attachment</th>
                                        <th scope="col" style="width: 150px">Link</th>
                                        <th scope="col" style="width: 150px">{{ __('label.action') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>


                <div class="card">
                    <div class="card-header fw-bold">
                        Expense details
                    </div>
                    <div class="container-fluid-s">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="expenseTable">
                                    <tr>
                                        <th></th>
                                        <th scope="col">Narration</th>
                                        <th scope="col">District</th>
                                        <th scope="col">Location</th>
                                        <th scope="col">Activity Code</th>
                                        <th scope="col">Account Code</th>
                                        <th scope="col">Donor Code</th>
                                        <th scope="col">Gross Amount</th>
                                        <th scope="col">Less Tax</th>
                                        <th scope="col">Net Amount Paid</th>
                                        <th style="width: 150px" class="sticky-col">{{ __('label.action') }}</th>
                                    </tr>
                                    @foreach ($advanceRequestSettlement->settlementExpenses as $expense)
                                        <tr id="expenseRow-{{ $expense->id }}">
                                            <td id="collapseButton" onclick="collapse(this)" style="cursor: pointer;">
                                                <i class="bi bi-plus-lg"></i>
                                            </td>
                                            <td class="narration">{{ $expense->narration }}</td>
                                            <td class="district">{{ $expense->getDistrictName() }}</td>
                                            <td class="location">{{ $expense->location }}</td>
                                            <td>{{ $expense->getActivityCode() }}</td>
                                            <td>{{ $expense->getAccountCode() }}</td>
                                            <td>{{ $expense->getDonorCode() }}</td>
                                            <td class="gross_amount">{{ $expense->gross_amount }}</td>
                                            <td class="tax_amount">{{ $expense->tax_amount }}</td>
                                            <td class="net_amount">{{ $expense->net_amount }}</td>
                                            <td class="sticky-col">
                                                <a class="btn btn-outline-primary btn-sm open-expense-modal-form"
                                                    data-toggle="modal" rel="tooltip" title="Edit Expense"
                                                    href="{{ route('advance.settlement.expense.edit', [$expense->advance_settlement_id, $expense->id]) }}">
                                                    <i class="bi-pencil"></i></a>
                                            </td>
                                        </tr>
                                        <tr id="hidden">
                                            <td></td>
                                            <td colspan="10">
                                                <div class="mb-3 d-flex align-items-center add-info justify-content-end">
                                                    <button data-toggle="modal"
                                                        class="btn btn-primary btn-sm open-expense-detail-modal-form"
                                                        href="{{ route('advance.settlement.expense.details.create', $expense->id) }}"><i
                                                            class="bi-plus"></i> Add Expense Detail
                                                    </button>
                                                </div>
                                                <table class="table table-bordered"
                                                    id="expenseTable-{{ $expense->id }}">
                                                    <tr>
                                                        <th scope="col">Date</th>
                                                        <th scope="col">Invoice/Bill No</th>
                                                        <th scope="col">Description</th>
                                                        <th scope="col">Gross Amount</th>
                                                        <th scope="col">Tax Amount (Less)</th>
                                                        <th scope="col">Net Amount Paid</th>
                                                        <th scope="col">Expense Type</th>
                                                        <th scope="col">Attachment</th>
                                                        <th style="width: 150px">Action</th>
                                                    </tr>
                                                    @foreach ($expense->details as $detail)
                                                        <tr id="detailRow-{{ $detail->id }}">
                                                            <td class="expense_date">{{ $detail->getExpenseDate() }}</td>
                                                            <td class="bill_number">{{ $detail->bill_number }}</td>
                                                            {{-- <td class="expense_category">{{ $detail->getExpenseCategory() }}</td> --}}
                                                            <td class="description">{{ $detail->getDescription() }}</td>
                                                            <td class="gross_amount">{{ $detail->gross_amount }}</td>
                                                            <td class="tax_amount">{{ $detail->tax_amount }}</td>
                                                            <td class="net_amount">{{ $detail->net_amount }}</td>
                                                            <td class="expense_type">{{ $detail->getExpenseType() }}</td>
                                                            <td class="attachment">
                                                                @if (file_exists('storage/' . $detail->attachment) && $detail->attachment != '')
                                                                    <div class="media">
                                                                        <a href="{!! asset('storage/' . $detail->attachment) !!}" target="_blank"
                                                                            class="fs-5" title="View Attachment"><i
                                                                                class="bi bi-file-earmark-medical"></i></a>
                                                                    </div>
                                                                @endif
                                                            </td>
                                                            <td class="action">
                                                                <a class="btn btn-outline-primary btn-sm open-expense-detail-modal-form"
                                                                    data-toggle="modal" rel="tooltip"
                                                                    title="Edit expense detail"
                                                                    href="{{ route('advance.settlement.expense.details.edit', [$detail->settlement_expense_id, $detail->id]) }}">
                                                                    <i class="bi-pencil"></i></a>
                                                                &emsp;<a href="javascript:;"
                                                                    class="btn btn-danger btn-sm delete-detail-record"
                                                                    data-href="{{ route('advance.settlement.expense.details.destroy', [$detail->settlement_expense_id, $detail->id]) }}">
                                                                    <i class="bi-trash"></i></a>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </table>
                                            </td>
                                        </tr>
                                    @endforeach
                                </table>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="card">
                    <div class="card-header fw-bold">
                        Expense Summary
                    </div>
                    <div class="container-fluid-s">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="settlementExpenseSummary">
                                    <thead class="thead-light">
                                        <tr>
                                            <th scope="col">Description</th>
                                            <th scope="col">Gross Amount</th>
                                            <th scope="col">Less Tax</th>
                                            <th scope="col">Net Amount Paid</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                @if ($advanceRequestSettlement->status_id == config('constant.RETURNED_STATUS'))
                    <div class="card">
                        <div class="card-header fw-bold">
                            Return Remarks
                        </div>
                        <div class="card-body">
                            {{ $advanceRequestSettlement->logs?->where('status_id', config('constant.RETURNED_STATUS'))?->first()?->log_remarks }}
                        </div>
                    </div>
                @endif

                <div class="gap-2 justify-content-end d-flex">
                    <button type="submit" name="btn" value="save" class="btn btn-primary btn-sm">Update
                    </button>
                    <button type="submit" name="btn" value="submit" class="btn btn-success btn-sm submit-record"
                        @if (!$authUser->can('submit', $advanceRequestSettlement)) style="display:none;" @endif>
                        Submit
                    </button>
                    <a href="{!! route('advance.requests.index') !!}" class="btn btn-danger btn-sm">Cancel</a>
                </div>
            </form>
        </div>
    </section>

    <div class="modal fade" id="activityModal" data-bs-backdrop="static" data-bs-keyboard="false"
        aria-labelledby="activityModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
            </div>
        </div>
    </div>

    <div class="modal fade" id="expenseDetailModal" data-bs-backdrop="static" data-bs-keyboard="false"
        aria-labelledby="expenseDetailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
            </div>
        </div>
    </div>

@stop
