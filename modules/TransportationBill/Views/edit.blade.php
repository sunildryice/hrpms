@extends('layouts.container')

@section('title', 'Edit Transportation Bill')

@section('page_js')
    <script type="text/javascript">
        document.addEventListener('DOMContentLoaded', function (e) {
            $('#navbarVerticalMenu').find('#transportation-bills-menu').addClass('active');
            const form = document.getElementById('transportationBillEditForm');
            const fv = FormValidation.formValidation(form, {
                fields: {
                    bill_date: {
                        validators: {
                            notEmpty: {
                                message: 'Bill date is required',
                            },
                        },
                    },
                    shipper_name: {
                        validators: {
                            notEmpty: {
                                message: 'Shipper name is required',
                            },
                        },
                    },
                    shipper_address: {
                        validators: {
                            notEmpty: {
                                message: 'Shipper name is required',
                            },
                        },
                    },
                    consignee_name: {
                        validators: {
                            notEmpty: {
                                message: 'Consignee name is required',
                            },
                        },
                    },
                    consignee_address: {
                        validators: {
                            notEmpty: {
                                message: 'Consignee address is required',
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

            $('[name="bill_date"]').datepicker({
                language: 'en-GB',
                autoHide: true,
                format: 'yyyy-mm-dd',
                endDate: '{{ date('Y-m-d') }}',
            }).on('change', function (e) {
                fv.revalidateField('bill_date');
            });
        });

        var oTable = $('#transportationBillDetailTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('transportation.bills.details.index', $transportationBill->id) }}",
            bFilter: false,
            bPaginate: false,
            bInfo: false,
            columns: [{
                data: 'item_description',
                name: 'item_description'
            },
                {
                    data: 'quantity',
                    name: 'quantity'
                },
                {
                    data: 'remarks',
                    name: 'remarks'
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

        $('#transportationBillDetailTable').on('click', '.delete-record', function (e) {
            e.preventDefault();
            $object = $(this);
            var $url = $object.attr('data-href');
            var successCallback = function (response) {
                toastr.success(response.message, 'Success', {
                    timeOut: 5000
                });
                if (response.transportationDetailCount) {
                    $('.submit-record').show();
                } else {
                    $('.submit-record').hide();
                }
                oTable.ajax.reload();
            }
            ajaxDeleteSweetAlert($url, successCallback);
        });

        $(document).on('click', '.open-detail-modal-form', function (e) {
            e.preventDefault();
            $('#openModal').find('.modal-content').html('');
            $('#openModal').modal('show').find('.modal-content').load($(this).attr('href'), function () {
                const form = document.getElementById('transportationBillDetailForm');
                const fv = FormValidation.formValidation(form, {
                    fields: {
                        item_description: {
                            validators: {
                                notEmpty: {
                                    message: 'Item description is required',
                                },
                            },
                        },
                        quantity: {
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
                        toastr.success(response.message, 'Success', {
                            timeOut: 5000
                        });
                        if (response.transportationDetailCount) {
                            $('.submit-record').show();
                        } else {
                            $('.submit-record').hide();
                        }
                        oTable.ajax.reload();
                    }
                    ajaxSubmit($url, 'POST', data, successCallback);
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
                                    <a href="{{ route('transportation.bills.index') }}"
                                       class="text-decoration-none text-dark">Transportation
                                        Bill</a>
                                </li>
                                <li class="breadcrumb-item" aria-current="page">@yield('title')</li>
                            </ol>
                        </nav>
                        <h4 class="m-0 lh1 mt-1 fs-6 text-uppercase fw-bold text-primary">@yield('title')</h4>
                    </div>
                </div>
            </div>

            <section class="registration">

                    <form action="{{ route('transportation.bills.update', $transportationBill->id) }}"
                          id="transportationBillEditForm" method="post" enctype="multipart/form-data"
                          autocomplete="off">
                          <div class="card">
                            <div class="card-body">
                                <div class="row mb-2">
                                    <div class="col-lg-2">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="validationdd" class="form-label required-label">Bill Date </label>
                                        </div>
                                    </div>
                                    <div class="col-lg-10">
                                        <input
                                            class="form-control @if ($errors->has('bill_date')) is-invalid @endif"
                                            type="text" readonly name="bill_date"
                                            value="{{ old('bill_date') ?: ($transportationBill->bill_date ? $transportationBill->bill_date->format('Y-m-d') : '') }}"/>
                                        @if ($errors->has('bill_date'))
                                            <div class="fv-plugins-message-container invalid-feedback">
                                                <div data-field="bill_date">
                                                    {!! $errors->first('bill_date') !!}
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-lg-2">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="atvcde" class="form-label required-label">Shipper
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-lg-10">
                                        <div class="row">
                                            <div class="col-lg-6">
                                                <div class="input-group has-validation">
                                                    <div class="input-group-append">
                                                        <span class="input-group-text required-label">Name</span>
                                                    </div>
                                                    <input
                                                        class="form-control @if ($errors->has('shipper_name')) is-invalid @endif"
                                                        type="text" name="shipper_name"
                                                        value="{{ old('shipper_name') ?: $transportationBill->shipper_name }}"/>
                                                    @if ($errors->has('shipper_name'))
                                                        <div class="fv-plugins-message-container invalid-feedback">
                                                            <div
                                                                data-field="shipper_name">{!! $errors->first('shipper_name') !!}</div>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="input-group has-validation">
                                                    <div class="input-group-append">
                                                        <span class="input-group-text required-label">Address</span>
                                                    </div>
                                                    <input
                                                        class="form-control @if ($errors->has('shipper_address')) is-invalid @endif"
                                                        type="text" name="shipper_address"
                                                        value="{{ old('shipper_address') ?: $transportationBill->shipper_address }}"/>
                                                    @if ($errors->has('shipper_address'))
                                                        <div class="fv-plugins-message-container invalid-feedback">
                                                            <div
                                                                data-field="shipper_address">{!! $errors->first('shipper_address') !!}
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-lg-2">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="atvcde" class="form-label required-label">Consignee
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-lg-10">
                                        <div class="row">
                                            <div class="col-lg-6">
                                                <div class="input-group has-validation">
                                                    <div class="input-group-append">
                                                        <span class="input-group-text required-label">Name</span>
                                                    </div>
                                                    <input
                                                        class="form-control @if ($errors->has('consignee_name')) is-invalid @endif"
                                                        type="text" name="consignee_name"
                                                        value="{{ old('consignee_name') ?: $transportationBill->consignee_name }}"/>
                                                    @if ($errors->has('consignee_name'))
                                                        <div class="fv-plugins-message-container invalid-feedback">
                                                            <div
                                                                data-field="consignee_name">{!! $errors->first('consignee_name') !!}
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="input-group has-validation">
                                                    <div class="input-group-append">
                                                        <span class="input-group-text required-label">Address</span>
                                                    </div>
                                                    <input
                                                        class="form-control @if ($errors->has('consignee_address')) is-invalid @endif"
                                                        type="text" name="consignee_address"
                                                        value="{{ old('consignee_address') ?: $transportationBill->consignee_address }}"/>
                                                    @if ($errors->has('consignee_address'))
                                                        <div class="fv-plugins-message-container invalid-feedback">
                                                            <div
                                                                data-field="consignee_address">{!! $errors->first('consignee_address') !!}
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-lg-2">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="validationRemarks" class="form-label">Remarks </label>
                                        </div>
                                    </div>
                                    <div class="col-lg-10">
                                        <textarea type="text"
                                                  class="form-control @if ($errors->has('remarks')) is-invalid @endif"
                                                  name="remarks">{{ old('remarks') ?: $transportationBill->remarks }}</textarea>
                                        @if ($errors->has('remarks'))
                                            <div class="fv-plugins-message-container invalid-feedback">
                                                <div data-field="remarks">{!! $errors->first('remarks') !!}</div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-lg-2">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="validationRemarks" class="form-label">Special Instruction </label>
                                        </div>
                                    </div>
                                    <div class="col-lg-10">
                                        <textarea type="text"
                                                  class="form-control @if ($errors->has('instruction')) is-invalid @endif"
                                                  name="instruction">{{ old('instruction') ?: $transportationBill->instruction }}</textarea>
                                        @if ($errors->has('instruction'))
                                            <div class="fv-plugins-message-container invalid-feedback">
                                                <div
                                                    data-field="instruction">{!! $errors->first('instruction') !!}</div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-lg-2">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="validationRemarks" class="form-label required-label">Receiver
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-lg-10">
                                        @php $selectedApproverId = old('approver_id') ?: $transportationBill->approver_id; @endphp
                                        <select name="approver_id"
                                                class="select2 form-control
                                            @if ($errors->has('approver_id')) is-invalid @endif"
                                                data-width="100%">
                                            <option value="">Select an Receiver</option>
                                            @foreach ($receivers as $approver)
                                                <option value="{{ $approver->id }}"
                                                    {{ $approver->id == $selectedApproverId ? 'selected' : '' }}>
                                                    {{ $approver->getFullNameWithEmpCode() }}</option>
                                            @endforeach
                                        </select>
                                        @if ($errors->has('approver_id'))
                                            <div class="fv-plugins-message-container invalid-feedback">
                                                <div data-field="approver_id">
                                                    {!! $errors->first('approver_id') !!}
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-lg-2">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="validationRemarks" class="form-label required-label">Alternative
                                                Receiver </label>
                                        </div>
                                    </div>
                                    <div class="col-lg-10">
                                        @php $selectedReceiverId = old('reviewer_id') ?: $transportationBill->reviewer_id; @endphp
                                        <select name="reviewer_id"
                                                class="select2 form-control
                                            @if ($errors->has('reviewer_id')) is-invalid @endif"
                                                data-width="100%">
                                            <option value="">Select an Alternative Receiver</option>
                                            @foreach ($receivers as $reviewer)
                                                <option value="{{ $reviewer->id }}"
                                                    {{ $reviewer->id == $selectedReceiverId ? 'selected' : '' }}>
                                                    {{ $reviewer->getFullNameWithEmpCode() }}</option>
                                            @endforeach
                                        </select>
                                        @if ($errors->has('reviewer_id'))
                                            <div class="fv-plugins-message-container invalid-feedback">
                                                <div data-field="reviewer_id">
                                                    {!! $errors->first('reviewer_id') !!}
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                {!! csrf_field() !!}
                                {!! method_field('PUT') !!}
                            </div>
                          </div>

                        <div class="card">
                            <div class="card-header fw-bold">
                                <div class="d-flex align-items-center add-info justify-content-between">
                                    <span> Transportation Bill Details</span>
                                    @if ($authUser->can('update', $transportationBill))
                                        <button data-toggle="modal"
                                                class="btn btn-primary btn-sm open-detail-modal-form"
                                                href="{!! route('transportation.bills.details.create', $transportationBill->id) !!}">
                                            <i class="bi-plus"></i> Add New Item
                                        </button>
                                    @endif
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table" id="transportationBillDetailTable">
                                        <thead class="thead-light">
                                        <tr>
                                            <th scope="col">{{ __('label.items') }}</th>
                                            <th scope="col">{{ __('label.quantity') }}</th>
                                            <th scope="col">{{ __('label.remarks') }}</th>
                                            <th style="width: 150px" class="sticky-col">{{ __('label.action') }}</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                        </div>
                        <div class="justify-content-end d-flex gap-2 mt-4">
                            <button type="submit" name="btn" value="save" class="btn btn-primary btn-sm">
                                Update
                            </button>
                            <button type="submit" name="btn" value="submit"
                                    class="btn btn-success btn-sm submit-record"
                                    @if (!$authUser->can('submit', $transportationBill)) style="display:none;" @endif>
                                Submit
                            </button>
                            <a href="{!! route('transportation.bills.index') !!}" class="btn btn-danger btn-sm">Cancel</a>
                        </div>
                    </form>
            </section>

@stop
