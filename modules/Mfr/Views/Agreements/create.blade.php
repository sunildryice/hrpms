@extends('layouts.container')

@section('title', 'Add New MFR')

@section('page_js')
    <script>
        const partners = @json($partnerOrganizations);
        $(document).ready(function() {
            $('#navbarVerticalMenu').find('#agreement-index').addClass('active');
        });
        document.addEventListener('DOMContentLoaded', function(e) {
            // $("#substitutes").select2({
            //     width: '100%',
            //     dropdownAutoWidth: true
            // });
            const form = document.getElementById('eventCompletionAddForm');
            const fv = FormValidation.formValidation(form, {
                fields: {
                    partner_organization_id: {
                        validators: {
                            notEmpty: {
                                message: 'The Partner organization is required',
                            },
                        },
                    },
                    district_id: {
                        validators: {
                            notEmpty: {
                                message: 'The District is required',
                            },
                        },
                    },
                    project_id: {
                        validators: {
                            notEmpty: {
                                message: 'The proect is required',
                            },
                        },
                    },
                    grant_number: {
                        validators: {
                            notEmpty: {
                                message: 'The Grant Agreement Number is required',
                            },
                        },
                    },
                    effective_from: {
                        validators: {
                            notEmpty: {
                                message: 'Agreement Period is required',
                            },
                            date: {
                                format: 'YYYY-MM-DD',
                                message: 'The value is not a valid date',
                            },
                        },
                    },
                    effective_to: {
                        validators: {
                            notEmpty: {
                                message: 'Agreement Period is required',
                            },
                            date: {
                                format: 'YYYY-MM-DD',
                                message: 'The value is not a valid date',
                            },
                            callback: {
                                message: 'The End date must be greater than Start date',
                                callback: function(value, validator, field) {
                                    const startDate = new Date(form.querySelector(
                                            '[name="effective_from"]')
                                        .value);
                                    return new Date(value.value) >= startDate;
                                },
                            }
                        },
                    },
                    approved_budget: {
                        validators: {
                            notEmpty: {
                                message: 'The approved budget is required',
                            },
                        },
                    },
                    opening_balance: {
                        validators: {
                            notEmpty: {
                                message: 'The opening balance is required',
                            },
                            callback: {
                                message: "The opening balance must be greater than approved balance",
                                callback: function(input) {
                                    if (form.querySelector('[name="approved_budget"]').value) {
                                        return +input.value <= +form.querySelector(
                                                '[name="approved_budget"]')
                                            .value;
                                    }
                                    return true;
                                },
                            }

                        },
                    },
                    opening_remarks: {
                        validators: {
                            notEmpty: {
                                message: 'The opening remarks is required'
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

            $('[name="approved_budget"]').on('input',function(){
                if(form.querySelector('[name="opening_balance"]').value){
                    fv.revalidateField('opening_balance');
                }
            })

            $(form.querySelector('[name="effective_from"]')).datepicker({
                language: 'en-GB',
                autoHide: true,
                format: 'yyyy-mm-dd',
            }).on('change', function(e) {
                fv.revalidateField('effective_from');
                if (form.querySelector('[name="effective_to"]').value) {
                    fv.revalidateField('effective_to');
                }
            });

            $(form.querySelector('[name="effective_to"]')).datepicker({
                language: 'en-GB',
                autoHide: true,
                format: 'yyyy-mm-dd',
            }).on('change', function(e) {
                fv.revalidateField('effective_to');
            });


            $(form).on('change', '[name="district_id"]', function(e) {
                fv.revalidateField('district_id');
            });
            $(form).on('change', '[name="activity_code_id"]', function(e) {
                fv.revalidateField('activity_code_id');
            });

            $(form).on('change', '[name="partner_organization_id"]', function(e) {
                let districtId = partners.find(p => p.id == $(this).val()).district_id;
                let districtField = form.querySelector('[name="district_id"]');
                districtField.value = districtId;
                $(districtField).trigger('change');
            });

        });
    </script>

@endsection

@section('page-content')



    <div class="pb-3 mb-3 page-header border-bottom">
        <div class="d-flex align-items-center">
            <div class="brd-crms flex-grow-1">
                <nav aria-label="breadcrumb">
                    <ol class="m-0 breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{!! route('dashboard.index') !!}" class="text-decoration-none text-dark">Home</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('mfr.agreement.index') }}" class="text-decoration-none text-dark">MFR</a>
                        </li>
                        <li class="breadcrumb-item" aria-current="page">@yield('title')</li>
                    </ol>
                </nav>
                <h4 class="m-0 mt-1 lh1 fs-6 text-uppercase fw-bold text-primary">@yield('title')</h4>
            </div>
        </div>
    </div>

    <section class="registration">
        <div class="card">
            <div class="card-header fw-bold">Partner Organization (PO) Fund Release/ MFR Approval</div>
            <form action="{{ route('mfr.agreement.store') }}" id="eventCompletionAddForm" method="post"
                enctype="multipart/form-data" autocomplete="off">
                <div class="card-body">

                    <div class="mb-2 row">
                        <div class="col-lg-3">
                            <div class="d-flex align-items-start h-100">
                                <label for="validationdistrict" class="form-label required-label">Partner organization
                                </label>
                            </div>
                        </div>
                        <div class="col-lg-9">
                            <select name="partner_organization_id" class="select2 form-control" data-width="100%">
                                <option value="">Select a Partner organization</option>
                                @foreach ($partnerOrganizations as $partner)
                                    <option value="{{ $partner->id }}"
                                        {{ $partner->id == old('partner_organization_id') ? 'selected' : '' }}>
                                        {{ $partner->name }}
                                    </option>
                                @endforeach
                            </select>
                            @if ($errors->has('partner_organization_id'))
                                <div class="fv-plugins-message-container invalid-feedback">
                                    <div data-field="partner_organization_id">
                                        {!! $errors->first('partner_organization_id') !!}
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="mb-2 row">
                        <div class="col-lg-3">
                            <div class="d-flex align-items-start h-100">
                                <label for="validationdistrict" class="form-label required-label">District
                                </label>
                            </div>
                        </div>
                        <div class="col-lg-9">
                            <select name="district_id" class="select2 form-control" data-width="100%">
                                <option value="">Select a District</option>
                                @foreach ($districts as $district)
                                    <option value="{{ $district->id }}"
                                        {{ $district->id == old('district_id') ? 'selected' : '' }}>
                                        {{ $district->district_name }}
                                    </option>
                                @endforeach
                            </select>
                            @if ($errors->has('district_id'))
                                <div class="fv-plugins-message-container invalid-feedback">
                                    <div data-field="district_id">
                                        {!! $errors->first('district_id') !!}
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="mb-2 row">
                        <div class="col-lg-3">
                            <div class="d-flex align-items-start h-100">
                                <label for="validationdistrict" class="form-label required-label">Project
                                </label>
                            </div>
                        </div>
                        <div class="col-lg-9">
                            <select name="project_id" class="select2 form-control" data-width="100%">
                                <option value="">Select a Project</option>
                                @foreach ($projectCodes as $projectCode)
                                    <option value="{{ $projectCode->id }}"
                                        {{ $projectCode->id == old('project_id') ? 'selected' : '' }}>
                                        {{ $projectCode->title }}
                                    </option>
                                @endforeach
                            </select>
                            @if ($errors->has('project_id'))
                                <div class="fv-plugins-message-container invalid-feedback">
                                    <div data-field="project_id">
                                        {!! $errors->first('project_id') !!}
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="mb-2 row">
                        <div class="col-lg-3">
                            <div class="d-flex align-items-start h-100">
                                <label for="validationGrant" class="form-label required-label">Grant Agreement
                                    Number</label>
                            </div>
                        </div>
                        <div class="col-lg-9">
                            <input type="text" class="form-control @if ($errors->has('grant_number')) is-invalid @endif"
                                name="grant_number" value="{{ old('grant_number') }}">
                            @if ($errors->has('grant_number'))
                                <div class="fv-plugins-message-container invalid-feedback">
                                    <div data-field="grant_number">{!! $errors->first('grant_number') !!}</div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="mb-2 row">
                        <div class="col-lg-3">
                            <div class="d-flex align-items-start h-100">
                                <label for="validationpd" class="form-label required-label">Agreement Period (from)
                                </label>
                            </div>
                        </div>
                        <div class="col-lg-9">
                            <input type="text"
                                class="form-control
                                        @if ($errors->has('effective_from')) is-invalid @endif"
                                readonly name="effective_from" value="{{ old('effective_from') }}" />
                            @if ($errors->has('effective_from'))
                                <div class="fv-plugins-message-container invalid-feedback">
                                    <div data-field="effective_from">{!! $errors->first('effective_from') !!}
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="mb-2 row">
                        <div class="col-lg-3">
                            <div class="d-flex align-items-start h-100">
                                <label for="validationpd" class="form-label required-label">Agreement Period (to)
                                </label>
                            </div>
                        </div>
                        <div class="col-lg-9">
                            <input type="text"
                                class="form-control
                                        @if ($errors->has('effective_to')) is-invalid @endif"
                                readonly name="effective_to" value="{{ old('effective_to') }}" />
                            @if ($errors->has('effective_to'))
                                <div class="fv-plugins-message-container invalid-feedback">
                                    <div data-field="effective_to">{!! $errors->first('effective_to') !!}
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="mb-2 row">
                        <div class="col-lg-3">
                            <div class="d-flex align-items-start h-100">
                                <label for="validationGrant" class="form-label required-label">Approved Budget NPR</label>
                            </div>
                        </div>
                        <div class="col-lg-9">
                            <input type="number" class="form-control @if ($errors->has('approved_budget')) is-invalid @endif"
                                name="approved_budget" value="{{ old('approved_budget') }}">
                            @if ($errors->has('approved_budget'))
                                <div class="fv-plugins-message-container invalid-feedback">
                                    <div data-field="approved_budget">{!! $errors->first('approved_budget') !!}</div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="mb-2 row">
                        <div class="col-lg-3">
                            <div class="d-flex align-items-start h-100">
                                <label for="validationGrant" class="form-label required-label">Opening Balance</label>
                            </div>
                        </div>
                        <div class="col-lg-9">
                            <input type="number"
                                class="form-control @if ($errors->has('opening_balance')) is-invalid @endif"
                                name="opening_balance" value="{{ old('opening_balance') }}">
                            @if ($errors->has('opening_balance'))
                                <div class="fv-plugins-message-container invalid-feedback">
                                    <div data-field="opening_balance">{!! $errors->first('opening_balance') !!}</div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="mb-2 row">
                        <div class="col-lg-3">
                            <div class="d-flex align-items-start h-100">
                                <label for="validationopening_remarks" class="form-label required-label">Opening Remarks
                                </label>
                            </div>
                        </div>
                        <div class="col-lg-9">
                            <textarea type="text" class="form-control @if ($errors->has('opening_remarks')) is-invalid @endif"
                                name="opening_remarks">
@if (old('opening_remarks'))
{{ old('opening_remarks') }}
@endif
</textarea>
                            @if ($errors->has('opening_remarks'))
                                <div class="fv-plugins-message-container invalid-feedback">
                                    <div data-field="opening_remarks">{!! $errors->first('opening_remarks') !!}</div>
                                </div>
                            @endif
                        </div>
                    </div>

                    {!! csrf_field() !!}
                    <div class="gap-2 border-0 card-footer justify-content-end d-flex">
                        <button type="submit" class="btn btn-success btn-sm">Create</button>
                        <a href="{!! route('event.completion.create') !!}" class="btn btn-danger btn-sm">Cancel</a>
                    </div>
            </form>
        </div>
    </section>

@stop
