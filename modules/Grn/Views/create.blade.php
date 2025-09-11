@extends('layouts.container')

@section('title', 'Create GRN')

@section('page_js')
    <script type="text/javascript">
        document.addEventListener('DOMContentLoaded', function (e) {
            $('#navbarVerticalMenu').find('#grns-menu').addClass('active');
            const form = document.getElementById('grnForm');
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
                            <form action="{{ route('grns.store') }}"
                                  id="grnForm" method="post"
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
                                                   value="{!! old('received_date') !!}"/>
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
                                                       class="m-0">Supplier</label>
                                            </div>
                                        </div>
                                        <div class="col-lg-9">
                                            <select class="form-control select2" name="supplier_id">
                                                <option value="">Select Supplier</option>
                                                @foreach($suppliers as $supplier)
                                                    <option value="{!! $supplier->id !!}">{!! $supplier->getSupplierNameandVAT() !!}</option>
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
                                                   value="{!! old('invoice_number') !!}"/>
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
                                                      rows="3">{!! old('received_note') !!}</textarea>
                                            @if($errors->has('received_note'))
                                                <div class="fv-plugins-message-container invalid-feedback">
                                                    <div data-field="received_note">
                                                        {!! $errors->first('received_note') !!}
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    {!! csrf_field() !!}
                                </div>
                                <div class="card-footer border-0 justify-content-end d-flex gap-2">
                                    <button type="submit" name="btn" class="btn btn-success btn-sm">
                                        Save
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
