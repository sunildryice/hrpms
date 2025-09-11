@extends('layouts.container')

@section('title', 'Edit Fund Request')

@section('page_js')
    <script type="text/javascript">
        document.addEventListener('DOMContentLoaded', function(e) {
            $('#navbarVerticalMenu').find('#fund-requests-menu').addClass('active');
            const form = document.getElementById('fundRequestEditForm');
            const fv = FormValidation.formValidation(form, {
                fields: {
                    // district_id: {
                    //     validators: {
                    //         notEmpty: {
                    //             message: 'District is required',
                    //         },
                    //     },
                    // },
                    request_for_office_id: {
                        validators: {
                            notEmpty: {
                                message: 'Office (requested for) is required',
                            },
                        },
                    },
                    year_month: {
                        validators: {
                            notEmpty: {
                                message: 'The month is required',
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
                    attachment: {
                        validators: {
                            file: {
                                extension: 'jpeg,jpg,png,pdf,doc,docx,dot,xlsx,xls,xlm,xla,xlc,xlt,xlw',
                                type: 'image/jpeg,image/png,application/pdf,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/msword,application/vnd.ms-office,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                                maxSize: '2097152',
                                message: 'The selected file is not valid or must not be greater than 2 MB.',
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

            $(form)
                // .on('change', '[name="district_id"]', function (e) {
                //     fv.revalidateField('district_id');
                // })
                .on('change', '[name="request_for_office_id"]', function(e) {
                    fv.revalidateField('request_for_office_id');
                })
                .on('change', '[name="project_code_id"]', function(e) {
                    fv.revalidateField('project_code_id');
                }).on('change', '[name="surplus_deficit"]', function() {
                    calculateNetAmount($(this));
                }).on('change', '[name="estimated_surplus"]', function() {
                    calculateNetAmount($(this));
                });

            $(form.querySelector('[name="year_month"]')).datepicker({
                language: 'en-GB',
                autoHide: true,
                format: 'yyyy-mm',
                startDate: '{!! date('Y-m', strtotime('-1 day')) !!}',
                {{-- startDate: '{!! date('Y-m', strtotime('+1 month')) !!}', --}}
            }).on('change', function(e) {
                console.log();
                fv.revalidateField('year_month');
            });

            function calculateNetAmount($object) {
                var surplusDeficit = $($object).closest('form').find('[name="surplus_deficit"]').val();
                var surplusDeficitAmount = parseFloat($($object).closest('form').find('[name="estimated_surplus"]')
                    .val());
                var requiredAmount = parseFloat($($object).closest('form').find('[name="required_amount"]').val());
                var netAmount = (surplusDeficit == 1) ? requiredAmount - surplusDeficitAmount : requiredAmount +
                    surplusDeficitAmount;
                $($object).closest('form').find('[name="net_amount"]').val(netAmount);
            }
        });

        var oTable = $('#fundRequestActivityTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('fund.requests.activities.index', $fundRequest->id) }}",
            bFilter: false,
            bPaginate: false,
            bInfo: false,
            columns: [
                {
                    data: 'activity',
                    name: 'activity'
                },
                {
                    data: 'estimated_amount',
                    name: 'estimated_amount'
                },
                {
                    data: 'budget_amount',
                    name: 'budget_amount'
                },
                {
                    data: 'project_target_unit',
                    name: 'project_target_unit'
                },
                {
                    data: 'dip_target_unit',
                    name: 'dip_target_unit'
                },
                {
                    data: 'variance_budget_amount',
                    name: 'variance_budget_amount'
                },
                {
                    data: 'variance_target_unit',
                    name: 'variance_target_unit'
                },
                {
                    data: 'justification_note',
                    name: 'justification_note',
                    className: 'text-wrap'
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false,
                    className: 'sticky-col'
                },
            ]
        });

        $('#fundRequestActivityTable').on('click', '.delete-record', function(e) {
            e.preventDefault();
            $object = $(this);
            var $url = $object.attr('data-href');
            var successCallback = function(response) {
                toastr.success(response.message, 'Success', {
                    timeOut: 5000
                });
                $('#fundRequestActivityTable').find('[name="required_amount"]').val(response
                    .fundRequest.required_amount);
                $('#fundRequestActivityTable').find('[name="net_amount"]').val(response
                    .fundRequest.net_amount);
                if (response.fundActivityCount) {
                    $('.submit-record').show();
                } else {
                    $('.submit-record').hide();
                }
                oTable.ajax.reload();
            }
            ajaxDeleteSweetAlert($url, successCallback);
        });

        $(document).on('click', '.open-activity-modal-form', function(e) {
            e.preventDefault();
            $('#openModal').find('.modal-content').html('');
            $('#openModal').modal('show').find('.modal-content').load($(this).attr('href'), function() {
                const form = document.getElementById('fundRequestActivityForm');
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
                        activity_code_id: {
                            validators: {
                                notEmpty: {
                                    message: 'Activity code is required',
                                },
                            },
                        },
                        estimated_amount: {
                            validators: {
                                notEmpty: {
                                    message: 'Estimated amount is required',
                                },
                                greaterThan: {
                                    message: 'The value must be greater than or equal to 1',
                                    min: 1,
                                },
                            },
                        },
                        budget_amount: {
                            validators: {
                                notEmpty: {
                                    message: 'Budget amount is required',
                                },
                                greaterThan: {
                                    message: 'The value must be greater than or equal to 0',
                                    min: 0,
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
                }).on('core.form.valid', function(event) {
                    $url = fv.form.action;
                    $form = fv.form;
                    data = $($form).serialize();
                    var successCallback = function(response) {
                        $('#openModal').modal('hide');
                        toastr.success(response.message, 'Success', {
                            timeOut: 5000
                        });
                        $('#fundRequestActivityTable').find('[name="required_amount"]').val(
                            response
                            .fundRequest.required_amount);
                        $('#fundRequestActivityTable').find('[name="net_amount"]').val(response
                            .fundRequest.net_amount);
                        if (response.fundActivityCount) {
                            $('.submit-record').show();
                        } else {
                            $('.submit-record').hide();
                        }
                        oTable.ajax.reload();
                    }
                    ajaxSubmit($url, 'POST', data, successCallback);
                });

                $(form).on('change', '[name="activity_code_id"]', function(e) {
                    fv.revalidateField('activity_code_id');
                });
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
                            <a href="{{ route('fund.requests.index') }}" class="text-decoration-none text-dark">Fund
                                Request</a>
                        </li>
                        <li class="breadcrumb-item" aria-current="page">@yield('title')</li>
                    </ol>
                </nav>
                <h4 class="m-0 mt-1 lh1 fs-6 text-uppercase fw-bold text-primary">@yield('title')</h4>
            </div>
        </div>
    </div>

    <section class="registration">
        <div>
            <form action="{{ route('fund.requests.update', $fundRequest->id) }}" id="fundRequestEditForm" method="post"
                enctype="multipart/form-data" autocomplete="off">
                <div class="card">
                    <div class="card-header fw-bold">Fund Request</div>
                    <div class="card-body">
                        <div class="mb-2 row">
                            <div class="col-lg-3">
                                <div class="d-flex align-items-start h-100">
                                    <label for="validationfundtype" class="form-label required-label">Year-Month</label>
                                </div>
                            </div>
                            <div class="col-lg-9">
                                <input type="text"
                                    class="form-control @if ($errors->has('year_month')) is-invalid @endif"
                                    name="year_month"
                                    value="{{ $fundRequest->year }}-{{ sprintf('%02s', $fundRequest->month) }}" readonly>
                                @if ($errors->has('year_month'))
                                    <div class="fv-plugins-message-container invalid-feedback">
                                        <div data-field="year_month">
                                            {!! $errors->first('year_month') !!}
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                        {{-- <div class="mb-2 row">
                                    <div class="col-lg-3">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="validationfundtype"
                                                   class="form-label required-label">District</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-9">
                                        @php $selectedDistrictId = $fundRequest->district_id; @endphp
                                        <select class="select2 form-control" name="district_id">
                                            <option value="">Select a District</option>
                                            @foreach ($districts as $district)
                                                <option value="{{ $district->id }}"
                                                    {{ $district->id == $selectedDistrictId ? 'selected' : '' }}>
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
                                </div> --}}
                        <div class="mb-2 row">
                            <div class="col-lg-3">
                                <div class="d-flex align-items-start h-100">
                                    <label for="request_for_office_id" class="form-label required-label">Office (requested
                                        for)</label>
                                </div>
                            </div>
                            <div class="col-lg-9">
                                @php $selectedOfficeId = $fundRequest->request_for_office_id @endphp
                                <select name="request_for_office_id" class="select2 form-control" data-width="100%">
                                    <option value="">Select an Office</option>
                                    @foreach ($offices as $office)
                                        <option value="{{ $office->id }}" data-fund="{{ $office->id }}"
                                            {{ $office->id == $selectedOfficeId ? 'selected' : '' }}>
                                            {{ $office->getOfficeName() }}
                                        </option>
                                    @endforeach
                                </select>
                                @if ($errors->has('request_for_office_id'))
                                    <div class="fv-plugins-message-container invalid-feedback">
                                        <div data-field="request_for_office_id">
                                            {!! $errors->first('request_for_office_id') !!}
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="mb-2 row">
                            <div class="col-lg-3">
                                <div class="d-flex align-items-start h-100">
                                    <label for="validationfundtype" class="form-label required-label">Project</label>
                                </div>
                            </div>
                            <div class="col-lg-9">
                                @php $selectedProjectId = old('project_code_id') ?: $fundRequest->project_code_id; @endphp
                                <select name="project_code_id" class="select2 form-control" data-width="100%">
                                    <option value="">Select a Project</option>
                                    @foreach ($projectCodes as $project)
                                        <option value="{{ $project->id }}" data-fund="{{ $project->id }}"
                                            {{ $project->id == $selectedProjectId ? 'selected' : '' }}>
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
                            <div class="col-lg-3">
                                <div class="d-flex align-items-start h-100">
                                    <label for="validationRemarks" class="form-label">Remarks</label>
                                </div>
                            </div>
                            <div class="col-lg-9">
                                <textarea type="text" class="form-control @if ($errors->has('remarks')) is-invalid @endif" name="remarks">{{ old('remarks') ?: $fundRequest->remarks }}</textarea>
                                @if ($errors->has('remarks'))
                                    <div class="fv-plugins-message-container invalid-feedback">
                                        <div data-field="remarks">{!! $errors->first('remarks') !!}</div>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="mb-2 row">
                            <div class="col-lg-3">
                                <div class="d-flex align-items-start h-100">
                                    <label for="validationRemarks" class="form-label">Attachment</label>
                                </div>
                            </div>
                            <div class="col-lg-9">
                                <input type="file" name="attachment"
                                    class="form-control js-document-upload @if ($errors->has('attachment')) is-invalid @endif" />
                                <small>Supported file types jpeg/jpg/png/pdf/doc/xls and file size of upto
                                    2MB.</small>
                                @if (file_exists('storage/' . $fundRequest->attachment) && $fundRequest->attachment != '')
                                    <a href="{!! asset('storage/' . $fundRequest->attachment) !!}" target="_blank" class="fs-5"
                                        title="View Attachment">
                                        <i class="bi bi-file-earmark-medical"></i>
                                    </a>
                                @endif
                                @if ($errors->has('attachment'))
                                    <div class="fv-plugins-message-container invalid-feedback">
                                        <div data-field="attachment">{!! $errors->first('attachment') !!}</div>
                                    </div>
                                @endif
                            </div>
                        </div>
                        {{-- <div class="mb-2 row">
                                    <div class="col-lg-3">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="validationRemarks" class="form-label required-label">Send
                                                To</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-9">
                                        @php $selectedApproverId = old('approver_id') ?: $fundRequest->approver_id; @endphp
                                        <select name="approver_id"
                                                class="select2 form-control
                                            @if ($errors->has('approver_id')) is-invalid @endif"
                                                data-width="100%">
                                            <option value="">Select an Approver</option>
                                            @foreach ($supervisors as $approver)
                                                <option value="{{ $approver->id }}"
                                                    {{ $approver->id == $selectedApproverId ? 'selected' : '' }}>
                                                    {{ $approver->full_name }}</option>
                                            @endforeach
                                        </select>
                                        @if ($errors->has('approver_id'))
                                            <div class="fv-plugins-message-container invalid-feedback">
                                                <div data-field="approver_id">
                                                    {!! $errors->first('approver_id') !!}
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div> --}}

                        <div class="mb-2 row">
                            <div class="col-lg-3">
                                <div class="d-flex align-items-start h-100">
                                    <label for="acccde" class="form-label required-label">Checker
                                    </label>
                                </div>
                            </div>

                            <div class="col-lg-3">
                                @php $selectedReviewerId = !empty($fundRequest->checker_id) ? $fundRequest->checker_id : old('checker_id') @endphp
                                <select name="checker_id"
                                    class="select2 form-control @if ($errors->has('checker_id')) is-invalid @endif">
                                    <option value="">Select Checker</option>
                                    @foreach ($reviewers as $reviewer)
                                        <option value="{{ $reviewer->id }}"
                                            @if ($selectedReviewerId == $reviewer->id) selected @endif>
                                            {{ $reviewer->getFullName() }}</option>
                                    @endforeach
                                </select>
                                @if ($errors->has('checker_id'))
                                    <div class="fv-plugins-message-container invalid-feedback">
                                        <div data-field="checker_id">
                                            {!! $errors->first('checker_id') !!}
                                        </div>
                                    </div>
                                @endif
                            </div>
                            {{-- <div class="col-lg-3"> --}}
                            {{--     <div class="d-flex align-items-start justify-content-end h-100"> --}}
                            {{--         <label for="acccde" class="form-label required-label">Approver</label> --}}
                            {{--     </div> --}}
                            {{-- </div> --}}
                            {{-- <div class="col-lg-3"> --}}
                            {{--     @php $selectedApproverId = !empty($fundRequest->approver_id) ? $fundRequest->approver_id : old('approver_id') @endphp --}}
                            {{--     <select name="approver_id" --}}
                            {{--         class="select2 form-control @if ($errors->has('approver_id')) is-invalid @endif"> --}}
                            {{--         <option value="">Select Approver</option> --}}
                            {{--         @foreach ($approvers as $approver) --}}
                            {{--             <option value="{{ $approver->id }}" --}}
                            {{--                 @if ($selectedApproverId == $approver->id) selected @endif> --}}
                            {{--                 {{ $approver->getFullName() }}</option> --}}
                            {{--         @endforeach --}}
                            {{--     </select> --}}
                            {{--     @if ($errors->has('approver_id')) --}}
                            {{--         <div class="fv-plugins-message-container invalid-feedback"> --}}
                            {{--             <div data-field="approver_id"> --}}
                            {{--                 {!! $errors->first('approver_id') !!} --}}
                            {{--             </div> --}}
                            {{--         </div> --}}
                            {{--     @endif --}}
                            {{-- </div> --}}
                        </div>

                        {!! csrf_field() !!}
                        {!! method_field('PUT') !!}
                    </div>
                </div>

                @if ($fundRequest->status_id == config('constant.RETURNED_STATUS'))
                    <div class="card">
                        <div class="card-header text-danger">Return Remark</div>
                        <div class="card-body">{{ $fundRequest->getReturnRemarks() }}</div>
                    </div>
                @endif

                <div class="card">
                    <div class="card-header fw-bold">
                        <div class="d-flex align-items-center add-info justify-content-between">
                            <span> Fund Request Activities</span>
                            @if ($authUser->can('update', $fundRequest))
                                <button data-toggle="modal" class="btn btn-primary btn-sm open-activity-modal-form"
                                    href="{!! route('fund.requests.activities.create', $fundRequest->id) !!}">
                                    <i class="bi-plus"></i> Add New
                                    Activity
                                </button>
                            @endif
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="fundRequestActivityTable">
                                <thead class="thead-light">
                                    <tr>
                                        <th scope="col">{{ __('label.activity') }}</th>
                                        <th scope="col">{{ __('label.estimated-amount') }}</th>
                                        <th scope="col">{{ __('label.budget-amount') }}</th>
                                        <th scope="col">{{ __('label.project-target-unit') }}</th>
                                        <th scope="col">{{ __('label.dip-target-unit') }}</th>
                                        <th scope="col">{{ __('label.budget-variance') }}</th>
                                        <th scope="col">{{ __('label.target-variance') }}</th>
                                        <th scope="col">{{ __('label.remarks') }}/@lang('label.variance-note')</th>
                                        <th style="width: 150px" class="sticky-col">{{ __('label.action') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="6" class="text-end">Total Fund Required</td>
                                        <td colspan="2">
                                            <input type="number" class="form-control" name="required_amount"
                                                readonly="readonly" value="{{ $fundRequest->required_amount }}">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="6" class="text-end">Estimated Surplus/(Deficit)</td>
                                        <td colspan="2">
                                            <select name="surplus_deficit" class="mb-1 form-control" data-width="100%">
                                                <option value="1"
                                                    {{ $fundRequest->surplus_deficit == '1' ? 'selected' : '' }}>
                                                    Surplus
                                                </option>
                                                <option value="2"
                                                    {{ $fundRequest->surplus_deficit == '2' ? 'selected' : '' }}>
                                                    Deficit
                                                </option>
                                            </select>
                                            <input type="number" class="form-control" name="estimated_surplus"
                                                value="{{ $fundRequest->estimated_surplus }}"
                                                placeholder="Estimated Surplus/Deficit" min=0>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="6" class="text-end">Net Amount</td>
                                        <td colspan="2">
                                            <input type="number" class="form-control" name="net_amount"
                                                readonly="readonly" value="{{ $fundRequest->net_amount }}">
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                </div>
                <div class="gap-2 mt-4 justify-content-end d-flex">
                    <button type="submit" name="btn" value="save" class="btn btn-primary btn-sm">
                        Update
                    </button>
                    <button type="submit" name="btn" value="submit" class="btn btn-success btn-sm submit-record"
                        @if (!$authUser->can('submit', $fundRequest)) style="display:none;" @endif>
                        Submit
                    </button>
                    <a href="{!! route('fund.requests.index') !!}" class="btn btn-danger btn-sm">Cancel</a>
                </div>
            </form>
        </div>
    </section>

@stop
