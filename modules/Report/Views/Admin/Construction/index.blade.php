@extends('layouts.container')

@section('title', 'Report : Construction')

@section('page_css')
<style>
    :root {
        --first-col-width: 40px;
    }

    .sticky-col {
        position: sticky;
        top: 0;
        background-color: white;
    }

    .table-container {
        height: calc(100vh - 215px);
        overflow: auto;
    }

    .first-col {
        width: var(--first-col-width);
        left: 0px;
        z-index: 100 !important;
        background: white !important;
    }

    .second-col {
        width: auto;
        left: var(--first-col-width);
        z-index: 100 !important;
        background: white !important;
    }
</style>
@endsection

@section('page_js')
    <script type="text/javascript">

        $(document).ready(function () {
            $('#navbarVerticalMenu').find('#construction-report-menu').addClass('active');
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
                    <a href="{{ route('report.construction.export', request()->only('district', 'year', 'donor')) }}" id="btn_export" class="btn btn-primary btn-sm">
                         Export
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <div class="card shadow-sm border rounded c-tabs-content active" id="employee-table" style="overflow: auto;">
            <div class="card-body">
                <form action="{{ route('report.construction.index') }}" method="POST">
                    @csrf
                    <div class="row mb-4"  style="align-items: flex-end">
                        <div class="col-md-2">
                            <label class="form-label" for="district">District</label>
                            <select class="form-control select2" name="district" id="district">
                                <option value="" onclick="resetValue('district')">Select district...</option>
                                @foreach ($districts as $district)
                                    <option value="{{ $district->id }}" @selected(filled(request()->district) && request()->district == $district->id) >{{ $district->getDistrictName() }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label" for="year">Year</label>
                            <select class="form-control select2" name="year" id="year">
                                <option value="" onclick="resetValue('year')">Select year...</option>
                                @foreach ($years as $year)
                                    <option value="{{ $year }}" @selected(filled(request()->year) && request()->year == $year) >{{ $year }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label" for="donor">Donor</label>
                            <input class="form-control" type="text" name="donor" id="donor" value="{{ filled(request()->donor) ? request()->donor : '' }}" >
                        </div>
                        <div class="col">
                            <button type="submit" class="btn btn-primary btn-sm m-1">Search</button>
                            <a href="{{ route('report.construction.index') }}" id="btn_reset" class="btn btn-secondary btn-sm m-1">Reset</a>
                        </div>
                    </div>
                    <span class="text-danger" id="error_message"></span>
                </form>
                <div class="table-responsive table-container">
                    <table class="table" id="constructionReportTable">
                        <thead class="bg-light sticky-top">
                        <tr>
                            <th class="sticky-col first-col">{{ __('label.sn') }}</th>
                            <th>Year</th>
                            <th class="sticky-col second-col">Health Facility Name</th>
                            <th>District</th>
                            <th>Location</th>
                            <th>Category</th>
                            <th>MOU Start Date</th>
                            <th>MOU End Date</th>
                            <th>Work Completion Date</th>
                            <th>Amendment Effective Date</th>
                            <th>Extension To Date</th>
                            <th>Total Project Cost NPR</th>
                            <th>OHW Commitment Value NPR</th>
                            <th>Other Party's Contribution NPR</th>
                            <th>Total Fund Transferred NPR</th>
                            <th>Expense Settled NPR</th>
                            <th>Advance/Payable NPR</th>
                            <th>Physical Work Progress %</th>
                            <th>Current Status</th>
                            <th>Donor tagging</th>
                            <th>Metal Plaque Text</th>
                        </tr>
                        </thead>
                        <tbody>
                            @foreach ($constructions as $key=>$construction)
                                <tr>
                                    <td class="sticky-col first-col">{{ ++$key }}</td>
                                    <td>{{ $construction->signed_date->format('Y') }}</td>
                                    <td class="sticky-col second-col">{{ $construction->health_facility_name }}</td>
                                    <td>{{ $construction->district->district_name }}</td>
                                    <td>{{ $construction->district->province->province_name .', '. $construction->getDistrictName() .', '. $construction->getLocalName() }}</td>
                                    <td>{{ $construction->type_of_work }}</td>
                                    <td>{{ $construction->effective_date_from?->toFormattedDateString() }}</td>
                                    <td>{{ $construction->effective_date_to?->toFormattedDateString() }}</td>
                                    <td>{{ $construction->work_completion_date?->toFormattedDateString() }}</td>
                                    <td>{{ $construction->latestAmendment?->effective_date?->toFormattedDateString() }}</td>
                                    <td>{{ $construction->latestAmendment?->extension_to_date?->toFormattedDateString() }}</td>
                                    <td>{{ $construction->total_contribution_amount }}</td>
                                    <td>{{ $construction->ohw_contribution }}</td>
                                    <td>{{ $construction->getOtherPartiesContribution() }}</td>
                                    <td>{{ $construction->getTotalFundTransferred() }}</td>
                                    <td>{{ $construction->getTotalExpenseSettled() }}</td>
                                    <td>{{ $construction->getTotalFundTransferred() - $construction->getTotalExpenseSettled() }}</td>
                                    <td>{{ $construction->latestConstructionProgress?->progress_percentage }}</td>
                                    <td></td>
                                    <td>{{ $construction->donor }}</td>
                                    <td>{{ $construction->metal_plaque_text }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
@stop
