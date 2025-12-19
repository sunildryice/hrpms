@extends('layouts.container')

@section('title', 'Approve Maintenance Request')

@section('page_js')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#navbarVerticalMenu').find('#approve-maintenance-requests-menu').addClass('active');
        });

        var oTable = $('#maintenanceRequestReviewTable').DataTable({
            scrollX: true,
            processing: true,
            serverSide: true,
            ajax: "{{ route('approve.maintenance.requests.index') }}",
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

        $(document).on('shown.bs.modal', '#openModal', function(e) {
            const form = document.getElementById('maintenanceApproveForm');
            const fv = FormValidation.formValidation(form, {
                fields: {
                    status_id: {
                        validators: {
                            notEmpty: {
                                message: 'Status field is required.',
                            },
                        },
                    },

                    log_remarks: {
                        validators: {
                            notEmpty: {
                                message: 'Remarks field is required.',
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

            $(form).find('[name="date"]').datepicker({
                language: 'en-GB',
                autoHide: true,
                format: 'yyyy-mm-dd',
                startDate: '{!! date('Y-m-d') !!}',
                zIndex: 2048,
            }).on('change', function(e) {
                fv.revalidateField('date');
            });

            $(form).on('change', '[name="employee_id"]', function(e) {
                $element = $(this);
                var employeeId = $element.val();
                var htmlToReplace = '<option value="">Select a Reviewer</option>';
                if (employeeId) {
                    var url = baseUrl + '/api/employee/supervisor/' + employeeId;
                    var successCallback = function(response) {
                        response.supervisors.forEach(function(supervisor) {
                            htmlToReplace += '<option value="' + supervisor.id + '">' +
                                supervisor.full_name + '</option>';
                        });
                        $($element).closest('form').find('[name="reviewer_id"]').html(htmlToReplace)
                            .val(null).trigger('change');
                        //  $($element).closest('form').find('[name="reviewer_id"]').select2("destroy").select2();
                        console.log(response);

                    }
                    var errorCallback = function(error) {
                        console.log(error);
                    }
                    ajaxNativeSubmit(url, 'GET', {}, 'json', successCallback, errorCallback);
                } else {
                    $($element).closest('form').find('[name="reviewer_id"]').html(htmlToReplace);
                }
                fv.revalidateField('reviewer_id');
                fv.revalidateField('employee_id');
            })
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
                        <li class="breadcrumb-item" aria-current="page">Approve Maintenance Request</li>
                    </ol>
                </nav>
                <h4 class="m-0 lh1 mt-1 fs-6 text-uppercase fw-bold text-primary">Approve Maintenance Request</h4>
            </div>
        </div>

    </div>
    <section class="registration">
        <div class="card" id="maintenance-request-review-table">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-borderedless" id="maintenanceRequestReviewTable">
                        <thead class="bg-light">
                            <tr>
                                <th style="width:45px;"></th>
                                <th>{{ __('label.maintenance-number') }}</th>
                                <th>{{ __('label.requester') }}</th>
                                {{-- <th>{{ __('label.estimate') }}</th> --}}
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
