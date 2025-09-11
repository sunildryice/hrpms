@extends('layouts.container')

@section('title', 'Edit GRN')

@section('page_js')
    <script type="text/javascript">
        document.addEventListener('DOMContentLoaded', function (e) {
            const form = document.getElementById('grnEditForm');
            const fv = FormValidation.formValidation(form, {
                fields: {
                    received_date: {
                        validators: {
                            notEmpty: {
                                message: 'Received date is required',
                            },
                            date: {
                                format: 'YYYY-MM-DD',
                                message: 'The value is not a valid date',
                            },
                        },
                    },
                    discount_amount: {
                        validators: {
                            greaterThan: {
                                message: 'The value must be greater than or equal to 0',
                                min: 0,
                            },
                        },
                    },
                    invoice_number: {
                        validators: {
                            integer: {
                                message: 'The value is not an integer',
                            },
                            greaterThan: {
                                message: 'The value must be greater than or equal to 1',
                                min: 1,
                            },
                        },
                    },
                },
                plugins: {
                    trigger: new FormValidation.plugins.Trigger(),
                    bootstrap5: new FormValidation.plugins.Bootstrap5(),
                    submitButton: new FormValidation.plugins.SubmitButton(),
                    defaultSubmit: new FormValidation.plugins.DefaultSubmit(),
                    icon: new FormValidation.plugins.Icon({
                        valid: 'bi bi-check2-square',
                        invalid: 'bi bi-x-lg',
                        validating: 'bi bi-arrow-repeat',
                    }),
                },
            });
            $('.received_date').datepicker({
                language: 'en-GB',
                autoHide: true,
                format: 'yyyy-mm-dd',
                startDate: '{{ $grn->purchaseOrder->order_date->format('Y-m-d') }}',
                endDate: '{{ date('Y-m-d') }}',
            }).on('change', function () {
                fv.revalidateField('received_date');
            });
        });

        var oTable = $('#grnItemTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('grns.items.index', $grn->id) }}",
            bFilter: false,
            bPaginate: false,
            bInfo: false,
            columns: [
                {data: 'item', name: 'item'},
                {data: 'unit', name: 'unit'},
                {data: 'quantity', name: 'quantity'},
                {data: 'unit_price', name: 'unit_price'},
                {data: 'total_amount', name: 'total_amount'},
                {data: 'action', name: 'action', orderable: false, searchable: false},
            ]
        });

        $('#grnItemTable').on('click', '.delete-record', function (e) {
            e.preventDefault();
            $object = $(this);
            var $url = $object.attr('data-href');
            var successCallback = function (response) {
                toastr.success(response.message, 'Success', {timeOut: 5000});
                oTable.ajax.reload();
            }
            ajaxDeleteSweetAlert($url, successCallback);
        });

        $(document).on('shown.bs.modal', '#openModal', function (e) {
            const form = document.getElementById('grnItemForm');
            const fv = FormValidation.formValidation(form, {
                fields: {
                    received_quantity: {
                        validators: {
                            notEmpty: {
                                message: 'Quantity is required',
                            },
                            greaterThan: {
                                message: 'The value must be greater than or equal to 1',
                                min: 1,
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
                    toastr.success(response.message, 'Success', {timeOut: 5000});
                    oTable.ajax.reload();
                }
                ajaxSubmit($url, 'POST', data, successCallback);
            });
        });
    </script>
@endsection
@section('page-content')
    <div class="m-content p-3">
        <div class="container-fluid">
            <div class="page-header pb-3 mb-3 border-bottom">
                <div class="d-flex align-items-center">
                    <div class="brd-crms flex-grow-1">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item">
                                    <a href="{!! route('dashboard.index') !!}" class="text-decoration-none text-dark">Home</a>
                                </li>
                                <li class="breadcrumb-item">
                                    <a href="{{ route('grns.index') }}" class="text-decoration-none">Good Receive
                                        Notes</a>
                                </li>
                                <li class="breadcrumb-item" aria-current="page">@yield('title')</li>
                            </ol>
                        </nav>
                        <h4 class="m-0 lh1 mt-1 fs-6 text-uppercase fw-bold text-primary">@yield('title')</h4>
                    </div>
                </div>
            </div>

            <section class="registration">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <form action="{{ route('grns.update', $grn->id) }}"
                                  id="grnEditForm" method="post"
                                  enctype="multipart/form-data" autocomplete="off">
                                <div class="card-body">
                                    <div class="row mb-2">
                                        <div class="col-lg-3">
                                            <div class="d-flex align-items-start h-100">
                                                <label for="validationSupplier"
                                                       class="form-label required-label">Received Date</label>
                                            </div>
                                        </div>
                                        <div class="col-lg-9">
                                            <input readonly class="form-control received_date" name="received_date"
                                                   value="{!! old('received_date') ?: $grn->received_date->format('Y-m-d') !!}"/>
                                            @if($errors->has('received_date'))
                                                <div class="fv-plugins-message-container invalid-feedback">
                                                    <div data-field="received_date">
                                                        {!! $errors->first('received_date') !!}
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="row mb-2">
                                        <div class="col-lg-3">
                                            <div class="d-flex align-items-start h-100">
                                                <label for="validationDiscount" class="m-0">Discount (If Any)</label>
                                            </div>
                                        </div>
                                        <div class="col-lg-9">
                                            <input class="form-control" name="discount_amount" type="number"
                                                   value="{!! old('discount_amount') ?: $grn->discount_amount !!}"/>
                                            @if($errors->has('discount_amount'))
                                                <div class="fv-plugins-message-container invalid-feedback">
                                                    <div data-field="discount_amount">
                                                        {!! $errors->first('discount_amount') !!}
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="row mb-2">
                                        <div class="col-lg-3">
                                            <div class="d-flex align-items-start h-100">
                                                <label for="validationInvoiceNumber" class="m-0">Invoice Number (If
                                                    Any)</label>
                                            </div>
                                        </div>
                                        <div class="col-lg-9">
                                            <input class="form-control" name="invoice_number" type="number"
                                                   value="{!! old('invoice_number') ?: $grn->invoice_number !!}"/>
                                            @if($errors->has('invoice_number'))
                                                <div class="fv-plugins-message-container invalid-feedback">
                                                    <div data-field="invoice_number">
                                                        {!! $errors->first('invoice_number') !!}
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="row mb-2">
                                        <div class="col-lg-3">
                                            <div class="d-flex align-items-start h-100">
                                                <label for="validationReceivedNote" class="m-0">Received Note</label>
                                            </div>
                                        </div>
                                        <div class="col-lg-9">
                                            <textarea class="form-control" name="received_note"
                                                      rows="3">{!! old('received_note') ?: $grn->received_note !!}</textarea>
                                            @if($errors->has('received_note'))
                                                <div class="fv-plugins-message-container invalid-feedback">
                                                    <div data-field="received_note">
                                                        {!! $errors->first('received_note') !!}
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="card-footer border-0 justify-content-end d-flex gap-2">
                                        <button type="submit" name="btn" value="save" class="btn btn-primary btn-sm">
                                            Update
                                        </button>
                                    </div>

                                    {!! csrf_field() !!}
                                    {!! method_field('PUT') !!}
                                </div>
                                <div class="card-body">
                                    <div class="card">
                                        <div class="card-header fw-bold">
                                            Items
                                        </div>
                                        <div class="container-fluid-s">
                                            <div class="card">
                                                <div class="card-body">
                                                    <table class="table" id="grnItemTable">
                                                        <thead class="thead-light">
                                                        <tr>
                                                            <th scope="col">{{ __('label.item') }}</th>
                                                            <th scope="col">{{ __('label.unit') }}</th>
                                                            <th scope="col">{{ __('label.quantity') }}</th>
                                                            <th scope="col">{{ __('label.unit-price') }}</th>
                                                            <th scope="col">{{ __('label.amount') }}</th>
                                                            <th style="width: 150px">{{ __('label.action') }}</th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                                <div class="card-footer border-0 justify-content-end d-flex gap-2">
                                    <button type="submit" name="btn" value="submit" class="btn btn-success btn-sm">
                                        Receive
                                    </button>
                                    <a href="{!! route('grns.index') !!}"
                                       class="btn btn-danger btn-sm">Cancel</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </section>

        </div>
    </div>

@stop
