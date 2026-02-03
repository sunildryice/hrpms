@extends('layouts.container')

@section('title', 'Timesheet')

@section('page_js')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#navbarVerticalMenu').find('#timesheets-index').addClass('active');

            var oTable = $('#TimeSheetTable').DataTable({
                scrollX: true,
                processing: true,
                serverSide: true,
                ajax: "{{ route('timesheet.index') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'timesheet_date',
                        name: 'timesheet_date'
                    },
                    {
                        data: 'project_id',
                        name: 'project_id'
                    },
                    {
                        data: 'activity_id',
                        name: 'activity_id'
                    },
                    {
                        data: 'hours_spent',
                        name: 'hours_spent'
                    },
                    {
                        data: 'attachment',
                        name: 'attachment'
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

            $('#TimeSheetTable').on('click', '.delete-record', function(e) {
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

            $(document).on('click', '.open-timesheet-modal-form', function(e) {
                e.preventDefault();
                $('#openModal').find('.modal-content').html('');
                $('#openModal').modal('show').find('.modal-content').load($(this).attr('href'), function() {
                    const form = document.getElementById('TimeSheetForm');

                    $(form).find(".select2").each(function() {
                        $(this)
                            .wrap("<div class=\"position-relative\"></div>")
                            .select2({
                                dropdownParent: $(this).parent(),
                                width: '100%',
                                dropdownAutoWidth: true
                            });
                    });

                    $('#project_id').on('change', function() {
                        const projectId = $(this).val();
                        const $activitySelect = $('#activity_id');

                        $.ajax({
                            url: '{{ route('timesheet.get-activities-by-project') }}',
                            method: 'GET',
                            data: {
                                project_id: projectId
                            },
                            dataType: 'json',
                            success: function(response) {
                                $activitySelect.html(
                                    '<option value="">Select Activity / Sub Activity</option>'
                                );

                                $.each(response.activities, function(index,
                                    activity) {
                                    $activitySelect.append(
                                        $('<option>', {
                                            value: activity.id,
                                            text: activity.title
                                        })
                                    );
                                });

                                $activitySelect.trigger(
                                    'change');
                            },
                            error: function() {
                                toastr.error('Failed to load activities');
                            }
                        });
                    });

                    const fv = FormValidation.formValidation(form, {
                        fields: {
                            project_id: {
                                validators: {
                                    notEmpty: {
                                        message: 'The Project is required'
                                    }
                                }
                            },
                            activity_id: {
                                validators: {
                                    notEmpty: {
                                        message: 'The Activity / Sub Activity is required'
                                    }
                                }
                            },
                            timesheet_date: {
                                validators: {
                                    notEmpty: {
                                        message: 'The date is required'
                                    },
                                    date: {
                                        format: 'YYYY-MM-DD',
                                        message: 'The date is not a valid date'
                                    }
                                },
                            },
                            hours_spent: {
                                validators: {
                                    notEmpty: {
                                        message: 'The hours spent is required'
                                    },
                                    numeric: {
                                        message: 'The hours spent must be a number'
                                    },
                                    between: {
                                        min: 0.1,
                                        max: 24,
                                        message: 'Hours spent should be between 0.1 and 24'
                                    }
                                },
                            },
                            attachment: {
                                validators: {
                                    file: {
                                        extension: 'jpeg,jpg,png,pdf',
                                        type: 'image/jpeg,image/png,application/pdf',
                                        maxSize: '5097152',
                                        message: 'The selected file is not valid file or must not be greater than 5 MB.',
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
                    }).on('core.form.valid', function() {
                        const $url = fv.form.action;
                        const formData = new FormData(form);

                        const successCallback = function(response) {
                            $('#openModal').modal('hide');
                            toastr.success(response.message || 'Saved successfully');
                            oTable.ajax.reload();
                        };

                        ajaxSubmitFormData($url, 'POST', formData, successCallback);
                    });


                    $('[name="timesheet_date"]').datepicker({
                        language: 'en-GB',
                        autoHide: true,
                        format: 'yyyy-mm-dd',
                        zIndex: 2048,
                        endDate: new Date(), 
                        todayHighlight: true,
                        todayBtn: true
                    }).on('change', function(e) {
                        fv.revalidateField('timesheet_date');
                    });
                });
            });

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
                            <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}"
                                    class="text-decoration-none text-dark">Home</a></li>
                            {{-- <li class="breadcrumb-item"><a href="#" class="text-decoration-none">HR</a></li> --}}
                            <li class="breadcrumb-item" aria-current="page">@yield('title')</li>
                        </ol>
                    </nav>
                    <h4 class="m-0 lh1 mt-1 fs-6 text-uppercase fw-bold text-primary">@yield('title')</h4>
                </div>
                <div class="add-info justify-content-end">
                    <a href="{{ route('timesheet.create') }}" class="btn btn-primary btn-sm open-timesheet-modal-form"
                        rel="tooltip" title="Add TimeSheet">
                        <i class="bi-plus"></i> Add New</a>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <div class="card shadow-sm border rounded c-tabs-content active">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table" id="TimeSheetTable">
                        <thead class="bg-light">
                            <tr>
                                <th>{{ __('label.sn') }}</th>
                                <th>{{ __('label.date') }}</th>
                                <th>{{ __('label.project') }}</th>
                                <th>{{ __('label.activity') }}</th>
                                <th>Hours Spent</th>
                                <th>{{ __('label.attachment') }}</th>
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
