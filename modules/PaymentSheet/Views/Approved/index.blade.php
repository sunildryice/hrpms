@extends('layouts.container')

@section('title', 'Approved Payment Sheets')

@section('page_js')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#navbarVerticalMenu').find('#approved-payment-sheets-menu').addClass('active');

            var oTable = $('#paymentSheetTable').DataTable({
                scrollX: true,
                processing: true,
                serverSide: true,
                ajax: "{{ route('approved.payment.sheets.index') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'supplier',
                        name: 'supplier',
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: 'vat_pan_number',
                        name: 'vat_pan_number',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'payment_sheet_number',
                        name: 'payment_sheet_number'
                    },
                    {
                        data: 'total_amount',
                        name: 'total_amount'
                    },
                    {
                        data: 'prepared_by',
                        name: 'prepared_by'
                    },
                    {
                        data: 'submitted_date',
                        name: 'submitted_date'
                    },
                    {
                        data: 'approved_date',
                        name: 'approved_date'
                    },
                    {
                        data: 'paid_date',
                        name: 'paid_date'
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

            $(document).on('click', '.open-payment-modal-form', function(e) {
                e.preventDefault();
                $('#openModal').find('.modal-content').html('');
                $('#openModal').modal('show').find('.modal-content').load($(this).attr('href'), function() {
                    const paymentForm = document.getElementById('paymentForm');

                    $(paymentForm.querySelector('[name="pay_date"]')).datepicker({
                        language: 'en-GB',
                        autoHide: true,
                        format: 'yyyy-mm-dd',
                        endDate: '{!! date('Y-m-d') !!}',
                        zIndex: 9999,
                    }).on('change', function(e) {
                        fv.revalidateField('pay_date');
                    });

                    const fv = FormValidation.formValidation(paymentForm, {
                            fields: {
                                pay_date: {
                                    validators: {
                                        notEmpty: {
                                            message: 'Payment Date is required',
                                        },
                                    },
                                },
                                payment_remarks: {
                                    validators: {
                                        notEmpty: {
                                            message: 'Remarks is required',
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
                        })
                        .on('core.form.valid', function(event) {
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
                            ajaxSweetAlert($url, 'POST', data, 'Make Payment!',
                            successCallback);
                            // ajaxSubmit($url, 'POST', data, successCallback);
                        });
                })
            })
        });
    </script>
@endsection
@section('page-content')
    <div class="container-fluid">
        <div class="pb-3 mb-3 border-bottom">
            <div class="d-flex flex-column flex-lg-row align-items-start align-items-lg-center gap-2">
                <div class="brd-crms flex-grow-1">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item">
                                <a href="{{ route('dashboard.index') }}" class="text-decoration-none text-dark">Home</a>
                            </li>
                            <li class="breadcrumb-item" aria-current="page">@yield('title')</li>
                        </ol>
                    </nav>
                    <h4 class="m-0 lh1 mt-1 fs-6 text-uppercase fw-bold text-primary">@yield('title')</h4>
                </div>
            </div>

        </div>
    </div>

    <div class="container-fluid">
        <div class="card shadow-sm border rounded c-tabs-content active">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table" id="paymentSheetTable">
                        <thead class="bg-light">
                            <tr>
                                <th>{{ __('label.sn') }}</th>
                                <th>{{ __('label.supplier-name') }}</th>
                                <th>{{ __('label.vat-pan-no') }}</th>
                                <th>{{ __('label.reference-no') }}</th>
                                <th>{{ __('label.amount') }}</th>
                                <th>{{ __('label.prepared-by') }}</th>
                                <th>{{ __('label.submit-date') }}</th>
                                <th>{{ __('label.approved-date') }}</th>
                                <th>{{ __('label.paid-date') }}</th>
                                <th>{{ __('label.action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@stop
