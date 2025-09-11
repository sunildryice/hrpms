@extends('layouts.container')

@section('title', 'Employee Exit Payable')
@section('page_css')
@endsection
@section('page_js')
    <script type="text/javascript">
        document.addEventListener('DOMContentLoaded', function (e) {
            $('#navbarVerticalMenu').find('#create-employee-exit-payable').addClass('active');
        });

        $(document).on('click', '.open-payable-modal-form', function(e) {
            e.preventDefault();
            $('#openModal').find('.modal-content').html('');
            $('#openModal').modal('show').find('.modal-content').load($(this).attr('href'), function (){
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
                        employee_id: {
                            validators: {
                                notEmpty: {
                                    message: 'Employee is required.',
                                },
                            },
                        },
                        // salary_date_from: {
                        //     validators: {
                        //         notEmpty: {
                        //             message: 'Salary Date from is required.',
                        //         },
                        //     },
                        // },
                        // salary_date_to: {
                        //     validators: {
                        //         notEmpty: {
                        //             message: 'Salary Date to is required.',
                        //         },
                        //     },
                        // },
                        leave_balance: {
                            validators: {
                                notEmpty: {
                                    message: 'Leave Balance is required.',
                                },
                            },
                        },
                        salary_amount: {
                            validators: {
                                notEmpty: {
                                    message: 'Salary amount is required.',
                                },
                            },
                            greaterThan: {
                                message: 'The value must be greater than or equal to 0',
                                min: 0,
                            },
                        },
                        festival_bonus: {
                            validators: {
                                notEmpty: {
                                    message: 'Festival bonus is required.',
                                },
                                greaterThan: {
                                    message: 'The value must be greater than or equal to 0',
                                    min: 0,
                                },
                            },
                        },
                        gratuity_amount: {
                            validators: {
                                notEmpty: {
                                    message: 'Gratuity amount is required.',
                                },
                                greaterThan: {
                                    message: 'The value must be greater than or equal to 0',
                                    min: 0,
                                },
                            },
                        },
                        other_amount: {
                            validators: {
                                notEmpty: {
                                    message: 'Other amount is required.',
                                },
                                greaterThan: {
                                    message: 'The value must be greater than or equal to 0',
                                    min: 0,
                                },
                            },
                        },
                        advance_amount: {
                            validators: {
                                notEmpty: {
                                    message: 'Advance amount is required.',
                                },
                                greaterThan: {
                                    message: 'The value must be greater than or equal to 0',
                                    min: 0,
                                },
                            },
                        },
                        loan_amount: {
                            validators: {
                                notEmpty: {
                                    message: 'Loan amount is required.',
                                },
                                greaterThan: {
                                    message: 'The value must be greater than or equal to 0',
                                    min: 0,
                                },
                            },
                        },
                        other_payable_amount: {
                            validators: {
                                notEmpty: {
                                    message: 'Other payable amount is required.',
                                },
                                greaterThan: {
                                    message: 'The value must be greater than or equal to 0',
                                    min: 0,
                                },
                            },
                        },
                        deduction_amount: {
                            validators: {
                                greaterThan: {
                                    message: 'The deduction amount must be greater than or equal to 0',
                                    min: 0,
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
                        salaryDate: new FormValidation.plugins.StartEndDate({
                            format: 'YYYY-MM-DD',
                            startDate: {
                                field: 'salary_date_from',
                                message: 'Start date must be a valid date and earlier than End date.',
                            },
                            endDate: {
                                field: 'salary_date_to',
                                message: 'End date must be a valid date and later than Start date.',
                            },
                        }),
                        festivalBonusDate: new FormValidation.plugins.StartEndDate({
                            format: 'YYYY-MM-DD',
                            startDate: {
                                field: 'festival_bonus_date_from',
                                message: 'Start date must be a valid date and earlier than End date.',
                            },
                            endDate: {
                                field: 'festival_bonus_date_to',
                                message: 'End date must be a valid date and later than Start date.',
                            },
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

                $(form).find('[name="salary_date_from"]').datepicker({
                        language: 'en-GB',
                        autoHide: true,
                        format: 'yyyy-mm-dd',
                        zIndex: 2048,
                    }).on('change', function (e) {
                        fv.revalidateField('salary_date_from');
                        fv.revalidateField('salary_date_to');
                    });

                $(form).find('[name="salary_date_to"]').datepicker({
                    language: 'en-GB',
                    autoHide: true,
                    format: 'yyyy-mm-dd',
                    zIndex: 2048,
                }).on('change', function (e) {
                    fv.revalidateField('salary_date_from');
                    fv.revalidateField('salary_date_to');
                });
                
                $(form).find('[name="festival_bonus_date_from"]').datepicker({
                        language: 'en-GB',
                        autoHide: true,
                        format: 'yyyy-mm-dd',
                        zIndex: 2048,
                    }).on('change', function (e) {
                        fv.revalidateField('festival_bonus_date_from');
                        fv.revalidateField('festival_bonus_date_to');
                    });

                $(form).find('[name="festival_bonus_date_to"]').datepicker({
                    language: 'en-GB',
                    autoHide: true,
                    format: 'yyyy-mm-dd',
                    zIndex: 2048,
                }).on('change', function (e) {
                    fv.revalidateField('festival_bonus_date_from');
                    fv.revalidateField('festival_bonus_date_to');
                });
            });
        });

        var oTable = $('#exitPayableTable').DataTable({
                scrollX: true,
            processing: true,
            serverSide: true,
            ajax: "{{ route('exit.payable.index') }}",
            bFilter: false,
            bPaginate: false,
            bInfo: false,
            columns: [
                {data: 'employee', name: 'employee'},
                {data: 'status', name: 'status'},
                {data: 'action', name: 'action', orderable: false, searchable: false, className:'sticky-col'},
            ]
        });

        // $('#exitPayableTable').on('click', '.delete-record', function (e) {
        //     e.preventDefault();
        //     $object = $(this);
        //     var $url = $object.attr('data-href');
        //     var successCallback = function (response) {
        //         toastr.success(response.message, 'Success', {timeOut: 5000});
        //         oTable.ajax.reload();
        //     }
        //     ajaxDeleteSweetAlert($url, successCallback);
        // });

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
            </div>
        </div>
        <section class="registration">

            <div class="card">
                <div class="card-body">
                    {{-- <div class="row mb-3">
                        <div class="py-2 d-flex ">
                            <div class="d-flex justify-content-end flex-grow-1">
                                 <button data-toggle="modal"
                                    class="btn btn-primary btn-sm open-modal-form"
                                    href="{!! route('employee.payable.create') !!}">
                                    <i class="bi-plus"></i> Add New Employee Payable
                                 </button>
                            </div>
                        </div>
                    </div> --}}
                    <div class="table-responsive mb-3">
                        <table class="table table-borderedless" id="exitPayableTable">
                            <thead class="bg-light">
                            <tr>
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
