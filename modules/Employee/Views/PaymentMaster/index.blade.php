@extends('layouts.container')

@section('title', __('label.payment-masters'))

@section('page_js')
    <script type="text/javascript">
        $(document).ready(function () {
            $('#navbarVerticalMenu').find('#payment-masters-menu').addClass('active');

            var oTable = $('#paymentMasterTable').DataTable({
                scrollX: true,
                processing: true,
                serverSide: true,
                ajax: "{{ route('employees.payments.masters.index', $employee->id) }}",
                columns: [{
                    data: 'start_date',
                    name: 'start_date'
                },
                    {
                        data: 'end_date',
                        name: 'end_date'
                    },
                    {
                        data: 'created_by',
                        name: 'created_by'
                    },
                    {
                        data: 'updated_at',
                        name: 'updated_at'
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

            $('#paymentMasterTable').on('click', '.delete-record', function (e) {
                e.preventDefault();
                $object = $(this);
                var $url = $object.attr('data-href');
                var successCallback = function (response) {
                    toastr.success(response.message, 'Success', {
                        timeOut: 5000
                    });
                    oTable.ajax.reload();
                }
                ajaxDeleteSweetAlert($url, successCallback);
            });

            $(document).on('click', '.open-payment-modal-form', function (e) {
                e.preventDefault();
                $('#openModal').find('.modal-content').html('');
                $('#openModal').modal('show').find('.modal-content').load($(this).attr('href'), function () {
                    const form = document.getElementById('paymentMasterForm');
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
                            start_date: {
                                validators: {
                                    notEmpty: {
                                        message: 'Start date is required',
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
                                        message: 'End date is required',
                                    },
                                    date: {
                                        format: 'YYYY-MM-DD',
                                        message: 'The value is not a valid date',
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
                                    field: 'start_date',
                                    message: 'Start date must be a valid date and earlier than end date.',
                                },
                                endDate: {
                                    field: 'end_date',
                                    message: 'End date must be a valid date and later than start date.',
                                },
                            }),
                        },
                    }).on('core.form.valid', function (event) {
                        $url = fv.form.action;
                        $form = fv.form;
                        data = $($form).serialize();
                        var successCallback = function (response) {
                            $('#openModal').modal('hide');
                            toastr.success(response.message, 'Success', {
                                timeOut: 5000
                            });
                            oTable.ajax.reload();
                        }
                        ajaxSubmit($url, 'POST', data, successCallback);
                    });

                    $(form).find('[name="start_date"]').datepicker({
                        language: 'en-GB',
                        autoHide: true,
                        format: 'yyyy-mm-dd',
                        zIndex: 2048,
                    }).on('change', function (e) {
                        fv.revalidateField('start_date');
                        fv.revalidateField('end_date');
                    });

                    $('form').find('[name="end_date"]').datepicker({
                        language: 'en-GB',
                        autoHide: true,
                        format: 'yyyy-mm-dd',
                        zIndex: 2048,
                    }).on('change', function (e) {
                        fv.revalidateField('start_date');
                        fv.revalidateField('end_date');
                    });

                });
            });
        });
    </script>
@endsection
@section('page-content')


        <div class="pb-3 mb-3 border-bottom">
            <div class="d-flex flex-column flex-lg-row align-items-start align-items-lg-center gap-2">
            <div class="brd-crms flex-grow-1">
                <nav aria-label="breadcrumb">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item">
                                <a href="{{ route('dashboard.index') }}"
                                   class="text-decoration-none text-dark">{{ __('label.home') }}</a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="{!! route('employees.index') !!}" class="text-decoration-none text-dark">Employees</a>
                            </li>
                            <li class="breadcrumb-item" aria-current="page">@yield('title')
                                : {{ $employee->getFullNameWithCode() }}</li>
                        </ol>
                    </nav>
                    <h4 class="m-0 lh1 mt-1 fs-6 text-uppercase fw-bold text-primary">@yield('title')</h4>
                </div>
                <div class="add-info justify-content-end">
                    <button data-toggle="modal" class="btn btn-primary btn-sm open-payment-modal-form"
                            href="{!! route('employees.payments.masters.create', $employee->id) !!}">
                        <i class="bi-plus"></i> Add New
                    </button>
                </div>
            </div>

        </div>

            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table" id="paymentMasterTable">
                            <thead class="thead-light">
                            <tr>
                                <th scope="col">{{ __('label.from-date') }}</th>
                                <th scope="col">{{ __('label.to-date') }}</th>
                                <th scope="col">{{ __('label.created-by') }}</th>
                                <th scope="col">{{ __('label.updated-on') }}</th>
                                <th style="width: 150px">{{ __('label.action') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

@stop
