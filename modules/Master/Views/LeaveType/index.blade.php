@extends('layouts.container')

@section('title', 'Leave Types')

@section('page_js')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#navbarVerticalMenu').find('#leave-types-menu').addClass('active');

            var oTable = $('#leaveTypeTable').DataTable({
                scrollX: true,
                processing: true,
                serverSide: true,
                ajax: "{{ route('master.leave.types.index') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'title',
                        name: 'title'
                    },
                    {
                        data: 'paid_status',
                        name: 'paid_status',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'number_of_days',
                        name: 'number_of_days',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'weekend_status',
                        name: 'weekend_status',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'applicable_to_all_status',
                        name: 'applicable_to_all_status',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'female_status',
                        name: 'female_status',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'male_status',
                        name: 'male_status',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'encashment_status',
                        name: 'encashment_status',
                        orderable: false,
                        searchable: false
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

            $('#leaveTypeTable').on('click', '.delete-record', function(e) {
                e.preventDefault();
                $object = $(this);
                var $url = $object.attr('data-href');
                var successCallback = function(response) {
                    toastr.success(response.message, 'Success', {
                        timeOut: 5000
                    });
                    oTable.ajax.reload();
                    // $($object).closest('tr').remove();
                }
                ajaxDeleteSweetAlert($url, successCallback);
            });


            $(document).on('click', '.open-leave-type-modal-form', function(e) {
                e.preventDefault();
                $('#openModal').find('.modal-content').html('');
                $('#openModal').modal('show').find('.modal-content').load($(this).attr('href'), function (){
                    const form = document.getElementById('leaveTypeForm');
                    $(".select2").select2({
                        dropdownParent: $('.modal'),
                        width: '100%',
                        dropdownAutoWidth: true
                    });

                    const fv = FormValidation.formValidation(form, {
                        fields: {
                            title: {
                                validators: {
                                    notEmpty: {
                                        message: 'Leave name is required',
                                    },
                                },
                            },
                            fiscal_year_id: {
                                validators: {
                                    notEmpty: {
                                        message: 'Fiscal year is required',
                                    },
                                },
                            },
                            leave_frequency: {
                                validators: {
                                    notEmpty: {
                                        message: 'This field is required',
                                    },
                                },
                            },
                            // number_of_days: {
                            //     validators: {
                            //         notEmpty: {
                            //             message: 'Balance in days is required',
                            //         },
                            //         between: {
                            //             min: 1,
                            //             max: 365,
                            //             message: 'Balance in days must be between 1 and 365',
                            //         },
                            //     },
                            // },
                            // maximum_carry_over: {
                            //     validators: {
                            //         notEmpty: {
                            //             message: 'Maximum carry over in a year is required',
                            //         },
                            //         between: {
                            //             min: 0,
                            //             max: 365,
                            //             message: 'Maximum carry over in a year must be between 0 and 365',
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
                    }).on('change', '[name="fiscal_year_id"]', function(e) {
                        fv.revalidateField('fiscal_year_id');
                    }).on('change', '[name="leave_frequency"]', function(e) {
                        fv.revalidateField('leave_frequency');
                    }).on('core.form.valid', function(event) {
                        $form = fv.form;
                        $url = $($form).attr('action');
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
                                    class="text-decoration-none text-dark">{{ __('label.home') }}</a></li>
                            <li class="breadcrumb-item"><a href="#"
                                    class="text-decoration-none">{{ __('label.master') }}</a></li>
                            <li class="breadcrumb-item" aria-current="page">@yield('title')</li>
                        </ol>
                    </nav>
                    <h4 class="m-0 lh1 mt-1 fs-6 text-uppercase fw-bold text-primary">@yield('title')</h4>
                </div>
                <div class="add-info justify-content-end">
                    <a href="{!! route('master.leave.types.create') !!}" data-toggle="modal" class="btn btn-primary btn-sm open-leave-type-modal-form">
                        <i class="bi-plus"></i> Add New
                    </a>
                </div>
            </div>

        </div>
    </div>

    <div class="container-fluid">
        <div class="card shadow-sm border rounded c-tabs-content active">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table" id="leaveTypeTable">
                        <thead>
                            <tr>
                                <th>SN</th>
                                <th>Leave name</th>
                                <th>Paid/Unpaid</th>
                                <th>Balance</th>
                                <th>Weekend Included?</th>
                                <th>Applicable to All?</th>
                                <th>Female Only?</th>
                                <th>Male Only?</th>
                                <th>Encashment?</th>
                                <th>Status</th>
                                <th>Action</th>
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
