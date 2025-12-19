@extends('layouts.container')

@section('title', 'Maintenance Request')


@section('page_js')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#navbarVerticalMenu').find('#maintenance-requests-menu').addClass('active');
        });

        var oTable = $('#maintenanceRequestTable').DataTable({
            scrollX: true,
            processing: true,
            serverSide: true,
            ajax: "{{ route('maintenance.requests.index') }}",
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'maintenance_number',
                    name: 'maintenance_number'
                },
                {
                    data: 'requester',
                    name: 'requester'
                },
                // {
                //     data: 'estimated_cost',
                //     name: 'estimated_cost'
                // },
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

        $('#maintenanceRequestTable').on('click', '.delete-record', function(e) {
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

        $('#maintenanceRequestTable').on('click', '.amend-record', function(e) {
            e.preventDefault();
            let url = $(this).attr('data-href');
            let number = $(this).attr('data-number');
            let successCallback = function(response) {
                toastr.success(response.message, 'Success', {
                    timeOut: 2000
                });
                oTable.ajax.reload();
                if (response.redirectUrl) {
                    window.location.href = response.redirectUrl;
                }
            };
            ajaxTextSweetAlert(url, 'POST', `Amend ${number}?`, 'Remarks', 'modification_remarks', successCallback);
        })


        $(document).on('click', '.open-maintenance-modal-form', function(e) {
            e.preventDefault();
            $('#openModal').find('.modal-content').html('');
            $('#openModal').modal('show').find('.modal-content').load($(this).attr('href'), function() {
                const form = document.getElementById('maintenanceAddForm') ? document.getElementById(
                    'maintenanceAddForm') : document.getElementById('maintenanceEditForm');

                $('[name="request_date"]').datepicker({
                    language: 'en-GB',
                    autoHide: true,
                    format: 'yyyy-mm-dd',
                    endDate: '{!! date('Y-m-d') !!}',
                    zIndex: 2048,
                }).on('change', function(e) {
                    fv.revalidateField('request_date');
                });

                const fv = FormValidation.formValidation(form, {
                    fields: {
                        request_date: {
                            validators: {
                                notEmpty: {
                                    message: 'The request date is required.',
                                },
                                date: {
                                    format: 'YYYY-MM-DD',
                                    message: 'The value is not a valid date.',
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
                        window.location.href = response.route;
                    }
                    ajaxSubmit($url, 'POST', data, successCallback);
                });

                $(form).on('change', '[name="employee_id"]', function(e) {
                    $element = $(this);
                    var employeeId = $element.val();
                    var htmlToReplace = '<option value="">Select a Reviewer</option>';
                    if (employeeId) {
                        var url = baseUrl + '/api/employee/supervisor/' + employeeId;
                        var successCallback = function(response) {
                            response.supervisors.forEach(function(supervisor) {
                                htmlToReplace += '<option value="' + supervisor.id +
                                    '">' + supervisor.full_name + '</option>';
                            });
                            $($element).closest('form').find('[name="reviewer_id"]').html(
                                htmlToReplace).val(null).trigger('change');
                            //  $($element).closest('form').find('[name="reviewer_id"]').select2("destroy").select2();
                            console.log(response);

                        }
                        var errorCallback = function(error) {
                            console.log(error);
                        }
                        ajaxNativeSubmit(url, 'GET', {}, 'json', successCallback, errorCallback);
                    } else {
                        $($element).closest('form').find('[name="reviewer_id"]').html(
                            htmlToReplace);
                    }
                    fv.revalidateField('reviewer_id');
                    fv.revalidateField('employee_id');
                });
            });
        });
    </script>
@endsection
@section('page-content')

    <div class="pb-3 mb-3 page-header border-bottom">
        <div class="gap-2 d-flex flex-column flex-lg-row align-items-start align-items-lg-center">
            <div class="brd-crms flex-grow-1">
                <nav aria-label="breadcrumb">
                    <ol class="m-0 breadcrumb">
                        <li class="breadcrumb-item"><a href="{!! route('dashboard.index') !!}"
                                class="text-decoration-none text-dark">Home</a>
                        </li>
                        <li class="breadcrumb-item" aria-current="page">Maintenance Request</li>
                    </ol>
                </nav>
                <h4 class="m-0 mt-1 lh1 fs-6 text-uppercase fw-bold text-primary">Maintenance Request</h4>
            </div>
            <div class="add-info justify-content-end">
                <button data-toggle="modal" class="btn btn-primary btn-sm open-maintenance-modal-form"
                    href="{!! route('maintenance.requests.create') !!}" rel="tooltip" title="Maintenance Request">
                    <i class="bi-plus me-1"></i>Add New
                </button>
            </div>
        </div>

    </div>
    <section class="registration">
        <div class="card" id="maintenance-request-table">
            <div class="card-body">
                <div class="table-responsive position-relative">
                    <table class="table table-borderedless" id="maintenanceRequestTable">
                        <thead class="bg-light">
                            <tr>
                                <th style="width:45px;"></th>
                                <th>{{ __('label.maintenance-number') }}</th>
                                <th>{{ __('label.requester') }}</th>
                                {{-- <th>{{ __('label.estimate') }}</th> --}}
                                <th>{{ __('label.remarks') }}</th>
                                <th style="width: 100px;">{{ __('label.status') }}</th>
                                <th class="sticky-col">{{ __('label.action') }}</th>
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
