@extends('layouts.container')

@section('title', 'Pending Employee Exit')

@section('page_js')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#navbarVerticalMenu').find('#pending-employees-exit-menu').addClass('active');

        $(document).on('click', '.open-pending-employee-exit-update-modal-form', function(e) {
            e.preventDefault();
            $('#openModal').modal('show').find('.modal-content').load($(this).attr('href'), function (){
                    const form = document.getElementById('pendingEmployeeExitUpdateForm');
                    const fv = FormValidation.formValidation(form, {
                        fields: {
                            // skip_exit_handover_note: {
                            //     validators: {
                            //         notEmpty: {
                            //             message: 'Employee is required',
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

        var oTable = $('#pendingExitHandOverTable').DataTable({
                scrollX: true,
            processing: true,
            serverSide: true,
            ajax: "{{ route('employee.exit.pending.index') }}",
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
                    data: 'handovernote_status',
                    name: 'handovernote_status',
                    orderable: false,
                    searchable: false,
                },
                {
                    data: 'exit_interview_status',
                    name: 'exit_interview_status',
                    orderable: false,
                    searchable: false,
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

    </script>
@endsection
@section('page-content')
    <div>
        <x-breadcrumb :items="[
            // ['route' => route('announcement.index'), 'title' => 'Announcement'],
        ]" />
    </div>


        <div class="card" id="employee-table">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table" id="pendingExitHandOverTable">
                        <thead class="bg-light">
                            <tr>
                                <th>{{ __('label.sn') }}</th>
                                <th>Employee Name</th>
                                <th>Last Date of Duty</th>
                                <th>Resignation Date</th>
                                <th>Exit Handover Status</th>
                                <th>Exit Interview Status</th>
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
