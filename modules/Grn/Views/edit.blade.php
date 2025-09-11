@extends('layouts.container')

@section('title', 'Edit GRN')

@section('page_js')
    <script type="text/javascript">
        document.addEventListener('DOMContentLoaded', function (e) {
            $('#navbarVerticalMenu').find('#grns-menu').addClass('active');
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
                endDate: '{{ date('Y-m-d') }}',
            }).on('change', function () {
                fv.revalidateField('received_date');
            });
        });

        var grnItemTable = $('#grnItemTable').DataTable({
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
                {data: 'total_price', name: 'total_price'},
                {data: 'discount_amount', name: 'discount_amount'},
                {data: 'vat_amount', name: 'vat_amount'},
                {data: 'total_amount', name: 'total_amount'},
                {data: 'specification', name: 'specification'},
                {data: 'action', name: 'action', orderable: false, searchable: false},
            ],
            drawCallback: function () {
                let table = this[0]
                let footer = table.getElementsByTagName('tfoot')[0];
                if (!footer) {
                    footer = document.createElement('tfoot');
                    table.appendChild(footer); 
                }

                let est_amount = this.api().column(4).data().reduce(function (a, b) {
                    return parseFloat(a) + parseFloat(b);
                }, 0);
                let discount_amount = this.api().column(5).data().reduce(function (a, b) {
                    return parseFloat(a) + parseFloat(b);
                }, 0);
                let vat_amount = this.api().column(6).data().reduce(function (a, b) {
                    return parseFloat(a) + parseFloat(b);
                }, 0);
                let total_amount = this.api().column(7).data().reduce(function (a, b) {
                    return parseFloat(a) + parseFloat(b);
                }, 0);

                est_amount = new Intl.NumberFormat('en-US').format(est_amount);
                discount_amount = new Intl.NumberFormat('en-US').format(discount_amount);
                vat_amount = new Intl.NumberFormat('en-US').format(vat_amount);
                total_amount = new Intl.NumberFormat('en-US').format(total_amount);

                footer.innerHTML = '';
                footer.innerHTML =  `<tr>
                    <td colspan='4'>Total Tentative Amount</td>
                    <td>${est_amount}</td>
                    <td>${discount_amount}</td>
                    <td>${vat_amount}</td>
                    <td colspan='3'>${total_amount}</td>
                </tr>`

            }
        });

        $('#grnItemTable').on('click', '.delete-record', function (e) {
            e.preventDefault();
            $object = $(this);
            var $url = $object.attr('data-href');
            var successCallback = function (response) {
                toastr.success(response.message, 'Success', {timeOut: 5000});
                grnItemTable.ajax.reload();
            }
            ajaxDeleteSweetAlert($url, successCallback);
        });

        $(document).on('click', '.open-item-modal-form', function (e) {
            e.preventDefault();
            $('#openModal').find('.modal-content').html('');
            $('#openModal').modal('show').find('.modal-content').load($(this).attr('href'), function () {
                const form = document.getElementById('grnItemForm');
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
                        toastr.success(response.message, 'Success', {timeOut: 5000});
                        grnItemTable.ajax.reload();
                    }
                    ajaxSubmit($url, 'POST', data, successCallback);
                });

                $(form).on('change', '[name="quantity"]', function (e) {
                    calculateTotalPrice(this);
                }).on('change', '[name="vat_applicable"]', function (e) {
                    calculateTotalPrice(this);
                }).on('change', '[name="unit_price"]', function (e) {
                    calculateTotalPrice(this);
                }).on('change', '[name="item_id"]', function (e) {
                    $element = $(this);
                    var itemId = $element.val();
                    var htmlToReplace = '<option value="">Select Unit</option>';
                    $($element).closest('form').find('[name="unit_id"]').html(htmlToReplace);
                    if (itemId) {
                        var url = baseUrl + '/api/master/items/' + itemId;
                        var successCallback = function (response) {
                            response.units.forEach(function (unit) {
                                htmlToReplace += '<option value="' + unit.id + '" selected="selected">' + unit.title + '</option>';
                            });
                            $($element).closest('form').find('[name="unit_id"]').html(htmlToReplace).trigger('change');
                        }
                        var errorCallback = function (error) {
                            console.log(error);
                        }
                        ajaxNativeSubmit(url, 'GET', {}, 'json', successCallback, errorCallback);
                    }
                }).on('change', '[name="activity_code_id"]', function (e) {
                    $element = $(this);
                    var activityCodeId = $element.val();
                    var htmlToReplace = '<option value="">Select Account Code</option>';
                    if (activityCodeId) {
                        var url = baseUrl + '/api/master/activity-codes/' + activityCodeId;
                        var successCallback = function (response) {
                            response.accountCodes.forEach(function (accountCode) {
                                htmlToReplace += '<option value="' + accountCode.id + '">' + accountCode.title + ' ' + accountCode.description + '</option>';
                            });
                            $($element).closest('form').find('[name="account_code_id"]').html(htmlToReplace).trigger('change');
                        }
                        var errorCallback = function (error) {
                            console.log(error);
                        }
                        ajaxNativeSubmit(url, 'GET', {}, 'json', successCallback, errorCallback);
                    } else {
                        $($element).closest('form').find('[name="account_code_id"]').html(htmlToReplace);
                    }
                });

                function calculateTotalPrice($element) {
                    quantity = parseFloat($($element).closest('form').find('[name="quantity"]').val());
                    unitPrice = parseFloat($($element).closest('form').find('[name="unit_price"]').val());
                    unitPrice = isNaN(unitPrice) ? 0 : unitPrice;
                    quantity = isNaN(quantity) ? 0 : quantity;
                    billAmount = unitPrice * quantity;
                    vatFlag = $($element).closest('form').find('[name="vat_applicable"]').prop('checked');
                    vatAmount = vatFlag ? parseFloat(billAmount * vatPercentage / 100) : 0;
                    $($element).closest('form').find('[name="sub_total"]').val(billAmount);
                    $($element).closest('form').find('[name="vat_amount"]').val(vatAmount);
                    $($element).closest('form').find('[name="total_amount"]').val(vatAmount + billAmount);
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
                                                <label for="validationSupplier"
                                                       class="form-label required-label">Supplier</label>
                                            </div>
                                        </div>
                                        <div class="col-lg-9">
                                            <select class="form-control" name="supplier_id">
                                                <option value="">Select Supplier</option>
                                                @foreach($suppliers as $supplier)
                                                    <option value="{!! $supplier->id !!}"
                                                            @if($supplier->id == $grn->supplier_id) selected @endif>{!! $supplier->getSupplierNameandVAT() !!}</option>
                                                @endforeach
                                            </select>
                                            @if($errors->has('supplier_id'))
                                                <div class="fv-plugins-message-container invalid-feedback">
                                                    <div data-field="supplier_id">
                                                        {!! $errors->first('supplier_id') !!}
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
                                            <input class="form-control" name="invoice_number" type="text"
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
                                        <div class="p2">
                                            <div class="d-flex align-items-center add-info justify-content-end">
                                                @if ($authUser->can('update', $grn))
                                                    <button data-toggle="modal"
                                                            class="btn btn-primary btn-sm open-item-modal-form"
                                                            href="{!! route('grns.items.create', $grn->id) !!}"
                                                    ><i class="bi-plus"></i> Add New Item
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="container-fluid-s">
                                            <div class="card">
                                                <div class="card-body" style="overflow: auto;">
                                                    <table class="table" id="grnItemTable">
                                                        <thead class="thead-light">
                                                        <tr>
                                                            <th scope="col">{{ __('label.item') }}</th>
                                                            <th scope="col">{{ __('label.unit') }}</th>
                                                            <th scope="col">{{ __('label.quantity') }}</th>
                                                            <th scope="col">{{ __('label.unit-price') }}</th>
                                                            <th scope="col">{{ __('label.amount') }}</th>
                                                            <th scope="col">{{ __('label.discount') }}</th>
                                                            <th scope="col">{{ __('label.vat-amount') }}</th>
                                                            <th scope="col">{{ __('label.total-amount') }}</th>
                                                            <th scope="col">{{ __('label.specification') }}</th>
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
