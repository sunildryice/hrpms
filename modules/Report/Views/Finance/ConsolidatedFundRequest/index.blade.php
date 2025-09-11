@extends('layouts.container')

@section('title', 'Report : Consolidated Fund Request')

@section('page_js')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#navbarVerticalMenu').find('#consolidated-fund-request-report-menu').addClass('active');
        });
    </script>
@endsection
@section('page-content')

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
                    <a href="{{ route('report.consolidated.fund.request.export', ['year' => $year, 'month' => $month, 'office_id' => request()->get('office_id') ?? [] ]) }}" id="btn_export" class="btn btn-primary btn-sm" title="Export">
                        <i class="bi bi-cloud-download"></i> Export
                    </a>

                    <a href="{{ route('report.consolidated.fund.request.print', ['year' => $year, 'month' => $month, 'office_id' => request()->get('office_id') ?? [] ]) }}" target="_blank" id="btn_print" class="btn btn-primary btn-sm" title="Print">
                        <i class="bi bi-printer"></i> Print
                    </a>
                </div>
            </div>
        </div>
        <div class="c-tabs-content active" id="employee-table">
                <form action="{{route('report.consolidated.fund.request.index')}}" method="GET">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="year">Year</label>
                                    <select class="form-control" name="year" id="year">
                                        @foreach ($years as $yr)
                                            <option value="{{$yr->title}}" {{$yr->title == $year ? 'selected' : ''}}>{{$yr->title}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="month">Month</label>
                                    <select class="form-control" name="month" id="month">
                                        @foreach ($months as $key=>$mon)
                                            <option value="{{$key}}" {{$key == $month ? 'selected' : ''}}>{{$mon}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label" for="office_id">Office</label>
                                    <select class="form-control select2" name="office_id[]" id="office_id" multiple>
                                        <option value="0" {{ in_array(0, request()->get('office_id') ?? []) ? 'selected' : ''}}>All</option>
                                        @foreach ($offices as $office)
                                            <option value="{{$office->id}}" {{ in_array($office->id, request()->get('office_id') ?? []) ? 'selected' : ''}}>
                                                {{ $office->getOfficeName() }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                            </div>


                        </div>
                        <span class="text-danger" id="error_message"></span>
                        <div class="card-footer text-end">
                            <button type="submit" id="btn_search" class="btn btn-primary btn-sm">Search</button>
                            <button type="reset" id="btn_reset" class="btn btn-danger btn-sm">Reset</button>
                        </div>
                    </div>
                </form>
                <div class="card">
                    <div class="card-header fw-bold">Consolidated Fund Request List</div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="consolidatedFundRequestReportTable">
                                <thead class="bg-light">
                                    <tr>
                                        <th>{{ __('label.sn') }}</th>
                                        <th>Activity Name</th>
                                        @foreach ($filteredOffices as $office)
                                            <th>Fund for: {{$office->getOfficeName()}}</th>
                                        @endforeach
                                        <th>Total Amount (Rs.)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($activityCodes as $key=>$activityCode)
                                        <tr>
                                            <td>{{++$key}}</td>
                                            {{-- <td>{{$activityCode->getActivityCodeWithDescription()}}</td> --}}
                                            <td>{{ $activityCode->getActivityCode() }}</td>
                                            @php
                                                $fundTotal = 0;
                                            @endphp
                                            @foreach ($filteredOffices as $office)
                                                @php
                                                    $fund = $fundRequestActivities->where('activity_code_id', '=', $activityCode->id)
                                                                   ->where('fundRequest.request_for_office_id', $office->id)
                                                                   ->sum('estimated_amount');

                                                    $fundTotal += $fund;
                                                @endphp
                                                <td>{{$fund == 0 ? '' : $fund}}</td>
                                            @endforeach
                                            <td>{{$fundTotal == 0 ? '' : $fundTotal}}</td>
                                        </tr>
                                    @endforeach
                                    <tr>
                                        <th colspan="2" class="text-end">TOTAL FUND REQUIRED</th>
                                        @php
                                            $activityFundTotal = 0;
                                        @endphp
                                        @foreach ($filteredOffices as $office)
                                            @php
                                                $officeFundRequired = $fundRequestActivities->where('fundRequest.request_for_office_id', $office->id)
                                                                    ->sum('estimated_amount');
                                                $activityFundTotal += $officeFundRequired;
                                            @endphp
                                            <th>{{$officeFundRequired == 0 ? '' : $officeFundRequired}}</th>
                                        @endforeach
                                        <th>{{$activityFundTotal == 0 ? '' : $activityFundTotal}}</th>
                                    </tr>

                                    <tr>
                                        <th colspan="2" class="text-end">Estimated Surplus/(Deficit) of Current Month</th>
                                        @php
                                            $activitySurplusDeficitTotal = 0;
                                        @endphp
                                        @foreach ($filteredOffices as $office)
                                            @php
                                                $officeSurplusDeficit = $fundRequests->where('request_for_office_id', $office->id)
                                                                                    ->sum('estimated_surplus');
                                                $activitySurplusDeficitTotal += $officeSurplusDeficit;
                                            @endphp
                                            <th>{{$officeSurplusDeficit == 0 ? '' : $officeSurplusDeficit}}</th>
                                        @endforeach
                                        <th>{{$activitySurplusDeficitTotal == 0 ? '' : $activitySurplusDeficitTotal}}</th>
                                    </tr>

                                    <tr>
                                        <th colspan="2" class="text-end">Net Fund Required</th>
                                        @php
                                            $activityNetFundRequiredTotal = 0;
                                        @endphp
                                        @foreach ($filteredOffices as $office)
                                            @php
                                                $officeNetFundRequired = $fundRequests->where('request_for_office_id', $office->id)
                                                                                    ->sum('net_amount');
                                                $activityNetFundRequiredTotal += $officeNetFundRequired;
                                            @endphp
                                            <th>{{$officeNetFundRequired == 0 ? '' : $officeNetFundRequired}}</th>
                                        @endforeach
                                        <th>{{$activityNetFundRequiredTotal == 0 ? '' : $activityNetFundRequiredTotal}}</th>
                                    </tr>

                                    <tr>
                                        <th colspan="2" class="text-end">Requester</th>
                                        @foreach ($filteredOffices as $office)
                                            @php
                                                $requesters = $fundRequests->where('request_for_office_id', $office->id)
                                                                ->map( fn($fundRequest) => $fundRequest->getRequesterName())
                                                                ->implode(", ");
                                            @endphp
                                            <th>{{ $requesters }}</th>
                                        @endforeach
                                        <th></th>
                                    </tr>

                                    <tr>
                                        <th colspan="2" class="text-end">Approver</th>
                                        @foreach ($filteredOffices as $office)
                                            @php
                                                $approvers = $fundRequests->where('request_for_office_id', $office->id)
                                                                ->map( fn($fundRequest) => $fundRequest->getApproverName())
                                                                ->implode(", ");
                                            @endphp
                                            <th>{{ $approvers }}</th>
                                        @endforeach
                                        <th></th>

                                    </tr>

                                    <tr>
                                        <th colspan="2" class="text-end">Approved On</th>
                                        @foreach ($filteredOffices as $office)
                                            @php
                                                $approvedDate = $fundRequests->where('request_for_office_id', $office->id)
                                                                ->map( fn($fundRequest) => $fundRequest->approvedLog?->created_at?->toFormattedDateString())
                                                                ->implode(", ");
                                            @endphp
                                            <th>{{ $approvedDate }}</th>
                                        @endforeach
                                        <th></th>

                                    </tr>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
        </div>
@stop
