@extends('layouts.container')

@section('title', __('label.offices'))

@section('page_js')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#navbarVerticalMenu').find('#offices-menu').addClass('active');

            var oTable = $('#officeTable').DataTable({
                scrollX: true,
                processing: true,
                serverSide: true,
                ajax: "{{ route('master.offices.index') }}",
                columns: [{
                        data: 'office_name',
                        name: 'office_name'
                    },
                    {
                        data: 'office_code',
                        name: 'office_code'
                    },
                    {
                        data: 'office_type',
                        name: 'office_type'
                    },
                    {
                        data: 'phone_number',
                        name: 'phone_number'
                    },
                    {
                        data: 'email_address',
                        name: 'email_address'
                    },
                    {
                        data: 'district',
                        name: 'district'
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

            $('#officeTable').on('click', '.delete-record', function(e) {
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


            $(document).on('click', '.open-office-modal-form', function(e) {
                e.preventDefault();
                $('#openModal').find('.modal-content').html('');
                $('#openModal').modal('show').find('.modal-content').load($(this).attr('href'), function (){
                    const form = document.getElementById('officeForm');
                    $(form).find(".select2").select2({
                        dropdownParent: $('.modal'),
                        width: '100%',
                        dropdownAutoWidth: true
                    });
                    const fv = FormValidation.formValidation(form, {
                        fields: {
                            office_name: {
                                validators: {
                                    notEmpty: {
                                        message: 'Office name is required',
                                    },
                                },
                            },
                            office_code: {
                                validators: {
                                    notEmpty: {
                                        message: 'Office code is required',
                                    },
                                },
                            },
                            office_type_id: {
                                validators: {
                                    notEmpty: {
                                        message: 'Office type is required',
                                    },
                                },
                            },
                            phone_number: {
                                validators: {
                                    notEmpty: {
                                        message: 'Phone number is required',
                                    },
                                },
                            },
                            email_address: {
                                validators: {
                                    emailAddress: {
                                        message: 'The input must be a email address',
                                    },
                                },
                            },
                            district_id: {
                                validators: {
                                    notEmpty: {
                                        message: 'District is required',
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
                    $(form).on('change', '[name="district_id"]', function(e) {
                        fv.revalidateField('district_id');
                    });

                    $(form).on('change', '[name="office_type_id"]', function (e) {
                        let officeTypeId = $(this).val();
                        let url = "{{route('master.offices.get.by.office.type', ':id')}}";
                        url = url.replace(':id', officeTypeId);
                        let callback = (data) => {
                            document.getElementById('parent_id').innerHTML = '';
                            document.getElementById('parent_id').innerHTML = data;
                        };
                        $.get(url, callback);
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
                                    class="text-decoration-none">{{ __('label.master') }}</a></li>
                            <li class="breadcrumb-item" aria-current="page">@yield('title')</li>
                        </ol>
                    </nav>
                    <h4 class="m-0 lh1 mt-1 fs-6 text-uppercase fw-bold text-primary">@yield('title')</h4>
                </div>
                <div class="add-info justify-content-end">
                    <button data-toggle="modal" class="btn btn-primary btn-sm open-office-modal-form"
                        href="{!! route('master.offices.create') !!}">
                        <i class="bi-plus"></i> Add New
                    </button>
                </div>
            </div>

        </div>
        <div class="container-fluid-s">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table" id="officeTable">
                            <thead class="thead-light">
                                <tr>
                                    <th scope="col">{{ __('label.office') }}</th>
                                    <th scope="col">{{ __('label.office-code') }}</th>
                                    <th scope="col">Office Type</th>
                                    <th scope="col">{{ __('label.phone-number') }}</th>
                                    <th scope="col">{{ __('label.email-address') }}</th>
                                    <th scope="col">{{ __('label.district') }}</th>
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
