@extends('layouts.container')

@section('title', 'Add New Inventory')

@section('page_js')
    <script type="text/javascript">
        document.addEventListener('DOMContentLoaded', function (e) {
            $('#navbarVerticalMenu').find('#inventories-menu').addClass('active');
            const form = document.getElementById('inventoryForm');
            const fv = FormValidation.formValidation(form, {
                fields: {
                    supplier_id: {
                        validators: {
                            notEmpty: {
                                message: 'Supplier is required',
                            },
                        },
                    },
                    item_id: {
                        validators: {
                            notEmpty: {
                                message: 'Item is required',
                            },
                        },
                    },
                    unit_id: {
                        validators: {
                            notEmpty: {
                                message: 'Unit is required',
                            },
                        },
                    },
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
                    purchase_date: {
                        validators: {
                            notEmpty: {
                                message: 'Purchase date is required',
                            },
                            date: {
                                format: 'YYYY-MM-DD',
                                message: 'The value is not a valid date',
                            },
                        },
                    },

                    distribution_type_id: {
                        validators: {
                            notEmpty: {
                                message: 'Type is required',
                            },
                        },
                    },
                    expiry_date: {
                        validators: {
                            date: {
                                format: 'YYYY-MM-DD',
                                message: 'The value is not a valid date',
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

            $(form).on('change', '[name="unit_price"]', function (e) {
                calculateTotalPrice(this);
                calculateTotalAmount(this);
            }).on('change', '[name="quantity"]', function (e) {
                calculateTotalPrice(this);
                calculateTotalAmount(this);
            }).on('change', '[name="vat_applicable"]', function (e) {
                calculateTotalAmount(this);
            }).on('change', '[name="item_id"]', function (e) {
                getUnit($(this));
            }).on('change', '[name="activity_code_id"]', function (e) {
                getAccountCode($(this));
            }).on('change', '[name="unit_id"]', function (e) {
                fv.revalidateField('unit_id');
            }).on('change', '[name="supplier_id"]', function (e) {
                fv.revalidateField('supplier_id');
            }).on('change', '[name="distribution_type_id"]', function (e) {
                fv.revalidateField('distribution_type_id');
            });

            $('[name="purchase_date"]').datepicker({
                language: 'en-GB',
                autoHide: true,
                format: 'yyyy-mm-dd',
                endDate: '{{ date('Y-m-d') }}',
            }).on('change', function (e) {
                fv.revalidateField('purchase_date');
            });

            $('[name="expiry_date"]').datepicker({
                language: 'en-GB',
                autoHide: true,
                format: 'yyyy-mm-dd',
                startDate: '{{ date('Y-m-d') }}',
            }).on('change', function (e) {
                fv.revalidateField('expiry_date');
            });

            itemSelected = $(form).find('[name="item_id"]').val();
            if (itemSelected) {
                getUnit($(form).find('[name="item_id"]'));
            }

            function getUnit($element) {
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
                fv.revalidateField('item_id');
            }

            function getAccountCode($element) {
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
            }

            function calculateTotalPrice($element) {
                quantity = $($element).closest('form').find('[name="quantity"]').val();
                unitPrice = $($element).closest('form').find('[name="unit_price"]').val();
                $($element).closest('form').find('.total_price').val(quantity * unitPrice);
            }

            function calculateTotalAmount($element) {
                    quantity = parseFloat($($element).closest('form').find('[name="quantity"]').val());
                    unitPrice = parseFloat($($element).closest('form').find('[name="unit_price"]').val());
                    unitPrice = isNaN(unitPrice) ? 0 : unitPrice;
                    quantity = isNaN(quantity) ? 0 : quantity;
                    totalPrice = unitPrice * quantity;
                    vatFlag = $($element).closest('form').find('[name="vat_applicable"]').prop('checked');
                    vatAmount = vatFlag ? parseFloat(totalPrice * vatPercentage / 100) : 0;
                    $($element).closest('form').find('[name="total_price"]').val(totalPrice);
                    $($element).closest('form').find('[name="vat_amount"]').val(vatAmount);
                    $($element).closest('form').find('[name="total_amount"]').val(vatAmount + totalPrice);
                }
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
                                    <a href="{{ route('inventories.index') }}"
                                       class="text-decoration-none">Inventories</a>
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
                            {{--                            <div class="card-header fw-bold">--}}
                            {{--                                <h3 class="m-0 fs-6">Add Purchase Request</h3>--}}
                            {{--                            </div>--}}
                            <form action="{{ route('inventories.store') }}" id="inventoryForm" method="post"
                                  enctype="multipart/form-data" autocomplete="off">
                                <div class="card-body">
                                    <div class="row mb-2">
                                        <div class="col-lg-3">
                                            <div class="d-flex align-items-start h-100">
                                                <label for="validationpurchasetype"
                                                       class="form-label required-label">Supplier</label>
                                            </div>
                                        </div>
                                        <div class="col-lg-9">
                                            <select name="supplier_id" class="select2 form-control" data-width="100%">
                                                <option value="">Select Supplier</option>
                                                @foreach($suppliers as $supplier)
                                                    <option value="{{ $supplier->id }}"
                                                            @if($supplier->id == old('supplier_id')) selected="selected" @endif>
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
                                                <label for="validationdd" class="form-label required-label">Purchase
                                                    Date</label>
                                            </div>
                                        </div>
                                        <div class="col-lg-9">
                                            <input
                                                class="form-control @if($errors->has('purchase_date')) is-invalid @endif"
                                                type="text" readonly name="purchase_date"
                                                value="{{ old('purchase_date') }}"/>
                                            @if($errors->has('purchase_date'))
                                                <div class="fv-plugins-message-container invalid-feedback">
                                                    <div data-field="purchase_date">
                                                        {!! $errors->first('purchase_date') !!}
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="row mb-2">
                                        <div class="col-lg-3">
                                            <div class="d-flex align-items-start h-100">
                                                <label for="validationRemarks" class="form-label required-label">Item</label>
                                            </div>
                                        </div>
                                        <div class="col-lg-9">
                                            <select name="item_id" class="select2 form-control" data-width="100%">
                                                <option value="">Select Item</option>
                                                @foreach($items as $item)
                                                    <option value="{{ $item->id }}"
                                                            @if($item->id == old('item_id')) selected="selected" @endif>
                                                        {{ $item->getItemName() }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @if($errors->has('item_id'))
                                                <div class="fv-plugins-message-container invalid-feedback">
                                                    <div data-field="item_id">
                                                        {!! $errors->first('item_id') !!}
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="row mb-2">
                                        <div class="col-lg-3">
                                            <div class="d-flex align-items-start h-100">
                                                <label for="" class="form-label required-label">Unit</label>
                                            </div>
                                        </div>
                                        <div class="col-lg-9">
                                            <select class="form-control select2" data-width="100%" name="unit_id">
                                                <option value="">Select Unit</option>
                                            </select>
                                            @if($errors->has('unit_id'))
                                                <div class="fv-plugins-message-container invalid-feedback">
                                                    <div data-field="unit_id">
                                                        {!! $errors->first('unit_id') !!}
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="row mb-2">
                                        <div class="col-lg-3">
                                            <div class="d-flex align-items-start h-100">
                                                <label for="" class="form-label required-label">Quantity</label>
                                            </div>
                                        </div>
                                        <div class="col-lg-9">
                                            <input type="number" class="form-control" name="quantity"
                                                   value="{{ old('quantity') }}" placeholder="Quantity">
                                            @if($errors->has('quantity'))
                                                <div class="fv-plugins-message-container invalid-feedback">
                                                    <div data-field="quantity">
                                                        {!! $errors->first('quantity') !!}
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="row mb-2">
                                        <div class="col-lg-3">
                                            <div class="d-flex align-items-start h-100">
                                                <label for="" class="form-label required-label">Unit Price</label>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <input type="number" class="form-control" name="unit_price"
                                                   value="{{ old('unit_price') }}" placeholder="Unit Price">
                                            @if($errors->has('unit_price'))
                                                <div class="fv-plugins-message-container invalid-feedback">
                                                    <div data-field="unit_price">
                                                        {!! $errors->first('unit_price') !!}
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="col-lg-3">
                                            <input type="number" class="form-control total_price"
                                                   value="{{ old('unit_price')*old('quantity') }}" readonly>
                                        </div>
                                    </div>

                                    <div class="row mb-2">
                                        <div class="col-lg-3">
                                            <div class="d-flex align-items-start h-100">
                                                <label for="validationdd" class="m-0">{{ __('label.vat-applicable') }}</label>
                                            </div>
                                        </div>
                                        <div class="col-lg-9">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" role="switch" id="flexSwitchCheckChecked"
                                                       name="vat_applicable" @if(old('vat_applicable')) checked @endif>
                                                <label class="form-check-label" for="flexSwitchCheckChecked"></label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mb-2">
                                        <div class="col-lg-6">
                                            <div class="row">
                                                <div class="col-lg-6">
                                                    <div class="d-flex align-items-start h-100">
                                                        <label for="" class="m-0">{{ __('label.vat-amount') }}</label>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <input readonly class="form-control" name="vat_amount" placeholder="{{ __('label.vat-amount') }}" value="{{ old('vat_amount') }}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="row">
                                                <div class="col-lg-6">
                                                    <div class="d-flex align-items-center justify-content-end h-100">
                                                        <label for="" class="m-0">{{ __('label.total-amount') }}</label>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <input readonly class="form-control" name="total_amount"
                                                    value="{{old('vat_applicable') ? ((old('unit_price') * old('quantity'))) + (((old('unit_price') * old('quantity')) * (config('constant.VAT_PERCENTAGE') / 100))) : old('unit_price') * old('quantity') }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- <div class="row mb-2">
                                    </div> --}}

                                    <div class="row mb-2">
                                        <div class="col-lg-3">
                                            <div class="d-flex align-items-start h-100">
                                                <label for="validationdd" class="form-label required-label">Expiry
                                                    Date (If any)</label>
                                            </div>
                                        </div>
                                        <div class="col-lg-9">
                                            <input
                                                class="form-control @if($errors->has('expiry_date')) is-invalid @endif"
                                                type="text" readonly name="expiry_date"
                                                value="{{ old('expiry_date') }}"/>
                                            @if($errors->has('expiry_date'))
                                                <div class="fv-plugins-message-container invalid-feedback">
                                                    <div data-field="expiry_date">
                                                        {!! $errors->first('expiry_date') !!}
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="row mb-2">
                                        <div class="col-lg-3">
                                            <div class="d-flex align-items-start h-100">
                                                <label for="" class="m-0">Activity Code</label>
                                            </div>
                                        </div>
                                        <div class="col-lg-9">
                                            <select class="form-control select2" data-width="100%"
                                                    name="activity_code_id">
                                                <option value="">Select Activity Code</option>
                                                @foreach($activityCodes as $activityCode)
                                                    <option value="{!! $activityCode->id !!}"
                                                            @if($activityCode->id == old('activity_code_id')) selected="selected" @endif>{{ $activityCode->getActivityCodeWithDescription() }}</option>
                                                @endforeach
                                            </select>
                                            @if($errors->has('activity_code_id'))
                                                <div class="fv-plugins-message-container invalid-feedback">
                                                    <div data-field="activity_code_id">
                                                        {!! $errors->first('activity_code_id') !!}
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="row mb-2">
                                        <div class="col-lg-3">
                                            <div class="d-flex align-items-start h-100">
                                                <label for="" class="m-0">Account Code</label>
                                            </div>
                                        </div>
                                        <div class="col-lg-9">
                                            <select class="form-control select2" data-width="100%"
                                                    name="account_code_id">
                                                <option value="">Select Account Code</option>
                                            </select>
                                            @if($errors->has('account_code_id'))
                                                <div class="fv-plugins-message-container invalid-feedback">
                                                    <div data-field="account_code_id">
                                                        {!! $errors->first('account_code_id') !!}
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="row mb-2">
                                        <div class="col-lg-3">
                                            <div class="d-flex align-items-start h-100">
                                                <label for="" class="m-0">Donor Code</label>
                                            </div>
                                        </div>
                                        <div class="col-lg-9">
                                            <select class="form-control select2" data-width="100%" name="donor_code_id">
                                                <option value="">Select Donor Code</option>
                                                @foreach($donorCodes as $donorCode)
                                                    <option value="{!! $donorCode->id !!}"
                                                            @if($donorCode->id == old('donor_code_id')) selected="selected" @endif>{{ $donorCode->getDonorCodeWithDescription() }}</option>
                                                @endforeach
                                            </select>
                                            @if($errors->has('donor_code_id'))
                                                <div class="fv-plugins-message-container invalid-feedback">
                                                    <div data-field="donor_code_id">
                                                        {!! $errors->first('donor_code_id') !!}
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="row mb-2">
                                        <div class="col-lg-3">
                                            <div class="d-flex align-items-start h-100">
                                                <label for="" class="form-label required-label">Type</label>
                                            </div>
                                        </div>
                                        <div class="col-lg-9">
                                            <select class="form-control select2" data-width="100%" name="distribution_type_id">
                                                <option value="">Select Type</option>
                                                @foreach($distributionTypes as $distributionType)
                                                    <option value="{!! $distributionType->id !!}"
                                                            @if($distributionType->id == old('distribution_type_id')) selected="selected" @endif>{{ $distributionType->title }}</option>
                                                @endforeach
                                            </select>
                                            @if($errors->has('distribution_type_id'))
                                                <div class="fv-plugins-message-container invalid-feedback">
                                                    <div data-field="distribution_type_id">
                                                        {!! $errors->first('distribution_type_id') !!}
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="row mb-2">
                                        <div class="col-lg-3">
                                            <div class="d-flex align-items-start h-100">
                                                <label for="" class="form-label required-label">Execution Type</label>
                                            </div>
                                        </div>
                                        <div class="col-lg-9">
                                            <select class="form-control" name="execution_id">
                                                <option value="">Select Type</option>
                                                @foreach($executionTypes as $executionType)
                                                    <option value="{{ $executionType->id }}" {{ $executionType->id == old('execution_id') ? 'selected' : '' }}>{{ $executionType->title }}</option>
                                                @endforeach
                                            </select>
                                            @if($errors->has('execution_id'))
                                                <div class="fv-plugins-message-container invalid-feedback">
                                                    <div data-field="execution_id">
                                                        {!! $errors->first('execution_id') !!}
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="row mb-2">
                                        <div class="col-lg-3">
                                            <div class="d-flex align-items-start h-100">
                                                <label for="" class="m-0">Description</label>
                                            </div>
                                        </div>
                                        <div class="col-lg-9">
                                            <textarea class="form-control" name="specification" id="specification" rows="2">{{ old('specification') }}</textarea>
                                            @if($errors->has('specification'))
                                                <div class="fv-plugins-message-container invalid-feedback">
                                                    <div data-field="specification">
                                                        {!! $errors->first('specification') !!}
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    {!! csrf_field() !!}
                                </div>
                                <div class="card-footer border-0 justify-content-end d-flex gap-2">
                                    <button type="submit" name="btn" value="save" class="btn btn-primary btn-sm">Save
                                    </button>
                                    <a href="{!! route('inventories.index') !!}"
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
