@extends('layouts.container')

@section('title', 'Report : Off Day Work')

@section('page_js')
    <script>
        $(document).ready(function () {
            $('#navbarVerticalMenu').find('#off-day-work-report-menu').addClass('active'); 

            $('[name="request_date"], [name="off_day_date"]').datepicker({
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
            <div class="d-flex flex-column flex-lg-row align-items-start align-items-lg-center gap-2">
                <div class="flex-grow-1">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}" class="text-decoration-none text-dark">Home</a></li>
                            <li class="breadcrumb-item" aria-current="page">@yield('title')</li>
                        </ol>
                    </nav>
                    <h4 class="m-0 mt-1 lh1 fs-6 text-uppercase fw-bold text-primary">@yield('title')</h4>
                </div>
                <div>
                    <a href="{{ route('report.off.day.work.export', request()->query()) }}"
                       class="btn btn-primary btn-sm">Export</a>
                </div>
            </div>
        </div>

        <div class="card shadow-sm border rounded">
            <div class="card-body">
                <form action="{{ route('report.off.day.work.index') }}" method="GET">
                    <div class="row mb-4" style="align-items: flex-end;">
                        <div class="col-md-2">
                            <label class="form-label">Year</label>
                            <select name="fiscal_year" class="form-control">
                                <option value="">Select Year</option>
                                @foreach ($fiscalYears as $year)
                                    <option value="{{ $year->id }}" {{ request('fiscal_year') == $year->id ? 'selected' : '' }}>
                                        {{ $year->title }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">Month</label>
                            <select name="month" class="form-control select2">
                                <option value="">All months</option>
                                @foreach ($months as $key => $monthName)
                                    <option value="{{ $key }}" {{ request('month') == $key ? 'selected' : '' }}>
                                        {{ $monthName }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">Date</label>
                            <input type="text" name="request_date" class="form-control"
                                   value="{{ request('request_date') }}" />
                        </div>
                        
                        <div class="col-md-2">
                            <label class="form-label">Office</label>
                            <select name="office" class="form-control select2">
                                <option value="">All offices</option>
                                @foreach ($offices as $office)
                                    <option value="{{ $office->id }}" {{ request('office') == $office->id ? 'selected' : '' }}>
                                        {{ $office->getOfficeName() }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">Employee</label>
                            <select name="employee" class="form-control select2">
                                <option value="">All employees</option>
                                @foreach ($employees as $emp)
                                    @if ($emp->user)
                                        <option value="{{ $emp->user->id }}" {{ request('employee') == $emp->user->id ? 'selected' : '' }}>
                                            {{ $emp->getFullName() }}
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                        </div>

                        <div class="col-auto mt-4">
                            <button type="submit" class="btn btn-primary btn-sm me-2">Search</button>
                            <a href="{{ route('report.off.day.work.index') }}" class="btn btn-secondary btn-sm">Reset</a>
                        </div>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>S.N.</th>
                                <th>Staff Name</th>
                                <th>Office</th>
                                <th>ODW Number</th>
                                <th>Off Day Date</th>
                                <th>Request Date</th>
                                <th>Projects</th>
                                <th>Reason</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($offDayWorks as $index => $odw)
                                <tr>
                                    <td>{{ $offDayWorks->perPage() * ($offDayWorks->currentPage() - 1) + $index + 1 }}</td>
                                    <td>{{ $odw->getRequesterName() }}</td>
                                    <td>{{ $odw->getOfficeName() ?? '-' }}</td>
                                    <td>{{ $odw->getRequestId() }}</td>
                                    <td>{{ $odw->getOffDayWorkDate() }}</td>
                                    <td>{{ $odw->getRequestDate() }}</td>
                                    <td>{{ implode(', ', $odw->getProjectNames()) ?: '-' }}</td>
                                    <td>{{ $odw->reason ?: '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4">No approved off-day work records found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    {{ $offDayWorks->withQueryString()->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection