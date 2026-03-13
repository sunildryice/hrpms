@extends('layouts.container')

@section('title', 'Report : WFH / Field Report')

@section('page_js')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#navbarVerticalMenu').find('#work-from-home-report-menu').addClass('active');
            $('[name="request_date"]').datepicker({
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
                    <a href="{{ route('report.work.from.home.export', $requestData) }}" id="btn_export"
                        class="btn btn-primary btn-sm">
                        Export
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <div class="rounded border shadow-sm card c-tabs-content active" id="wfh-table" style="overflow: auto;">
            <div class="card-body">
                <form action="{{ route('report.work.from.home.index') }}" method="get">
                    <div class="mb-4 row" style="align-items: flex-end">
                        <div class="col-md-2">
                            <label class="form-label" for="type">Type</label>
                            <select class="form-control select2" name="type" id="type">
                                <option value="">Select Type</option>
                                @foreach ($typeOptions as $val => $label)
                                    <option value="{{ $val }}"
                                        {{ $val == request()->get('type') ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-1">
                            <label class="form-label" for="fiscal_year">Year</label>
                            <select class="form-control select2" name="fiscal_year" id="fiscal_year">
                                <option value="">Select Year</option>
                                @foreach ($fiscalYears as $year)
                                    <option value="{{ $year->id }}"
                                        {{ $year->id == request()->get('fiscal_year') ? 'selected' : '' }}>
                                        {{ $year->title }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-1">
                            <label class="form-label" for="month">Month</label>
                            <select class="form-control select2" name="month" id="month">
                                <option value="">Select all</option>
                                @foreach ($months as $key => $month)
                                    <option value="{{ $key }}"
                                        {{ $key == request()->get('month') ? 'selected' : '' }}>
                                        {{ $month }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label" for="request_date">Date</label>
                            <input type="text" class="form-control" name="request_date" id="request_date"
                                value="{{ $request_date }}" />
                        </div>
                        <div class="col-md-2">
                            <label class="form-label" for="office">Office</label>
                            <select class="form-control select2" name="office" id="office">
                                <option value="">Select Office</option>
                                @foreach ($offices as $office)
                                    <option value="{{ $office->id }}"
                                        {{ $office->id == request()->get('office') ? 'selected' : '' }}>
                                        {{ $office->getOfficeName() }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label" for="employee">Employee</label>
                            <select class="form-control select2" name="employee" id="employee">
                                <option value="">Select Employee</option>
                                @foreach ($employees as $employee)
                                    @if ($employee->user)
                                        <option value="{{ $employee->user->id }}"
                                            {{ $employee->user->id == request()->employee ? 'selected' : '' }}>
                                            {{ $employee->getFullName() }}
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="col">
                            <button type="submit" name="btn" value="search"
                                class="m-1 btn btn-primary btn-sm">Search</button>
                            <a href="{{ route('report.work.from.home.index') }}"
                                class="m-1 btn btn-secondary btn-sm">Reset</a>
                        </div>
                    </div>
                    <span class="text-danger" id="error_message"></span>
                </form>

                <div class="table-responsive">
                    <table class="table table-bordered" id="workFromHomeReportTable">
                        <thead>
                            <tr>
                                <th>{{ __('label.sn') }}</th>
                                <th>Staff Name</th>
                                <th>Office</th>
                                <th>Type</th>
                                <th>WFH Number</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Request Date</th>
                                <th>Total Days</th>
                                <th>Projects</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($workFromHomes as $index => $wfh)
                                <tr>
                                    <td>{{ $workFromHomes->perPage() * ($workFromHomes->currentPage() - 1) + $index + 1 }}
                                    </td>
                                    <td>{{ $wfh->getRequesterName() }}</td>
                                    <td>{{ $wfh->getOfficeName() ?? '-' }}</td>
                                    <td>{{ $wfh->getTypeName() }}</td>
                                    <td>{{ $wfh->getRequestId() }}</td>
                                    <td>{{ $wfh->getStartDate() }}</td>
                                    <td>{{ $wfh->getEndDate() }}</td>
                                    <td>{{ $wfh->getRequestDate() }}</td>
                                    <td>{{ $wfh->getTotalDays() }} day{{ $wfh->getTotalDays() > 1 ? 's' : '' }}</td>
                                    <td>{{ implode(', ', $wfh->getProjectNames()) ?: '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    {{ $workFromHomes->withQueryString()->links() }}
                </div>
            </div>
        </div>
    </div>
@stop
