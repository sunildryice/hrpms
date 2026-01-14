<div class="card-header fw-bold">Bank Details</div>
@php
    $action = $employee->finance->created_at
        ? route('employees.finance.update', [$employee->id, $employee->finance->id])
        : route('employees.finance.store', $employee->id);

@endphp
<form class="needs-validation" action="{{ $action }}" method="POST" id="financeForm" autocomplete="off"
    enctype="multipart/form-data">
    <div class="card-body">
        {!! csrf_field() !!}

        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="validationward" class="form-label">CIT Number</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="text" class="form-control @if ($errors->has('cit_number')) is-invalid @endif"
                    id="validationward" value="{{ old('cit_number') ?: $employee->finance->cit_number }}"
                    name="cit_number" placeholder="CIT Number">
                @if ($errors->has('cit_number'))
                    <div class="fv-plugins-message-container invalid-feedback">
                        <div data-field="cit_number">{!! $errors->first('cit_number') !!}</div>
                    </div>
                @endif
            </div>
        </div>

        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="validationtole" class="form-label  required-label">Bank Name</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="text" class="form-control @if ($errors->has('bank_name')) is-invalid @endif"
                    id="validationBank" value="{{ old('bank_name') ?: $employee->finance->bank_name }}"
                    placeholder="Bank Name" name="bank_name">
                @if ($errors->has('bank_name'))
                    <div class="fv-plugins-message-container invalid-feedback">
                        <div data-field="bank_name">{!! $errors->first('bank_name') !!}</div>
                    </div>
                @endif
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="validationtole" class="form-label required-label">Branch Name</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="text" class="form-control @if ($errors->has('branch_name')) is-invalid @endif"
                    id="validationBranch" value="{{ old('branch_name') ?: $employee->finance->branch_name }}"
                    placeholder="Branch Name" name="branch_name">
                @if ($errors->has('branch_name'))
                    <div class="fv-plugins-message-container invalid-feedback">
                        <div data-field="branch_name">{!! $errors->first('branch_name') !!}</div>
                    </div>
                @endif
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="validationtole" class="form-label  required-label">Account Holder Name</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="text" class="form-control @if ($errors->has('account_holder_name')) is-invalid @endif"
                    id="validationAccountHolder"
                    value="{{ old('account_holder_name') ?: $employee->finance->account_holder_name }}"
                    placeholder="Account Holder Name" name="account_holder_name">
                @if ($errors->has('account_holder_name'))
                    <div class="fv-plugins-message-container invalid-feedback">
                        <div data-field="account_holder_name">{!! $errors->first('account_holder_name') !!}</div>
                    </div>
                @endif
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="validationtole" class="form-label  required-label">Account Number</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="text" class="form-control @if ($errors->has('account_number')) is-invalid @endif"
                    id="validationAccount" value="{{ old('account_number') ?: $employee->finance->account_number }}"
                    placeholder="Account Number" name="account_number">
                @if ($errors->has('account_number'))
                    <div class="fv-plugins-message-container invalid-feedback">
                        <div data-field="account_number">{!! $errors->first('account_number') !!}</div>
                    </div>
                @endif
            </div>
        </div>
        {{-- <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="forDisability" class="form-label">Disability ?</label>
                </div>
            </div>
            <div class="col-lg-9">
                <div class=" form-switch">
                    <input class="form-check-input" type="checkbox" role="switch" name="disabled" @if
                        ($employee->finance->disabled) checked @endif>
                    <label class="form-check-label" for="disabled"></label>
                </div>
            </div>
        </div> --}}
    </div>
    <div class="card-footer border-0 justify-content-end d-flex gap-2">
        <button type="submit" class="btn btn-primary btn-sm">
            @if ($employee->finance->created_at)
                Update
                {!! method_field('PUT') !!}
            @else
                Save
            @endif
        </button>
    </div>
</form>
@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function (e) {
            const fv = FormValidation.formValidation(document.getElementById('financeForm'), {
                fields: {
                    ssf_number: {
                        validators: {
                            notEmpty: {
                                message: 'SSF number is required',
                            },
                        },
                    },
                    bank_name: {
                        validators: {
                            notEmpty: {
                                message: 'Bank name is required',
                            },
                        },
                    },
                    branch_name: {
                        validators: {
                            notEmpty: {
                                message: 'Branch name is required',
                            },
                        },
                    },
                    account_holder_name: {
                        validators: {
                            notEmpty: {
                                message: 'Account holder name is required',
                            },
                        },
                    },
                    account_number: {
                        validators: {
                            notEmpty: {
                                message: 'Account number is required',
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
        });
    </script>
@endpush