@extends('layouts.container')

@section('title', 'Report : Monthly Fund Request')

@section('page_js')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#navbarVerticalMenu').find('#monthly-fund-request-report-menu').addClass('active');
        });

        // $('#')
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
                    <a href="{{ route('report.monthly.fund.request.export', ['year' => $year, 'month' => $month, 'office_id' => $officeId, 'user_id' => $userId]) }}" id="btn_export" class="btn btn-primary btn-sm">
                        Export
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <div class="card shadow-sm border rounded c-tabs-content active" id="employee-table" style="overflow: auto;">
            <div class="card-body">
                <form action="{{route('report.monthly.fund.request.index')}}" method="GET">
                    <div class="row mb-4" style="align-items: flex-end">
                        <div class="col-md-2">
                            <label class="form-label" for="year">Year</label>
                            <select class="form-control" name="year" id="year">
                                @foreach ($years as $yr)
                                    <option value="{{$yr->title}}" {{$yr->title == $year ? 'selected' : ''}}>{{$yr->title}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label" for="month">Month</label>
                            <select class="form-control" name="month" id="month">
                                @foreach ($months as $key=>$mon)
                                    <option value="{{$key}}" {{$key == $month ? 'selected' : ''}}>{{$mon}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label" for="office_id">Office</label>
                            <select class="form-control select2" name="office_id" id="office_id">
                                <option value="">Select office...</option>
                                @foreach ($offices as $off)
                                    <option value="{{$off->id}}" {{$off->id == $officeId ? 'selected' : ''}}>{{$off->getOfficeName()}}</option>
                                @endforeach
                            </select>
                        </div>
                        {{-- <div class="col-md-2">
                            <label class="form-label" for="district_id">District</label>
                            <select class="form-control select2" name="district_id" id="district_id">
                                <option value="">Select district</option>
                                @foreach ($districts as $dist)
                                    <option value="{{$dist->id}}" {{$dist->id == $districtId ? 'selected' : ''}}>{{$dist->getDistrictName()}}</option>
                                @endforeach
                            </select>
                        </div> --}}
                        <div class="col-md-2">
                            <label class="form-label" for="requester">Requester</label>
                            <select class="form-control select2" name="requester" id="requester">
                                <option value="">Select employee</option>
                                @foreach ($employees as $emp)
                                    @if ($emp->user)
                                        <option value="{{$emp->user->id}}" {{$emp->user->id == $userId ? 'selected' : ''}}>{{$emp->getFullName()}}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="col">
                            <button type="submit" id="btn_search" class="btn btn-primary btn-sm m-1">Search</button>
                            {{-- <button type="reset" id="btn_reset" class="btn btn-secondary btn-sm m-1">Reset</button> --}}
                        </div>
                    </div>
                    <span class="text-danger" id="error_message"></span>
                </form>
                <div class="table-responsive">
                    <table class="table table-bordered" id="monthlyFundRequestReportTable">
                        <thead class="bg-light">
                            <tr>
                                <th>{{ __('label.sn') }}</th>
                                <th>Activity Name</th>
                                <th>Estimated Fund</th>
                                <th>Projected Target</th>
                                <th>Budget</th>
                                <th>DIP Target</th>
                                <th>Budget Variance</th>
                                <th>Target Variance</th>
                                <th>Remarks/Variance notes</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $estimatedFundTotal     = 0;
                                $projectedTargetTotal   = 0;
                                $budgetTotal            = 0;
                                $dipTargetTotal         = 0;
                                $budgetVarianceTotal    = 0;
                                $targetVarianceTotal    = 0;
                            @endphp
                            @foreach ($activityCodes as $key=>$activityCode)
                                @php
                                    $estimatedFund          = $fundRequestActivities->where('activity_code_id', '=', $activityCode->id)
                                                                                ->sum('estimated_amount');
                                    $estimatedFundTotal     += $estimatedFund;


                                    $projectedTarget        = $fundRequestActivities->where('activity_code_id', '=', $activityCode->id)
                                                                                ->sum('project_target_unit');
                                    $projectedTargetTotal   += $projectedTarget;


                                    $budget                 = $fundRequestActivities->where('activity_code_id', '=', $activityCode->id)
                                                                                ->sum('budget_amount');
                                    $budgetTotal            += $budget;


                                    $dipTarget              = $fundRequestActivities->where('activity_code_id', '=', $activityCode->id)
                                                                                ->sum('dip_target_unit');
                                    $dipTargetTotal         += $dipTarget;


                                    $budgetVariance         = $fundRequestActivities->where('activity_code_id', '=', $activityCode->id)
                                                                                ->sum('variance_budget_amount');
                                    $budgetVarianceTotal    += $budgetVariance;


                                    $targetVariance         = $fundRequestActivities->where('activity_code_id', '=', $activityCode->id)
                                                                                ->sum('variance_target_unit');
                                    $targetVarianceTotal    += $targetVariance;

                                @endphp

                                <tr>
                                    <td>{{++$key}}</td>
                                    <td>{{$activityCode->getActivityCodeWithDescription()}}</td>
                                    <td>{{$estimatedFund == 0 ? '' : $estimatedFund }}</td>
                                    <td>{{$projectedTarget == 0 ? '' : $projectedTarget }}</td>
                                    <td>{{$budget == 0 ? '' : $budget }}</td>
                                    <td>{{$dipTarget == 0 ? '' : $dipTarget }}</td>
                                    <td>{{$budgetVariance == 0 ? '' : $budgetVariance }}</td>
                                    <td>{{$targetVariance == 0 ? '' : $targetVariance }}</td>
                                    <td></td>
                                </tr>
                            @endforeach

                            <tr>
                                <th colspan="2">TOTAL FUND REQUIRED</th>
                                <th>{{$estimatedFundTotal}}</th>
                                <th>{{$projectedTargetTotal}}</th>
                                <th>{{$budgetTotal}}</th>
                                <th>{{$dipTargetTotal}}</th>
                                <th>{{$budgetVarianceTotal}}</th>
                                <th>{{$targetVarianceTotal}}</th>
                                <th></th>
                            </tr>

                            <tr>
                                <td colspan="2">Estimated Surplus/(Deficit) of Current Month</td>
                                <th colspan="7">{{$fundRequests->sum('estimated_surplus')}}</th>
                            </tr>

                            <tr>
                                <th colspan="2">Net Fund Required</th>
                                <th colspan="7">{{$fundRequests->sum('net_amount')}}</th>
                            </tr>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@stop
