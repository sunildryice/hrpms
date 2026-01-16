@extends('layouts.container')

@section('title', 'View Travel Report')

@section('page_js')
    <script type="text/javascript">
        $(document).ready(function() {
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
                    <div class="card-header fw-bold">Travel Information</div>
                    @include('TravelRequest::Partials.detail')
                </div>
                <div class="card">
                    <div class="card-header fw-bold">Travel Report Summary</div>
                    @include('TravelRequest::Partials.report-detail')
                </div>
            </div>

            <div class="col-lg-9">
                <div class="card">
                    <div class="card-header fw-bold">Travel Report </div>
                    <div class="card-body">

                        <div class="card mb-3">
                            <div class="card-header fw-bold">General Objective/Purpose of Travel</div>
                            <div class="card-body">{{ nl2br(e($travelReport->objectives)) }}</div>
                        </div>

                        <div class="card mb-3">
                            <div class="card-header fw-bold">Major Achievement</div>
                            <div class="card-body">{{ nl2br(e($travelReport->major_achievement)) }}</div>
                        </div>

                        <div class="card mb-3">
                            <div class="card-header fw-bold">Daily Carried Activities / Completed Tasks</div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th style="width: 10%">Day</th>
                                                <th style="width: 15%">Date</th>
                                                <th>Carried Activities / Completed Tasks</th>
                                                <th style="width: 25%">Remarks</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $itineraries =
                                                    $travelRequest?->travelRequestDayItineraries ?? collect();
                                            @endphp

                                            @forelse($itineraries as $index => $itinerary)
                                                @php
                                                    $date = \Carbon\Carbon::parse($itinerary->date);
                                                    $weekday = $date->format('l');
                                                    $formattedDate = $date->format('d M Y');
                                                @endphp

                                                <tr>
                                                    <td class="text-center fw-bold">{{ $weekday }}</td>
                                                    <td class="text-nowrap">{{ $formattedDate }}</td>
                                                    <td>
                                                        {!! $itinerary->completed_tasks
                                                            ? nl2br(e($itinerary->completed_tasks))
                                                            : '<em class="text-muted">Not filled</em>' !!}
                                                    </td>
                                                    <td>
                                                        {!! $itinerary->remarks ? nl2br(e($itinerary->remarks)) : '' !!}
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="4" class="text-center text-muted">
                                                        No itinerary days available for this travel request.
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="card mb-3">
                            <div class="card-header fw-bold">Not Completed Activities & Reasons</div>
                            <div class="card-body">{{ nl2br(e($travelReport->not_completed_activities)) }}</div>
                        </div>

                        <div class="card mb-3">
                            <div class="card-header fw-bold">Conclusion & Recommendations</div>
                            <div class="card-body">{{ nl2br(e($travelReport->conclusion_recommendations)) }}</div>
                        </div>

                        <div class="card mb-3">
                            @include('Attachment::list', [
                                'modelType' => 'Modules\TravelRequest\Models\TravelReport',
                                'modelId' => $travelReport->id,
                            ])
                        </div>

                        <div class="card">
                            <div class="card-header fw-bold">Approval & Comments History</div>
                            <div class="card-body">
                                <div class="c-b">
                                    @foreach ($travelReport->logs as $log)
                                        <div class="d-flex py-2 flex-row gap-2 mb-2 border-bottom">
                                            <div class="rounded-circle user-icon">
                                                <i class="bi-person-circle fs-5"></i>
                                            </div>
                                            <div class="w-100">
                                                <div
                                                    class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                                                    <div
                                                        class="d-flex flex-column flex-md-row align-items-start align-items-md-center gap-md-2 mb-2 mb-md-0">
                                                        <label class="form-label mb-0">{{ $log->getCreatedBy() }}</label>
                                                        <span class="badge bg-primary c-badge">
                                                            {!! $log->createdBy->employee->latestTenure->getDesignationName() !!}
                                                        </span>
                                                    </div>
                                                    <small title="{{ $log->created_at }}">
                                                        {{ $log->created_at->format('M d, Y h:i A') }}
                                                    </small>
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
            </div>
        </div>
    </section>
@endsection
