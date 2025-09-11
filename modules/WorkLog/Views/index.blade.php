@extends('layouts.container')

@section('title', 'Work Log')
@section('page_css')
@endsection
@section('page_js')
    <script>
        $(document).ready(function () {
            $('.step-item').click(function () {
                $('.step-item').removeClass('active');
                $(this).addClass('active');
                var tagid = $(this).data('tag');
                $('.c-tabs-content').removeClass('active').addClass('hide');
                $('#' + tagid).addClass('active').removeClass('hide');
            });
        });

        var oTable = $('#workPlanMonthlyLogTable').DataTable({
                scrollX: true,
            processing: true,
            serverSide: true,
            ajax: "{{ route('monthly.work.logs.index') }}",
            columns: [{
                data: 'DT_RowIndex',
                name: 'DT_RowIndex',
                orderable: false,
                searchable: false
            },
                {
                    data: 'year_month',
                    name: 'year_month'
                },
                {
                    data: 'employee',
                    name: 'employee'
                },
                {
                    data: 'planned',
                    name: 'planned'
                },
                {
                    data: 'completed',
                    name: 'completed'
                },
                {
                    data: 'summary',
                    name: 'summary'
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

        $(document).ready(function () {
            $('#navbarVerticalMenu').find('#work-logs-menu').addClass('active');
        });

        $('#workPlanMonthlyLogTable').on('click', '.delete-record', function (e) {
            e.preventDefault();
            $object = $(this);
            var $url = $object.attr('data-href');
            var successCallback = function (response) {
                toastr.success(response.message, 'Success', {
                    timeOut: 5000
                });
                $($object).closest('tr').remove();
            }
            ajaxDeleteSweetAlert($url, successCallback);
        });

        $('#workPlanMonthlyLogTable').on('click', '.submit-record', function (e) {
            e.preventDefault();
            $object = $(this);
            var $url = $object.attr('data-href');
            submitSweetAlert($url);
        });

        $(document).on('click', '.open-worklog-modal-form', function (e) {
            e.preventDefault();
            $('#openModal').find('.modal-content').html('');
            $('#openModal').modal('show').find('.modal-content').load($(this).attr('href'), function () {
                const form = document.getElementById('workLogAddForm') ? document.getElementById(
                    'workLogAddForm') : document.getElementById('workLogEditForm');
                const fv = FormValidation.formValidation(form, {
                    fields: {
                        year: {
                            validators: {
                                notEmpty: {
                                    message: 'Year Field is required',
                                },
                                // date: {
                                //     format: 'YYYY',
                                //     message: 'The value is not a valid year',
                                // },
                            },
                        },
                        month: {
                            validators: {
                                notEmpty: {
                                    message: 'Month field is required',
                                },
                                numeric: {
                                    message: 'Month field should be number.',
                                },
                                between: {
                                    inclusive: true,
                                    min: 1,
                                    max: 12,
                                    message: 'The value must be valid.',
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
                        toastr.success(response.message, 'Success', {
                            timeOut: 5000
                        });
                        oTable.ajax.reload();
                        // console.log(response);
                    }
                    ajaxSubmit($url, 'POST', data, successCallback);
                });

                $(form).on('change', '[name="year"]', function (e) {
                    fv.revalidateField('year');
                }).on('change', '[name="month"]', function (e) {
                    fv.revalidateField('month');
                });
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
                            <li class="breadcrumb-item" aria-current="page"><a href="#"
                                                                               class="text-decoration-none text-dark">Worklog</a>
                            </li>
                            <li class="breadcrumb-item" aria-current="page">Monthly Worklog</li>
                        </ol>
                    </nav>
                    <h4 class="m-0 lh1 mt-1 fs-6 text-uppercase fw-bold text-primary">Monthly Worklog</h4>
                </div>
                <div class="add-info justify-content-end">
                    <button data-toggle="modal" class="btn btn-primary btn-sm open-worklog-modal-form"
                        href="{!! route('monthly.work.logs.create') !!}" rel="tooltip" title="Monthly Worklog">
                        <i class="bi-plus"></i>Add New
                    </button>
                </div>
            </div>
        </div>
        <section class="registration">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-borderedless" id="workPlanMonthlyLogTable">
                            <thead class="bg-light">
                            <tr>
                                <th style="width:45px;">SN</th>
                                <th style="width:15%;">{{ __('label.year-month') }}</th>
                                <th>{{ __('label.name') }}</th>
                                <th>{{ __('label.planned') }}</th>
                                <th>{{ __('label.completed') }}</th>
                                <th>{{ __('label.summary') }}</th>
                                <th>{{ __('label.status') }}</th>
                                <th style="width: 140px;">{{ __('label.action') }}</th>
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
