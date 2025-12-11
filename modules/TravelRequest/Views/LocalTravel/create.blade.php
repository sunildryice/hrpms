@extends('layouts.container')

@section('title', 'Add New Local Travel Reimbursement')

@section('page_js')
    <script type="text/javascript">
        document.addEventListener('DOMContentLoaded', function(e) {
            $('#navbarVerticalMenu').find('#local-travel-reimbursements-menu').addClass('active');
            const form = document.getElementById('localTravelAddForm');
            const fv = FormValidation.formValidation(form, {
                fields: {
                    project_code_id: {
                        validators: {
                            notEmpty: {
                                message: 'Project is required',
                            },
                        },
                    },
                    title: {
                        validators: {
                            notEmpty: {
                                message: 'Purpose is required',
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
            $(form).on('change', '[name="activity_code_id"]', function(e) {
                $element = $(this);
                var activityCodeId = $element.val();
                var htmlToReplace = '<option value="">Select Account Code</option>';
                $($element).closest('form').find('[name="account_code_id"]').html(htmlToReplace);
                if (activityCodeId) {
                    var url = baseUrl + '/api/master/activity-codes/' + activityCodeId;
                    var successCallback = function(response) {
                        response.accountCodes.forEach(function(accountCode) {
                            htmlToReplace += '<option value="' + accountCode.id + '">' +
                                accountCode.title + ' ' + accountCode.description + '</option>';
                        });
                        $($element).closest('form').find('[name="account_code_id"]').html(htmlToReplace)
                            .trigger('change');
                    }
                    var errorCallback = function(error) {
                        console.log(error);
                    }
                    ajaxNativeSubmit(url, 'GET', {}, 'json', successCallback, errorCallback);
                }
                fv.revalidateField('activity_code_id');
                fv.revalidateField('account_code_id');
            }).on('change', '[name="account_code_id"]', function(e) {
                fv.revalidateField('account_code_id');
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
                            <a href="{{ route('local.travel.reimbursements.index') }}"
                                class="text-decoration-none text-dark">Local Travel Reimbursements</a>
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
            <form action="{{ route('local.travel.reimbursements.store') }}" id="localTravelAddForm" method="post"
                enctype="multipart/form-data" autocomplete="off">
                <div class="card-body">
                    <div class="mb-2 row">
                        <div class="row mb-2">
                            <div class="col-lg-2">
                                <div class="d-flex align-items-start h-100">
                                    <label for="validationProject" class="form-label required-label">Project
                                    </label>
                                </div>
                            </div>
                            <div class="col-lg-10">
                                <select name="project_code_id"
                                    class="select2 form-control
                                        @if ($errors->has('project_code_id')) is-invalid @endif"
                                    data-width="100%">
                                    <option value="">Select Project</option>
                                    @foreach ($projects as $project)
                                        <option value="{{ $project->id }}"
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

                        <div class="mb-2 row">
                            <div class="col-lg-2">
                                <div class="d-flex align-items-start h-100">
                                    <label for="validationRemarks" class="form-label required-label">Purpose</label>
                                </div>
                            </div>
                            <div class="col-lg-10">
                                <input type="text" value="{{ old('title') }}"
                                    class="form-control @if ($errors->has('title')) is-invalid @endif"
                                    name="title" />
                                @if ($errors->has('title'))
                                    <div class="fv-plugins-message-container invalid-feedback">
                                        <div data-field="title">{!! $errors->first('title') !!}</div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- <div class="mb-2 row">
                            <div class="col-lg-2">
                                <div class="d-flex align-items-start h-100">
                                    <label for="validationProject" class="form-label">Request For
                                    </label>
                                </div>
                            </div>
                            <div class="col-lg-10">
                                <select name="employee_id"
                                    class="select2 form-control
                                        @if ($errors->has('employee_id')) is-invalid @endif"
                                    data-width="100%">
                                    <option value="">Select Employee/Consultant</option>
                                    @foreach ($consultants as $consultant)
                                        <option value="{{ $consultant->id }}"
                                            {{ $consultant->id == old('employee_id') ? 'selected' : '' }}>
                                            {{ $consultant->getFullName() }}
                                        </option>
                                    @endforeach
                                </select>
                                @if ($errors->has('employee_id'))
                                    <div class="fv-plugins-message-container invalid-feedback">
                                        <div data-field="employee_id">
                                            {!! $errors->first('employee_id') !!}
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="mb-2 row">
                            <div class="col-lg-2">
                                <div class="d-flex align-items-start h-100">
                                    <label for="validationpurchasetype" class="form-label">Travel Request (If any)</label>
                                </div>
                            </div>
                            <div class="col-lg-10">
                                <select name="travel_request_id" class="select2 form-control" data-width="100%">
                                    <option value="">Select Travel Request</option>
                                    @foreach ($travelRequests as $travelRequest)
                                        <option value="{{ $travelRequest->id }}" data-purchase="{{ $travelRequest->id }}"
                                            {{ $travelRequest->id == old('travel_request_id') ? 'selected' : '' }}>
                                            {{ $travelRequest->getTravelRequestNumber() }}
                                        </option>
                                    @endforeach
                                </select>
                                @if ($errors->has('travel_request_id'))
                                    <div class="fv-plugins-message-container invalid-feedback">
                                        <div data-field="travel_request_id">
                                            {!! $errors->first('travel_request_id') !!}
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div> --}}

                        <div class="mb-2 row">
                            <div class="col-lg-2">
                                <div class="d-flex align-items-start h-100">
                                    <label for="validationRemarks" class="form-label">Reason For Travel</label>
                                </div>
                            </div>
                            <div class="col-lg-10">
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
                    <div class="gap-2 border-0 card-footer justify-content-end d-flex">
                        <button type="submit" name="btn" value="save" class="btn btn-primary btn-sm">Save
                        </button>
                        <a href="{!! route('local.travel.reimbursements.index') !!}" class="btn btn-danger btn-sm">Cancel</a>
                    </div>
            </form>
        </div>
    </section>

@stop
