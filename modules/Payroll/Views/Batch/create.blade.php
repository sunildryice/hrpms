@extends('layouts.container')

@section('title', 'Add New Payroll Batch')

@section('page_js')
    <script type="text/javascript">
        document.addEventListener('DOMContentLoaded', function (e) {
            $('#navbarVerticalMenu').find('#payroll-batches-menu').addClass('active');
            const form = document.getElementById('payrollBatchAddForm');
            const fv = FormValidation.formValidation(form, {
                fields: {
                    fiscal_year_id: {
                        validators: {
                            notEmpty: {
                                message: 'Fiscal year is required',
                            },
                        },
                    },
                    posted_date: {
                        validators: {
                            notEmpty: {
                                message: 'The posted date is required',
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
            }).on('change', '[name="fiscal_year_id"]', function(e){
                fv.revalidateField('fiscal_year_id');
            });

            $('[name="posted_date"]').datepicker({
                language: 'en-GB',
                autoHide: true,
                format: 'yyyy-mm-dd',
                endDate: '{{ date('Y-m-d') }}',
            }).on('change', function (e) {
                fv.revalidateField('posted_date');
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
                                    <a href="{{ route('payroll.batches.index') }}" class="text-decoration-none">Payroll
                                        Batches</a>
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
                            <form action="{{ route('payroll.batches.store') }}" id="payrollBatchAddForm" method="post"
                                  enctype="multipart/form-data" autocomplete="off">
                                <div class="card-body">
                                    <div class="row mb-2">
                                        <div class="col-lg-3">
                                            <div class="d-flex align-items-start h-100">
                                                <label for="validationpayrolltype" class="form-label required-label">FY  </label>
                                            </div>
                                        </div>
                                        <div class="col-lg-9">
                                            <select name="fiscal_year_id" class="select2 form-control" data-width="100%">
                                                <option value="">Select FY</option>
                                                @foreach($fiscalYears as $fiscalYear)
                                                    <option value="{{ $fiscalYear->id }}">
                                                        {{ $fiscalYear->getFiscalYear() }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @if($errors->has('fiscal_year_id'))
                                                <div class="fv-plugins-message-container invalid-feedback">
                                                    <div data-field="fiscal_year_id">
                                                        {!! $errors->first('fiscal_year_id') !!}
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="row mb-2">
                                        <div class="col-lg-3">
                                            <div class="d-flex align-items-start h-100">
                                                <label for="validationpayrolltype" class="form-label required-label">Month  </label>
                                            </div>
                                        </div>
                                        <div class="col-lg-9">
                                            <select name="month" class="select2 form-control" data-width="100%">
                                                <option value="">Select Month</option>
                                                @foreach($months as $id=>$month)
                                                    <option value="{{ $id }}">
                                                        {{ $month }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @if($errors->has('month'))
                                                <div class="fv-plugins-message-container invalid-feedback">
                                                    <div data-field="month">
                                                        {!! $errors->first('month') !!}
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="row mb-2">
                                        <div class="col-lg-3">
                                            <div class="d-flex align-items-start h-100">
                                                <label for="validationdd" class="form-label required-label">Posted Date </label>
                                            </div>
                                        </div>
                                        <div class="col-lg-9">
                                            <input class="form-control @if($errors->has('posted_date')) is-invalid @endif"
                                                   type="text" readonly name="posted_date" value="{{ old('posted_date') }}"/>
                                            @if($errors->has('posted_date'))
                                                <div class="fv-plugins-message-container invalid-feedback">
                                                    <div data-field="posted_date">
                                                        {!! $errors->first('posted_date') !!}
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="row mb-2">
                                        <div class="col-lg-3">
                                            <div class="d-flex align-items-start h-100">
                                                <label for="validationRemarks" class="m-0">Description</label>
                                            </div>
                                        </div>
                                        <div class="col-lg-9">
                                            <textarea type="text"
                                                      class="form-control @if($errors->has('description')) is-invalid @endif"
                                                      name="description">{{ old('description') }}</textarea>
                                            @if($errors->has('description'))
                                                <div class="fv-plugins-message-container invalid-feedback">
                                                    <div
                                                        data-field="description">{!! $errors->first('description') !!}</div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    {!! csrf_field() !!}
                                </div>
                                <div class="card-footer border-0 justify-content-end d-flex gap-2">
                                    <button type="submit" name="btn" value="save" class="btn btn-primary btn-sm">Save
                                    </button>
                                    <a href="{!! route('payroll.batches.index') !!}"
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
