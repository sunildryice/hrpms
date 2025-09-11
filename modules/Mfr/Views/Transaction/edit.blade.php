@extends('layouts.container')
@section('title', 'Edit Transaction')

@section('page_js')
    <script type="text/javascript">
        document.addEventListener('DOMContentLoaded', function(e) {
            $('#navbarVerticalMenu').find('#agreement-index').addClass('active');

            const form = document.getElementById('transactionAddForm');
            const fv = FormValidation.formValidation(form, {
                fields: {
                    transaction_type: {
                        validators: {
                            notEmpty: {
                                message: 'Transaction type is required'
                            }
                        }
                    },
                    transaction_date: {
                        validators: {
                            notEmpty: {
                                message: 'Work completion date is required'
                            },
                        },
                        date: {
                            format: 'YYYY-MM-DD',
                            message: 'The value is not a valid date',
                        },
                    },
                    release_amount: {
                        validators: {
                            notEmpty: {
                                message: 'Fund Release Amount is required'
                            }
                        }
                    },
                    expense_amount: {
                        validators: {
                            notEmpty: {
                                message: 'Expense Amount is required'
                            }
                        }
                    },
                    reimbursed_amount: {
                        validators: {
                            notEmpty: {
                                message: 'Reimbursed Amount is required'
                            }
                        }

                    },
                    remarks: {
                        validators: {
                            notEmpty: {
                                message: 'Remarks is required'
                            }
                        }
                    },
                    question_remarks: {
                        validators: {
                            notEmpty: {
                                message: 'Question remarks is required'
                            }
                        }
                    },
                    reviewer_id: {
                        validators: {
                            notEmpty: {
                                message: 'Reviewer is required'
                            }
                        }
                    }

                },
                plugins: {
                    trigger: new FormValidation.plugins.Trigger(),
                    bootstrap5: new FormValidation.plugins.Bootstrap5(),
                    submitButton: new FormValidation.plugins.SubmitButton(),
                    defaultSubmit: new FormValidation.plugins.DefaultSubmit(),
                    excluded: new FormValidation.plugins.Excluded({
                        excluded: function(field, ele) {
                            let selectedType = $('[name="transaction_type"] :selected').val();
                            if (selectedType == 1) {
                                return field == 'expense_amount' || field ==
                                    'reimbursed_amount' || field == 'question_remarks';
                            } else {
                                return field == 'question_remarks' && $(
                                    '[name="questioned_cost"]').val() == 0;
                            }
                        },
                    }),
                    icon: new FormValidation.plugins.Icon({
                        valid: 'bi bi-check2-square',
                        invalid: 'bi bi-x-lg',
                        validating: 'bi bi-arrow-repeat',
                    }),
                },
            });

            $(form.querySelector('[name="transaction_date"]')).datepicker({
                language: 'en-GB',
                autoHide: true,
                format: 'yyyy-mm-dd',
            }).on('change', function(e) {
                //fv.revalidateField('transaction_date');
                //if (form.querySelector('[name="transaction_date"]').value) {
                //   fv.revalidateField('transaction_date');
                //}
            });

            $('[name="transaction_type"]').change(function() {
                if ($(this).val() == 1) {
                    $('.mfr').hide();
                } else {
                    $('.mfr').show();
                }
            })

            function calculateQuestionCost() {
                let questionCost = $('[name="expense_amount"]').val() - $(
                    '[name="reimbursed_amount"]').val();
                $('[name="questioned_cost"]').val(questionCost);
                if (questionCost) {
                    $('.question-label').addClass('required-label');
                } else {
                    $('.question-label').removeClass('required-label');
                }
            }

            $('[name="expense_amount"]').on('change', function() {
                calculateQuestionCost();
            });

            $('[name="reimbursed_amount"]').on('change', function() {
                calculateQuestionCost();
            });

        });
    </script>
@endsection
@section('page-content')
    <div class="p-3 m-content">
        <div class="container-fluid">

            <div class="pb-3 mb-3 page-header border-bottom">
                <div class="d-flex align-items-center">
                    <div class="brd-crms flex-grow-1">
                        <nav aria-label="breadcrumb">
                            <ol class="m-0 breadcrumb">
                                <li class="breadcrumb-item">
                                    <a href="{!! route('dashboard.index') !!}" class="text-decoration-none text-dark">Home</a>
                                </li>
                                <li class="breadcrumb-item">
                                    <a href="{{ route('mfr.agreement.show.transactions', $agreement->id) }}"
                                        class="text-decoration-none">Transactions</a>
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

                    <div class="col-lg-12">
                        <div class="card">
                            <form action="{{ route('mfr.transaction.update', [$transaction->id]) }}" id="transactionAddForm"
                                method="post" enctype="multipart/form-data" autocomplete="off">
                                <div class="card-body">
                                    <div class="mb-2 row">
                                        <div class="col-lg-3">
                                            <div class="d-flex align-items-start h-100">
                                                <label for="validationdistrict" class="form-label">Partner
                                                    organization
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-lg-9">
                                            <input disabled class="form-control"
                                                value={{ $agreement->partnerOrganization->name }} />
                                        </div>
                                    </div>

                                    <div class="mb-2 row">
                                        <div class="col-lg-3">
                                            <div class="d-flex align-items-start h-100">
                                                <label for="validationdistrict" class="form-label">District
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-lg-9">
                                            <input disabled class="form-control"
                                                value={{ $agreement->district->district_name }} />
                                        </div>
                                    </div>

                                    <div class="mb-2 row">
                                        <div class="col-lg-3">
                                            <div class="d-flex align-items-start h-100">
                                                <label for="validationdistrict" class="form-label">Project
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-lg-9">
                                            <input disabled class="form-control" value={{ $agreement->project->title }} />
                                        </div>
                                    </div>

                                    <div class="mb-2 row">
                                        <div class="col-lg-3">
                                            <div class="d-flex align-items-start h-100">
                                                <label for="validationGrant" class="form-label">Grant Agreement
                                                    Number</label>
                                            </div>
                                        </div>
                                        <div class="col-lg-9">
                                            <input type="text"
                                                class="form-control @if ($errors->has('grant_number')) is-invalid @endif"
                                                name="grant_number" value="{{ $agreement->grant_number }}" disabled>
                                        </div>
                                    </div>

                                    <div class="mb-2 row">
                                        <div class="col-lg-3">
                                            <div class="d-flex align-items-start h-100">
                                                <label for="validationpd" class="form-label">Agreement Period
                                                    (from)
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-lg-9">
                                            <input type="text"
                                                class="form-control
                                        @if ($errors->has('effective_from')) is-invalid @endif"
                                                disabled name="effective_from"
                                                value="{{ $agreement->getEffectiveFromDate() }}" />
                                        </div>
                                    </div>

                                    <div class="mb-2 row">
                                        <div class="col-lg-3">
                                            <div class="d-flex align-items-start h-100">
                                                <label for="validationpd" class="form-label">Agreement Period
                                                    (to)
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-lg-9">
                                            <input type="text"
                                                class="form-control
                                        @if ($errors->has('effective_to')) is-invalid @endif"
                                                disabled name="effective_to"
                                                value="{{ $agreement->getEffectiveToDate() }}" />
                                        </div>
                                    </div>

                                    <div class="mb-2 row">
                                        <div class="col-lg-3">
                                            <div class="d-flex align-items-start h-100">
                                                <label for="validationGrant" class="form-label">Approved Budget
                                                    NPR</label>
                                            </div>
                                        </div>
                                        <div class="col-lg-9">
                                            <input type="number"
                                                class="form-control @if ($errors->has('approved_budget')) is-invalid @endif"
                                                name="approved_budget" value="{{ $agreement->getApprovedBudget() }}" disabled>
                                        </div>
                                    </div>


                                    {!! csrf_field() !!}
                                    {!! method_field('PUT') !!}

                                    <div class="mt-5">
                                        <div class="card">
                                            <div class="card-header fw-bold">
                                                Current Transaction Details
                                            </div>

                                            <div class="card-body">
                                                <div class="mb-2 row">
                                                    <div class="col-lg-3">
                                                        <div class="d-flex align-items-start h-100">
                                                            <label for="validationdistrict" class="form-label">Type
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-9">
                                                        <select name="transaction_type" class="select2 form-control"
                                                            data-width="100%">
                                                            <option value="">Select a Type</option>
                                                            <option value="1"
                                                                {{ '1' == (old('transaction_type') ?? $transaction->transaction_type) ? 'selected' : '' }}>
                                                                Fund Release
                                                            </option>
                                                            <option value="2"
                                                                {{ '2' == (old('transaction_type') ?? $transaction->transaction_type) ? 'selected' : '' }}>
                                                                Fund Release/MFR Approval
                                                            </option>
                                                        </select>
                                                        @if ($errors->has('transaction_type'))
                                                            <div class="fv-plugins-message-container invalid-feedback">
                                                                <div data-field="transaction_type">
                                                                    {!! $errors->first('transaction_type') !!}
                                                                </div>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>

                                                <div class="mb-2 row">
                                                    <div class="col-lg-3">
                                                        <div class="d-flex align-items-start h-100">
                                                            <label for="validationpd"
                                                                class="form-label required-label">Transaction Date
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-9">
                                                        <input type="text" class="form-control" readonly
                                                            name="transaction_date"
                                                            value="{{ old('transaction_date') ?? $transaction->transaction_date->format('Y-m-d') }}" />
                                                        @if ($errors->has('transaction_date'))
                                                            <div class="fv-plugins-message-container invalid-feedback">
                                                                <div data-field="transaction_date">{!! $errors->first('transaction_date') !!}
                                                                </div>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>

                                                <div class="mb-2 row">
                                                    <div class="col-lg-3">
                                                        <div class="d-flex align-items-start h-100">
                                                            <label for="validationGrant"
                                                                class="form-label required-label">{{ __('label.advance-released') }}
                                                                NPR</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-9">
                                                        <input type="number"
                                                            class="form-control @if ($errors->has('release_amount')) is-invalid @endif"
                                                            name="release_amount"
                                                            value="{{ old('release_amount') ?? $transaction->release_amount }}">
                                                        @if ($errors->has('release_amount'))
                                                            <div class="fv-plugins-message-container invalid-feedback">
                                                                <div data-field="release_amount">{!! $errors->first('release_amount') !!}
                                                                </div>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>

                                                <div class="mfr"
                                                    @if ($transaction->transaction_type == '1') style="display:none;" @endif>
                                                    <div class="mb-2 row">
                                                        <div class="col-lg-3">
                                                            <div class="d-flex align-items-start h-100">
                                                                <label for="validationGrant"
                                                                    class="form-label required-label">{{ __('label.mfr-expenditure') }}
                                                                    NPR</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-9">
                                                            <input type="number"
                                                                class="form-control @if ($errors->has('expense_amount')) is-invalid @endif"
                                                                name="expense_amount"
                                                                value="{{ old('expense_amount') ?? $transaction->expense_amount }}">
                                                            @if ($errors->has('expense_amount'))
                                                                <div class="fv-plugins-message-container invalid-feedback">
                                                                    <div data-field="expense_amount">
                                                                        {!! $errors->first('expense_amount') !!}
                                                                    </div>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>

                                                    <div class="mb-2 row">
                                                        <div class="col-lg-3">
                                                            <div class="d-flex align-items-start h-100">
                                                                <label for="validationGrant"
                                                                    class="form-label required-label">{{ __('label.expenditure-reimbursed') }}
                                                                    NPR</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-3">
                                                            <input type="number"
                                                                class="form-control @if ($errors->has('reimbursed_amount')) is-invalid @endif"
                                                                name="reimbursed_amount"
                                                                value="{{ old('reimbursed_amount') ?? $transaction->reimbursed_amount }}">
                                                            @if ($errors->has('reimbursed_amount'))
                                                                <div class="fv-plugins-message-container invalid-feedback">
                                                                    <div data-field="reimbursed_amount">
                                                                        {!! $errors->first('reimbursed_amount') !!}
                                                                    </div>
                                                                </div>
                                                            @endif
                                                        </div>

                                                        <div class="col-lg-2">
                                                            <div class="d-flex align-items-start h-100">
                                                                <label for="validationdd" class="form-label">Questioned
                                                                    Cost</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-3">
                                                            <input readonly class="form-control" name="questioned_cost"
                                                                value="{{ $transaction->expense_amount - $transaction->reimbursed_amount }}" />
                                                        </div>

                                                    </div>
                                                </div>

                                                <div class="mb-2 row">
                                                    <div class="col-lg-3">
                                                        <div class="d-flex align-items-start h-100">
                                                            <label for="validationremarks"
                                                                class="form-label required-label">Remarks
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-9">
                                                        <textarea type="text" class="form-control" name="remarks">{{ old('remarks') ?? $transaction->remarks }}</textarea>
                                                        @if ($errors->has('remarks'))
                                                            <div class="fv-plugins-message-container invalid-feedback">
                                                                <div data-field="remarks">{!! $errors->first('remarks') !!}
                                                                </div>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>

                                                <div class="mb-2 row mfr"
                                                    @if ($transaction->transaction_type == '1') style="display:none;" @endif>
                                                    <div class="col-lg-3">
                                                        <div class="d-flex align-items-start h-100">
                                                            <label for="validationquestion_remarks"
                                                                class="form-label question-label @if ($transaction->getQuestionedCost()) required-label @endif">Comments
                                                                on question cost</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-9">
                                                        <textarea type="text" class="form-control" name="question_remarks">{{ old('question_remarks') ?? $transaction->question_remarks }}</textarea>
                                                        @if ($errors->has('question_remarks'))
                                                            <div class="fv-plugins-message-container invalid-feedback">
                                                                <div data-field="question_remarks">{!! $errors->first('question_remarks') !!}
                                                                </div>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>

                                                <div class="mb-2 row">
                                                    <div class="col-lg-3">
                                                        <div class="d-flex align-items-start h-100">
                                                            <label for="validationdistrict"
                                                                class="form-label required-label">Reviewer
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-9">
                                                        <select name="reviewer_id" class="select2 form-control"
                                                            data-width="100%">
                                                            <option value="">Select a Reviewer</option>
                                                            @foreach ($reviewers as $reviewer)
                                                                <option value="{{ $reviewer->id }}"
                                                                    @if ((old('reviewer_id') ?? $transaction->reviewer_id) == $reviewer->id) selected @endif>
                                                                    {{ $reviewer->getFullName() }}</option>
                                                            @endforeach
                                                        </select>
                                                        @if ($errors->has('reviewer_id'))
                                                            <div class="fv-plugins-message-container invalid-feedback">
                                                                <div data-field="reviewer_id">
                                                                    {!! $errors->first('reviewer_id') !!}
                                                                </div>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>

                                                <div class="gap-2 border-0 card-footer justify-content-end d-flex">
                                                    <button type="submit" name="btn" value="save"
                                                        class="btn btn-primary btn-sm">Update
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                        @include('Attachment::index', [
                                            'modelType' => 'Modules\Mfr\Models\Transaction',
                                            'modelId' => $transaction->id,
                                        ])

                                        @include('Mfr::Partials.transactions')

                                        @if ($transaction->status_id == config('constant.RETURNED_STATUS'))
                                            <div class="card mt-2">
                                                <div class="card-header fw-bold">
                                                    Return Remarks
                                                </div>
                                                <div class="card-body">
                                                    {{ $transaction->logs?->where('status_id', config('constant.RETURNED_STATUS'))?->first()?->log_remarks }}
                                                </div>
                                            </div>
                                        @endif

                                    </div>
                                    <div class="gap-2 border-0 card-footer justify-content-end d-flex">
                                        <button type="submit" name="btn" value="submit"
                                            class="btn btn-success btn-sm">
                                            Submit
                                        </button>
                                        <a href="{!! route('mfr.agreement.show.transactions', $agreement->id) !!}" class="btn btn-danger btn-sm">Cancel</a>
                                    </div>
                            </form>
                        </div>

                    </div>
                </div>
            </section>

        </div>
    </div>

@stop
