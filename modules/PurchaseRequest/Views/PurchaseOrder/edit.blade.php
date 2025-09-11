@extends('layouts.container')

@section('title', 'Edit Purchase Order')

@section('page_js')
    <script type="text/javascript">
        document.addEventListener('DOMContentLoaded', function (e) {
            $('#navbarVerticalMenu').find('#purchase-orders-menu').addClass('active');

            const form = document.getElementById('purchaseOrderEditForm');
            const fv = FormValidation.formValidation(form, {
                fields: {
                    district_id: {
                        validators: {
                            notEmpty: {
                                message: 'District is required',
                            },
                        },
                    },
                    supplier_id: {
                        validators: {
                            notEmpty: {
                                message: 'Supplier is required',
                            },
                        },
                    },
                    reviewer_id: {
                        validators: {
                            notEmpty: {
                                message: 'The reviewer is required',
                            },
                        },
                    },
                    approver_id: {
                        validators: {
                            notEmpty: {
                                message: 'The approver is required',
                            },
                        },
                    },
                    delivery_date: {
                        validators: {
                            notEmpty: {
                                message: 'The delivery date is required',
                            },
                            date: {
                                format: 'YYYY-MM-DD',
                                message: 'Delivery date must be a valid date.'
                            }
                        },
                    },
                    currency_id: {
                        validators: {
                            notEmpty: {
                                message: 'Currency is required.'
                            }
                        }
                    }
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

            $('[name="delivery_date"]').datepicker({
                language: 'en-GB',
                autoHide: true,
                format: 'yyyy-mm-dd',
                startDate: '{{ $purchaseOrder->delivery_date ? $purchaseOrder->delivery_date->format("Y-m-d") : date("Y-m-d") }}'
            }).on('change', function (e) {
                fv.revalidateField('delivery_date');
            });

            $(form).on('change', '[name="district_id"]', function(e){
                fv.revalidateField('district_id');
            }).on('change', '[name="supplier_id"]', function(e){
                fv.revalidateField('supplier_id');
            }).on('change', '[name="approver_id"]', function(e){
                fv.revalidateField('approver_id');
            });

            $(document).on('click', '.open-pr-modal-form', function(e) {
                e.preventDefault();
                $('#purchaseRequestItemModal').find('.modal-content').html('');
                $('#purchaseRequestItemModal').modal('show').find('.modal-content').load($(this).attr(
                        'href'),
                    function() {
                        const form = document.getElementById('purchaseOrderCombineForm');
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
                                    purchase_request_id: {
                                        validators: {
                                            notEmpty: {
                                                message: 'Purchase Request is required.',
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
                                form.submit();
                            });
                    });
            });
        });

        var oTable = $('#purchaseOrderItemTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('purchase.orders.items.index', $purchaseOrder->id) }}",
            bFilter: false,
            bPaginate: false,
            bInfo: false,
            columns: [
                {data: 'item', name: 'item'},
                {data: 'unit', name: 'unit'},
                // {data: 'delivery_date', name: 'delivery_date'},
                {data: 'quantity', name: 'quantity'},
                {data: 'unit_price', name: 'unit_price'},
                {data: 'total_price', name: 'total_price'},
                {data: 'vat_amount', name: 'vat_amount'},
                {data: 'total_amount', name: 'total_amount'},
                {data: 'activity', name: 'activity'},
                {data: 'account', name: 'account'},
                {data: 'donor', name: 'donor'},
                {data: 'specification', name: 'specification'},
                {data: 'action', name: 'action', orderable: false, searchable: false},
            ]
        });

        $('#purchaseOrderItemTable').on('click', '.delete-record', function (e) {
            e.preventDefault();
            $object = $(this);
            var $url = $object.attr('data-href');
            var successCallback = function (response) {
                toastr.success(response.message, 'Success', {timeOut: 5000});
                $('#purchaseOrderItemTable').find('.order_sub_total').text(response.purchaseOrder.sub_total);
                $('#purchaseOrderItemTable').find('.order_vat_amount').text(response.purchaseOrder.vat_amount);
                $('#purchaseOrderItemTable').find('.order_total_amount').text(response.purchaseOrder.total_amount);
                oTable.ajax.reload();
            }
            ajaxDeleteSweetAlert($url, successCallback);
        });

        $(document).on('click', '.open-item-modal-form', function(e) {
            e.preventDefault();
            $('#openModal').find('.modal-content').html('');
            $('#openModal').modal('show').find('.modal-content').load($(this).attr('href'), function () {
            const form = document.getElementById('purchaseOrderItemForm');
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
                    // delivery_date: {
                    //     validators: {
                    //         notEmpty: {
                    //             message: 'Delivery date is required',
                    //         },
                    //         date: {
                    //             format: 'YYYY-MM-DD',
                    //             message: 'The value is not a valid date',
                    //         },
                    //     },
                    // },
                    quantity: {
                        validators: {
                            notEmpty: {
                                message: 'Quantity is required',
                            },
                            greaterThan: {
                                message: 'The value must be greater than or equal to 0.01',
                                min: 0.01,
                            },
                        },
                    },
                    unit_price: {
                        validators: {
                            notEmpty: {
                                message: 'Unit price is required',
                            },
                            greaterThan: {
                                message: 'The value must be greater than or equal to 0.01',
                                min: 0.01,
                            },
                        },
                    },
                    specification: {
                        validators: {
                            notEmpty: {
                                message: 'Specification is required',
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
                    $('#purchaseOrderItemTable').find('.order_sub_total').text(response.purchaseOrder.sub_total);
                    $('#purchaseOrderItemTable').find('.order_vat_amount').text(response.purchaseOrder.vat_amount);
                    $('#purchaseOrderItemTable').find('.order_total_amount').text(response.purchaseOrder.total_amount);
                    oTable.ajax.reload();
                }
                ajaxSubmit($url, 'POST', data, successCallback);
            });

            $('[name="delivery_date"]').datepicker({
                language: 'en-GB',
                autoHide: true,
                format: 'yyyy-mm-dd',
                startDate: '{{ date('Y-m-d', strtotime(date('Y-m-d'). ' + 1 days')) }}',
                zIndex: 2048,
            }).on('change', function (e) {
                fv.revalidateField('delivery_date');
            });

            $(form).change('[name="unit_price"]', function (e){
                calculateTotalPrice(this);
            }).change('[name="quantity"]', function (e){
                calculateTotalPrice(this);
            });

            function calculateTotalPrice($element){
                quantity = $($element).closest('form').find('[name="quantity"]').val();
                unitPrice = $($element).closest('form').find('[name="unit_price"]').val();
                $($element).closest('form').find('.total_price').val(quantity*unitPrice);
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
                                    <a href="{{ route('purchase.orders.index') }}" class="text-decoration-none">Purchase
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
                    <div class="col-lg-12">
                        <div class="card">
                            <form action="{{ route('purchase.orders.update', $purchaseOrder->id) }}"
                                  id="purchaseOrderEditForm" method="post"
                                  enctype="multipart/form-data" autocomplete="off">
                                <div class="card-body">
                                    <div class="row mb-2">
                                        <div class="col-lg-3">
                                            <div class="d-flex align-items-start h-100">
                                                <label for="validationpurchasetype" class="form-label required-label">District </label>
                                            </div>
                                        </div>
                                        <div class="col-lg-9">
                                            @php $selectedDistrictId = $purchaseOrder->districts()->pluck('id')->toArray(); @endphp
                                            <select class="select2 form-control" name="district_ids[]" multiple>
                                                <option value="">Select a District</option>
                                                @foreach($districts as $district)
                                                    <option value="{{ $district->id }}"
                                                        {{ in_array($district->id, $selectedDistrictId) ? 'selected="selected"':"" }}>
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
                                                <label for="validationSupplier"
                                                       class="form-label required-label">Supplier </label>
                                            </div>
                                        </div>
                                        <div class="col-lg-9">
                                            @php $selectedSupplierId = $purchaseOrder->supplier_id ?: old('supplier_id'); @endphp
                                            <select class="select2 form-control" name="supplier_id">
                                                <option value="">Select a Supplier</option>
                                                @foreach($suppliers as $supplier)
                                                    <option value="{{ $supplier->id }}"
                                                        {{ $supplier->id == $selectedSupplierId ? 'selected="selected"':"" }}>
                                                        {{ $supplier->getSupplierNameandVAT() }}
                                                    </option>
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
                                                <label for="delivery_date" class="form-label required-label">Delivery Date</label>
                                            </div>
                                        </div>
                                        <div class="col-lg-9">
                                            <div class="d-flex align-items-center">
                                                <input type="text" class="form-control @if($errors->has('delivery_date')) is-invalid @endif" name="delivery_date" id="delivery_date"
                                                value="{{$purchaseOrder->delivery_date?->format('Y-m-d')}}">
                                                @if ($errors->has('delivery_date'))
                                                    <div class="fv-plugins-message-container invalid-feedback">
                                                        <div data-field="delivery_date">
                                                            {{$errors->first('delivery_date')}}
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mb-2">
                                        <div class="col-lg-3">
                                            <div class="d-flex align-items-start h-100">
                                                <label for="validationRemarks" class="form-label">Delivery Location</label>
                                            </div>
                                        </div>
                                        <div class="col-lg-9">
                                            <input type="text"
                                                      class="form-control @if($errors->has('delivery_location')) is-invalid @endif"
                                                      name="delivery_location" value=" {{old('delivery_location') ?: $purchaseOrder->delivery_location }}" />
                                            @if($errors->has('delivery_location'))
                                                <div class="fv-plugins-message-container invalid-feedback">
                                                    <div
                                                        data-field="delivery_location">{!! $errors->first('delivery_location') !!}</div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="row mb-2">
                                        <div class="col-lg-3">
                                            <div class="d-flex align-items-start h-100">
                                                <label for="validationRemarks" class="form-label">Delivery Instructions</label>
                                            </div>
                                        </div>
                                        <div class="col-lg-9">
                                            <textarea type="text"
                                                      class="form-control @if($errors->has('delivery_instructions')) is-invalid @endif"
                                                      name="delivery_instructions">{{ old('delivery_instructions')?: $purchaseOrder->delivery_instructions }}</textarea>
                                            @if($errors->has('delivery_instructions'))
                                                <div class="fv-plugins-message-container invalid-feedback">
                                                    <div
                                                        data-field="delivery_instructions">{!! $errors->first('delivery_instructions') !!}</div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="row mb-0">
                                        <div class="col-lg-3">
                                            <div class="d-flex align-items-start h-100">
                                                <label for="currency_id" class="form-label required-label">Currency</label>
                                            </div>
                                        </div>
                                        <div class="col-lg-9">
                                            <div class="d-flex align-items-center">
                                                <select class="form-control @if($errors->has('currency_id')) is-invalid @endif" name="currency_id" id="currency_id">
                                                    @foreach ($currencies as $currency)
                                                        <option value="{{$currency->id}}" {{$purchaseOrder->currency_id == $currency->id ? 'selected' : ''}}>{{$currency->getTitle()}}</option>
                                                    @endforeach
                                                </select>
                                                @if ($errors->has('currency_id'))
                                                    <div class="fv-plugins-message-container invalid-feedback">
                                                        <div data-field="currency_id">
                                                            {{$errors->first('currency_id')}}
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mb-2">
                                        <div class="col-lg-3">
                                            <div class="d-flex align-items-start h-100">
                                                <label for="validationRemarks" class="form-label required-label">Send To </label>
                                            </div>
                                        </div>
                                        <div class="col-lg-3">
                                            @php $selectedReviewerId = old('reviewer_id') ?: $purchaseOrder->reviewer_id; @endphp
                                            <select name="reviewer_id" class="select2 form-control
                                                @if($errors->has('reviewer_id')) is-invalid @endif" data-width="100%">
                                                <option value="">Select an Reviewer</option>
                                                @foreach($reviewers as $reviewer)
                                                    <option
                                                        value="{{ $reviewer->id }}" {{$reviewer->id == $selectedReviewerId ? "selected":""}}>{{ $reviewer->full_name }}</option>
                                                @endforeach
                                            </select>
                                            @if($errors->has('reviewer_id'))
                                                <div class="fv-plugins-message-container invalid-feedback">
                                                    <div data-field="reviewer_id">
                                                        {!! $errors->first('reviewer_id') !!}
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="col-lg-3">
                                            <div class="d-flex align-items-start h-100">
                                                <label for="validationRemarks" class="form-label required-label">Approver </label>
                                            </div>
                                        </div>
                                        <div class="col-lg-3">
                                            @php $selectedApproverId = old('approver_id') ?: $purchaseOrder->approver_id; @endphp
                                            <select name="approver_id" class="select2 form-control
                                                @if($errors->has('approver_id')) is-invalid @endif" data-width="100%">
                                                <option value="">Select Approver</option>
                                                @foreach($approvers as $approver)
                                                    <option
                                                        value="{{ $approver->id }}" {{$approver->id == $selectedApproverId ? "selected":""}}>{{ $approver->full_name }}</option>
                                                @endforeach
                                            </select>
                                            @if($errors->has('approver_id'))
                                                <div class="fv-plugins-message-container invalid-feedback">
                                                    <div data-field="approver_id">
                                                        {!! $errors->first('approver_id') !!}
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="card-footer border-0 justify-content-end d-flex gap-2">
                                        <button type="submit" name="btn" value="save" class="btn btn-primary btn-sm">Update
                                        </button>
                                    </div>

                                    {!! csrf_field() !!}
                                    {!! method_field('PUT') !!}
                                </div>
                                <div class="card-body">
                                    <div class="card">
                                        <div class="card-header fw-bold">

                                             <div class="d-flex align-items-center add-info justify-content-between">
                                    <span>  Items</span>
                                        <button class="btn btn-primary btn-sm text-capitalize open-pr-modal-form" href="{!! route('approved.purchase.requests.orders.createItem', $purchaseOrder->id) !!}">
                                            <i class="bi-plus"></i> Add Items
                                        </button>
                                </div>
                                        </div>
                                        <div class="container-fluid-s">
                                            <div class="card">
                                                <div class="card-body">
                                                    <div class="table-responsive">
                                                    <table class="table" id="purchaseOrderItemTable">
                                                        <thead class="thead-light">
                                                        <tr>
                                                            <th scope="col">{{ __('label.item') }}</th>
                                                            <th scope="col">{{ __('label.unit') }}</th>
                                                            {{-- <th scope="col">{{ __('label.delivery-date') }}</th> --}}
                                                            <th scope="col">{{ __('label.quantity') }}</th>
                                                            <th scope="col">{{ __('label.unit-price') }}</th>
                                                            <th scope="col">{{ __('label.amount') }}</th>
                                                            <th scope="col">{{ __('label.vat-amount') }}</th>
                                                            <th scope="col">{{ __('label.total-amount') }}</th>
                                                            <th scope="col">{{ __('label.activity') }}</th>
                                                            <th scope="col">{{ __('label.account') }}</th>
                                                            <th scope="col">{{ __('label.donor') }}</th>
                                                            <th scope="col">{{ __('label.specification') }}</th>
                                                            <th style="width: 150px">{{ __('label.action') }}</th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        </tbody>
                                                        <tr>
                                                            <td colspan="4">Total</td>
                                                            <td class="order_sub_total">{!! $purchaseOrder->sub_total !!}</td>
                                                            <td class="order_vat_amount">{!! $purchaseOrder->vat_amount !!}</td>
                                                            <td class="order_total_amount">{!! $purchaseOrder->total_amount !!}</td>
                                                            <td colspan="4"></td>
                                                        </tr>
                                                    </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                                <div class="card-footer border-0 justify-content-end d-flex gap-2">
                                    <button type="submit" name="btn" value="submit" class="btn btn-success btn-sm">
                                        Submit
                                    </button>
                                    <a href="{!! route('purchase.orders.index') !!}"
                                       class="btn btn-danger btn-sm">Cancel</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>

    <div class="modal fade" id="purchaseRequestItemModal" data-bs-backdrop="static" data-bs-keyboard="false"
        aria-labelledby="purchaseRequestItemModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
            </div>
        </div>
    </div>

@stop
