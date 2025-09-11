@extends('layouts.container')

@section('title', 'Add New Distribution Request')

@section('page_js')
    <script type="text/javascript">
        document.addEventListener('DOMContentLoaded', function(e) {
            $('#navbarVerticalMenu').find('#distribution-requests-menu').addClass('active');
            const form = document.getElementById('distributionRequestAddForm');
            const fv = FormValidation.formValidation(form, {
                fields: {
                    district_id: {
                        validators: {
                            notEmpty: {
                                message: 'District is required',
                            },
                        },
                    },
                    office_id: {
                        validators: {
                            notEmpty: {
                                message: 'The office is required',
                            },
                        },
                    },
                    project_code_id: {
                        validators: {
                            notEmpty: {
                                message: 'The project code is required',
                            },
                        },
                    },
                    health_facility_id: {
                        validators: {
                            notEmpty: {
                                message: 'The health facility is required',
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

            $(form).on('change', '[name="district_id"]', function(e) {
                fv.revalidateField('district_id');
            }).on('change', '[name="project_code_id"]', function(e) {
                fv.revalidateField('project_code_id');
            }).on('change', '[name="office_id"]', function(e) {
                fv.revalidateField('office_id');
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
                                    <a href="{{ route('distribution.requests.index') }}"
                                        class="text-decoration-none">Distribution
                                        Requests</a>
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
                            <form action="{{ route('distribution.requests.store') }}" id="distributionRequestAddForm"
                                method="post" enctype="multipart/form-data" autocomplete="off">
                                <div class="card-body">
                                    <div class="row mb-2">
                                        <div class="col-lg-3">
                                            <div class="d-flex align-items-start h-100">
                                                <label for="validationdistributiontype"
                                                    class="form-label required-label">District</label>
                                            </div>
                                        </div>
                                        <div class="col-lg-9">
                                            <select name="district_id" class="select2 form-control" data-width="100%">
                                                <option value="">Select a District</option>
                                                @foreach ($districts as $district)
                                                    <option value="{{ $district->id }}"
                                                        data-distribution="{{ $district->id }}"
                                                        {{ $district->id == old('district_id') ? 'selected' : '' }}>
                                                        {{ $district->getDistrictName() }}
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

                                    <div class="row mb-2">
                                        <div class="col-lg-3">
                                            <div class="d-flex align-items-start h-100">
                                                <label for="validationdistributiontype"
                                                    class="form-label required-label">Office</label>
                                            </div>
                                        </div>
                                        <div class="col-lg-9">
                                            <select name="office_id" class="select2 form-control" data-width="100%">
                                                <option value="">Select a Office</option>
                                                @foreach ($offices as $office)
                                                    <option value="{{ $office->id }}"
                                                        data-distribution="{{ $office->id }}"
                                                        {{ $office->id == old('office_id') ? 'selected' : '' }}>
                                                        {{ $office->getOfficeName() }}
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
                                                <label for="validationdistributiontype"
                                                    class="form-label required-label">Project</label>
                                            </div>
                                        </div>
                                        <div class="col-lg-9">
                                            <select name="project_code_id" class="select2 form-control" data-width="100%">
                                                <option value="">Select a Project</option>
                                                @foreach ($projectCodes as $project)
                                                    <option value="{{ $project->id }}"
                                                        data-distribution="{{ $project->id }}"
                                                        {{ $project->id == old('project_code_id') ? 'selected' : '' }}>
                                                        {{ $project->getProjectCodeWithDescription() }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @if ($errors->has('project_code_id'))
                                                <div class="fv-plugins-message-container invalid-feedback">
                                                    <div data-field="project_code_id">
                                                        {!! $errors->first('project_code_id') !!}
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="row mb-2">
                                        <div class="col-lg-3">
                                            <div class="d-flex align-items-start h-100">
                                                <label for="validationHealthFacility"
                                                    class="form-label required-label">Health
                                                    Facility</label>
                                            </div>
                                        </div>
                                        <div class="col-lg-9">
                                            <select
                                                class="form-control select2 @if ($errors->has('health_facility_id')) is-invalid @endif"
                                                name="health_facility_id" id="health_facility_id">
                                                <option value="">Select a health facility</option>
                                                @foreach ($healthFacilities as $healthFacility)
                                                    <option value="{{ $healthFacility->id }}"
                                                        {{ old('health_facility_id') == $healthFacility->id ? 'selected' : '' }}>
                                                        {{ $healthFacility->title }}</option>
                                                @endforeach
                                            </select>
                                            @if ($errors->has('health_facility_id'))
                                                <div class="fv-plugins-message-container invalid-feedback">
                                                    <div data-field="health_facility_id">{!! $errors->first('health_facility_id') !!}</div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="row mb-2">
                                        <div class="col-lg-3">
                                            <div class="d-flex align-items-start h-100">
                                                <label for="validationdistributiontype"
                                                    class="form-label ">Purchase Request</label>
                                            </div>
                                        </div>
                                        <div class="col-lg-9">
                                            @php
                                                $selectedPRId = old('purchase_request_ids') ?? [];
                                            @endphp
                                            <select name="purchase_request_ids[]" class="select2 form-control"
                                                data-width="100%" multiple>
                                                @foreach ($purchaseRequests as $purchaseRequest)
                                                    <option value="{{ $purchaseRequest->id }}"
                                                        data-distribution="{{ $purchaseRequest->id }}"
                                                        {{ in_array($purchaseRequest->id, $selectedPRId) ? 'selected' : '' }}>
                                                        {{ $purchaseRequest->getPurchaseRequestNumber() }}
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

                                    <div class="row mb-2">
                                        <div class="col-lg-3">
                                            <div class="d-flex align-items-start h-100">
                                                <label for="validationRemarks" class="m-0">Remarks</label>
                                            </div>
                                        </div>
                                        <div class="col-lg-9">
                                            <textarea type="text" class="form-control @if ($errors->has('remarks')) is-invalid @endif" name="remarks">{{ old('remarks') }}</textarea>
                                            @if ($errors->has('remarks'))
                                                <div class="fv-plugins-message-container invalid-feedback">
                                                    <div data-field="remarks">{!! $errors->first('remarks') !!}</div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    {!! csrf_field() !!}
                                </div>
                                <div class="card-footer border-0 justify-content-end d-flex gap-2">
                                    <button type="submit" name="btn" value="save"
                                        class="btn btn-primary btn-sm">Save
                                    </button>
                                    <a href="{!! route('distribution.requests.index') !!}" class="btn btn-danger btn-sm">Cancel</a>
                                </div>
                            </form>
                        </div>

                    </div>
                </div>
            </section>

        </div>
    </div>

@stop
