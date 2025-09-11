@extends('layouts.container')

@section('title', 'Lta Contracts')

@section('page_js')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#navbarVerticalMenu').find('#lta-menu').addClass('active');

            var oTable = $('#ltaTable').DataTable({
                scrollX: true,
                processing: true,
                serverSide: true,
                ajax: "{{ route('lta.index') }}",
                columns: [{
                        data: 'supplier_name',
                        name: 'supplier_name'
                    },
                    {
                        data: 'contract_number',
                        name: 'contract_number'
                    },
                    {
                        data: 'contract_date',
                        name: 'contract_date'
                    },
                    {
                        data: 'expiry_date',
                        name: 'expiry_date'
                    },
                    {
                        data: 'remarks',
                        name: 'remarks'
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

            $('#ltaTable').on('click', '.delete-record', function(e) {
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


            $(document).on('click', '.open-lta-modal-form', function(e) {
                e.preventDefault();
                $('#openModal').find('.modal-content').html('');
                $('#openModal').modal('show').find('.modal-content').load($(this).attr('href'), function() {
                    // $(".select2").select2({
                    //     dropdownParent: $('.modal'),
                    //     width: '100%',
                    //     dropdownAutoWidth: true
                    // });
                    $('.datepicker').datepicker({
                        language: 'en-GB',
                        autoHide: true,
                        format: 'yyyy-mm-dd',
                        endDate: '{!! date('Y-m-d') !!}',
                        zIndex: 2048,
                    });

                    $('[name="start_date"]').datepicker({
                        language: 'en-GB',
                        autoHide: true,
                        format: 'yyyy-mm-dd',
                        zIndex: 2048,
                    });
                    $('[name="end_date"]').datepicker({
                        language: 'en-GB',
                        autoHide: true,
                        format: 'yyyy-mm-dd',
                        zIndex: 2048,
                    });

                    const form = document.getElementById('ltaForm');
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
                            supplier_id: {
                                validators: {
                                    notEmpty: {
                                        message: 'Organization / Individual is required.',
                                    },
                                },
                            },
                            office_id: {
                                validators: {
                                    notEmpty: {
                                        message: 'Office is required.',
                                    },
                                },
                            },
                            contract_number: {
                                validators: {
                                    notEmpty: {
                                        message: 'The contract number is required.',
                                    },
                                },
                            },
                            // contract_amount: {
                            //     validators: {
                            //         notEmpty: {
                            //             message: 'The contract amount is required.',
                            //         },
                            //         numeric: {
                            //             message: 'The value is not a number',
                            //             thousandsSeparator: '',
                            //             decimalSeparator: '.',
                            //         },
                            //     },
                            // },
                            contract_date: {
                                validators: {
                                    notEmpty: {
                                        message: 'The contract date is required.',
                                    },
                                    date: {
                                        format: 'YYYY-MM-DD',
                                        message: 'The value is not a valid date',
                                    },
                                },
                            },
                            start_date: {
                                validators: {
                                    notEmpty: {
                                        message: 'The start date is required',
                                    },
                                    date: {
                                        format: 'YYYY-MM-DD',
                                        message: 'The value is not a valid date',
                                    },
                                },
                            },
                            end_date: {
                                validators: {
                                    notEmpty: {
                                        message: 'The end date is required.',
                                    },
                                    date: {
                                        format: 'YYYY-MM-DD',
                                        message: 'The value is not a valid date',
                                    },
                                },
                            },
                            attachment: {
                                validators: {
                                    file: {
                                        extension: 'jpeg,jpg,png,pdf',
                                        type: 'image/jpeg,image/png,application/pdf',
                                        maxSize: '2097152',
                                        message: 'The selected file is not valid file or must not be greater than 2 MB.',
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
                        $url = fv.form.action;
                        $form = fv.form;
                        data = $($form).serialize();
                        var formData = new FormData();
                        $('form input, form select, form textarea').each(function(index) {
                            var input = $(this);
                            formData.append(input.attr('name'), input.val());
                        });
                        var attachmentFiles = form.querySelector('[name="attachment"]')
                            .files;
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

                    $(form).on('change', '[name="supplier_id"]', function(e) {
                        $element = $(this);
                        var supplierId = $element.val();
                        if (supplierId) {
                            var url = baseUrl + '/suppliers/' + supplierId;
                            var successCallback = function(response) {
                                $($element).closest('form').find(
                                    '[name="contact_name"]').val(response.supplier
                                    .contact_person_name);
                                $($element).closest('form').find(
                                    '[name="contact_number"]').val(response.supplier
                                    .contact_number);
                                $($element).closest('form').find('[name="address"]')
                                    .val(response.supplier.address1);
                            }
                            var errorCallback = function(error) {
                                console.log(error);
                            }
                            ajaxNativeSubmit(url, 'GET', {}, 'json', successCallback,
                                errorCallback);
                        }
                        fv.revalidateField('supplier_id');
                    }).on('change', '[name="contract_date"]', function(e) {
                        fv.revalidateField('contract_date');
                    }).on('change', '[name="effective_date"]', function() {
                        fv.revalidateField('effective_date');
                        fv.revalidateField('expiry_date');
                    }).on('change', '[name="expiry_date"]', function() {
                        fv.revalidateField('effective_date');
                        fv.revalidateField('expiry_date');
                    });
                });
            });
        });
    </script>
@endsection
@section('page-content')

    <div class="m-content p-3">
        <div class="pb-3 mb-3 border-bottom">
            <div class="d-flex flex-column flex-lg-row align-items-start align-items-lg-center gap-2">
                <div class="brd-crms flex-grow-1">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}"
                                    class="text-decoration-none text-dark">{{ __('label.home') }}</a></li>
                            <li class="breadcrumb-item" aria-current="page">@yield('title')</li>
                        </ol>
                    </nav>
                    <h4 class="m-0 lh1 mt-1 fs-6 text-uppercase fw-bold text-primary">@yield('title')</h4>
                </div>
                <div class="add-info justify-content-end">
                    <button data-toggle="modal" class="btn btn-primary btn-sm open-lta-modal-form"
                        href="{!! route('lta.create') !!}">
                        <i class="bi-plus"></i> Add New
                    </button>
                </div>
            </div>

        </div>
        <div class="container-fluid-s">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table" id="ltaTable">
                            <thead class="bg-light">
                                <tr>
                                    <th>{{ __('label.organization-name') }}</th>
                                    <th>{{ __('label.contract-number') }}</th>
                                    <th>{{ __('label.contract-date') }}</th>
                                    <th>{{ __('label.expiry-date') }}</th>
                                    <th>{{ __('label.remarks') }}</th>
                                    <th style="width: 140px;" class="sticky-col">{{ __('label.action') }}</th>
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
@stop
