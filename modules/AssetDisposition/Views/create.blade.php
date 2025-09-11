@extends('layouts.container')

@section('title', 'Create Asset Disposition Request')

@section('page_js')
    <script>
        $(document).ready(function() {
            $('#navbarVerticalMenu').find('#asset-disposition-menu').addClass('active');
            const errors = @json($errors->all());
            console.log(errors);
        });
        document.addEventListener('DOMContentLoaded', function(e) {
            
            const form = document.getElementById('assetDispositionCreateForm');
            const fv = FormValidation.formValidation(form, {
                fields: {
                    office_id:{
                        validators: {
                            notEmpty: {
                                message: 'Office is required',
                            }
                        }
                    },
                    disposition_type_id: {
                        validators: {
                            notEmpty: {
                                message: 'The disposition type is required',
                            },
                        },
                    },
                    disposition_date: {
                        validators: {
                            notEmpty: {
                                message: 'The Program date is required',
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

            

            $(form.querySelector('[name="disposition_date"]')).datepicker({
                language: 'en-GB',
                autoHide: true,
                format: 'yyyy-mm-dd',
                endDate: '{!! date('Y-m-d') !!}',
            }).on('change', function(e) {
                fv.revalidateField('disposition_date');
            });


            $(form).on('change', '[name="asset_id"]', function(e) {
                fv.revalidateField('asset_id');
            });
            $(form).on('change', '[name="office_id"]', function(e) {
                fv.revalidateField('office_id');
            });
            $(form).on('change', '[name="disposition_type_id"]', function(e) {
                fv.revalidateField('disposition_type_id');
            });
        });
    </script>

@endsection

@section('page-content')



    <div class="page-header pb-3 mb-3 border-bottom">
        <div class="d-flex flex-column flex-lg-row align-items-start align-items-lg-center gap-2">
            <div class="brd-crms flex-grow-1">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{!! route('dashboard.index') !!}"
                                class="text-decoration-none text-dark">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('asset.disposition.index') }}"
                                class="text-decoration-none text-dark">Asset Disposition</a></li>
                        <li class="breadcrumb-item" aria-current="page">Create Asest Disposition Request</li>
                    </ol>
                </nav>
                <h4 class="m-0 lh1 mt-1 fs-6 text-uppercase fw-bold text-primary">Create Asest Disposition Request</h4>
            </div>
        </div>
    </div>

    <section class="registration">
        <div class="card">
            <div class="card-header fw-bold">Asset Disposition</div>
            <form action="{{ route('asset.disposition.store') }}" id="assetDispositionCreateForm" method="post"
                enctype="multipart/form-data" autocomplete="off">
                <div class="card-body">
                    <div class="row mb-2">
                        <div class="col-lg-3">
                            <div class="d-flex align-items-start h-100">
                                <label for="validationOffice" class="form-label required-label">Offices
                                </label>
                            </div>
                        </div>
                        <div class="col-lg-9">
                            <select name="office_id" class="select2 form-control" data-width="100%">
                                <option value="">Select an Office</option>
                                @foreach ($offices as $office)
                                    <option value="{{ $office->id }}"
                                        {{ $office->id == old('office_id') ? 'selected' : '' }}>
                                        {{ $office->office_name }}
                                    </option>
                                @endforeach
                            </select>
                            @if ($errors->has('office_id'))
                                <div class="fv-plugins-message-container invalid-feedback">
                                    <div data-field="office_id">
                                        {!! $errors->first('office_id') !!}
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-lg-3">
                            <div class="d-flex align-items-start h-100">
                                <label for="validationActivity" class="form-label">Disposition Type</label>
                            </div>
                        </div>
                        <div class="col-lg-9">
                            <select class="form-control select2" data-width="100%" name="disposition_type_id">
                                <option value="">Select Disposition Type</option>
                                @foreach ($dispositionTypes as $dispositionType)
                                    <option value="{!! $dispositionType->id !!}"
                                        {{ $dispositionType->id == old('disposition_type_id') ? 'selected' : '' }}>
                                        {{ $dispositionType->getDispositionType() }}</option>
                                @endforeach
                            </select>
                            @if ($errors->has('disposition_type_id'))
                                <div class="fv-plugins-message-container invalid-feedback">
                                    <div data-field="disposition_type_id">
                                        {!! $errors->first('disposition_type_id') !!}
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                   
                    <div class="row mb-2">
                        <div class="col-lg-3">
                            <div class="d-flex align-items-start h-100">
                                <label for="validationDispositionDate" class="form-label required-label">Disposition Date
                                </label>
                            </div>
                        </div>
                        <div class="col-lg-9">
                            <input type="text"
                                class="form-control
                                        @if ($errors->has('disposition_date')) is-invalid @endif"
                                readonly name="disposition_date" value="{{ old('disposition_date') }}" />
                            @if ($errors->has('disposition_date'))
                                <div class="fv-plugins-message-container invalid-feedback">
                                    <div data-field="disposition_date">{!! $errors->first('disposition_date') !!}
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    {!! csrf_field() !!}
                    <div class="card-footer border-0 justify-content-end d-flex gap-2">
                        <button type="submit" value="submit" class="btn btn-primary btn-sm" name="btn">Create</button>
                        <a href="{!! route('asset.disposition.index') !!}" class="btn btn-danger btn-sm">Cancel</a>
                    </div>
            </form>
        </div>
    </section>

@stop
