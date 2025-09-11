@extends('layouts.container')

@section('title', __('label.suppliers'))

@section('page_js')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#navbarVerticalMenu').find('#supplier-menu').addClass('active');

            var oTable = $('#supplierTable').DataTable({
                scrollX: true,
                processing: true,
                serverSide: true,
                scrollX: true,
                ajax: "{{ route('suppliers.index') }}",
                columns: [{
                        data: 'supplier_name',
                        name: 'supplier_name'
                    },
                    {
                        data: 'vat_pan_number',
                        name: 'vat_pan_number'
                    },
                    {
                        data: 'contact_number',
                        name: 'contact_number'
                    },
                    {
                        data: 'email_address',
                        name: 'email_address'
                    },
                    {
                        data: 'contact_person_name',
                        name: 'contact_person_name'
                    },
                    {
                        data: 'contact_person_email_address',
                        name: 'contact_person_email_address'
                    },
                    {
                        data: 'account_number',
                        name: 'account_number'
                    },
                    {
                        data: 'account_name',
                        name: 'account_name'
                    },
                    {
                        data: 'bank_name',
                        name: 'bank_name'
                    },
                    {
                        data: 'branch_name',
                        name: 'branch_name'
                    },
                    // {
                    //     data: 'swift_code',
                    //     name: 'swift_code'
                    // },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                ]
            });

            $('#supplierTable').on('click', '.delete-record', function(e) {
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


            $(document).on('click', '.open-supplier-modal-form', function(e) {
                e.preventDefault();
                $('#openModal').find('.modal-content').html('');
                $('#openModal').modal('show').find('.modal-content').load($(this).attr('href'), function() {
                    const form = document.getElementById('supplierAddForm') ? document
                        .getElementById(
                            'supplierAddForm') : document.getElementById('supplierEditForm');
                    const fv = FormValidation.formValidation(form, {
                        fields: {
                            supplier_name: {
                                validators: {
                                    notEmpty: {
                                        message: 'Supplier name is required.',
                                    },
                                },
                            },
                            email_address: {
                                validators: {
                                    emailAddress: {
                                        message: 'The value is not a valid email address.',
                                    },
                                },
                            },
                            contact_person_email_address: {
                                validators: {
                                    emailAddress: {
                                        message: 'The value is not a valid email address.',
                                    },
                                },
                            },
                            vat_pan_number: {
                                validators: {
                                    between: {
                                        min: 100000000,
                                        max: 999999999,
                                        message: 'The VAT/PAN number is of 9 digits.',
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
                            <li class="breadcrumb-item"><a href="#"
                                    class="text-decoration-none">{{ __('label.master') }}</a>
                            </li>
                            <li class="breadcrumb-item" aria-current="page">@yield('title')</li>
                        </ol>
                    </nav>
                    <h4 class="m-0 lh1 mt-1 fs-6 text-uppercase fw-bold text-primary">@yield('title')</h4>
                </div>
                <div class="add-info justify-content-end">
                    <button data-toggle="modal" class="btn btn-primary btn-sm open-supplier-modal-form"
                        href="{!! route('suppliers.create') !!}">
                        <i class="bi-plus"></i> Add New
                    </button>
                </div>
            </div>

        </div>
        <div class="container-fluid-s">
            <div class="card">
                <span><a style="float: right;" href="{{ route('suppliers.export') }}" role="button"
                        class="btn btn-sm btn-primary mt-3 me-3">Export</a></span>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table" id="supplierTable">
                            <thead class="bg-light">
                                <tr>
                                    <th>{{ __('label.supplier-name') }}</th>
                                    <th>{{ __('label.vat-pan-no') }}</th>
                                    <th>{{ __('label.contact-no') }}</th>
                                    <th>{{ __('label.email-address') }}</th>
                                    <th>{{ __('label.contact-person-name') }}</th>
                                    <th>{{ __('label.contact-person-email') }}</th>
                                    <th>{{ __('label.account-number') }}</th>
                                    <th>{{ __('label.account-name') }}</th>
                                    <th>{{ __('label.bank-name') }}</th>
                                    <th>{{ __('label.branch-name') }}</th>
                                    {{-- <th>{{ __('label.swift-code') }}</th> --}}
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
    </div>

@stop
