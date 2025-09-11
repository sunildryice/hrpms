@extends('layouts.container')

@section('title', 'Add New Transportation Bill')

@section('page_js')
    <script type="text/javascript">
        document.addEventListener('DOMContentLoaded', function (e) {
            $('#navbarVerticalMenu').find('#transportation-bills-menu').addClass('active');
            const form = document.getElementById('transportationBillAddForm');
            const fv = FormValidation.formValidation(form, {
                fields: {
                    // bill_number: {
                    //     validators: {
                    //         notEmpty: {
                    //             message: 'Bill number is required',
                    //         },
                    //     },
                    // },
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
                                    <a href="{{ route('transportation.bills.index') }}" class="text-decoration-none text-dark">Transportation
                                        Bills</a>
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
                    <form action="{{ route('transportation.bills.store') }}" id="transportationBillAddForm"
                          method="post" enctype="multipart/form-data" autocomplete="off">
                        <div class="card-body">
                            {{-- <div class="row mb-2">
                                <div class="col-lg-3">
                                    <div class="d-flex align-items-start h-100">
                                        <label for="validationdd" class="form-label required-label">Bill Number</label>
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <input
                                        class="form-control @if($errors->has('bill_number')) is-invalid @endif"
                                        type="text" name="bill_number" value="{{ old('bill_number') }}"/>
                                    @if($errors->has('bill_number'))
                                        <div class="fv-plugins-message-container invalid-feedback">
                                            <div data-field="bill_number">
                                                {!! $errors->first('bill_number') !!}
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div> --}}
                            <div class="row mb-2">
                                <div class="col-lg-2">
                                    <div class="d-flex align-items-start h-100">
                                        <label for="validationdd" class="form-label required-label">Bill Date</label>
                                    </div>
                                </div>
                                <div class="col-lg-10">
                                    <input class="form-control @if($errors->has('bill_date')) is-invalid @endif"
                                           type="text" readonly name="bill_date"
                                           value="{{ old('bill_date') }}"/>
                                    @if($errors->has('bill_date'))
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
                                                    class="form-control @if($errors->has('shipper_name')) is-invalid @endif"
                                                    type="text" name="shipper_name"
                                                    value="{{ old('shipper_name') }}"/>
                                                @if($errors->has('shipper_name'))
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
                                                    class="form-control @if($errors->has('shipper_address')) is-invalid @endif"
                                                    type="text" name="shipper_address"
                                                    value="{{ old('shipper_address') }}"/>
                                                @if($errors->has('shipper_address'))
                                                    <div class="fv-plugins-message-container invalid-feedback">
                                                        <div
                                                            data-field="shipper_address">{!! $errors->first('shipper_address') !!}</div>
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
                                                    class="form-control @if($errors->has('consignee_name')) is-invalid @endif"
                                                    type="text" name="consignee_name"
                                                    value="{{ old('consignee_name') }}"/>
                                                @if($errors->has('consignee_name'))
                                                    <div class="fv-plugins-message-container invalid-feedback">
                                                        <div
                                                            data-field="consignee_name">{!! $errors->first('consignee_name') !!}</div>
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
                                                    class="form-control @if($errors->has('consignee_address')) is-invalid @endif"
                                                    type="text" name="consignee_address"
                                                    value="{{ old('consignee_address') }}"/>
                                                @if($errors->has('consignee_address'))
                                                    <div class="fv-plugins-message-container invalid-feedback">
                                                        <div
                                                            data-field="consignee_address">{!! $errors->first('consignee_address') !!}</div>
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
                                        <label for="validationRemarks" class="m-0">Remarks</label>
                                    </div>
                                </div>
                                <div class="col-lg-10">
                                    <textarea type="text"
                                              class="form-control @if($errors->has('remarks')) is-invalid @endif"
                                              name="remarks">{{ old('remarks') }}</textarea>
                                    @if($errors->has('remarks'))
                                        <div class="fv-plugins-message-container invalid-feedback">
                                            <div
                                                data-field="remarks">{!! $errors->first('remarks') !!}</div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-lg-2">
                                    <div class="d-flex align-items-start h-100">
                                        <label for="validationRemarks" class="m-0">Special Instruction</label>
                                    </div>
                                </div>
                                <div class="col-lg-10">
                                    <textarea type="text"
                                              class="form-control @if($errors->has('instruction')) is-invalid @endif"
                                              name="instruction">{{ old('instruction') }}</textarea>
                                    @if($errors->has('instruction'))
                                        <div class="fv-plugins-message-container invalid-feedback">
                                            <div
                                                data-field="instruction">{!! $errors->first('instruction') !!}</div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            {!! csrf_field() !!}
                        </div>
                        <div class="card-footer border-0 justify-content-end d-flex gap-2">
                            <button type="submit" name="btn" value="save" class="btn btn-primary btn-sm">Save
                            </button>
                            <a href="{!! route('transportation.bills.index') !!}"
                               class="btn btn-danger btn-sm">Cancel</a>
                        </div>
                    </form>
                </div>
            </section>

@stop
