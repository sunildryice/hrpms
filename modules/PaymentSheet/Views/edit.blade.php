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
                    data: 'charged_office',
                    name: 'charged_office',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'description',
                    name: 'description',
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
            ],
            drawCallback: function() {
                    let data = this.api().ajax.json();
                    let table = this[0];
                    let footer = table.getElementsByTagName('tfoot')[0];
                    if (!footer) {
                        footer = document.createElement("tfoot");
                        table.appendChild(footer);
                    }

                    let deduction_amount = "{{$paymentSheet->deduction_amount}}";
                    let paid_amount = Math.round((data.sum_net_amount - deduction_amount) * 100) / 100;

                    footer.innerHTML = '';
                    footer.innerHTML = `<tr>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td>${data.sum_total_amount}</td>
                                            <td>${data.sum_vat_amount}</td>
                                            <td>${data.sum_tds_amount}</td>
                                            <td id="net_amount">${data.sum_net_amount}</td>
                                            <td></td>
                                        </tr>`;

                    footer.innerHTML += `<tr>
                                            <td colspan="8"></td>
                                            <td>Deduction Amount</td>
                                            <td>
                                                <input type="text" class="form-control" name="deduction_amount" id="deduction_amount" value="{{$paymentSheet->deduction_amount}}">
                                            </td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td colspan="8"></td>
                                            <td>Paid Amount</td>
                                            <td id="paid_amount"> ${paid_amount}</td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td colspan="7"></td>
                                            <td>Deduction Remarks</td>
                                            <td colspan="3">
                                                <input type="text" class="form-control" name="deduction_remarks" id="deduction_remarks" value="{{ $paymentSheet->deduction_remarks }}" placeholder="Deduction remarks">
                                            </td>
                                        </tr>`;

                    $('#deduction_amount').on('change', function (e) {
                        deduction_amount = e.target.value ;
                        let data = { '_token': '{{csrf_token()}}', '_method': 'PUT', 'deduction_amount': deduction_amount};
                        let url  = "{{route('api.payment.sheets.update', $paymentSheet->id)}}";
                        var successCallback = function(response) {
                            $('#deduction_amount').val(response.paymentSheet.deduction_amount);
                            $('#paid_amount').html(Math.round(response.paymentSheet.paid_amount*100)/100);
                        }
                        ajaxNativeSubmit(url, 'PUT', data, 'json', successCallback);
                    });

                    $('#deduction_remarks').on('change', function (e) {
                        deduction_remarks = e.target.value ;
                        let data = { '_token': '{{csrf_token()}}', '_method': 'PUT', 'deduction_remarks': deduction_remarks};
                        let url  = "{{route('api.payment.sheets.update', $paymentSheet->id)}}";
                        var successCallback = function(response) {
                            // console.log(response);
                        }
                        ajaxNativeSubmit(url, 'PUT', data, 'json', successCallback);
                    });
                },
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
                        total_amount: {
                            validators: {
                                notEmpty: {
                                    message: 'Amount is required.'
                                }
                            }
                        }
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
                            $($element).closest('form').find('[name="left_amount"]').val(response.leftAmount);
                            if (response.paymentBill.vat_flag == 1) {
                                $($element).closest('form').find('.vatBlock').show();
                                $($element).closest('form').find('[name="tds_percentage"]').val(response.vatTdsPercentage).attr('readonly', true);
                            } else {
                                $($element).closest('form').find('.vatBlock').hide();
                                $($element).closest('form').find('[name="tds_percentage"]').attr('readonly', false);
                            }
                        }
                        var errorCallback = function (error) {
                            console.log(error);
                        }
                        ajaxNativeSubmit(url, 'GET', {}, 'json', successCallback, errorCallback);
                    } else {
                        $($element).closest('form').find('[name="left_amount"]').val('100');
                    }
                    fv.revalidateField('payment_bill_id');
                    // fv.revalidateField('total_amount');
                    calculateTotalAmount(this);
                })
                .on('change', '[name="tds_percentage"]', function (e) {
                    calculateTotalAmount(this);
                }).on('change', '[name="charged_office_id"]', function (e) {
                    fv.revalidateField('charged_office_id');
                }).on('change', '[name="tds_applicable"]', function (e) {
                    calculateTotalAmount(this);
                })
                .on('change', '[name="total_amount"]', function (e) {
                    calculateTotalAmount(this);
                });

                function calculateTotalAmount($element) {
                    percentage = parseFloat($($element).closest('form').find('[name="tds_percentage"]').val());

                    if($($element).closest('form').find('[name="payment_bill_id"]').is('input:text')) {
                        vatFlag = $($element).closest('form').find('[name="bill_amount"]').data('vat-applicable');
                    } else {
                        vatFlag = $($element).closest('form').find('[name="payment_bill_id"] option:selected').data('vat-applicable');
                    }

                    totalAmount = parseFloat($($element).closest('form').find('[name="total_amount"]').val());
                    tdsPercent = $($element).closest('form').find('[name="tds_percentage"]').val();
                    vatAmount = amountWithVat = tdsAmount = 0;

                    if (!vatFlag) {
                        tdsAmount = parseFloat(totalAmount * tdsPercent / 100);
                    } else {
                        vatAmount = parseFloat(totalAmount * vatPercentage / 100);
                        amountWithVat = totalAmount ? Math.round((totalAmount + vatAmount) * 100, 2) / 100 : 0;
                        // tdsAmount = parseFloat(totalAmount * vatTdsPercentage / 100);
                        tdsAmount = parseFloat(totalAmount * tdsPercent / 100);
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


        // Start - Attachment Scripts Section
        var attachmentTable = $('#attachmentTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('payment.sheets.attachment.index', $paymentSheet->id) }}",
            bFilter: false,
            bPaginate: false,
            bInfo: false,
            columns: [
                {
                    data: 'title',
                    name: 'title'
                },
                {
                    data: 'attachment',
                    name: 'attachment',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'link',
                    name: 'link',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                },
            ]
        });

        $('#attachmentTable').on('click', '.delete-record', function(e) {
            e.preventDefault();
            $object = $(this);
            var $url = $object.attr('data-href');
            var successCallback = function(response) {
                toastr.success(response.message, 'Success', {
                    timeOut: 5000
                });
                attachmentTable.ajax.reload();
            }
            ajaxDeleteSweetAlert($url, successCallback);
        });

        $(document).on('click', '.open-attachment-create-modal-form', function(e) {
            e.preventDefault();
            $('#openModal').find('.modal-content').html('');
            $('#openModal').modal('show').find('.modal-content').load($(this).attr('href'), function (){
                const form = document.getElementById('attachmentCreateForm');
                const fv = FormValidation.formValidation(form, {
                    fields: {
                        title: {
                            validators: {
                                notEmpty: {
                                    message: 'The attachment title is required.',
                                },
                            },
                        },
                        attachment: {
                            validators: {
                                // notEmpty: {
                                //     message: 'The attachment is required.',
                                // },
                                file: {
                                    extension: 'jpeg,jpg,png,pdf',
                                    type: 'image/jpeg,image/png,application/pdf',
                                    maxSize: '5097152',
                                    message: 'The selected file is not valid file or must not be greater than 5 MB.',
                                },
                            },
                        },
                        attachment_link: {
                            validators: {
                                uri: {
                                    protocol: 'http, https, ftp',
                                    message: 'Please enter a valid link.'
                                }
                            }
                        }
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
                    let form = document.getElementById('attachmentCreateForm');
                    let data = new FormData(form);
                    let url  = form.getAttribute('action');

                    var successCallback = function(response) {
                        $('#openModal').modal('hide');
                        toastr.success(response.message, 'Success', {
                            timeOut: 5000
                        });
                        attachmentTable.ajax.reload();
                    }
                    ajaxSubmitFormData(url, 'POST', data, successCallback);
                });
            });
        });

        $(document).on('click', '.open-attachment-edit-modal-form', function(e) {
            e.preventDefault();
            $('#openModal').find('.modal-content').html('');
            $('#openModal').modal('show').find('.modal-content').load($(this).attr('href'), function (){
                const form = document.getElementById('attachmentEditForm');
                const fv = FormValidation.formValidation(form, {
                    fields: {
                        title: {
                            validators: {
                                notEmpty: {
                                    message: 'The attachment title is required.',
                                },
                            },
                        },
                        attachment: {
                            validators: {
                                file: {
                                    extension: 'jpeg,jpg,png,pdf',
                                    type: 'image/jpeg,image/png,application/pdf',
                                    maxSize: '5097152',
                                    message: 'The selected file is not valid file or must not be greater than 5 MB.',
                                },
                            },
                        },
                        attachment_link: {
                            validators: {
                                uri: {
                                    protocol: 'http, https, ftp',
                                    message: 'Please enter a valid link.'
                                }
                            }
                        }
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
                    let form = document.getElementById('attachmentEditForm');
                    let data = new FormData(form);
                    let url  = form.getAttribute('action');

                    var successCallback = function(response) {
                        $('#openModal').modal('hide');
                        toastr.success(response.message, 'Success', {
                            timeOut: 5000
                        });
                        attachmentTable.ajax.reload();
                    }
                    ajaxSubmitFormData(url, 'POST', data, successCallback);
                });
            });
        });

        // Start - Attachment Scripts Section


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
                                            <select @if($paymentSheet->is_from_po) disabled @endif
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

                                        {{-- <div class="col-lg-3"> --}}
                                        {{--     <div class="d-flex align-items-start h-100"> --}}
                                        {{--         <label for="validationdd" class="m-0">District</label> --}}
                                        {{--     </div> --}}
                                        {{-- </div> --}}
                                        {{-- <div class="col-lg-3"> --}}
                                        {{--     <select name="district_id" class="select2 form-control" data-width="100%"> --}}
                                        {{--         <option value="">Select a District</option> --}}
                                        {{--         @foreach($districts as $district) --}}
                                        {{--             <option value="{{ $district->id }}" --}}
                                        {{--                     @if($paymentSheet->district_id == $district->id) selected @endif> --}}
                                        {{--                 {{ $district->getDistrictName() }} --}}
                                        {{--             </option> --}}
                                        {{--         @endforeach --}}
                                        {{--     </select> --}}
                                        {{--     @if($errors->has('district_id')) --}}
                                        {{--         <div class="fv-plugins-message-container invalid-feedback"> --}}
                                        {{--             <div data-field="district_id"> --}}
                                        {{--                 {!! $errors->first('district_id') !!} --}}
                                        {{--             </div> --}}
                                        {{--         </div> --}}
                                        {{--     @endif --}}
                                        {{-- </div> --}}

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
                                            @php $selectedApproverId = old('approver_id') ?: (isset($paymentSheet->approver_id) && isset($paymentSheet->recommender_id) ? $paymentSheet->recommender_id : $paymentSheet->approver_id ) ; @endphp
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
                                        <div class="card-header fw-bold" style="display: flex; flex-direction: row; align-items: center; justify-content: space-between;">
                                            <span>
                                                Attachments
                                            </span>
                                            <button data-toggle="modal"
                                                class="btn btn-primary btn-sm open-attachment-create-modal-form"
                                                href="{{ route('payment.sheets.attachment.create', $paymentSheet->id) }}"><i
                                                    class="bi-plus"></i> Add Attachment
                                            </button>
                                        </div>
                                        <div class="container-fluid-s">
                                            <div class="card-body">
                                                <div class="table-responsive">
                                                    <table class="table" id="attachmentTable">
                                                        <thead class="bg-light">
                                                            <tr>
                                                                <th scope="col">Title</th>
                                                                <th scope="col" style="width: 150px">Attachment</th>
                                                                <th scope="col" style="width: 150px">Link</th>
                                                                <th scope="col" style="width: 150px">{{ __('label.action') }}</th>
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


                                <div class="card-body">
                                    <div class="card mb-2">
                                        <div class="card-header fw-bold">
                                            <div class="d-flex align-items-center add-info justify-content-between">
                                                <span>
                                                    Payment Sheet Details
                                                </span>
                                                @if ($authUser->can('update', $paymentSheet))
                                                    <button data-toggle="modal"
                                                            class="btn btn-primary btn-sm open-detail-modal-form"
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
                                                                <th scope="col">{{ __('label.charged-office') }}</th>
                                                                <th scope="col">{{ __('label.description') }}</th>
                                                                <th scope="col">{{ __('label.bill-amount') }}</th>
                                                                <th scope="col">{{ __('label.vat-amount') }}</th>
                                                                <th scope="col">{{ __('label.tds-amount') }}</th>
                                                                <th scope="col">{{ __('label.total-amount') }}</th>
                                                                <th scope="col" style="width: 100px;">{{ __('label.action') }}</th>
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

                                @if ($paymentSheet->status_id == config('constant.RETURNED_STATUS'))
                                    <div class="card-body">
                                        <div class="card">
                                            <div class="card-header fw-bold">
                                                Reason for return
                                            </div>
                                            <div class="card-body">
                                                <p>{{ $paymentSheet->logs->where('status_id', config('constant.RETURNED_STATUS'))->last()?->log_remarks }}</p>
                                            </div>
                                        </div>
                                    </div>
                                @endif

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
