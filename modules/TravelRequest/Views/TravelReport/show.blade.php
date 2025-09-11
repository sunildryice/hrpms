@extends('layouts.container')

@section('title', 'View Travel Report')

@section('page_js')
    <script type="text/javascript">
        $(document).ready(function () {
            $('#navbarVerticalMenu').find('#travel-report-menu').addClass('active');
        });
    </script>
@endsection
@section('page-content')

    <div class="page-header pb-3 mb-3 border-bottom">
        <div class="d-flex align-items-center">
            <div class="brd-crms flex-grow-1">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item">
                            <a href="{!! route('dashboard.index') !!}" class="text-decoration-none text-dark">Home</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('travel.reports.index') }}" class="text-decoration-none text-dark">Travel
                                Report</a>
                        </li>
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
                <div class="card">
                    <div class="card-header fw-bold">
                        Travel Information
                    </div>
                    @include('TravelRequest::Partials.detail')
                </div>
                <div class="card">
                    <div class="card-header fw-bold">
                        Travel Report Information
                    </div>
                    @include('TravelRequest::Partials.report-detail')
                </div>
            </div>
            <div class="col-lg-9">
                <div class="card">
                    <div class="card-header fw-bold">
                        Travel Report
                    </div>
                    <div class="card-body">
                        <div id="reportTable">
                            <div class="card">
                                <div class="card-header fw-bold">{{ __('label.objectives') }}</div>
                                <div class="card-body cb-height">
                                    {{ $travelReport->objectives }}
                                </div>
                            </div>
                            <div class="card">
                                <div class="card-header fw-bold">
                                    {{ __('label.a') . '. ' . __('label.observation') }}</div>
                                <div class="card-body cb-height">
                                    {{ $travelReport->observation }}
                                </div>
                            </div>
                            <div class="card">
                                <div class="card-header fw-bold">
                                    {{ __('label.b') . '. ' . __('label.activities') }}</div>
                                <div class="card-body cb-height">
                                    {{ $travelReport->activities }}
                                </div>
                            </div>
                            <div class="card">
                                <div class="card-header fw-bold">
                                    {{ __('label.c') . '. ' . __('label.recommendation') }}</div>
                                <div class="card-body cb-height">
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table" id="travelRecommendationTable">
                                        <thead>
                                        <tr>
                                            <th colspan="5" style="alignment: center">Recommendation for</th>
                                        </tr>
                                        <tr>
                                            <th>{{ __('label.serial-no') }}</th>
                                            <th>{{ __('label.what') }}</th>
                                            <th>{{ __('label.when') }}</th>
                                            <th>{{ __('label.who') }}</th>
                                            <th>{{ __('label.remarks') }}</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @forelse($travelReport->travelReportRecommendations as $index=>$recommendation)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ $recommendation['recommendation_subject'] }}</td>
                                                <td>{{ $recommendation['recommendation_date'] }}</td>
                                                <td>{{ $recommendation['recommendation_responsible'] }}</td>
                                                <td>{{ $recommendation['recommendation_remarks'] }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center">
                                                    {{ __('message.record-not-found') }}</td>
                                            </tr>
                                        @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-header fw-bold">
                                {{ __('label.d') . '. ' . __('label.other-comments') }}</div>
                            <div class="card-body cb-height">
                                {{ $travelReport->other_comments }}
                            </div>
                        </div>

                        <div class="card">
                            @include('Attachment::list', [
                           'modelType' => 'Modules\TravelRequest\Models\TravelReport',
                           'modelId' => $travelReport->id,
                       ])
                        </div>

                    </div>
                </div>
                <div class="card">
                    <div class="card-header fw-bold">
                        Travel Report Process
                    </div>
                    <div class="card-body">
                        <div class="c-b">
                            @foreach ($travelReport->logs as $log)
                                <div class="d-flex py-2 flex-row gap-2 mb-2 border-bottom ">
                                    <div width="40" height="40" class="rounded-circle mr-3 user-icon">
                                        <i class="bi-person-circle fs-5"></i>
                                    </div>
                                    <div class="w-100">
                                        <div
                                            class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                                            <div
                                                class="d-flex flex-column flex-md-row align-items-start align-items-md-center gap-md-2 mb-2 mb-md-0">
                                                <label class="form-label mb-0">{{ $log->getCreatedBy() }}</label>
                                                <span
                                                    class="badge bg-primary c-badge">{!! $log->createdBy->employee->latestTenure->getDesignationName() !!}</span>
                                            </div>
                                            <small
                                                title="{{$log->created_at}}">{{ $log->created_at->format('M d, Y h:i A') }}</small>
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
        </div>
    </section>
@stop
