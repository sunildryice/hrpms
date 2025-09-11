@extends('layouts.container')

@section('title', __('label.payment-masters'))

@section('page_js')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#navbarVerticalMenu').find('#payment-masters-menu').addClass('active');

            var oTable = $('#paymentDetailTable').DataTable({
                scrollX: true,
                processing: true,
                serverSide: true,
                ajax: "{{ route('employees.payments.masters.details.index', $paymentMaster->id) }}",
                columns: [{
                        data: 'payment_item',
                        name: 'payment_item'
                    },
                    {
                        data: 'type',
                        name: 'type'
                    },
                    {
                        data: 'amount',
                        name: 'amount'
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

            $('#paymentDetailTable').on('click', '.delete-record', function(e) {
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

            $(document).on('click', '.open-detail-modal-form', function (e) {
                e.preventDefault();
                $('#openModal').find('.modal-content').html('');
                $('#openModal').modal('show').find('.modal-content').load($(this).attr('href'), function () {
                    const form = document.getElementById('paymentDetailForm');
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
                            payment_item_id: {
                                validators: {
                                    notEmpty: {
                                        message: 'Payment item is required.',
                                    },
                                },
                            },
                            amount: {
                                validators: {
                                    notEmpty: {
                                        message: 'Amount is required.',
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
                            oTable.ajax.reload();
                        }
                        ajaxSubmit($url, 'POST', data, successCallback);
                    });

                    $(form).on('change', '[name="payment_item_id"]', function(e) {
                        fv.revalidateField('payment_item_id');
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
                            <li class="breadcrumb-item">
                                <a href="{{ route('dashboard.index') }}"
                                    class="text-decoration-none text-dark">{{ __('label.home') }}</a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="{!! route('employees.index') !!}" class="text-decoration-none">Employees</a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="{!! route('employees.payments.masters.index', $paymentMaster->employee_id) !!}" class="text-decoration-none">Payment Master</a>
                            </li>
                            <li class="breadcrumb-item" aria-current="page">@yield('title')
                                : {{ $paymentMaster->employee->getFullNameWithCode() }}</li>
                        </ol>
                    </nav>
                    <h4 class="m-0 lh1 mt-1 fs-6 text-uppercase fw-bold text-primary">@yield('title')</h4>
                </div>
                <div class="add-info justify-content-end">
                    <button data-toggle="modal" class="btn btn-primary btn-sm open-detail-modal-form"
                        href="{!! route('employees.payments.masters.details.create', $paymentMaster->id) !!}">
                        <i class="bi-plus"></i> Add New
                    </button>
                </div>
            </div>

        </div>
        <div class="container-fluid-s">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table" id="paymentDetailTable">
                            <thead class="thead-light">
                                <tr>
                                    <th scope="col">{{ __('label.payment-item') }}</th>
                                    <th scope="col">{{ __('label.type') }}</th>
                                    <th scope="col">{{ __('label.amount') }}</th>
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

        </div>
    </div>

@stop
