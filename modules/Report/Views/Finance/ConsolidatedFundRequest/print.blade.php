@extends('layouts.container-report')

@section('title', 'Consolidated Fund Request')
@section('page_css')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Mukta:wght@200;500;600;800&display=swap" rel="stylesheet">
    <style>
        table {
            border: 1px solid;
        }

        .table thead th {
            font-size: 0.94375rem;

        }

        tbody,
        td,
        tfoot,
        th,
        thead,
        tr {
            border-width: 0.1px;
        }

        .table tr th,
        .table tr td {
            padding: 0.25rem 0.75rem;
        }
    </style>
@endsection
@section('page_js')

@endsection

@section('page-content')

    <div class="print-title fw-bold mb-5 translate-middle text-center ">
        <div class="fs-5"> HERD International</div>
        {{-- <div class="fs-8">{{ $filteredOffices->map(fn($office) => $office->office_name)->implode(', ') }}</div> --}}
        <div class="fs-8"> Consolidated Fund Request </div>
    </div>

    <div class="print-header mb-4">
        <div class="row">
            <div class="col-lg-8"></div>
            <div class="col-lg-4">
                <div class="d-flex flex-column justify-content-end">
                    <div class="d-flex flex-column justify-content-end brand-logo mb-4 flex-grow-1">
                        <div class="d-flex flex-column justify-content-end float-right">
                            <img src="{{ asset('img/logonp.png') }}" alt="" class="align-self-end pe-5 logo-img">
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="print-body mb-5">
        <table class="table mb-4 table-bordered" id="consolidatedFundRequestReportTable">
            <thead class="bg-light">
                <tr>
                    <th>{{ __('label.sn') }}</th>
                    <th>Activity Name</th>
                    @foreach ($filteredOffices as $office)
                        <th>Fund for: {{ $office->getOfficeName() }}</th>
                    @endforeach
                    <th>Total Amount (Rs.)</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($activityCodes as $key => $activityCode)
                    <tr>
                        <td>{{ ++$key }}</td>
                        {{-- <td>{{$activityCode->getActivityCodeWithDescription()}}</td> --}}
                        <td>{{ $activityCode->getActivityCode() }}</td>
                        @php
                            $fundTotal = 0;
                        @endphp
                        @foreach ($filteredOffices as $office)
                            @php
                                $fund = $fundRequestActivities
                                    ->where('activity_code_id', '=', $activityCode->id)
                                    ->where('fundRequest.request_for_office_id', $office->id)
                                    ->sum('estimated_amount');

                                $fundTotal += $fund;
                            @endphp
                            <td>{{ $fund == 0 ? '' : $fund }}</td>
                        @endforeach
                        <td>{{ $fundTotal == 0 ? '' : $fundTotal }}</td>
                    </tr>
                @endforeach
                <tr>
                    <th colspan="2">TOTAL FUND REQUIRED</th>
                    @php
                        $activityFundTotal = 0;
                    @endphp
                    @foreach ($filteredOffices as $office)
                        @php
                            $officeFundRequired = $fundRequestActivities
                                ->where('fundRequest.request_for_office_id', $office->id)
                                ->sum('estimated_amount');
                            $activityFundTotal += $officeFundRequired;
                        @endphp
                        <th>{{ $officeFundRequired == 0 ? '' : $officeFundRequired }}</th>
                    @endforeach
                    <th>{{ $activityFundTotal == 0 ? '' : $activityFundTotal }}</th>
                </tr>

                <tr>
                    <td colspan="2">Estimated Surplus/(Deficit) of Current Month</td>
                    @php
                        $activitySurplusDeficitTotal = 0;
                    @endphp
                    @foreach ($filteredOffices as $office)
                        @php
                            $officeSurplusDeficit = $fundRequests
                                ->where('request_for_office_id', $office->id)
                                ->sum('estimated_surplus');
                            $activitySurplusDeficitTotal += $officeSurplusDeficit;
                        @endphp
                        <td>{{ $officeSurplusDeficit == 0 ? '' : $officeSurplusDeficit }}</td>
                    @endforeach
                    <td>{{ $activitySurplusDeficitTotal == 0 ? '' : $activitySurplusDeficitTotal }}</td>
                </tr>

                <tr>
                    <th colspan="2">Net Fund Required</th>
                    @php
                        $activityNetFundRequiredTotal = 0;
                    @endphp
                    @foreach ($filteredOffices as $office)
                        @php
                            $officeNetFundRequired = $fundRequests
                                ->where('request_for_office_id', $office->id)
                                ->sum('net_amount');
                            $activityNetFundRequiredTotal += $officeNetFundRequired;
                        @endphp
                        <th>{{ $officeNetFundRequired == 0 ? '' : $officeNetFundRequired }}</th>
                    @endforeach
                    <th>{{ $activityNetFundRequiredTotal == 0 ? '' : $activityNetFundRequiredTotal }}</th>
                </tr>

                <tr>
                    <th colspan="2">Requester</th>
                    @foreach ($filteredOffices as $office)
                        @php
                            $requesters = $fundRequests
                                ->where('request_for_office_id', $office->id)
                                ->map(fn($fundRequest) => $fundRequest->getRequesterName())
                                ->implode(', ');
                        @endphp
                        <td>{{ $requesters }}</td>
                    @endforeach
                    <td></td>
                </tr>

                <tr>
                    <th colspan="2">Approver</th>
                    @foreach ($filteredOffices as $office)
                        @php
                            $approvers = $fundRequests
                                ->where('request_for_office_id', $office->id)
                                ->map(fn($fundRequest) => $fundRequest->getApproverName())
                                ->implode(', ');
                        @endphp
                        <td>{{ $approvers }}</td>
                    @endforeach
                    <td></td>
                </tr>

                <tr>
                    <th colspan="2">Approved On</th>
                    @foreach ($filteredOffices as $office)
                        @php
                            $approvedDate = $fundRequests
                                ->where('request_for_office_id', $office->id)
                                ->map(
                                    fn(
                                        $fundRequest,
                                    ) => $fundRequest->approvedLog?->created_at?->toFormattedDateString(),
                                )
                                ->implode(', ');
                        @endphp
                        <td>{{ $approvedDate }}</td>
                    @endforeach
                    <td></td>
                </tr>

            </tbody>
        </table>
    </div>

    <script>
        window.onload = print;
    </script>


@endsection
