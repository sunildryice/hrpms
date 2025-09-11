@php $authUser = auth()->user();@endphp
@extends('layouts.container')

@section('title', 'Purchase Order Detail')

@section('page_js')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#navbarVerticalMenu').find('#approved-purchase-orders-menu').addClass('active');

            var oTable = $('#purchaseOrderItemTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('purchase.orders.items.index', $purchaseOrder->id) }}",
                bFilter: false,
                bPaginate: false,
                bInfo: false,
                columns: [{
                        data: 'item',
                        name: 'item'
                    },
                    {
                        data: 'specification',
                        name: 'specification'
                    },
                    {
                        data: 'unit',
                        name: 'unit'
                    },

                    {
                        data: 'quantity',
                        name: 'quantity'
                    },
                    {
                        data: 'unit_price',
                        name: 'unit_price'
                    },
                    {
                        data: 'total_price',
                        name: 'total_price'
                    },
                    {
                        data: 'vat_amount',
                        name: 'vat_amount'
                    },
                    {
                        data: 'total_amount',
                        name: 'total_amount'
                    },
                    {
                        data: 'activity',
                        name: 'activity'
                    },
                    {
                        data: 'account',
                        name: 'account'
                    },
                    {
                        data: 'donor',
                        name: 'donor'
                    },
                ]
            });

            var oTable = $('#grnTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('approved.purchase.orders.grns.index', $purchaseOrder->id) }}",
                bFilter: false,
                bPaginate: false,
                bInfo: false,
                columns: [{
                        data: 'order_number',
                        name: 'order_number'
                    },
                    {
                        data: 'grn_number',
                        name: 'grn_number'
                    },
                    {
                        data: 'invoice_number',
                        name: 'invoice_number'
                    },
                    {
                        data: 'received_date',
                        name: 'received_date'
                    },
                    {
                        data: 'supplier',
                        name: 'supplier'
                    },
                    {
                        data: 'grn_amount',
                        name: 'grn_amount'
                    },
                    {
                        data: 'status',
                        name: 'status',
                        orderable: false
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        className: 'sticky-col'
                    },
                ]
            });

            $('#grnTable').on('click', '.delete-record', function(e) {
                e.preventDefault();
                $object = $(this);
                var $url = $object.attr('data-href');
                console.log($object);
                var successCallback = function(response) {
                    toastr.success(response.message, 'Success', {
                        timeOut: 5000
                    });
                    oTable.ajax.reload();
                }
                ajaxDeleteSweetAlert($url, successCallback);
            });

            var oTable = $('#sheetsTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('approved.purchase.orders.payment.sheet.index', $purchaseOrder->id) }}",
                bFilter: false,
                bPaginate: false,
                bInfo: false,
                columns: [{
                        data: 'supplier',
                        name: 'supplier',
                    },
                    {
                        data: 'vat_pan_number',
                        name: 'vat_pan_number',
                    },
                    {
                        data: 'payment_sheet_number',
                        name: 'payment_sheet_number'
                    },
                    {
                        data: 'total_amount',
                        name: 'total_amount'
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

            $(document).on('click', '.open-item-modal-form', function(e) {
                e.preventDefault();
                $('#openModal').find('.modal-content').html('');
                $('#openModal').modal('show').find('.modal-content').load($(this).attr('href'), function() {
                    const form = document.getElementById('paymentSheetForm');
                    $(form).find(".select2").each(function() {
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
                            supplier_id: {
                                validators: {
                                    notEmpty: {
                                        message: 'Supplier is required',
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
                        $url = fv.form.action;
                        $form = fv.form;
                        data = $($form).serialize();
                        var successCallback = function(response) {
                            $('#openModal').modal('hide');
                            $('#itemHeading').html('Item Package: ' + response.package);
                            toastr.success(response.message, 'Success', {
                                timeOut: 5000
                            });

                            if (response.purchaseItemCount) {
                                $('.open-forward-modal-form').show();
                                $('.destroy-all').show();

                            } else {
                                $('.open-forward-modal-form').hide();
                                $('.destroy-all').hide();

                            }
                            oTable.ajax.reload();
                        }
                        ajaxSubmit($url, 'POST', data, successCallback);
                    });

                    $(form).on('change', '[name="supplier_id"]', function(e) {
                        $element = $(this);
                        $(form).find('[name="vat_pan_number"]').val($(
                            '[name="supplier_id"] option:selected').data('vat'));
                        $element = $(this);
                        var supplierId = $element.val();
                        var htmlToReplace =
                            '<option value="">Select Purchase Order</option>';
                        if (supplierId) {
                            var url = baseUrl + '/api/suppliers/' + supplierId;
                            var successCallback = function(response) {
                                response.purchaseOrders.forEach(function(
                                purchaseOrder) {
                                    htmlToReplace += '<option value="' +
                                        purchaseOrder.id + '">' + purchaseOrder
                                        .prefix + '-' + purchaseOrder
                                        .order_number + '</option>';
                                });
                                $($element).closest('form').find(".purchase_order_id")
                                    .html(htmlToReplace).trigger('change');
                            }
                            var errorCallback = function(error) {
                                console.log(error);
                            }
                            ajaxNativeSubmit(url, 'GET', {}, 'json', successCallback,
                                errorCallback);
                        } else {
                            $($element).closest('form').find(".purchase_order_id").html(
                                htmlToReplace);
                        }
                        fv.revalidateField('supplier_id');
                    });


                });
            });

            $('#sheetsTable').on('click', '.delete-record', function(e) {
                e.preventDefault();
                $object = $(this);
                var $url = $object.attr('data-href');
                console.log($object);
                var successCallback = function(response) {
                    toastr.success(response.message, 'Success', {
                        timeOut: 5000
                    });
                    oTable.ajax.reload();
                }
                ajaxDeleteSweetAlert($url, successCallback);
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
                                    <a href="{{ route('approved.purchase.orders.index') }}"
                                        class="text-decoration-none">Purchase
                                        Orders</a>
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
                    <div class="col-lg-3">
                        <div class="card">
                            <div class="card-header fw-bold">
                                Purchase Order Details
                            </div>
                            @include('PurchaseOrder::Partials.detail')
                        </div>
                    </div>
                    <div class="col-lg-9">
                        <div class="card">
                            <div class="card-header fw-bold">
                                Purchase Order Items
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="table-responsive">
                                            <table class="table" id="purchaseOrderItemTable">
                                                <thead class="thead-light">
                                                    <tr>
                                                        <th scope="col">{{ __('label.item') }}</th>
                                                        <th scope="col">{{ __('label.specification') }}</th>
                                                        <th scope="col">{{ __('label.unit') }}</th>
                                                        <th scope="col">{{ __('label.quantity') }}</th>
                                                        <th scope="col">{{ __('label.unit-price') }}</th>
                                                        <th scope="col">{{ __('label.sub-total') }}</th>
                                                        <th scope="col">{{ __('label.vat-amount') }}</th>
                                                        <th scope="col">{{ __('label.total-amount') }}</th>
                                                        <th scope="col">{{ __('label.activity') }}</th>
                                                        <th scope="col">{{ __('label.account') }}</th>
                                                        <th scope="col">{{ __('label.donor') }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                </tbody>
                                                <tr>
                                                    <td colspan="4">Total</td>
                                                    <td>{!! $purchaseOrder->sub_total !!}</td>
                                                    <td>{!! $purchaseOrder->vat_amount !!}</td>
                                                    <td>{!! $purchaseOrder->total_amount !!}</td>
                                                    <td colspan="3"></td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header fw-bold">
                                {{ __('label.grn') }}
                            </div>
                            <div class="p-2">
                                <div class="d-flex align-items-center add-info justify-content-end">
                                    <a class="btn btn-primary btn-sm" href="{!! route('approved.purchase.orders.grns.create', $purchaseOrder->id) !!}">
                                        <i class="bi-plus"></i> Add New GRN
                                    </a>
                                </div>
                            </div>
                            <div class="container-fluid-s">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table" id="grnTable">
                                                <thead class="thead-light">
                                                    <tr>
                                                        <th scope="col">{{ __('label.order-number') }}</th>
                                                        <th scope="col">{{ __('label.grn-number') }}</th>
                                                        <th scope="col">{{ __('label.invoice-number') }}</th>
                                                        <th scope="col">{{ __('label.grn-date') }}</th>
                                                        <th scope="col">{{ __('label.supplier') }}</th>
                                                        <th scope="col">{{ __('label.grn-amount') }}</th>
                                                        <th scope="col">{{ __('label.status') }}</th>
                                                        <th style="width: 150px" class="sticky-col">
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
                        <div class="card">
                            <div class="card-header fw-bold">
                                {{ __('label.payment-sheet') }}
                            </div>
                            <div class="p-2">
                                <div class="d-flex align-items-center add-info justify-content-end">
                                    <button data-toggle="modal" class="btn btn-primary btn-sm open-item-modal-form"
                                        href="{!! route('approved.purchase.orders.payment.sheet.create', $purchaseOrder->id) !!}">
                                        <i class="bi-plus"></i> Add New Payment Sheet
                                    </button>
                                </div>
                            </div>
                            <div class="container-fluid-s">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table" id="sheetsTable">
                                                <thead class="thead-light">
                                                    <tr>
                                                        <th>{{ __('label.supplier-name') }}</th>
                                                        <th>{{ __('label.vat-pan-no') }}</th>
                                                        <th>{{ __('label.reference-no') }}</th>
                                                        <th>{{ __('label.amount') }}</th>
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
                            </div>
                        </div>
                        @include('PurchaseOrder::Partials.item-summary')

                    </div>
                </div>
            </section>
        </div>
    </div>
@stop
