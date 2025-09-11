@extends('layouts.container')

@section('title', 'Report : Leave Requests')

@section('page_js')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#navbarVerticalMenu').find('#leave-request-report-menu').addClass('active');
            $(document.querySelector('[name="request_date"]')).datepicker({
                language: 'en-GB',
                autoHide: true,
                format: 'yyyy-mm-dd',
            });
        });
    </script>
@endsection

@section('page-content')
    <div class="container-fluid">
        <div class="pb-3 mb-3 border-bottom">
            <div class="gap-2 d-flex flex-column flex-lg-row align-items-start align-items-lg-center">
                <div class="brd-crms flex-grow-1">
                    <nav aria-label="breadcrumb">
                        <ol class="m-0 breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{ route('dashboard.index') }}" class="text-decoration-none text-dark">Home</a>
                            </li>
                            <li class="breadcrumb-item" aria-current="page">@yield('title')</li>
                        </ol>
                    </nav>
                    <h4 class="m-0 mt-1 lh1 fs-6 text-uppercase fw-bold text-primary">@yield('title')</h4>
                </div>
                <div class="add-info justify-content-end">
                    <a href="{{ route('report.leave.requests.export', $requestData) }}" id="btn_export"
                        class="btn btn-primary btn-sm">
                        Export
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <div class="rounded border shadow-sm card c-tabs-content active" id="employee-table" style="overflow: auto;">
            <div class="card-body">
                <form action="{{ route('report.leave.requests.index') }}" method="get">
                    <div class="mb-4 row" style="align-items: flex-end">
                        <div class="col-md-2">
                            <label class="form-label" for="fiscal_year">Year</label>
                            <select class="form-control" name="fiscal_year" id="fiscal_year">
                                <option value="">Select Year</option>
                                @foreach ($fiscalYears as $year)
                                    <option value="{{ $year->id }}"
                                        {{ $year->id == request()->get('fiscal_year') ? 'selected' : '' }}>
                                        {{ $year->title }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label" for="month">Month</label>
                            <select class="form-control select2" name="month" id="month">
                                <option value="">Select all</option>
                                @foreach ($months as $key => $month)
                                    <option value="{{ $key }}"
                                        {{ $key == request()->get('month') ? 'selected' : '' }}>{{ $month }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label" for="month">Date</label>
                            <input type="text" class="form-control" name="request_date" id="request_date" value="{{$request_date}}" />
                        </div>
                        <div class="col-md-2">
                            <label class="form-label" for="office">Office</label>
                            <select class="form-control select2" name="office" id="office">
                                <option value="">Select Office</option>
                                @foreach ($offices as $office)
                                    <option value="{{ $office->id }}" @if ($office->id == request()->get('office')) selected @endif>
                                        {{ $office->getOfficeName() }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label" for="employee">Employee</label>
                            <select class="form-control select2" name="employee" id="employee">
                                <option value="">Select Employee</option>
                                @foreach ($employees as $employee)
                                    @if ($employee->user)
                                        <option value="{{ $employee->user->id }}" @selected($employee->user->id == request()->employee)>
                                            {{ $employee->getFullName() }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="col">
                            <button type="submit" name="btn" value="search"
                                class="m-1 btn btn-primary btn-sm">Search</button>
                            <a href="{{ route('report.leave.requests.index') }}"
                                class="m-1 btn btn-secondary btn-sm">Reset</a>
                        </div>
                    </div>
                    <span class="text-danger" id="error_message"></span>
                </form>
                <div class="table-responsive">
                    <table class="table table-bordered" id="leaveRequestReportTable">
                        <thead>
                            <tr>
                                <th>{{ __('label.sn') }}</th>
                                <th>Staff Name</th>
                                <th>Office</th>
                                <th>Leave Number</th>
                                <th>Leave Type</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Request Date</th>
                                <th>Request Days/Hours</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($leaveRequests as $index => $leaveRequest)
                                <tr>
                                    <td>{{ $leaveRequests->perPage() * ($leaveRequests->currentPage() - 1) + $index + 1 }}
                                    </td>
                                    <td>{{ $leaveRequest->getRequesterName() }}</td>
                                    <td>{{ $leaveRequest->getOfficeName() }}</td>
                                    <td>{{ $leaveRequest->getLeaveNumber() }}</td>
                                    <td>{{ $leaveRequest->getLeaveType() }}</td>
                                    <td>{{ $leaveRequest->getStartDate() }}</td>
                                    <td>{{ $leaveRequest->getEndDate() }}</td>
                                    <td>{{ $leaveRequest->getRequestDate() }}</td>
                                    <td>{{ $leaveRequest->getLeaveDuration() . ' ' . $leaveRequest->leaveType->getLeaveBasis() }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    {{ $leaveRequests->withQueryString()->links() }}
                </div>
            </div>
        </div>
    </div>
@stop
