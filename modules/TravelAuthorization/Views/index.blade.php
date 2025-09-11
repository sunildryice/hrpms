@extends('layouts.container')

@section('title', 'Travel Authorization Request')

@section('page_js')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#navbarVerticalMenu').find('#ta-request-menu').addClass('active');

            var oTable = $('#travelRequestTable').DataTable({
                scrollX: true,
                processing: true,
                serverSide: true,
                ajax: "{{ route('ta.requests.index') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'request_number',
                        name: 'request_number'
                    },
                    {
                        data: 'requester',
                        name: 'requester'
                    },
                    {
                        data: 'office',
                        name: 'office'
                    },
                    {
                        data: 'submitted_date',
                        name: 'submitted_date'
                    },
                    {
                        data: 'officials',
                        name: 'officials'
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

            $(document).on('click', '.add-new', function(e) {
                e.preventDefault();
                let url = $(this).attr('href');
                Swal.fire({
                    title: 'Create Travel Authorization Request?',
                    text: "",
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Create'
                }).then((result) => {
                    if (result.value) {
                        $.ajax({
                            type: 'POST',
                            url: url,
                            data: {},
                            dataType: 'json',
                            success: function(response) {
                                toastr.success(response.message, 'Success', {
                                    timeOut: 5000
                                });
                                window.location.href = response.redirectUrl;
                            },
                            error: function(err) {
                                showErrorMessageInSweatAlert(err);
                            }
                        });

                    }
                });
            })

            $('#travel-request-table').on('click', '.delete-record', function(e) {
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

            $(document).on('click', '.cancel-ta-request', function(e) {
                e.preventDefault();
                $('#openModal').find('.modal-content').html('');
                $('#openModal').modal('show').find('.modal-content').load($(this).attr('href'), function() {
                    const cancelForm = document.getElementById('cancelForm');

                    const fv = FormValidation.formValidation(cancelForm, {
                            fields: {
                                cancel_remarks: {
                                    validators: {
                                        notEmpty: {
                                            message: 'Remarks is required',
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
                        })
                        .on('core.form.valid', function(event) {
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
                            ajaxSweetAlert($url, 'POST', data, 'Cancel TR',
                                successCallback);
                        });
                })
            });

            $('#travel-request-table').on('click', '.amend-request', function(e) {
                e.preventDefault();
                $object = $(this);
                var $url = $object.attr('data-href');
                let number = $object.attr('data-number');
                var successCallback = function(response) {
                    toastr.success(response.message, 'Success', {
                        timeOut: 5000
                    });
                    oTable.ajax.reload();
                }
                var confirmText = `Amend ${number} ?`;
                ajaxSweetAlert($url, 'POST', {}, confirmText, successCallback);
            });

            $('#travel-request-table').on('click', '.create-settlement', function(e) {
                e.preventDefault();
                $object = $(this);
                var $url = $object.attr('data-href');
                var successCallback = function(response) {
                    toastr.success(response.message, 'Success', {
                        timeOut: 5000
                    });
                    location.href = response.redirectUrl;
                }
                ajaxSweetAlert($url, 'POST', {}, 'Create Settlement', successCallback);
            });
        });
    </script>
@endsection
@section('page-content')

    <div class="pb-3 mb-3 border-bottom">
        <div class="gap-2 d-flex flex-column flex-lg-row align-items-start align-items-lg-center">
            <div class="brd-crms flex-grow-1">
                <nav aria-label="breadcrumb">
                    <ol class="m-0 breadcrumb">
                        <li class="breadcrumb-item"><a href="{!! route('dashboard.index') !!}"
                                class="text-decoration-none text-dark">Home</a>
                        </li>
                        <li class="breadcrumb-item" aria-current="page">Travel Authorization Request</li>
                    </ol>
                </nav>
                <h4 class="m-0 mt-1 lh1 fs-6 text-uppercase fw-bold text-primary">Travel Authorization Request</h4>
            </div>
            <div class="add-info justify-content-end">
                <button href="{!! route('ta.requests.store') !!}" class="btn btn-primary btn-sm add-new" rel="tooltip"
                    title="Add Travel Authorization Request">
                    <i class="bi-plus"></i> Add New</button>
            </div>
        </div>
    </div>

    <div class="card" id="travel-request-table">
        <div class="card-header fw-bold">Travel Authorization Request List</div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-borderedless" id="travelRequestTable">
                    <thead class="bg-light">
                        <tr>
                            <th style="width:45px;">{{ __('label.sn') }}</th>
                            <th>{{ __('label.travel-number') }}</th>
                            <th>{{ __('label.requester') }}</th>
                            <th>{{ __('label.office') }}</th>
                            <th>{{ __('label.submit-date') }}</th>
                            <th>{{ __('label.officials') }}</th>
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

@stop
