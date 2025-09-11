@extends('layouts.container')

@section('title', 'Approved Local Travel Reimbursements')

@section('page_js')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#navbarVerticalMenu').find('#approved-local-travel-reimbursements-menu').addClass('active');

            var oTable = $('#localTravelTable').DataTable({
                scrollX: true,
                processing: true,
                serverSide: true,
                ajax: "{{ route('approved.local.travel.reimbursements.index') }}",
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
                        data: 'local_travel_number',
                        name: 'local_travel_number'
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
                        data: 'submitted_date',
                        name: 'submitted_date'
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
                        className:'sticky-col'
                    },
                ]
            });

            $(document).on('click', '.open-payment-modal-form', function(e) {
                e.preventDefault();
                $('#openModal').find('.modal-content').html('');
                $('#openModal').modal('show').find('.modal-content').load($(this).attr('href'), function() {
                    const paymentForm = document.getElementById('paymentForm');

                    $(paymentForm.querySelector('[name="pay_date"]')).datepicker({
                        language: 'en-GB',
                        autoHide: true,
                        format: 'yyyy-mm-dd',
                        endDate: '{!! date('Y-m-d') !!}',
                        zIndex: 9999,
                    }).on('change', function(e) {
                        fv.revalidateField('pay_date');
                    });

                    const fv = FormValidation.formValidation(paymentForm, {
                            fields: {
                                pay_date: {
                                    validators: {
                                        notEmpty: {
                                            message: 'Payment Date is required',
                                        },
                                    },
                                },
                                payment_remarks: {
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
                            ajaxSweetAlert($url, 'POST', data, 'Make Payment!',
                                successCallback);
                        });
                })
            })
        });
    </script>
@endsection
@section('page-content')

        <div class="pb-3 mb-3 border-bottom">
            <div class="d-flex flex-column flex-lg-row align-items-start align-items-lg-center gap-2">
            <div class="brd-crms flex-grow-1">
                <nav aria-label="breadcrumb">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item">
                                <a href="{{ route('dashboard.index') }}" class="text-decoration-none text-dark">Home</a>
                            </li>
                            <li class="breadcrumb-item" aria-current="page">@yield('title')</li>
                        </ol>
                    </nav>
                    <h4 class="m-0 lh1 mt-1 fs-6 text-uppercase fw-bold text-primary">@yield('title')</h4>
                </div>
            </div>

        </div>
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table" id="localTravelTable">
                        <thead class="bg-light">
                            <tr>
                                <th>{{ __('label.sn') }}</th>
                                <th>{{ __('label.purpose') }}</th>
                                <th>{{ __('label.local-travel-number') }}</th>
                                <th>{{ __('label.travel-number') }}</th>
                                <th>{{ __('label.requester') }}</th>
                                <th>Submitted Date</th>
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
