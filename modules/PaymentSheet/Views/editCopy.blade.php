@extends('layouts.container')

@section('title', 'Edit Payment Sheet')

@section('page_js')
    <script type="text/javascript">
        document.addEventListener('DOMContentLoaded', function (e) {
            $('#navbarVerticalMenu').find('#payment-sheets-menu').addClass('active');
            const form = document.getElementById('paymentSheetEditForm');
            const fv = FormValidation.formValidation(form, {
                fields: {
                    verifier_id: {
                        validators: {
                            notEmpty: {
                                message: 'Verifier is required',
                            },
                        },
                    },
                    approver_id: {
                        validators: {
                            notEmpty: {
                                message: 'Approver is required',
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

            $(form).on('change', '[name="supplier_id"]', function (e) {
                $element = $(this);
                $(form).find('[name="vat_pan_number"]').val($('[name="supplier_id"] option:selected').data(
                    'vat'));
                $element = $(this);
                var supplierId = $element.val();
                var htmlToReplace = '<option value="">Select Purchase Order</option>';
                if (supplierId) {
                    var url = baseUrl + '/api/suppliers/' + supplierId;
                    var successCallback = function (response) {
                        response.purchaseOrders.forEach(function (purchaseOrder) {
                            htmlToReplace += '<option value="' + purchaseOrder.id + '">' +
                                purchaseOrder.prefix + '-' + purchaseOrder.order_number +
                                '</option>';
                        });
                        $($element).closest('form').find(".purchase_order_id").html(htmlToReplace)
                            .trigger('change');
                    }
                    var errorCallback = function (error) {
                        console.log(error);
                    }
                    ajaxNativeSubmit(url, 'GET', {}, 'json', successCallback, errorCallback);
                } else {
                    $($element).closest('form').find(".purchase_order_id").html(htmlToReplace);
                }
                fv.revalidateField('supplier_id');
            });
        });

        var oTable = $('#paymentSheetDetailTable').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            ajax: "{{ route('payment.sheets.details.index', $paymentSheet->id) }}",
            bFilter: false,
            bPaginate: false,
            bInfo: false,
            columns: [{
                data: 'bill_number',
                name: 'bill_number',
                orderable: false,
                searchable: false
            },
                {
                    data: 'activity',
                    name: 'activity',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'account',
                    name: 'account',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'donor',
                    name: 'donor',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'total_amount',
                    name: 'total_amount'
                },
                {
                    data: 'vat_amount',
                    name: 'vat_amount'
                },
                {
                    data: 'tds_amount',
                    name: 'tds_amount'
                },
                {
                    data: 'net_amount',
                    name: 'net_amount'
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

        $('#paymentSheetDetailTable').on('click', '.delete-record', function (e) {
            e.preventDefault();
            $object = $(this);
            var $url = $object.attr('data-href');
            var successCallback = function (response) {
                toastr.success(response.message, 'Success', {
                    timeOut: 5000
                });
                if (response.paymentDetailCount) {
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
                const form = document.getElementById('paymentSheetDetailForm');
                $(form).find(".select2").each(function () {
                    $(this)
                        .wrap("<div class=\"position-relative\"></div>")
                        .select2({
                            dropdownParent: $(this).parent(),
                            width: '100%',
                            dropdownAutoWidth: true
                        });
                });
                const fv = FormValidation.formValidation(form, {
                    fields: {
                        payment_bill_id: {
                            validators: {
                                notEmpty: {
                                    message: 'Bill number is required',
                                },
                            },
                        },
                        activity_code_id: {
                            validators: {
                                notEmpty: {
                                    message: 'Activity code is required',
                                },
                            },
                        },
                        account_code_id: {
                            validators: {
                                notEmpty: {
                                    message: 'Account code is required',
                                },
                            },
                        },
                        charged_office_id: {
                            validators: {
                                notEmpty: {
                                    message: 'Charged to office is required',
                                },
                            },
                        },
                        processed_by_office_id: {
                            validators: {
                                notEmpty: {
                                    message: 'Processed by office is required',
                                },
                            },
                        },
                        percentage: {
                            validators: {
                                // notEmpty: {
                                //     message: 'Percentage is required',
                                // },
                                greaterThan: {
                                    message: 'The value must be greater than or equal to 1',
                                    min: 1,
                                },
                                lessThan: {
                                    message: 'The value must be less than or equal to 100',
                                    max: 100,
                                },
                                callback: {
                                    message: 'Percentage is required and total percentage can not be greater than 100.',
                                    callback: function (input, validator, $field) {
                                        const value = parseInt(input.value);
                                        if (value <= parseInt($('[name="left_percentage"]').val())) {
                                            validator.updateStatus('percentage', validator.STATUS_VALID);
                                            return true;
                                        } else {
                                            return false;
                                        }
                                    }
                                }
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
                        if (response.paymentDetailCount) {
                            $('.submit-record').show();
                        } else {
                            $('.submit-record').hide();
                        }
                        oTable.ajax.reload();
                    }
                    ajaxSubmit($url, 'POST', data, successCallback);
                });

                $(form).on('change', '[name="activity_code_id"]', function (e) {
                    $element = $(this);
                    var activityCodeId = $element.val();
                    var htmlToReplace = '<option value="">Select Account Code</option>';
                    if (activityCodeId) {
                        var url = baseUrl + '/api/master/activity-codes/' + activityCodeId;
                        var successCallback = function (response) {
                            response.accountCodes.forEach(function (accountCode) {
                                htmlToReplace += '<option value="' + accountCode.id + '">' +
                                    accountCode.title + ' ' + accountCode.description + '</option>';
                            });
                            $($element).closest('form').find('[name="account_code_id"]').html(htmlToReplace)
                                .trigger('change');
                        }
                        var errorCallback = function (error) {
                            console.log(error);
                        }
                        ajaxNativeSubmit(url, 'GET', {}, 'json', successCallback, errorCallback);
                    } else {
                        $($element).closest('form').find('[name="account_code_id"]').html(htmlToReplace);
                    }
                    fv.revalidateField('activity_code_id');
                }).on('change', '[name="account_code_id"]', function (e) {
                    fv.revalidateField('account_code_id');
                }).on('change', '[name="payment_bill_id"]', function (e) {
                    $element = $(this);
                    $(form).find('[name="bill_amount"]').val($('[name="payment_bill_id"] option:selected').data(
                        'bill-amount'));
                    var paymentBillId = $element.val();
                    var paymentSheetId = $('[name="payment_sheet"]').val();
                    var detailId = $('[name="payment_sheet_detail_id"]').val();
                    if (paymentBillId) {
                        var url = baseUrl + '/api/payment-sheet/bills/' + paymentBillId;
                        var successCallback = function (response) {
                            $($element).closest('form').find('[name="left_percentage"]').val(response.leftPercentage);
                            if (response.paymentBill.vat_flag == 1) {
                                $($element).closest('form').find('.vatBlock').show();
                                $($element).closest('form').find('.nonVatBlock').hide();
                            } else {
                                $($element).closest('form').find('.vatBlock').hide();
                                $($element).closest('form').find('.nonVatBlock').show();
                            }
                        }
                        var errorCallback = function (error) {
                            console.log(error);
                        }
                        ajaxNativeSubmit(url, 'GET', {}, 'json', successCallback, errorCallback);
                    } else {
                        $($element).closest('form').find('[name="left_percentage"]').val('100');
                    }
                    fv.revalidateField('payment_bill_id');
                    fv.revalidateField('percentage');
                    calculateTotalAmount(this);
                }).on('change', '[name="percentage"]', function (e) {
                    fv.revalidateField('percentage');
                    calculateTotalAmount(this);
                }).on('change', '[name="processed_by_office_id"]', function (e) {
                    fv.revalidateField('processed_by_office_id');
                }).on('change', '[name="charged_office_id"]', function (e) {
                    fv.revalidateField('charged_office_id');
                }).on('change', '[name="tds_applicable"]', function (e) {
                    calculateTotalAmount(this);
                })

                function calculateTotalAmount($element) {
                    billAmount = parseFloat($($element).closest('form').find('[name="bill_amount"]').val());
                    percentage = parseFloat($($element).closest('form').find('[name="percentage"]').val());
                    vatFlag = $($element).closest('form').find('[name="payment_bill_id"] option:selected').data('vat-applicable');
                    totalAmount = parseFloat(billAmount * percentage / 100);
                    vatAmount = amountWithVat = tdsAmount = 0;

                    if (!vatFlag) {
                        tdsFlag = $($element).closest('form').find('[name="tds_applicable"]').prop('checked');
                        tdsAmount = tdsFlag ? parseFloat(totalAmount * tdsPercentage / 100) : 0;
                    } else {
                        vatAmount = parseFloat(totalAmount * vatPercentage / 100);
                        amountWithVat = totalAmount ? Math.round((totalAmount + vatAmount) * 100, 2) / 100 : 0;
                        tdsAmount = parseFloat(totalAmount * vatTdsPercentage / 100);
                    }
                    netAmount = totalAmount ? Math.round((totalAmount + vatAmount - tdsAmount) * 100, 2) / 100 : 0;
                    $($element).closest('form').find('[name="total_amount"]').val(totalAmount);
                    $($element).closest('form').find('[name="vat_amount"]').val(vatAmount);
                    $($element).closest('form').find('[name="amount_with_vat"]').val(amountWithVat);
                    $($element).closest('form').find('[name="tds_amount"]').val(tdsAmount);
                    $($element).closest('form').find('[name="net_amount"]').val(netAmount);
                }
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
                                    <a href="{{ route('payment.sheets.index') }}" class="text-decoration-none">Payment
                                        Sheet</a>
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
                            <form action="{{ route('payment.sheets.update', $paymentSheet->id) }}"
                                  id="paymentSheetEditForm"
                                  method="post" enctype="multipart/form-data" autocomplete="off">
                                <div class="card-body">
                                    <div class="row mb-2">
                                        <div class="col-lg-3">
                                            <div class="d-flex align-items-start h-100">
                                                <label for="validationdd"
                                                       class="form-label required-label">{{ __('label.supplier') }}</label>
                                            </div>
                                        </div>
                                        <div class="col-lg-3">
                                            <select
                                                class="select2 form-control @if ($errors->has('supplier_id')) is-invalid @endif"
                                                name="supplier_id" disabled>
                                                <option value="">Select Supplier</option>
                                                @foreach ($suppliers as $supplier)
                                                    <option value="{{ $supplier->id }}"
                                                            data-vat="{{ $supplier->vat_pan_number }}"
                                                            @if ($supplier->id == $paymentSheet->supplier_id) selected @endif>
                                                        {{ $supplier->getSupplierName() }}</option>
                                                @endforeach
                                            </select>
                                            @if ($errors->has('supplier_id'))
                                                <div class="fv-plugins-message-container invalid-feedback">
                                                    <div data-field="supplier_id">
                                                        {!! $errors->first('supplier_id') !!}
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="col-lg-3">
                                            <div class="d-flex align-items-start h-100">
                                                <label for="validationdd"
                                                       class="m-0">{{ __('label.vat-pan-no') }}</label>
                                            </div>
                                        </div>
                                        <div class="col-lg-3">
                                            <input class="form-control" name="vat_pan_number"
                                                   value="{{ old('vat_pan_number') ?: $paymentSheet->getSupplierVatPanNumber() }}"
                                                   readonly/>
                                        </div>
                                    </div>

                                    <div class="row mb-2">
                                        <div class="col-lg-3">
                                            <div class="d-flex align-items-start h-100">
                                                <label for="validationdd"
                                                       class="m-0">{{ __('label.purchase-order') }}</label>
                                            </div>
                                        </div>
                                        @php $selectedPurchaseOrderIds = $paymentSheet->purchaseOrders->pluck('id')->toArray() @endphp
                                        <div class="col-lg-3">
                                            <select
                                                class="select2 purchase_order_id form-control @if ($errors->has('purchase_order_ids')) is-invalid @endif"
                                                name="purchase_order_ids[]" multiple="multiple">
                                                <option value="">Select Purchase Orders</option>
                                                @foreach ($purchaseOrders as $purchaseOrder)
                                                    <option value="{{ $purchaseOrder->id }}"
                                                            @if (in_array($purchaseOrder->id, $selectedPurchaseOrderIds)) selected @endif>
                                                        {{ $purchaseOrder->getPurchaseOrderNumber() }}</option>
                                                @endforeach
                                            </select>
                                            @if ($errors->has('purchase_order_ids'))
                                                <div class="fv-plugins-message-container invalid-feedback">
                                                    <div data-field="purchase_order_ids">
                                                        {!! $errors->first('purchase_order_ids') !!}
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="col-lg-3">
                                            <div class="d-flex align-items-start h-100">
                                                <label for="validationdd" class="m-0">District</label>
                                            </div>
                                        </div>
                                        <div class="col-lg-3">
                                            <select name="district_id" class="select2 form-control" data-width="100%">
                                                <option value="">Select a District</option>
                                                @foreach($districts as $district)
                                                    <option value="{{ $district->id }}"
                                                            @if($paymentSheet->district_id == $district->id) selected @endif>
                                                        {{ $district->getDistrictName() }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @if($errors->has('district_id'))
                                                <div class="fv-plugins-message-container invalid-feedback">
                                                    <div data-field="district_id">
                                                        {!! $errors->first('district_id') !!}
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="row mb-2">
                                        <div class="col-lg-3">
                                            <div class="d-flex align-items-start h-100">
                                                <label class="m-0" for="purpose">Purpose</label>
                                            </div>
                                        </div>
                                        <div class="col-lg-9">
                                            <input class="form-control" type="text" name="purpose" id="purpose" value="{{$paymentSheet->purpose}}">
                                        </div>
                                    </div>
                                    <div class="row mb-2">
                                        <div class="col-lg-3">
                                            <div class="d-flex align-items-start h-100">
                                                <label for="validationRemarks" class="form-label required-label">Send
                                                    To</label>
                                            </div>
                                        </div>
                                        <div class="col-lg-3">
                                            @php $selectedVerifierId = old('verifier_id') ?: $paymentSheet->verifier_id; @endphp
                                            <select name="verifier_id"
                                                    class="select2 form-control
                                                @if ($errors->has('verifier_id')) is-invalid @endif"
                                                    data-width="100%">
                                                <option value="">Select Verifier</option>
                                                @foreach ($verifiers as $verifier)
                                                    <option value="{{ $verifier->id }}"
                                                        {{ $verifier->id == $selectedVerifierId ? 'selected' : '' }}>
                                                        {{ $verifier->full_name }}</option>
                                                @endforeach
                                            </select>
                                            @if ($errors->has('verifier_id'))
                                                <div class="fv-plugins-message-container invalid-feedback">
                                                    <div data-field="verifier_id">
                                                        {!! $errors->first('verifier_id') !!}
                                                    </div>
                                                </div>
                                            @endif
                                        </div>

                                        <div class="col-lg-3">
                                            <div class="d-flex align-items-start h-100">
                                                <label for="validationRemarks"
                                                       class="form-label required-label">Approver</label>
                                            </div>
                                        </div>
                                        <div class="col-lg-3">
                                            @php $selectedApproverId = old('approver_id') ?: $paymentSheet->approver_id; @endphp
                                            <select name="approver_id"
                                                    class="select2 form-control
                                                @if ($errors->has('approver_id')) is-invalid @endif"
                                                    data-width="100%">
                                                <option value="">Select Approver</option>
                                                @foreach ($approvers as $approver)
                                                    <option value="{{ $approver->id }}"
                                                        {{ $approver->id == $selectedApproverId ? 'selected' : '' }}>
                                                        {{ $approver->full_name }}</option>
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

                                    {!! method_field('PUT') !!}
                                    {!! csrf_field() !!}
                                    <div class="card-footer border-0 justify-content-end d-flex gap-2">
                                        <button type="submit" name="btn" value="save"
                                                class="btn btn-primary btn-sm">Update
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="card">
                                        <div class="card-header fw-bold">
                                            Payment Sheet Details
                                        </div>
                                        <div class="p2">
                                            <div class="d-flex align-items-center add-info justify-content-end">
                                                @if ($authUser->can('update', $paymentSheet))
                                                    <button data-toggle="modal"
                                                            class="btn btn-primary btn-sm open-detail-modal-form m-2"
                                                            href="{!! route('payment.sheets.details.create', $paymentSheet->id) !!}">
                                                        <i class="bi-plus"></i> Add New
                                                        Detail
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="container-fluid-s">
                                            <div class="card">
                                                <div class="card-body ">
                                                    <div class="table-responsive">
                                                        <table class="table table-responsive-sm"
                                                               id="paymentSheetDetailTable">
                                                            <thead class="thead-light">
                                                            <tr>
                                                                <th scope="col">{{ __('label.bill-no') }}</th>
                                                                <th scope="col">{{ __('label.activity') }}</th>
                                                                <th scope="col">{{ __('label.account') }}</th>
                                                                <th scope="col">{{ __('label.donor') }}</th>
                                                                <th scope="col">{{ __('label.bill-amount') }}</th>
                                                                <th scope="col">{{ __('label.vat-amount') }}</th>
                                                                <th scope="col">{{ __('label.tds-amount') }}</th>
                                                                <th scope="col">{{ __('label.total-amount') }}</th>
                                                                <th scope="col" style="width: 100px;">
                                                                    {{ __('label.action') }}</th>
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
                                </div>
                                <div class="card-footer border-0 justify-content-end d-flex gap-2">
                                    <button type="submit" name="btn" value="submit"
                                            class="btn btn-success btn-sm submit-record"
                                            @if (!$authUser->can('submit', $paymentSheet)) style="display:none;" @endif>
                                        Submit
                                    </button>
                                    <a href="{!! route('payment.sheets.index') !!}"
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
