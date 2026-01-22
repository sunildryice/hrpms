@extends('layouts.container')

@section('title', 'Activity Access Periods')

@section('page_js')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#navbarVerticalMenu').find('#project-index').addClass('active');
            var oTable = $('#activityAccessPeriodTable').DataTable({
                scrollX: true,
                processing: true,
                serverSide: true,
                ajax: "{{ route('activity-access-periods.index') }}",
                bFilter: false,
                columns: [{
                        data: null,
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row, meta) {
                            return meta.row + 1;
                        }
                    },
                    {
                        data: 'start_date',
                        name: 'start_date'
                    },
                    {
                        data: 'end_date',
                        name: 'end_date'
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


            $('#activityAccessPeriodTable').on('click', '.cancel-record', function(e) {
                e.preventDefault();
                let url = $(this).attr('data-href');
                let number = $(this).attr('data-number');
                let successCallback = function(response) {
                    toastr.success(response.message, 'Success', {
                        timeOut: 2000
                    });
                    oTable.ajax.reload();
                };
                ajaxTextSweetAlert(url, 'POST', `Cancel ${number}?`, 'Remarks', 'log_remarks',
                    successCallback);
            })

            $('#activityAccessPeriodTable').on('click', '.delete-record', function(e) {
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

            $(document).on('click', '.open-activity-access-period-modal-form', function(e) {
                e.preventDefault();
                $('#openModal').find('.modal-content').html('');
                $('#openModal').modal('show').find('.modal-content').load($(this).attr('href'), function() {
                    const form = document.getElementById('activityAccessPeriodCreateForm') ?
                        document.getElementById('activityAccessPeriodCreateForm') :
                        document.getElementById('activityAccessPeriodEditForm');
                    if (!form) return;

                    const fv = FormValidation.formValidation(form, {
                        fields: {
                            start_date: {
                                validators: {
                                    notEmpty: {
                                        message: 'Start date is required'
                                    },
                                    date: {
                                        format: 'YYYY-MM-DD',
                                        message: 'Invalid date format'
                                    }
                                }
                            },
                            end_date: {
                                validators: {
                                    notEmpty: {
                                        message: 'End date is required'
                                    },
                                    date: {
                                        format: 'YYYY-MM-DD',
                                        message: 'Invalid date format'
                                    }
                                }
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
                        const data = $(fv.form).serialize();
                        const successCallback = function(response) {
                            $('#openModal').modal('hide');
                            toastr.success(response.message || 'Saved successfully');
                            oTable.ajax.reload();
                        };
                        ajaxSubmit($url, 'POST', data, successCallback);
                    });

                    $('#openModal').find('[name="start_date"]').datepicker({
                        language: 'en-GB',
                        autoHide: true,
                        format: 'yyyy-mm-dd',
                        zIndex: 2048,
                    }).on('change', function() {
                        fv.revalidateField('start_date');
                    });
                    $('#openModal').find('[name="end_date"]').datepicker({
                        language: 'en-GB',
                        autoHide: true,
                        format: 'yyyy-mm-dd',
                        zIndex: 2048,
                    }).on('change', function() {
                        fv.revalidateField('end_date');
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
                    <button data-toggle="modal" class="btn btn-primary btn-sm open-activity-access-period-modal-form"
                        href="{{ route('activity-access-periods.create') }}" title="Add Access Period">
                        <i class="bi-plus"></i> Add New
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <div class="card shadow-sm border rounded c-tabs-content active" id="employee-table">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table" id="activityAccessPeriodTable">
                        <thead class="bg-light">
                            <tr>
                                <th>{{ __('label.sn') }}</th>
                                <th>{{ __('label.start-date') }}</th>
                                <th>{{ __('label.end-date') }}</th>
                                <th>{{ __('label.status') }}</th>
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
