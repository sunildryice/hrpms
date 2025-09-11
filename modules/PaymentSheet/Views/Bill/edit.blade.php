@extends('layouts.container')

@section('title', 'Edit Payment Bill')

@section('page_js')
    <script type="text/javascript">
        document.addEventListener('DOMContentLoaded', function (e) {
            $('#navbarVerticalMenu').find('#payment-bills-menu').addClass('active');
            const form = document.getElementById('paymentBillEditForm');
            const fv = FormValidation.formValidation(form, {
                fields: {
                    category_id: {
                        validators: {
                            notEmpty: {
                                message: 'Category is required',
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
                    bill_date: {
                        validators: {
                            notEmpty: {
                                message: 'Bill date is required',
                            },
                            date: {
                                format: 'YYYY-MM-DD',
                                message: 'The value is not a valid date',
                            },
                        },
                    },
                    bill_amount: {
                        validators: {
                            notEmpty: {
                                message: 'Bill amount is required',
                            },
                        },
                    },
                    bill_number: {
                        validators: {
                            notEmpty: {
                                message: 'Bill number is required',
                            },
                        },
                    },
                    attachment: {
                        validators: {
                            file: {
                                extension: 'jpeg,jpg,png,pdf',
                                type: 'image/jpeg,image/png,application/pdf',
                                maxSize: 2097152, // 2048 * 1024
                                message: 'The selected file is not valid image or pdf or must not be greater than 2 MB.',
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

            $(form).on('change', '[name="supplier_id"]', function (e){
                fv.revalidateField('supplier_id');
            }).on('change','[name="category_id"]', function (e){
                fv.revalidateField('category_id');
            }).on('change','[name="vat_applicable"]', function (e){
                calculateTotalPrice(this);
            }).on('change','[name="bill_amount"]', function (e){
                calculateTotalPrice(this);
            });

            function calculateTotalPrice($element){
                billAmount = parseFloat($($element).closest('form').find('[name="bill_amount"]').val());
                vatFlag = $($element).closest('form').find('[name="vat_applicable"]').prop('checked');
                vatAmount = vatFlag ? parseFloat(billAmount * vatPercentage/100) : 0;
                $($element).closest('form').find('[name="vat_amount"]').val(vatAmount);
                $($element).closest('form').find('[name="total_amount"]').val(vatAmount+billAmount);
            }
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
                                    <a href="{{ route('payment.bills.index') }}" class="text-decoration-none text-dark">Payment
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
                <div class="card">
                    <form action="{{ route('payment.bills.update', $paymentBill->id) }}"
                          id="paymentBillEditForm" method="post"
                          enctype="multipart/form-data" autocomplete="off">
                        <div class="card-body">
                            <div class="row mb-2">
                                <div class="col-lg-2">
                                    <div class="d-flex align-items-start h-100">
                                        <label for="validationdd" class="form-label required-label">{{ __('label.supplier') }}</label>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <select
                                        class="select2 form-control @if($errors->has('supplier_id')) is-invalid @endif"
                                        name="supplier_id" >
                                        <option value="">Select Supplier</option>
                                        @foreach($suppliers as $supplier)
                                            <option value="{{ $supplier->id }}"
                                            @if($supplier->id == $paymentBill->supplier_id) selected @endif>{{ $supplier->getSupplierName() }}</option>
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
                                <div class="col-lg-2">
                                    <div class="d-flex align-items-start h-100">
                                        <label for="validationdd" class="m-0">{{ __('label.bill-category') }}</label>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <select
                                        class="select2 form-control @if($errors->has('category_id')) is-invalid @endif"
                                        name="category_id" >
                                        <option value="">Select Category</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}"
                                                    @if($category->id == $paymentBill->category_id) selected @endif>{{ $category->title }}</option>
                                        @endforeach
                                    </select>
                                    @if($errors->has('category_id'))
                                        <div class="fv-plugins-message-container invalid-feedback">
                                            <div data-field="category_id">
                                                {!! $errors->first('category_id') !!}
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="row mb-2">
                                <div class="col-lg-2">
                                    <div class="d-flex align-items-start h-100">
                                        <label for="validationdd" class="form-label required-label">{{ __('label.bill-date') }}</label>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <input class="form-control @if($errors->has('bill_date')) is-invalid @endif"
                                           name="bill_date" value="{{ old('bill_date') ?: ($paymentBill->bill_date ? $paymentBill->bill_date->format('Y-m-d') : "") }}" readonly/>
                                    @if($errors->has('bill_date'))
                                        <div class="fv-plugins-message-container invalid-feedback">
                                            <div data-field="bill_date">
                                                {!! $errors->first('bill_date') !!}
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                <div class="col-lg-2">
                                    <div class="d-flex align-items-start h-100">
                                        <label for="validationdd" class="form-label required-label">{{ __('label.bill-no') }}</label>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <input class="form-control @if($errors->has('bill_number')) is-invalid @endif"
                                           name="bill_number" value="{{ old('bill_number') ?: $paymentBill->bill_number }}"/>
                                    @if($errors->has('bill_number'))
                                        <div class="fv-plugins-message-container invalid-feedback">
                                            <div data-field="bill_number">
                                                {!! $errors->first('bill_number') !!}
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="row mb-2">
                                <div class="col-lg-2">
                                    <div class="d-flex align-items-start h-100">
                                        <label for="validationdd" class="m-0">{{ __('label.bill-amount') }}</label>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <input class="form-control @if($errors->has('bill_amount')) is-invalid @endif"
                                           name="bill_amount" value="{{ old('bill_amount') ?: $paymentBill->bill_amount }}" type="number"/>
                                    @if($errors->has('bill_amount'))
                                        <div class="fv-plugins-message-container invalid-feedback">
                                            <div data-field="bill_amount">
                                                {!! $errors->first('bill_amount') !!}
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                <div class="col-lg-2">
                                    <div class="d-flex align-items-start h-100">
                                        <label for="validationdd" class="m-0">{{ __('label.vat-applicable') }}</label>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" role="switch" id="flexSwitchCheckChecked"
                                               name="vat_applicable" @if(old('vat_applicable') || $paymentBill->vat_flag) checked @endif>
                                        <label class="form-check-label" for="flexSwitchCheckChecked"></label>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-2">
                                <div class="col-lg-2">
                                    <div class="d-flex align-items-start h-100">
                                        <label for="validationdd" class="m-0">{{ __('label.vat-amount') }}</label>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <input type="number" class="form-control" readonly
                                           name="vat_amount" value="{{ old('vat_amount') ?: $paymentBill->vat_amount }}"/>
                                </div>
                                <div class="col-lg-2">
                                    <div class="d-flex align-items-start h-100">
                                        <label for="validationdd" class="m-0">{{ __('label.total-amount') }}</label>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <input type="number" class="form-control" readonly
                                           name="total_amount" value="{{ old('total_amount') ?: $paymentBill->total_amount }}"/>
                                </div>
                            </div>

                            <div class="row mb-2">
                                <div class="col-lg-2">
                                    <div class="d-flex align-items-start h-100">
                                        <label for="validationdd" class="m-0">{{ __('label.remarks') }}</label>
                                    </div>
                                </div>
                                <div class="col-lg-10">
                                    <textarea class="form-control @if($errors->has('remarks')) is-invalid @endif"
                                              name="remarks">{{ old('remarks') ?: $paymentBill->remarks }}</textarea>
                                    @if($errors->has('remarks'))
                                        <div class="fv-plugins-message-container invalid-feedback">
                                            <div data-field="remarks">
                                                {!! $errors->first('remarks') !!}
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-lg-2">
                                    <div class="d-flex align-items-start h-100">
                                        <label for="validationcitizenship" class="m-0">Attachment</label>
                                    </div>
                                </div>
                                <div class="col-lg-10">
                                    <input type="file" class="form-control @if($errors->has('attachment')) is-invalid @endif" name="attachment"/>
                                    @if(file_exists('storage/'.$paymentBill->attachment) && $paymentBill->attachment != '')
                                    <div class="media">
                                        <a href="{{ asset('storage/'.$paymentBill->attachment) }}" target="_blank" name='attachment_exist' class="fs-5"
                                            title="View Attachment">
                                            <i class="bi bi-file-earmark-medical"></i>
                                        </a>
                                    </div>
                                    @endif
                                    <small>Supported file types jpeg/jpg/png/pdf and file size of upto 2MB.</small>
                                    @if($errors->has('attachment'))
                                        <div class="fv-plugins-message-container invalid-feedback">
                                            <div data-field="attachment">{!! $errors->first('attachment') !!}</div>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            {!! method_field('PUT') !!}
                            {!! csrf_field() !!}
                        </div>
                        <div class="card-footer border-0 justify-content-end d-flex gap-2">
                            <button type="submit" name="btn" value="save" class="btn btn-primary btn-sm">
                                Update
                            </button>
                            <a href="{!! route('payment.bills.index') !!}"
                               class="btn btn-danger btn-sm">Cancel</a>
                        </div>
                    </form>
                </div>
            </section>

@stop
