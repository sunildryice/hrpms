@extends('layouts.container')

@section('title', 'Employee Exit Payable')
@section('page_css')
@endsection
@section('page_js')
    <script type="text/javascript">
            document.addEventListener('DOMContentLoaded', function (e) {
             $('#navbarVerticalMenu').find('[href="#navbarEmployeeExit"]').addClass('active').attr('aria-expanded', 'true');
            $('#navbarVerticalMenu').find('#navbarEmployeeExit').addClass('show');
            $('#navbarVerticalMenu').find('#update-employees-exit-payable').addClass('active');
            // $('#navbarVerticalMenu').find('#advance-requests-menu').addClass('active');

            const form = document.getElementById('exitHandOverNoteEditForm');
            const fv = FormValidation.formValidation(form, {
                fields: {
                    duty_description: {
                        validators: {
                            notEmpty: {
                                message: 'Duty Description is required',
                            },
                        },
                    },
                    reporting_procedures: {
                        validators: {
                            notEmpty: {
                                message: 'Reporting Procedures is required',
                            },
                        },
                    },

                    meeting_description: {
                        validators: {
                            notEmpty: {
                                message: 'Meeting Description is required',
                            },
                        },
                    },

                     contact_after_exit: {
                        validators: {
                            notEmpty: {
                                message: 'Contact After Exit is required',
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

        });

            $(document).on('shown.bs.modal', '#openModal', function (e) {
            const form = document.getElementById('employeePayable');
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
                    donor_code_id: {
                        validators: {
                            notEmpty: {
                                message: 'Donor code is required',
                            },
                        },
                    },
                    description: {
                        validators: {
                            notEmpty: {
                                message: 'Description code is required',
                            },
                        },
                    },
                    amount: {
                        validators: {
                            notEmpty: {
                                message: 'Amount is required',
                            },
                            greaterThan: {
                                message: 'The value must be greater than or equal to 0.01',
                                min: 0.01,
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
            }).on('core.form.valid', function (event) {
                $url = fv.form.action;
                $form = fv.form;
                data = $($form).serialize();
                var successCallback = function (response) {
                    $('#openModal').modal('hide');
                    toastr.success(response.message, 'Success', {timeOut: 5000});
                    oTable.ajax.reload();
                }
                ajaxSubmit($url, 'POST', data, successCallback);
            });
        });

        var oTable = $('#exitPayableTable').DataTable({
                scrollX: true,
            processing: true,
            serverSide: true,
            ajax: "{{ route('exit.approve.payable.index') }}",
            bFilter: false,
            bPaginate: false,
            bInfo: false,
            columns: [
                {data: 'employee', name: 'employee'},
                {data: 'status', name: 'status'},
                {data: 'action', name: 'action', orderable: false, searchable: false},
            ]
        });

         $('#exitPayableTable').on('click', '.delete-record', function (e) {
            e.preventDefault();
            $object = $(this);
            var $url = $object.attr('data-href');
            var successCallback = function (response) {
                toastr.success(response.message, 'Success', {timeOut: 5000});
                oTable.ajax.reload();
            }
            ajaxDeleteSweetAlert($url, successCallback);
        });

    </script>
@endsection

@section('page-content')

        <div class="page-header pb-3 mb-3 border-bottom">
            <div class="d-flex flex-column flex-lg-row align-items-start align-items-lg-center gap-2">
            <div class="brd-crms flex-grow-1">
                <nav aria-label="breadcrumb">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}" class="text-decoration-none text-dark">Home</a></li>
                            <li class="breadcrumb-item" aria-current="page">@yield('title')</li>
                        </ol>
                    </nav>
                    <h4 class="m-0 lh1 mt-1 fs-6 text-uppercase fw-bold text-primary">@yield('title')</h4>
                </div>
               {{-- <div class="add-info justify-content-end">
                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#exampleModal"><i
                            class="bi-wrench-adjustable me-1"></i> New Employee Exit</button>
                </div> --}}
            </div>
        </div>
        <section class="registration">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive mb-3">
                        <table class="table table-borderedless" id="exitPayableTable">
                            <thead class="bg-light">
                            <tr>

                                <!-- <th style="width:45px;"></th> -->
                                <th class="">Employee</th>
                                <th>Status</th>
                                <th style="width: 130px;">Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
          </div>

        </section>
@stop
