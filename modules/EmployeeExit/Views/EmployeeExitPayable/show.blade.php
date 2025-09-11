@extends('layouts.container')

@section('title', 'Show Employee Exit Payable')
@section('page_css')
@endsection
@section('page_js')
    <script type="text/javascript">
        $(document).ready(function () {
            $('#navbarVerticalMenu').find('#create-employee-exit-payable').addClass('active');

        });
    </script>
@endsection

@section('page-content')

        <div class="page-header pb-3 mb-3 border-bottom">
            <div class="d-flex flex-column flex-lg-row align-items-start align-items-lg-center gap-2">
            <div class="brd-crms flex-grow-1">
                <nav aria-label="breadcrumb">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}"
                                                           class="text-decoration-none text-dark">Home</a></li>
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
                    <div class="rounded border shadow-sm vertical-navigation sm-menu-vr pt-3 pb-3 bg-white">
                        <ul class="m-0 list-unstyled">
                            @if($employeeExitPayable->employee_id == $authUser->employee_id)
                                <li class="nav-item"><a
                                        href="@if($authUser->can('update', $employeeExitPayable->exitHandOverNote)){{route('exit.employee.handover.note.edit')}}  @else {{route('exit.employee.handover.note.show')}} @endif"
                                        class="nav-link  text-decoration-none"><i
                                            class="nav-icon bi-info-circle"></i> Handover Note</a></li>
                                <li class="nav-item"><a
                                        href="@if($authUser->can('update', $employeeExitPayable->exitHandOverNote->exitInterview)) {{route('exit.employee.interview.edit')}} @else {{route('exit.employee.interview.show')}} @endif"
                                        class="nav-link text-decoration-none"><i
                                            class="nav-icon bi-people"></i> Exit interview</a></li>
                            @endif
                            <li class="nav-item"><a href="{{route('exit.payable.show',$employeeExitPayable->id)}}"
                                                    class="nav-link active text-decoration-none"><i
                                        class="nav-icon bi bi-currency-exchange"></i>Payable</a></li>

                        </ul>
                    </div>
                </div>
                <div class="col-lg-9">
                    <div class="card">
                        <div class="card-body">
                            <div class="row mb-2">
                                <div class="col-lg-3">
                                    <div class="d-flex align-items-start h-100">
                                        <label class="form-label required-label">Employee</label>
                                    </div>
                                </div>
                                <div class="col-lg-9">
                                    <select class="form-control select2" data-width="100%" name="employee_id"
                                            disabled="true">
                                        <option value="">Select Employee</option>
                                        @foreach($employees as $employee)
                                            <option value="{!! $employee->id !!}"
                                                    @if($employeeExitPayable->employee_id == $employee->id) selected @endif>{{ $employee->getFullName() }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-lg-3">
                                    <div class="d-flex align-items-start h-100">
                                        <label class="form-label required-label">Salary Date From</label>
                                    </div>
                                </div>
                                <div class="col-lg-9">
                                    <input type="text" class="form-control" name="salary_date_from"
                                           value="{{$employeeExitPayable->salary_date_from}}"
                                           placeholder="Salary Date from" readonly>
                                </div>
                            </div>

                            <div class="row mb-2">
                                <div class="col-lg-3">
                                    <div class="d-flex align-items-start h-100">
                                        <label class="form-label required-label">Salary Date To</label>
                                    </div>
                                </div>
                                <div class="col-lg-9">
                                    <input type="text" class="form-control" name="salary_date_to"
                                           value="{{$employeeExitPayable->salary_date_to}}" placeholder="Salary Date To"
                                           readonly>
                                </div>
                            </div>

                            <div class="row mb-2">
                                <div class="col-lg-3">
                                    <div class="d-flex align-items-start h-100">
                                        <label class="form-label required-label">Leave Balance</label>
                                    </div>
                                </div>
                                <div class="col-lg-9">
                                    <input type="number" class="form-control" name="leave_balance"
                                           value="{{$employeeExitPayable->leave_balance}}" placeholder="Leave Balance"
                                           readonly>
                                </div>
                            </div>

                            <div class="row mb-2">
                                <div class="col-lg-3">
                                    <div class="d-flex align-items-start h-100">
                                        <label class="form-label required-label">Salary Amount</label>
                                    </div>
                                </div>
                                <div class="col-lg-9">
                                    <input type="number" class="form-control" name="salary_amount"
                                           value="{{$employeeExitPayable->salary_amount}}" placeholder="Salary Amount"
                                           readonly>
                                </div>
                            </div>

                            <div class="row mb-2">
                                <div class="col-lg-3">
                                    <div class="d-flex align-items-start h-100">
                                        <label class="form-label required-label">Festival Bonus</label>
                                    </div>
                                </div>
                                <div class="col-lg-9">
                                    <input type="number" class="form-control" name="festival_bonus"
                                           value="{{$employeeExitPayable->festival_bonus}}" placeholder="Festival Bonus"
                                           readonly>
                                </div>
                            </div>

                            <div class="row mb-2">
                                <div class="col-lg-3">
                                    <div class="d-flex align-items-start h-100">
                                        <label class="form-label required-label">Festival Bonus From</label>
                                    </div>
                                </div>
                                <div class="col-lg-9">
                                    <input type="text" class="form-control" name="salary_date_from"
                                           value="{{$employeeExitPayable->festival_bonus_date_from}}"
                                           placeholder="Festival Bonus Date from" readonly>
                                </div>
                            </div>

                            <div class="row mb-2">
                                <div class="col-lg-3">
                                    <div class="d-flex align-items-start h-100">
                                        <label class="form-label required-label">Festival Bonus Date To</label>
                                    </div>
                                </div>
                                <div class="col-lg-9">
                                    <input type="text" class="form-control" name="salary_date_to"
                                           value="{{$employeeExitPayable->festival_bonus_date_to}}" placeholder="Festival Bonus Date To"
                                           readonly>
                                </div>
                            </div>

                            <div class="row mb-2">
                                <div class="col-lg-3">
                                    <div class="d-flex align-items-start h-100">
                                        <label class="form-label required-label">Gratuity Amount</label>
                                    </div>
                                </div>
                                <div class="col-lg-9">
                                    <input type="number" class="form-control" name="gratuity_amount"
                                           value="{{$employeeExitPayable->gratuity_amount}}"
                                           placeholder="Gratuity Amount" readonly>
                                </div>
                            </div>

                            <div class="row mb-2">
                                <div class="col-lg-3">
                                    <div class="d-flex align-items-start h-100">
                                        <label class="form-label required-label">Other Amount</label>
                                    </div>
                                </div>
                                <div class="col-lg-9">
                                    <input type="number" class="form-control" name="other_amount"
                                           value="{{$employeeExitPayable->other_amount}}" placeholder="Other Amount"
                                           readonly>
                                </div>
                            </div>

                            <div class="row mb-2">
                                <div class="col-lg-3">
                                    <div class="d-flex align-items-start h-100">
                                        <label class="form-label required-label">Advance Amount</label>
                                    </div>
                                </div>
                                <div class="col-lg-9">
                                    <input type="number" class="form-control" name="advance_amount"
                                           value="{{$employeeExitPayable->advance_amount}}" placeholder="Advance Amount"
                                           readonly>
                                </div>
                            </div>

                            <div class="row mb-2">
                                <div class="col-lg-3">
                                    <div class="d-flex align-items-start h-100">
                                        <label class="form-label required-label">Loan Amount</label>
                                    </div>
                                </div>
                                <div class="col-lg-9">
                                    <input type="number" class="form-control" name="loan_amount"
                                           value="{{$employeeExitPayable->loan_amount}}" placeholder="Loan Amount"
                                           readonly>
                                </div>
                            </div>

                            <div class="row mb-2">
                                <div class="col-lg-3">
                                    <div class="d-flex align-items-start h-100">
                                        <label class="form-label required-label">Other Payable Amount</label>
                                    </div>
                                </div>
                                <div class="col-lg-9">
                                    <input type="number" class="form-control" name="other_payable_amount"
                                           value="{{$employeeExitPayable->other_payable_amount}}"
                                           placeholder="Other Payable Amount" readonly>
                                </div>
                            </div>
                            @if($employeeExitPayable->deduction_amount)
                                <div class="row mb-2">
                                    <div class="col-lg-3">
                                        <div class="d-flex align-items-start h-100">
                                            <label class="form-label required-label">Deduction Amount</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-9">
                                        <input type="number" class="form-control" name="deduction_amount"
                                               value="{{$employeeExitPayable->deduction_amount}}"
                                               placeholder="Deduction Amount" readonly>
                                    </div>
                                </div>
                            @endif
                            @if($employeeExitPayable->remarks)
                                <div class="row mb-2">
                                    <div class="col-lg-3">
                                        <div class="d-flex align-items-start h-100">
                                            <label class="form-label required-label">Remarks</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-9">
                                        <input type="text" class="form-control" name="remarks"
                                               value="{{$employeeExitPayable->remarks}}" placeholder="Remarks" readonly>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                    @if($employeeExitPayable->logs->count()>0)
                        <div class="card">
                            <div class="card-header fw-bold">
                                Payable Process
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-12">
                                        @foreach($employeeExitPayable->logs as $log)
                                            <div class="d-flex py-2 flex-row gap-2 mb-2 border-bottom ">
                                                <div width="40" height="40" class="rounded-circle mr-3 user-icon">
                                                    <i class="bi-person-circle fs-5"></i>
                                                </div>
                                                <div class="w-100">
                                                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center gap-md-2 mb-2 mb-md-0">
                                                            <span class="me-2">{{ $log->getCreatedBy() }}</span>
                                                            <span
                                                                class="badge bg-primary c-badge">{!! $log->createdBy->employee->latestTenure->getDesignationName() !!}</span>
                                                        </div>
                                                        <small title="{{$log->created_at}}">{{ $log->created_at->format('M d, Y h:i A') }}</small>
                                                    </div>
                                                    <p class="text-justify comment-text mb-0 mt-1">
                                                        {{ $log->log_remarks }}
                                                    </p>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </section>
@stop
