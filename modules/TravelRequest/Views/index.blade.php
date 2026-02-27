@extends('layouts.container')

@section('title', 'Travel Request')

@section('page_js')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#navbarVerticalMenu').find('#travel-request-menu').addClass('active');

            var oTable = $('#travelRequestTable').DataTable({
                scrollX: true,
                processing: true,
                serverSide: true,
                ajax: "{{ route('travel.requests.index') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'departure_date',
                        name: 'departure_date'
                    },
                    {
                        data: 'return_date',
                        name: 'return_date'
                    },
                    {
                        data: 'duration',
                        name: 'duration'
                    },
                    {
                        data: 'final_destination',
                        name: 'final_destination'
                    },
                    {
                        data: 'travel_number',
                        name: 'travel_number'
                    },
                    {
                        data: 'requester',
                        name: 'requester'
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

            $(document).on('click', '.cancel-travel-request', function(e) {
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

            $('#travel-request-table').on('click', '.amend-travel-request', function(e) {
                e.preventDefault();
                $object = $(this);
                var $url = $object.attr('data-href');
                var successCallback = function(response) {
                    toastr.success(response.message, 'Success', {
                        timeOut: 5000
                    });
                    location.reload();
                }
                var confirmText = 'Amend this travel request'
                ajaxSweetAlert($url, 'POST', {}, confirmText, successCallback);
            });

            $('#travel-request-table').on('click', '.travel-advance-request', async function(e) {
                e.preventDefault();
                $object = $(this);
                let travelNumber = $object.attr('data-travel-number');
                let travelId = $object.attr('data-travel-request-id');
                var url = $object.attr('data-href');
                var successCallback = function(response) {
                    toastr.success(response.message, 'Success', {
                        timeOut: 5000
                    });
                    oTable.ajax.reload();
                }
                var errorCallback = function(response) {
                    toastr.error('Error', {
                        timeOut: 5000
                    });
                    oTable.ajax.reload();
                }
                var confirmText = 'Amend this purchase request'
                const {
                    value: field
                } = await Swal.fire({
                    title: travelNumber + ': Requste Advance',
                    input: "number",
                    inputLabel: "Amount",
                    inputAttributes: {
                        name: 'requested_advance_amount'
                    },
                    showCancelButton: true,
                });
                if (field) {
                    ajaxSubmit(url, 'POST', {
                        'requested_advance_amount': field
                    }, successCallback, errorCallback);
                }
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
                ajaxSweetAlert($url, 'POST', {}, 'Create Travel Claim', successCallback);
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
                        <li class="breadcrumb-item" aria-current="page">Travel Request</li>
                    </ol>
                </nav>
                <h4 class="m-0 mt-1 lh1 fs-6 text-uppercase fw-bold text-primary">Travel Request</h4>
            </div>
            <div class="add-info justify-content-end">
                <a href="{!! route('travel.requests.create') !!}" class="btn btn-primary btn-sm" rel="tooltip" title="Add Travel Request">
                    <i class="bi-plus"></i> Add New</a>
            </div>
        </div>
    </div>

    <div class="card" id="travel-request-table">
        <div class="card-header fw-bold">Travel Request List</div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-borderedless" id="travelRequestTable">
                    <thead class="bg-light">
                        <tr>
                            <th style="width:45px;">{{ __('label.sn') }}</th>
                            <th>{{ __('label.from-date') }}</th>
                            <th>{{ __('label.to-date') }}</th>
                            <th>{{ __('label.total-days') }}</th>
                            <th>{{ __('label.destination') }}</th>
                            <th>{{ __('label.travel-number') }}</th>
                            <th>{{ __('label.requester') }}</th>
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
