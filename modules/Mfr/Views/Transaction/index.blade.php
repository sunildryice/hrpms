@extends('layouts.container')

@section('title', 'Transactions')

@section('page_js')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#navbarVerticalMenu').find('#agreement-index').addClass('active');

            var oTable = $('#agreementTable').DataTable({
                scrollX: true,
                processing: true,
                serverSide: true,
                ajax: "{{ route('mfr.agreement.show.transactions', $agreement->id) }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'transaction_date',
                        name: 'transaction_date'
                    },
                    {
                        data: 'release_amount',
                        data: 'release_amount',
                    },
                    {
                        data: 'expense_amount',
                        name: 'expense_amount'
                    },
                    {
                        data: 'reimbursed_amount',
                        name: 'reimbursed_amount'
                    },
                    {
                        data: 'questioned_cost',
                        name: 'questioned_cost'
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        className: "sticky-col"
                    },
                ]
            });

            $('#agreementTable').on('click', '.delete-record', function(e) {
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

            $(document).on('click', '.open-transaction-modal-form', function(e) {
                e.preventDefault();
                $('#openModal').find('.modal-content').html('');
                $('#openModal').modal('show').find('.modal-content').load($(this).attr('href'), function() {
                    const form = document.getElementById('transactionForm');
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
                            transaction_type: {
                                validators: {
                                    notEmpty: {
                                        message: 'Transaction type is required',
                                    },
                                },
                            },
                            transaction_date: {
                                validators: {
                                    notEmpty: {
                                        message: 'Transaction date is required',
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
                        fv.form.submit();
                    });

                    $(form.querySelector('[name="transaction_date"]')).datepicker({
                        language: 'en-GB',
                        autoHide: true,
                        format: 'yyyy-mm-dd',
                        zIndex: 999999,
                    }).on('change', function(e) {
                        //fv.revalidateField('transaction_date');
                        //if (form.querySelector('[name="transaction_date"]').value) {
                        //   fv.revalidateField('transaction_date');
                        //}
                    });


                });
            });

            let error = {!! $errors !!};
            // console.log(error);
            if (error.attendance_file) {
                $('#importAttendanceModal').modal('show');
            }

            // $('#importAttendanceModal').show();

        });
    </script>
@endsection
@section('page-content')

    <div class="pb-3 mb-3 border-bottom">
        <div class="gap-2 d-flex flex-column flex-lg-row align-items-start align-items-lg-center">
            <div class="brd-crms flex-grow-1">
                <nav aria-label="breadcrumb">
                    <ol class="m-0 breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{!! route('dashboard.index') !!}"
                                class="text-decoration-none text-dark">{{ __('label.home') }}</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{!! route('mfr.agreement.index') !!}" class="text-decoration-none text-dark">MFR</a>
                        </li>
                        <li class="breadcrumb-item" aria-current="page">Transactions</li>
                    </ol>
                </nav>
                <h4 class="m-0 mt-1 lh1 fs-6 text-uppercase fw-bold text-primary">@yield('title'):<a rel="tooltip"
                        title="View Mfr Agreement" href="{{ route('mfr.agreement.show', $agreement->id) }}"
                        class="text-decoration-none">
                        {{ $agreement->getPOName() }} <i class="bi-box-arrow-up-right"></i></a></h4>
            </div>
            <div class="mb-2">
                {{-- @if (auth()->user()->can('create', $agreement)) --}}
                    <button type="button" class="btn btn-primary btn-sm open-transaction-modal-form"
                        href="{{ route('mfr.transaction.create', $agreement->id) }}">
                        Add New
                    </button>
                {{-- @endif --}}
            </div>
        </div>
    </div>



    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-borderedless" id="agreementTable">
                    <thead class="bg-light">
                        <tr>
                            <th>{{ __('label.sn') }}</th>
                            <th>{{ __('label.transaction-date') }}</th>
                            <th>{{ __('label.advance-released') }}</th>
                            <th>{{ __('label.mfr-expenditure') }}</th>
                            <th>{{ __('label.expenditure-reimbursed') }}</th>
                            <th>{{ __('label.questioned-cost') }}</th>
                            <th>{{ __('label.status') }}</th>
                            <th style="width:95px;">{{ __('label.action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

@stop
