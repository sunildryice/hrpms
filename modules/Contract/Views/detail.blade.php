@extends('layouts.container')

@section('title', 'Contract Detail')

@section('page_js')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#navbarVerticalMenu').find('#contracts-menu').addClass('active');
        });

        var oTable = $('#amendmentTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('contracts.amendments.index', $contract->id) }}",
            bFilter: false,
            bPaginate: false,
            bInfo: false,
            columns: [{
                    data: 'amendment_number',
                    name: 'amendment_number'
                },
                {
                    data: 'expiry_date',
                    name: 'expiry_date'
                },
                {
                    data: 'contract_amount',
                    name: 'contract_amount'
                },
                {
                    data: 'attachment',
                    name: 'attachment'
                },
                {
                    data: 'remarks',
                    name: 'remarks'
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                },
            ]
        });

        $('#amendmentTable').on('click', '.delete-record', function(e) {
            e.preventDefault();
            $object = $(this);
            var $url = $object.attr('data-href');
            var successCallback = function(response) {
                toastr.success(response.message, 'Success', {
                    timeOut: 5000
                });
                oTable.ajax.reload();
            }
            ajaxDeleteSweetAlert($url, successCallback);
        });

        $(document).on('click', '.open-contract-modal-form', function(e) {
            e.preventDefault();
            $('#openModal').find('.modal-content').html('');
            $('#openModal').modal('show').find('.modal-content').load($(this).attr('href'), function() {
                $('[name="expiry_date"]').datepicker({
                    language: 'en-GB',
                    autoHide: true,
                    format: 'yyyy-mm-dd',
                    zIndex: 2048,
                });
                const amendForm = document.getElementById('contractAmendForm');
                const amendFv = FormValidation.formValidation(amendForm, {
                    fields: {
                        effective_date: {
                            validators: {
                                notEmpty: {
                                    message: 'The effective date is required.',
                                },
                                date: {
                                    format: 'YYYY-MM-DD',
                                    message: 'The value is not a valid date',
                                },
                            },
                        },
                        expiry_date: {
                            validators: {
                                notEmpty: {
                                    message: 'The expiry date is required.',
                                },
                                date: {
                                    format: 'YYYY-MM-DD',
                                    message: 'The value is not a valid date',
                                },
                            },
                        },
                        contract_amount: {
                            validators: {
                                notEmpty: {
                                    message: 'The contract amount is required.',
                                },
                                numeric: {
                                    message: 'The value is not a number',
                                    thousandsSeparator: '',
                                    decimalSeparator: '.',
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

                        startEndDate: new FormValidation.plugins.StartEndDate({
                            format: 'YYYY-MM-DD',
                            startDate: {
                                field: 'effective_date',
                                message: 'Effective date must be a valid date and earlier than expiry date.',
                            },
                            endDate: {
                                field: 'expiry_date',
                                message: 'Expiry date must be a valid date and later than effective date.',
                            },
                        }),
                    },
                }).on('core.form.valid', function(event) {
                    $url = amendFv.form.action;
                    $form = amendFv.form;
                    data = $($form).serialize();
                    var formData = new FormData();
                    $('form input, form select, form textarea').each(function(index) {
                        var input = $(this);
                        formData.append(input.attr('name'), input.val());
                    });
                    var attachmentFiles = amendForm.querySelector('[name="attachment"]').files;
                    if (attachmentFiles.length > 0) {
                        formData.append('attachment', attachmentFiles[0]);
                    }

                    var successCallback = function(response) {
                        $('#openModal').modal('hide');
                        toastr.success(response.message, 'Success', {
                            timeOut: 5000
                        });
                        oTable.ajax.reload();
                    }
                    ajaxSubmitFormData($url, 'POST', formData, successCallback);
                });

                $(amendForm).on('change', '[name="contract_date"]', function(e) {
                    amendFv.revalidateField('contract_date');
                }).on('change', '[name="expiry_date"]', function() {
                    amendFv.revalidateField('expiry_date');
                });
            });
        });
    </script>
@endsection
@section('page-content')
    <div class="p-3 m-content">
        <div class="container-fluid">

            <div class="pb-3 mb-3 page-header border-bottom">
                <div class="d-flex align-items-center">
                    <div class="brd-crms flex-grow-1">
                        <nav aria-label="breadcrumb">
                            <ol class="m-0 breadcrumb">
                                <li class="breadcrumb-item">
                                    <a href="{!! route('dashboard.index') !!}" class="text-decoration-none text-dark">Home</a>
                                </li>
                                <li class="breadcrumb-item">
                                    <a href="{{ route('contracts.index') }}" class="text-decoration-none">Contracts</a>
                                </li>
                                <li class="breadcrumb-item" aria-current="page">@yield('title')</li>
                            </ol>
                        </nav>
                        <h4 class="m-0 mt-1 lh1 fs-6 text-uppercase fw-bold text-primary">@yield('title')</h4>
                    </div>
                </div>
            </div>

            <section class="registration">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-header fw-bold">
                                Contract Details
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="table-responsive">
                                            <table class="table display table-bordered table-condensed">
                                                <tr>
                                                    <td class="gray-bg">Supplier</td>
                                                    <td>{{ $contract->getSupplierName() }}</td>
                                                    <td class="gray-bg">VAT/PAN No.</td>
                                                    <td>{{ $contract->getVATPANNo() }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="gray-bg">Contract Number</td>
                                                    <td>{{ $contract->contract_number }}</td>
                                                    <td class="gray-bg">Contract Amount</td>
                                                    <td>{{ number_format($contract->contract_amount, 2) }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="gray-bg">Description</td>
                                                    <td colspan="3">{{ $contract->description }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="gray-bg">Contact Name</td>
                                                    <td>{{ $contract->contact_name }}</td>
                                                    <td class="gray-bg">Contact Number</td>
                                                    <td>{{ $contract->contact_number }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="gray-bg">Address</td>
                                                    <td>{{ $contract->address }}</td>
                                                    <td class="gray-bg">Contract Date</td>
                                                    <td>{{ $contract->getContractDate() }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="gray-bg">Effective Date</td>
                                                    <td>{{ $contract->getEffectiveDate() }}</td>
                                                    <td class="gray-bg">Expiry Date</td>
                                                    <td>{{ $contract->expiry_date->format('M d, Y') }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="gray-bg">Reminder Days</td>
                                                    <td>{{ $contract->reminder_days }}</td>
                                                    <td class="gray-bg">Termination Days</td>
                                                    <td>{{ $contract->termination_days }}</td>
                                                </tr>
                                                <tr>

                                                    <td class="gray-bg">Focal Person</td>
                                                    <td>{{ $contract->getFocalPersonName() }}</td>
                                                    <td class="gray-bg">Contract Attachment</td>
                                                    <td>
                                                        @if (file_exists('storage/' . $contract->attachment) && $contract->attachment != '')
                                                            <a href="{!! asset('storage/' . $contract->attachment) !!}" target="_blank" class="fs-5"
                                                                title="View Attachment">
                                                                <i class="bi bi-file-earmark-medical"></i>
                                                            </a>
                                                        @else
                                                            File does not exists.
                                                        @endif
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="gray-bg">Remarks</td>
                                                    <td colspan="3">{{ $contract->remarks }}</td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header fw-bold d-flex justify-content-between">
                                <span>
                                    Contract Amendments
                                </span>
                                <div>
                                    <div class="d-flex align-items-center add-info justify-content-end">
                                        @if ($authUser->can('amend', $contract))
                                            <button data-toggle="modal"
                                                class="mt-2 btn btn-primary btn-sm open-contract-modal-form me-2"
                                                href="{!! route('contracts.amendments.create', $contract->id) !!}"><i class="bi-plus"></i> Amend Contract
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="table-responsive">
                                            <table class="table display table-bordered table-condensed" id="amendmentTable">
                                                <thead>
                                                    <tr>
                                                        <td class="gray-bg">Amend Number</td>
                                                        <td class="gray-bg">Expiry Date</td>
                                                        <td class="gray-bg">Amount</td>
                                                        <td class="gray-bg">Attachment</td>
                                                        <td class="gray-bg">Remarks</td>
                                                        <td class="gray-bg">Action</td>
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
                    </div>
                </div>
            </section>
        </div>
    </div>
@stop
