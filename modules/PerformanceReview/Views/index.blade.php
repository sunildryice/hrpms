@extends('layouts.container')

@section('title', 'Performance Review')

@section('page_js')
    <script type="text/javascript">
        $(function() {
            $('#navbarVerticalMenu').find('#performance-index').addClass('active');

            var oTable = $('#performanceIndexTable').DataTable({
                scrollX: true,
                processing: true,
                serverSide: true,
                ajax: "{{ route('performance.index') }}",
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
                        data: 'fiscal_year',
                        name: 'fiscal_year'
                    },
                    {
                        data: 'review_type',
                        name: 'review_type'
                    },
                    {
                        data: 'review_from',
                        name: 'review_from',
                    },
                    {
                        data: 'review_to',
                        name: 'review_to'
                    },
                    {
                        data: 'deadline_date',
                        name: 'deadline_date'
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
                    }
                ]
            });

            $('#performanceIndexTable').on('click', '.delete-record', function(e) {
                e.preventDefault();
                $object = $(this);
                var $url = $object.attr('data-href');
                var successCallback = function(response) {
                    toastr.success(response.message, 'Success', {
                        timeout: 5000
                    });
                    oTable.ajax.reload();
                };
                ajaxDeleteSweetAlert($url, successCallback);
            });

            $(document).on('click', '.open-import-modal-form', function(e) {
                e.preventDefault();
                document.querySelector(".preloader").style.display = "block";
                $('#openModal').find('.modal-content').html('');
                $('#openModal').modal('show').find('.modal-content').load($(this).attr('href'), function() {
                    document.querySelector(".preloader").style.display = "none";
                    const form = document.getElementById('keygoalImportForm');
                    const fv = FormValidation.formValidation(form, {
                        fields: {
                            attachment: {
                                validators: {
                                    notEmpty: {
                                        message: 'Attachment is required',
                                    },
                                    file: {
                                        extension: 'xls,xlsx',
                                        type: 'application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                                        message: 'Please choose an Excel file',
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
                        const $url = fv.form.action;
                        const $form = fv.form;
                        const data = new FormData($form);

                        const successCallback = function(response) {

                            $('#openModal').modal('hide');
                            toastr.success(response.message, 'Success', {
                                timeOut: 5000
                            });
                            $('#keygoalTable').DataTable().ajax.reload();
                        };
                        document.querySelector(".preloader").style.display = "block";
                        ajaxSubmitFormData($url, 'POST', data, function(response) {
                            successCallback(response);
                            document.querySelector(".preloader").style.display =
                                "none";
                        });
                    });
                });
            });

        });
    </script>
@endsection

@section('page-content')

    <div class="page-header pb-3 mb-3 border-bottom">
        <div class="d-flex align-items-center">
            <div class="brd-crms flex-grow-1">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item">
                            <a href="{!! route('dashboard.index') !!}" class="text-decoration-none text-dark">Home</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('performance.index') }}" class="text-decoration-none text-dark">Performance
                                Review</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">@yield('title')</li>
                    </ol>
                </nav>
                <h4 class="m-0 lh1 mt-1 fs-6 text-uppercase fw-bold text-primary">@yield('title')</h4>
            </div>
            <div class="add-info justify-content-end">
                <a href="{{ route('performance.create') }}" class="btn btn-primary btn-sm">
                    <i class="bi-plus"></i> New Performance Review
                </a>
                {{-- <button data-toggle="modal" class="btn btn-secondary btn-sm open-import-modal-form"
                    href="{{ route('performance.keygoals.import') }}">
                    <i class="bi-plus"></i> Import Key Goals Review
                    </a>
                </button> --}}
            </div>
        </div>
    </div>
    <div class="card" id="performance-review-table">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table" id="performanceIndexTable">
                    <thead class="bg-light">
                        <tr>
                            <th>{{ __('label.sn') }}</th>
                            <th>Employee Name</th>
                            <th>Fiscal Year</th>
                            <th>Review Type</th>
                            <th>Review From</th>
                            <th>Review To</th>
                            <th>Deadline</th>
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

@stop
