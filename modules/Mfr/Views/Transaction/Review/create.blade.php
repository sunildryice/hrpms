@extends('layouts.container')

@section('title', 'Review Transaction')

@section('page_js')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#navbarVerticalMenu').find('#review-transaction').addClass('active');
        });

        document.addEventListener('DOMContentLoaded', function(e) {
            const form = document.getElementById('travelClaimApproveForm');
            const fv = FormValidation.formValidation(form, {
                fields: {
                    status_id: {
                        validators: {
                            notEmpty: {
                                message: 'Status is required',
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
                    verifier_id: {
                        validators: {
                            notEmpty: {
                                message: 'The verifier is required',
                            },
                        }
                    }
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
                            return (field === 'verifier_id' && statusId !== 11) || (field ===
                                'status_id' && statusId === 11);
                        },
                    }),
                    icon: new FormValidation.plugins.Icon({
                        valid: 'bi bi-check2-square',
                        invalid: 'bi bi-x-lg',
                        validating: 'bi bi-arrow-repeat',
                    }),
                },
            });

            $(form).on('change', '[name="status_id"]', function(e) {
                fv.revalidateField('status_id');
                if (this.value == 11) {
                    $(form).find('#recommendBlock').show();
                } else {
                    $(form).find('#recommendBlock').hide();
                }
            }).on('change', '[name="verifier_id"]', function(e) {
                fv.revalidateField('verifier_id');
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
                            <a href="{{ route('mfr.transaction.review.index') }}"
                                class="text-decoration-none text-dark">Review Transaction
                            </a>
                        </li>
                        <li class="breadcrumb-item" aria-current="page">@yield('title')</li>
                    </ol>
                </nav>
                <h4 class="m-0 mt-1 lh1 fs-6 text-uppercase fw-bold text-primary">@yield('title')</h4>
            </div>
        </div>
    </div>

    <section class="registration">
        <div class="row">
            <div class="col-lg-3">
                <div class="card">
                    <div class="card-header fw-bold">
                        Agreement Details
                    </div>
                    @include('Mfr::Partials.agreement-detail')
                </div>
                <div class="card">
                    <div class="card-header fw-bold">
                        Current Transaction Details
                    </div>
                    @include('Mfr::Partials.transaction-detail')
                </div>
            </div>
            <div class="col-lg-9">
                @include('Mfr::Partials.transactions')

                <form action="{{ route('mfr.transaction.review.store', $transaction->id) }}" id="travelClaimApproveForm"
                    method="post" enctype="multipart/form-data" autocomplete="off">
                    <div class="card">
                        <div class="card-header">Process</div>
                        <div class="card-body">
                            <div class="c-b">
                                @foreach ($transaction->logs as $log)
                                    <div class="flex-row gap-2 py-2 mb-2 d-flex border-bottom">
                                        <div width="40" height="40" class="mr-3 rounded-circle user-icon">
                                            <i class="bi-person"></i>
                                        </div>
                                        <div class="w-100">
                                            <div
                                                class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                                                <div
                                                    class="mb-2 d-flex flex-column flex-md-row align-items-start align-items-md-center gap-md-2 mb-md-0">
                                                    <label class="mb-0 form-label">{{ $log->getCreatedBy() }}</label>
                                                    <span class="badge bg-primary c-badge">
                                                        {!! $log->createdBy->employee->latestTenure->getDesignationName() !!}
                                                    </span>
                                                </div>
                                                <small>{{ $log->created_at->diffForHumans() }}</small>
                                            </div>
                                            <p class="mt-1 mb-0 text-justify comment-text">
                                                {{ $log->log_remarks }}
                                            </p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <div class="pt-4 border-top">
                                <div class="mb-2 row">
                                    <div class="col-lg-3">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="validationleavetype"
                                                class="form-label required-label">Status</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-9">
                                        <select name="status_id" class="select2 form-control" data-width="100%">
                                            <option value="">Select a Status</option>
                                            <option value="2">Return to Requester</option>
                                            <option value="{{ config('constant.VERIFIED_STATUS') }}">Verify</option>
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

                                <div class="mb-2 row" id="recommendBlock" style="display: none;">
                                    <div class="col-lg-3">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="validationleavetype"
                                                class="form-label required-label">Verifier</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-9">
                                        <select name="verifier_id" class="select2 form-control" data-width="100%">
                                            <option value="">Select a Verifier</option>
                                            @foreach ($verifiers as $approver)
                                                <option value="{{ $approver->id }}">
                                                    {{ $approver->getFullName() }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @if ($errors->has('verifier_id'))
                                            <div class="fv-plugins-message-container invalid-feedback">
                                                <div data-field="verifier_id">
                                                    {!! $errors->first('verifier_id') !!}
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>


                                <div class="mb-2 row">
                                    <div class="col-lg-3">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="validationRemarks" class="form-label required-label">Remarks</label>
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

                        <div class="gap-2 card-footer border-top justify-content-end d-flex">
                            <button type="submit" name="btn" value="submit" class="btn btn-success btn-sm">
                                Submit
                            </button>
                            <a href="{!! route('mfr.transaction.review.index') !!}" class="btn btn-danger btn-sm">Cancel</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
@stop
