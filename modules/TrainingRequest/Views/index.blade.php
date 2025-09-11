@extends('layouts.container')

@section('title', 'Training Request')

@section('page_js')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#navbarVerticalMenu').find('#training-requests-menu').addClass('active');
        });

        var oTable = $('#trainingRequestTable').DataTable({
                scrollX: true,
            processing: true,
            serverSide: true,
            ajax: "{{ route('training.requests.index') }}",
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'training_number',
                    name: 'training_number'
                },
                {
                    data: 'name_of_course',
                    name: 'name_of_course'
                },
                {
                    data: 'duration',
                    name: 'duration'
                },
                {
                    data: 'remarks',
                    name: 'remarks'
                },
                {
                    data: 'status',
                    name: 'status',
                    orderable: false,
                    searchable: false
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

        $('#trainingRequestTable').on('click', '.delete-record', function(e) {
            e.preventDefault();
            $object = $(this);
            var $url = $object.attr('data-href');
            var successCallback = function(response) {
                toastr.success(response.message, 'Success', {
                    timeOut: 5000
                });
                oTable.ajax.reload();
                $($object).closest('tr').remove();
            }
            ajaxDeleteSweetAlert($url, successCallback);
        });

        document.addEventListener('DOMContentLoaded', function(e) {
            const form = document.getElementById('trainingAddForm') ? document.getElementById(
                'trainingAddForm') : document.getElementById('trainingEditForm');
            const fv = FormValidation.formValidation(form, {
                fields: {
                    title: {
                        validators: {
                            notEmpty: {
                                message: 'Course title field is required.',
                            },
                        },
                    },
                    activity_code_id: {
                        validators: {
                            notEmpty: {
                                message: 'Activity Code field is required.',
                            },
                        },
                    },
                    account_code_id: {
                        validators: {
                            notEmpty: {
                                message: 'Account Code field is required.',
                            },
                        },
                    },
                    own_time: {
                        validators: {
                            notEmpty: {
                                message: 'Own time field is required.',
                            },
                            numeric: {
                                message: 'Own time should be a number.'
                            },
                        },
                    },
                    work_time: {
                        validators: {
                            notEmpty: {
                                message: 'Work time Code field is required.',
                            },
                            numeric: {
                                message: 'Work time should be a number.'
                            },
                        },
                    },
                    start_date: {
                        validators: {
                            notEmpty: {
                                message: 'Training start date is required',
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
                                message: 'Training end date is required',
                            },
                            date: {
                                format: 'YYYY-MM-DD',
                                message: 'The value is not a valid date',
                            },
                        },
                    },
                    description: {
                        validators: {
                            notEmpty: {
                                message: 'Description field is required.',
                            },
                        },
                    },
                    attachment: {
                        validators: {
                            file: {
                                extension: 'jpeg,jpg,png,pdf',
                                type: 'image/jpeg,image/png,application/pdf',
                                maxSize: '2097152',
                                message: 'The selected file is not valid type or must not be greater than 2 MB.',
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
                            message: 'Start date must be a valid date and earlier than to date.',
                        },
                        endDate: {
                            field: 'end_date',
                            message: 'End date must be a valid date and later than from date.',
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
                var attachmentFiles = form.querySelector('[name="attachment"]').files;
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


            $('#openModal').find('[name="start_date"]').datepicker({
                language: 'en-GB',
                autoHide: true,
                format: 'yyyy-mm-dd',
                startDate: '{!! date('Y-m-d') !!}',
                zIndex: 2048,
            }).on('change', function(e) {
                var start_date = $(this).val();
                $('#openModal').find('[name="end_date"]').datepicker("option", "startDate", start_date);
                fv.revalidateField('start_date');
                fv.revalidateField('end_date');
            });

            $('#openModal').find('[name="end_date"]').datepicker({
                language: 'en-GB',
                autoHide: true,
                format: 'yyyy-mm-dd',
                startDate: '{!! date('Y-m-d') !!}',
                zIndex: 2048,
            }).on('change', function(e) {
                fv.revalidateField('start_date');
                fv.revalidateField('end_date');
            });

            $('[name="start_date"]').datepicker({
                language: 'en-GB',
                autoHide: true,
                format: 'yyyy-mm-dd',
                startDate: '2022-04-02',
            }).on('change', function(e) {
                var start_date = $(this).val();
                $('[name="end_date"]').datepicker("option", "startDate", start_date);
                fv.revalidateField('start_date');
                fv.revalidateField('end_date');
            });

            $('[name="end_date"]').datepicker({
                language: 'en-GB',
                autoHide: true,
                format: 'yyyy-mm-dd',
            }).on('change', function(e) {
                fv.revalidateField('start_date');
                fv.revalidateField('end_date');
            });
        });
    </script>
@endsection
@section('page-content')

    <div class="page-header pb-3 mb-3 border-bottom">
        <div class="d-flex flex-column flex-lg-row align-items-start align-items-lg-center gap-2">
            <div class="brd-crms flex-grow-1">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{!! route('dashboard.index') !!}"
                                class="text-decoration-none text-dark">Home</a>
                        </li>
                        <li class="breadcrumb-item" aria-current="page">Training Request</li>
                    </ol>
                </nav>
                <h4 class="m-0 lh1 mt-1 fs-6 text-uppercase fw-bold text-primary">Training Request</h4>
            </div>
            <div class="add-info justify-content-end">
                <a class="btn btn-primary btn-sm" href="{!! route('training.requests.create') !!}" rel="tooltip" title="Training Request">
                    <i class="bi-plus"></i>Add New
                </a>
            </div>
        </div>

    </div>
    <section class="registration">
        <div class="card" id="training-request-table">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-borderedless" id="trainingRequestTable">
                        <thead class="bg-light">
                            <tr>
                                <th style="width:45px;"></th>
                                <th>{{ __('label.training-number') }}</th>
                                <th>{{ __('label.name-of-course') }}</th>
                                <th>{{ __('label.duration') }}</th>
                                <th>{{ __('label.remarks') }}</th>
                                <th style="width: 100px;">{{ __('label.status') }}</th>
                                <th style="width: 164px;">{{ __('label.action') }}</th>
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
