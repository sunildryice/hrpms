@extends('layouts.container')

@section('title', 'Employee Exit HandOver')

@section('page_js')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#navbarVerticalMenu').find('#employees-exit-menu').addClass('active');

        $(document).on('click', '.open-handover-modal-form', function(e) {
            e.preventDefault();
            $('#openModal').modal('show').find('.modal-content').load($(this).attr('href'), function (){
                    const form = document.getElementById('AddHandOverForm');
                    $(form).find(".select2").each(function() {
                        $(this)
                            .wrap("<div class=\"position-relative\"></div>")
                            .select2({
                                dropdownParent: $(this).parent(),
                                width: '100%',
                                dropdownAutoWidth: true
                            });
                    });

                    $('.datepicker').datepicker({
                        language: 'en-GB',
                        autoHide: true,
                        format: 'yyyy-mm-dd',
                        // endDate: '{!! date('Y-m-d') !!}',
                        zIndex: 2048,
                    });

                    const fv = FormValidation.formValidation(form, {
                        fields: {
                            employee_id: {
                                validators: {
                                    notEmpty: {
                                        message: 'Employee is required',
                                    },
                                },
                            },
                            last_duty_date: {
                                validators: {
                                    notEmpty: {
                                        message: 'Last Date of Duty is required',
                                    },
                                },
                            },
                            // resignation_date: {
                            //     validators: {
                            //         notEmpty: {
                            //             message: 'Resignation Date is required',
                            //         },
                            //     },
                            // },

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

        var oTable = $('#exitHandOverRequestTable').DataTable({
                scrollX: true,
            processing: true,
            serverSide: true,
            ajax: "{{ route('employee.exits.index') }}",
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'employee_name',
                    name: 'employee_name'
                },
                {
                    data: 'last_duty_date',
                    name: 'last_duty_date'
                },{
                    data: 'resignation_date',
                    name: 'resignation_date'
                },
                {
                    data: 'insurance',
                    name: 'insurance'
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false,
                    className:'sticky-col'
                },
            ]
        });

        $('#exitHandOverRequestTable').on('click', '.delete-record', function(e) {
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

    </script>
@endsection
@section('page-content')

        <div class="pb-3 mb-3 border-bottom">
            <div class="d-flex flex-column flex-lg-row align-items-start align-items-lg-center gap-2">
            <div class="brd-crms flex-grow-1">
                <nav aria-label="breadcrumb">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}"
                                    class="text-decoration-none text-dark">Home</a></li>
                            {{-- <li class="breadcrumb-item"><a href="#" class="text-decoration-none">HR</a></li> --}}
                            <li class="breadcrumb-item" aria-current="page">@yield('title')</li>
                        </ol>
                    </nav>
                    <h4 class="m-0 lh1 mt-1 fs-6 text-uppercase fw-bold text-primary">@yield('title')</h4>
                </div>
                <div class="add-info justify-content-end">
                    <button data-toggle="modal" class="btn btn-primary btn-sm open-handover-modal-form"
                        href="{!! route('employee.exits.create') !!}"><i class="bi-plus"></i> Add Exit HandOver
                    </button>
                </div>
            </div>

        </div>
        <div class="card" id="employee-table">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table" id="exitHandOverRequestTable">
                        <thead class="bg-light">
                            <tr>
                                <th>{{ __('label.sn') }}</th>
                                <th>Employee Name</th>
                                <th>Last Date of Duty</th>
                                <th>Resignation Date</th>
                                <th>Is Insurance ?</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
@stop
