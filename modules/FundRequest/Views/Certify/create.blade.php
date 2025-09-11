@php $authUser = auth()->user();@endphp
@extends('layouts.container')

@section('title', 'Certify Fund Request')

@section('page_js')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#navbarVerticalMenu').find('#certify-fund-requests-menu').addClass('active');
            console.log(@json($errors->all()))
            var oTable = $('#fundRequestActivityTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('fund.requests.activities.index', $fundRequest->id) }}",
                bFilter: false,
                bPaginate: false,
                bInfo: false,
                columns: [{
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
                            $('#fundRequestActivityTable').find(
                                '[name="required_amount"]').val(response
                                .fundRequest.required_amount);
                            $('#fundRequestActivityTable').find('[name="net_amount"]')
                                .val(response
                                    .fundRequest.net_amount);

                            oTable.ajax.reload();
                        }
                        ajaxSubmit($url, 'POST', data, successCallback);
                    });

                    $(form).on('change', '[name="activity_code_id"]', function(e) {
                        fv.revalidateField('activity_code_id');
                    });
                });
            });
        });

        document.addEventListener('DOMContentLoaded', function(e) {

            const form = document.getElementById('fundRequestReviewForm');
            const fv = FormValidation.formValidation(form, {
                fields: {
                    status_id: {
                        validators: {
                            notEmpty: {
                                message: 'Status is required',
                            },
                        },
                    },
                    reviewer_id: {
                        validators: {
                            notEmpty: {
                                message: 'Certifier is required.',
                            },
                        },
                    },
                    log_remarks: {
                        validators: {
                            notEmpty: {
                                message: 'The remarks is required',
                            },
                        },
                    },
                },
                plugins: {
                    trigger: new FormValidation.plugins.Trigger(),
                    bootstrap5: new FormValidation.plugins.Bootstrap5(),
                    submitButton: new FormValidation.plugins.SubmitButton(),
                    defaultSubmit: new FormValidation.plugins.DefaultSubmit(),
                    excluded: new FormValidation.plugins.Excluded({
                        excluded: function(field, ele, eles) {
                            const statusId = parseInt(form.querySelector('[name="status_id"]')
                                .value);
                            const submitButton = $('[name="btn"]:focus').data('submit');
                            if (field == 'status_id' || field == 'log_remarks') {
                                return submitButton === 'save';
                            }
                            return (field === 'reviewer_id' && statusId !== 14) || (field ===
                                'status_id' && statusId === 14);
                        },
                    }),
                    icon: new FormValidation.plugins.Icon({
                        valid: 'bi bi-check2-square',
                        invalid: 'bi bi-x-lg',
                        validating: 'bi bi-arrow-repeat',
                    }),
                },
            });

            $(form).on('change', '[name="surplus_deficit"]', function() {
                calculateNetAmount($(this));
            }).on('change', '[name="estimated_surplus"]', function() {
                calculateNetAmount($(this));
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

            $(form).on('change', '[name="status_id"]', function(e) {
                fv.revalidateField('status_id');
                if (this.value == 14) {
                    $(form).find('#certifyBlock').show();
                } else {
                    $(form).find('#certifyBlock').hide();
                }
            }).on('change', '[name="reviewer_id"]', function(e) {
                fv.revalidateField('reviewer_id');
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
                                    <a href="{{ route('review.fund.requests.index') }}" class="text-decoration-none">Fund
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
                    <div class="col-lg-3">
                        <div class="card">
                            <div class="card-header fw-bold">
                                Fund Request Details
                            </div>
                            @include('FundRequest::Partials.detail')
                        </div>
                    </div>
                    <div class="col-lg-9">
                        <form action="{{ route('certify.fund.requests.store', $fundRequest->id) }}" id="fundRequestReviewForm"
                            method="post" enctype="multipart/form-data" autocomplete="off">
                            <div class="card">
                                <div class="card-header fw-bold">
                                    Fund Request Activities
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="table-responsive">
                                                <table class="table" id="fundRequestActivityTable">
                                                    <thead class="thead-light">
                                                        <tr>
                                                            <th scope="col">{{ __('label.activity') }}</th>
                                                            <th scope="col" style="width: 120px;" rel="tooltip"
                                                                title="{{ __('label.estimated-amount') }}">EA
                                                            </th>
                                                            <th scope="col" style="width: 120px;" rel="tooltip"
                                                                title="{{ __('label.budget-amount') }}">BA
                                                            </th>
                                                            <th scope="col" style="width: 120px;" rel="tooltip"
                                                                title="{{ __('label.project-target-unit') }}">PTU
                                                            </th>
                                                            <th scope="col" style="width: 120px;">
                                                                {{ __('label.dip-target-unit') }}</th>
                                                            <th scope="col">{{ __('label.budget-variance') }}</th>
                                                            <th scope="col">{{ __('label.target-variance') }}</th>
                                                            <th scope="col">{{ __('label.remarks-variance-note') }}</th>
                                                            <th style="" class="sticky-col">
                                                                {{ __('label.action') }}</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                    </tbody>
                                                    <tfoot>
                                                        <tr>
                                                            <td colspan="7" class="text-end">Total Fund Required</td>
                                                            <td colspan="2">
                                                                <input type="number" class="form-control"
                                                                    name="required_amount" readonly="readonly"
                                                                    value="{{ $fundRequest->required_amount }}">
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="7" class="text-end">Estimated Surplus/(Deficit)
                                                            </td>
                                                            <td colspan="2">
                                                                <select name="surplus_deficit" class="form-control mb-1"
                                                                    data-width="100%">
                                                                    <option value="1"
                                                                        {{ $fundRequest->surplus_deficit == '1' ? 'selected' : '' }}>
                                                                        Surplus
                                                                    </option>
                                                                    <option value="2"
                                                                        {{ $fundRequest->surplus_deficit == '2' ? 'selected' : '' }}>
                                                                        Deficit
                                                                    </option>
                                                                </select>
                                                                <input type="number" class="form-control"
                                                                    name="estimated_surplus"
                                                                    value="{{ $fundRequest->estimated_surplus }}"
                                                                    placeholder="Estimated Surplus/Deficit" min=0>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="7" class="text-end">Net Amount</td>
                                                            <td colspan="2">
                                                                <input type="number" class="form-control" name="net_amount"
                                                                    readonly="readonly"
                                                                    value="{{ $fundRequest->net_amount }}">
                                                            </td>
                                                        </tr>
                                                    </tfoot>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card shadow-sm border rounded mt-2">
                                <div class="card-header fw-bold">
                                    Fund Request Process
                                </div>

                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-lg-12">
                                            @foreach ($fundRequest->logs as $log)
                                                <div class="d-flex py-2 flex-row gap-2 mb-2 border-bottom ">
                                                    <div width="40" height="40"
                                                        class="rounded-circle mr-3 user-icon">
                                                        <i class="bi-person-circle fs-5"></i>
                                                    </div>
                                                    <div class="w-100">
                                                        <div
                                                            class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                                                            <div
                                                                class="d-flex flex-column flex-md-row align-items-start align-items-md-center gap-md-2 mb-2 mb-md-0">
                                                                <span class="me-2">{{ $log->getCreatedBy() }}</span>
                                                                <span
                                                                    class="badge bg-primary c-badge">{!! $log->createdBy->employee->latestTenure->getDesignationName() !!}</span>
                                                            </div>
                                                            <small>{{ $log->created_at->diffForHumans() }}</small>
                                                        </div>
                                                        <p class="text-justify comment-text mb-0 mt-1">
                                                            {{ $log->log_remarks }}
                                                        </p>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                        <div class="col-lg-12">
                                            <div class="row mb-2">
                                                <div class="col-lg-3">
                                                    <div class="d-flex align-items-start h-100">
                                                        <label for="validationleavetype"
                                                            class="form-label required-label">Status</label>
                                                    </div>
                                                </div>
                                                <div class="col-lg-9">
                                                    <select name="status_id" class="select2 form-control"
                                                        data-width="100%">
                                                        <option value="">Select a Status</option>
                                                        <option value="{{ config('constant.RETURNED_STATUS') }}">Return to
                                                            Requester</option>
                                                        <option value="{{ config('constant.REJECTED_STATUS') }}">Reject
                                                        </option>
                                                        <option value="{{ config('constant.VERIFIED2_STATUS') }}">Certify
                                                        </option>
                                                    </select>
                                                    @if ($errors->has('status_id'))
                                                        <div class="fv-plugins-message-container invalid-feedback">
                                                            <div data-field="status_id">
                                                                {!! $errors->first('status_id') !!}
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="row mb-2" id="certifyBlock" style="display:none ;">
                                                <div class="col-lg-3">
                                                    <div class="d-flex align-items-start h-100">
                                                        <label for="validationleavetype"
                                                            class="form-label required-label">Send To</label>
                                                    </div>
                                                </div>
                                                <div class="col-lg-9">
                                                    <select name="reviewer_id" class="select2 form-control"
                                                        data-width="100%">
                                                        <option value="">Select Reviewers</option>
                                                        @foreach ($reviewers as $approver)
                                                            <option value="{{ $approver->id }}">
                                                                {{ $approver->getFullName() }}
                                                            </option>
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
                                            </div>
                                            <div class="row mb-2">
                                                <div class="col-lg-3">
                                                    <div class="d-flex align-items-start h-100">
                                                        <label for="validationRemarks"
                                                            class="form-label required-label">Remarks</label>
                                                    </div>
                                                </div>
                                                <div class="col-lg-9">
                                                    <textarea type="text" class="form-control @if ($errors->has('log_remarks')) is-invalid @endif" name="log_remarks">{{ old('log_remarks') }}</textarea>
                                                    @if ($errors->has('log_remarks'))
                                                        <div class="fv-plugins-message-container invalid-feedback">
                                                            <div data-field="log_remarks">{!! $errors->first('log_remarks') !!}</div>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                            {!! csrf_field() !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer border-0 justify-content-end d-flex gap-2">
                                    {{-- <button type="submit" name="btn" value="save" class="btn btn-primary btn-sm" --}}
                                    {{--     data-submit="save"> --}}
                                    {{--     Update --}}
                                    {{-- </button> --}}
                                    <button type="submit" name="btn" value="submit" class="btn btn-success btn-sm"
                                        data-submit="submit">
                                        Submit
                                    </button>
                                    <a href="{!! route('review.fund.requests.index') !!}" class="btn btn-danger btn-sm">Cancel</a>
                                </div>
                        </form>
                    </div>
                </div>
        </div>
        </section>

    </div>
    </div>
@stop
