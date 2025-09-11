@extends('layouts.container')

@section('title', 'Create Approve Employee Exit Payable')
@section('page_css')
@endsection
@section('page_js')
    <script type="text/javascript">
        $(document).ready(function () {
            $('#navbarVerticalMenu').find('#update-employees-exit-payable').addClass('active');

        });

        document.addEventListener('DOMContentLoaded', function (e) {
            const form = document.getElementById('payableApproveForm');
            const fv = FormValidation.formValidation(form, {
                fields: {
                    status_id: {
                        validators: {
                            notEmpty: {
                                message: 'Status is required',
                            },
                        },
                    },
                    recommended_to: {
                        validators: {
                            notEmpty: {
                                message: 'Recommended to is required.',
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
                        excluded: function (field, ele, eles) {
                            const statusId = parseInt(form.querySelector('[name="status_id"]').value);
                            return (field === 'recommended_to' && statusId !== 4) || (field === 'status_id' && statusId === 4);
                        },
                    }),
                    icon: new FormValidation.plugins.Icon({
                        valid: 'bi bi-check2-square',
                        invalid: 'bi bi-x-lg',
                        validating: 'bi bi-arrow-repeat',
                    }),
                },
            });

            $(form).on('change', '[name="status_id"]', function (e) {
                fv.revalidateField('status_id');
                if (this.value == 4) {
                    $(form).find('#recommendBlock').show();
                } else {
                    $(form).find('#recommendBlock').hide();
                }
            }).on('change', '[name="recommended_to"]', function (e) {
                fv.revalidateField('recommended_to');
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
                            <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}" class="text-decoration-none text-dark">Home</a></li>
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
                        <div class="card-body">
                            <div class="row mb-2">
                                <div class="col-lg-3">
                                    <div class="d-flex align-items-start h-100">
                                        <label for="" class="form-label required-label">Employee</label>
                                    </div>
                                </div>
                                <div class="col-lg-9">
                                <select class="form-control select2" data-width="100%" name="employee_id" disabled="true">
                                        <option value="">Select Employee</option>
                                        @foreach($employees as $employee)
                                            <option value="{!! $employee->id !!}" @if($employeeExitPayable->employee_id == $employee->id) selected @endif>{{ $employee->getFullName() }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-lg-3">
                                    <div class="d-flex align-items-start h-100">
                                        <label for="" class="form-label required-label">Salary Date From</label>
                                    </div>
                                </div>
                                <div class="col-lg-9">
                                <input type="text" class="form-control" name="salary_date_from" value="{{$employeeExitPayable->salary_date_from}}" placeholder="Salary Date from" readonly>
                                </div>
                            </div>

                            <div class="row mb-2">
                                <div class="col-lg-3">
                                    <div class="d-flex align-items-start h-100">
                                        <label for="" class="form-label required-label">Salary Date To</label>
                                    </div>
                                </div>
                                <div class="col-lg-9">
                                <input type="text" class="form-control" name="salary_date_to" value="{{$employeeExitPayable->salary_date_to}}" placeholder="Salary Date To" readonly>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-lg-3">
                                    <div class="d-flex align-items-start h-100">
                                        <label for="" class="form-label required-label">Leave Balance</label>
                                    </div>
                                </div>
                                <div class="col-lg-9">
                                <input type="number" class="form-control" name="leave_balance" value="{{$employeeExitPayable->leave_balance}}" placeholder="Leave Balance" readonly>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-lg-3">
                                    <div class="d-flex align-items-start h-100">
                                        <label for="" class="form-label required-label">Salary Amount</label>
                                    </div>
                                </div>
                                <div class="col-lg-9">
                                <input type="number" class="form-control" name="salary_amount" value="{{$employeeExitPayable->salary_amount}}" placeholder="Salary Amount" readonly>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-lg-3">
                                    <div class="d-flex align-items-start h-100">
                                        <label for="" class="form-label required-label">Festival Bonus</label>
                                    </div>
                                </div>
                                <div class="col-lg-9">
                                <input type="number" class="form-control" name="festival_bonus" value="{{$employeeExitPayable->festival_bonus}}" placeholder="Festival Bonus" readonly>
                                </div>
                            </div>

                            <div class="row mb-2">
                                <div class="col-lg-3">
                                    <div class="d-flex align-items-start h-100">
                                        <label for="" class="form-label required-label">Festival Bonus Date From</label>
                                    </div>
                                </div>
                                <div class="col-lg-9">
                                <input type="text" class="form-control" name="festival_bonus_from" value="{{$employeeExitPayable->festival_bonus_date_from}}" placeholder="Festival Bonus from" readonly>
                                </div>
                            </div>

                            <div class="row mb-2">
                                <div class="col-lg-3">
                                    <div class="d-flex align-items-start h-100">
                                        <label for="" class="form-label required-label">Festival Bonus Date To</label>
                                    </div>
                                </div>
                                <div class="col-lg-9">
                                <input type="text" class="form-control" name="festival_bonus_to" value="{{$employeeExitPayable->festival_bonus_date_to}}" placeholder="Festival Bonus Date To" readonly>
                                </div>
                            </div>

                            <div class="row mb-2">
                                <div class="col-lg-3">
                                    <div class="d-flex align-items-start h-100">
                                        <label for="" class="form-label required-label">Gratuity Amount</label>
                                    </div>
                                </div>
                                <div class="col-lg-9">
                                <input type="number" class="form-control" name="gratuity_amount" value="{{$employeeExitPayable->gratuity_amount}}" placeholder="Gratuity Amount" readonly>
                                </div>
                            </div>

                            <div class="row mb-2">
                                <div class="col-lg-3">
                                    <div class="d-flex align-items-start h-100">
                                        <label for="" class="form-label required-label">Other Amount</label>
                                    </div>
                                </div>
                                <div class="col-lg-9">
                                <input type="number" class="form-control" name="other_amount" value="{{$employeeExitPayable->other_amount}}" placeholder="Other Amount" readonly>
                                </div>
                            </div>

                            <div class="row mb-2">
                                <div class="col-lg-3">
                                    <div class="d-flex align-items-start h-100">
                                        <label for="" class="form-label required-label">Advance Amount</label>
                                    </div>
                                </div>
                                <div class="col-lg-9">
                                <input type="number" class="form-control" name="advance_amount" value="{{$employeeExitPayable->advance_amount}}" placeholder="Advance Amount" readonly>
                                </div>
                            </div>

                            <div class="row mb-2">
                                <div class="col-lg-3">
                                    <div class="d-flex align-items-start h-100">
                                        <label for="" class="form-label required-label">Loan Amount</label>
                                    </div>
                                </div>
                                <div class="col-lg-9">
                                <input type="number" class="form-control" name="loan_amount" value="{{$employeeExitPayable->loan_amount}}" placeholder="Loan Amount" readonly>
                                </div>
                            </div>

                            <div class="row mb-2">
                                <div class="col-lg-3">
                                    <div class="d-flex align-items-start h-100">
                                        <label for="" class="form-label required-label">Other Payable Amount</label>
                                    </div>
                                </div>
                                <div class="col-lg-9">
                                <input type="number" class="form-control" name="other_payable_amount" value="{{$employeeExitPayable->other_payable_amount}}" placeholder="Other Payable Amount" readonly>
                                </div>
                            </div>
                            @if($employeeExitPayable->deduction_amount)
                                <div class="row mb-2">
                                    <div class="col-lg-3">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="" class="form-label required-label">Deduction Amount</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-9">
                                    <input type="number" class="form-control" name="deduction_amount" value="{{$employeeExitPayable->deduction_amount}}" placeholder="Deduction Amount" readonly>
                                    </div>
                                </div>
                            @endif
                            @if($employeeExitPayable->remarks)
                                <div class="row mb-2">
                                    <div class="col-lg-3">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="" class="m-0">Remarks</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-9">
                                    <input type="text" class="form-control" name="remarks" value="{{$employeeExitPayable->remarks}}" placeholder="Remarks" readonly>
                                    </div>
                                </div>
                            @endif
                            <form action="{{ route('exit.approve.payable.store', $employeeExitPayable->id) }}"
                                id="payableApproveForm" method="post"
                                enctype="multipart/form-data" autocomplete="off">
                                <div class="row mb-2">
                                    <div class="col-lg-3">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="validationleavetype" class="form-label required-label">Status</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-9">
                                        <select name="status_id" class="select2 form-control"
                                                data-width="100%">
                                            <option value="">Select a Status</option>
                                            <option value="2">Return to Requester</option>
                                            {{-- <option value="8">Reject</option> --}}
                                        @if($employeeExitPayable->status_id == 3)
                                            <option value="4">Recommend</option>
                                        @endif
                                            <option value="6">Approve</option>
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
                                <div class="row mb-2" id="recommendBlock" style="display: none;">
                                    <div class="col-lg-3">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="validationleavetype"
                                                    class="form-label required-label">Recommended</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-9">
                                        <select name="recommended_to" class="select2 form-control"
                                                data-width="100%">
                                            <option value="">Select Recommended To</option>
                                            @foreach ($supervisors as $approver)
                                                <option value="{{ $approver->id }}">
                                                    {{ $approver->getFullName() }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @if ($errors->has('recommended_to'))
                                            <div class="fv-plugins-message-container invalid-feedback">
                                                <div data-field="recommended_to">
                                                    {!! $errors->first('recommended_to') !!}
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-lg-3">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="validationRemarks" class="form-label required-label">Remarks</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-9">
                                        <textarea type="text"
                                                    class="form-control @if ($errors->has('log_remarks')) is-invalid @endif"
                                                    name="log_remarks">{{ old('log_remarks') }}</textarea>
                                        @if ($errors->has('log_remarks'))
                                            <div class="fv-plugins-message-container invalid-feedback">
                                                <div
                                                    data-field="log_remarks">{!! $errors->first('log_remarks') !!}</div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                {!! csrf_field() !!}
                                <div class="card-footer border-0 justify-content-end d-flex gap-2">
                                    <button type="submit" name="btn" value="submit" class="btn btn-success btn-sm">
                                        Submit
                                    </button>
                                    <a href="{!! route('exit.approve.payable.index') !!}"
                                    class="btn btn-danger btn-sm">Cancel</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
        </section>
@stop
