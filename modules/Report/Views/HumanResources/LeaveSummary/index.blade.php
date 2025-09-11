@extends('layouts.container')

@section('title', 'Report : Leave Summary Records')

@section('page_js')
    <script type="text/javascript">
        $(document).ready(function () {
            $('#navbarVerticalMenu').find('#leave-summary-report-menu').addClass('active');

            let oldEmployee = '{{request()->get("employee")}}';
            let fiscalYearId = '{{request()->get("fiscal_year")}}';
            let oldMonth = '{{request()->get("month")}}';

            $('#leaveSummaryTable').DataTable({
                scrollX: true,
                "paging": false,
                "searching": false,
            });

            if (oldEmployee) {
                $('#employee').val(oldEmployee).trigger('change');
            }

            if (oldMonth) {
                $('#month').val(oldMonth).trigger('change');
            }

            $('#btn_export').attr('href', '');
            $('#btn_export').attr('href', $('#btn_export').attr('href') +
                '/report/leave/summary/export?employee=' + oldEmployee + '&fiscal_year=' + fiscalYearId + '&month=' + oldMonth);
        });

        $('#btn_reset').on('click', function (e) {
            e.preventDefault();
            $('#employee').val('').trigger('change');
            $('#month').val('').trigger('change');
            $('#month').prop('selectedIndex',0);
        });

    </script>
@endsection

@section('page-content')
    <div class="container-fluid">
        <div class="pb-3 mb-3 border-bottom">
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
                <div class="add-info justify-content-end">
                    <a href="{{ route('report.leave.summary.export') }}" id="btn_export" class="btn btn-primary btn-sm">
                        Export
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <div class="card shadow-sm border rounded c-tabs-content active" id="employee-table" style="overflow: auto;">
            <div class="card-body">
                <form action="{{route('report.leave.summary.index')}}" method="get">
                    <div class="row mb-4" style="align-items: flex-end">
                        <div class="col-md-2">
                            <label class="form-label" for="fiscal_year">Year</label>
                            <select class="form-control" name="fiscal_year" id="fiscal_year">
                                @foreach ($fiscalYears as $year)
                                    <option
                                        value="{{$year->id}}" {{$year->id == request()->get('fiscal_year') ? 'selected' : ''}}>{{$year->title}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label" for="month">Month</label>
                            <select class="form-control select2" name="month" id="month">
                                <option value="">Select all</option>
                                @foreach ($months as $key=>$month)
                                    <option
                                        value="{{$key}}" {{$key == request()->get('month') ? 'selected' : ''}}>{{$month}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label" for="employee">Employee</label>
                            <select class="form-control select2" name="employee" id="employee">
                                <option value="">Select employee...</option>
                                @foreach ($employees as $employee)
                                    @if ($employee->user)
                                        <option
                                            value="{{ $employee->employee_code }}">{{ $employee->getFullName() }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="col">
                            <button type="submit" id="btn_search" class="btn btn-primary btn-sm m-1">Search</button>
                            <button type="reset" id="btn_reset" class="btn btn-secondary btn-sm m-1">Reset</button>
                        </div>
                    </div>
                    <span class="text-danger" id="error_message"></span>
                </form>
                <div class="table-responsive">
                    <table class="table table-bordered" id="leaveSummaryTable">
                        <thead>
                        <tr>
                            <th rowspan="2">{{ __('label.sn') }}</th>
                            {{-- <th rowspan="2">Staff Name</th> --}}
                            <th rowspan="2">Staff Type</th>
                            @foreach($leaveTypes as $leaveType)
                                @if($leaveType->maximum_carry_over > 0)
                                    <th colspan="5" class="text-center">{{ $leaveType->title }} ({{ $leaveType->getLeaveBasis() }})</th>
                                @else
                                    <th colspan="1" class="text-center">{{ $leaveType->title }} ({{ $leaveType->getLeaveBasis() }})</th>
                                @endif
                            @endforeach
                            <th rowspan="2" class="text-center">Total Leave Balance (Days)</th>
                            <th rowspan="2" class="text-center">Remarks</th>
                        </tr>
                        <tr>
                            @foreach($leaveTypes as $leaveType)
                                @if($leaveType->maximum_carry_over > 0)
                                    <th>Carryover (@if(request()->get('month')) Last Month @else Last Year @endif)</th>
                                    <th>Earned</th>
                                    <th>Taken</th>
                                    <th>Paid</th>
                                    <th>Balance</th>
                                @else
                                    <th>Taken</th>
                                @endif

                            @endforeach
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($filteredEmployees as $index=>$employee)
                            @php $totalBalance = 0; @endphp
                            <tr>
                                <td>{{ $index+1 }}</td>
                                <td>{{ $employee->getFullName() }}</td>
                                {{-- <td>{{ $employee->getEmployeeType() }}</td> --}}
                                @foreach($leaveTypes as $leaveType)
                                    @php $employeeLeaves = $leaves->filter(function($leave) use ($employee, $leaveType){
                                            return $leave->employee_id == $employee->id && $leave->leave_type_id == $leaveType->id;
                                        })->sortBy('reported_date');
                                        $balance = $employeeLeaves->count() ? $employeeLeaves->last()->balance : 0;
                                    @endphp
                                    @if($leaveType->maximum_carry_over > 0)
                                        @php
                                            $totalBalance += $leaveType->getLeaveBasis() == 'Hour' ? round($balance/8,2): $balance;
                                        @endphp
                                        <td>{{ $employeeLeaves->count() ? $employeeLeaves->first()->opening_balance : '-' }}</td>
                                        <td>{{ $employeeLeaves->sum('earned') }}</td>
                                        <td>{{ $employeeLeaves->sum('taken') }}</td>
                                        <td>{{ $employeeLeaves->sum('paid') }}</td>
                                        <td>{{ $balance ?: '-' }}</td>
                                    @else
                                        <td>{{ $employeeLeaves->sum('taken') }}</td>
                                    @endif
                                @endforeach
                                <td>{{ round($totalBalance,2) }}</td>
                                <td></td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@stop
