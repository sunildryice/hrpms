@extends('layouts.container')

@section('title', 'PMS Dashboard')

@section('page_css')
    <style>
        .dashboard-header h4 {
            letter-spacing: .02em;
        }

        .stat-card {
            border-radius: .75rem;
            padding: 1.1rem 1.25rem;
            color: #fff;
            position: relative;
            overflow: hidden;
        }

        .stat-card small {
            text-transform: uppercase;
            letter-spacing: .12em;
            font-weight: 600;
        }

        .stat-card h3 {
            font-weight: 700;
            margin: .35rem 0 0;
        }

        .stat-card .badge {
            background: rgba(255, 255, 255, .2);
        }

        .stat-card-total {
            background: #01b1f7;
        }

        .stat-card-completed {
            background: #27ae60;
        }

        .stat-card-progress {
            background: #f8c90c;
        }

        .stat-card-pending {
            background: #eb7d1d;
        }

        .stat-card-nr {
            background: #e74c3c;
        }

        .activity-status-chart {
            min-height: 320px;
        }
    </style>
@endsection

@section('page_js')
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var Completed = '#27ae60';
            var UnderProgress = '#f8c90c';
            var NotStarted = '#eb7d1d';
            var NoLongerRequired = '#e74c3c';

            var options = {
                series: [
                    {{ $statusDistribution['completed'] }},
                    {{ $statusDistribution['under_progress'] }},
                    {{ $statusDistribution['not_started'] }},
                    {{ $statusDistribution['no_required'] }}
                ],
                chart: {
                    width: '100%',
                    height: 360,
                    type: 'pie',
                    toolbar: {
                        show: true
                    },
                },
                labels: ['Completed', 'Under Progress', 'Not Started', 'No Longer Required'],
                colors: [Completed, UnderProgress, NotStarted, NoLongerRequired],
                responsive: [{
                    breakpoint: 480,
                    options: {
                        chart: {
                            height: 320
                        },
                        legend: {
                            position: 'bottom'
                        }
                    }
                }],
            };

            var chart = new ApexCharts(document.querySelector("#projectPmsStatusChart"), options);
            chart.render();

            $(document.querySelector('[name="from_date"]')).datepicker({
                language: 'en-GB',
                autoHide: true,
                format: 'yyyy-mm-dd',
                startDate: '{{ $project->start_date->format('Y-m-d') }}',
                endDate: '{{ $project->completion_date->format('Y-m-d') }}'

            });

            $(document.querySelector('[name="to_date"]')).datepicker({
                language: 'en-GB',
                autoHide: true,
                format: 'yyyy-mm-dd',
                startDate: '{{ $project->start_date->format('Y-m-d') }}',
                endDate: '{{ $project->completion_date->format('Y-m-d') }}'
            });
        });
    </script>
@endsection

@section('page-content')
    <div class="container-fluid">
        <div class="pb-3 mb-3 border-bottom">
            <div class="d-flex flex-column flex-lg-row align-items-start align-items-lg-center gap-2 dashboard-header">
                <div class="brd-crms flex-grow-1">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item">
                                <a href="{{ route('dashboard.index') }}" class="text-decoration-none text-dark">Home</a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="{{ route('project.index') }}" class="text-decoration-none text-dark">Project</a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="{{ route('project.show', $project->id) }}"
                                    class="text-decoration-none text-dark">{{ $project->short_name ?? $project->title }}</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
                        </ol>
                    </nav>
                    <h4 class="m-0 lh1 mt-1 fs-6 text-uppercase fw-bold text-primary">
                        Dashboard – {{ $project->short_name ?? $project->title }}
                    </h4>
                </div>
                @include('Project::Partials.project-header-actions', ['project' => $project])
            </div>
        </div>

        <div class="row mb-2">
            <div class="col-12">
                <div class="card shadow-sm">
                    {{-- <div class="card-header">
                        <span class="mb-0 fw-bold">Filter Activities</span>
                    </div> --}}
                    <div class="card-body">
                        <form method="GET" class="row g-3 align-items-end">
                            <div class="col-md-2 col-sm-6">
                                <label for="from_date" class="form-label">From Date</label>
                                <input type="text" name="from_date" id="from_date" class="form-control"
                                    value="{{ old('from_date', $fromDate ?? '') }}" placeholder="yyyy-mm-dd">
                            </div>

                            <div class="col-md-2 col-sm-6">
                                <label for="to_date" class="form-label">To Date</label>
                                <input type="text" name="to_date" id="to_date" class="form-control"
                                    value="{{ old('to_date', $toDate ?? '') }}" placeholder="yyyy-mm-dd">
                            </div>

                            <div class="col-md-2 col-sm-12 d-flex gap-1 align-items-end">
                                <button type="submit" class="m-1 btn btn-primary btn-sm">Search</button>
                                <a href="{{ route('project.dashboard', $project->id) }}"
                                    class="m-1 btn btn-secondary btn-sm">Reset</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3 mb-3">
            {{-- <div class="col-md-3">
                <div class="stat-card stat-card-total">
                    <small class="text-capitalize">Total</small>
                    <h3 class="mb-1">{{ $totalActivities }}</h3>
                    <span class="badge rounded-pill mt-1">
                        {{ optional($project->start_date)->format('M d, Y') }} –
                        {{ optional($project->completion_date)->format('M d, Y') }}
                    </span>
                </div>
            </div> --}}
            <div class="col-md-3">
                <div class="stat-card stat-card-completed">
                    <small class="text-capitalize">Completed</small>
                    <h3 class="mb-1">{{ $statusDistribution['completed'] }}</h3>
                    <span class="badge rounded-pill mt-1">{{ $percentages['completed'] }}% completion</span>
                </div>
            </div>

            <div class="col-md-3">
                <div class="stat-card stat-card-progress">
                    <small class="text-capitalize">Under progress</small>
                    <h3 class="mb-1">{{ $statusDistribution['under_progress'] }}</h3>
                    <span class="badge rounded-pill mt-1">{{ $percentages['under_progress'] }}%</span>
                </div>
            </div>

            <div class="col-md-3">
                <div class="stat-card stat-card-pending">
                    <small class="text-capitalize">Not started</small>
                    <h3 class="mb-1">{{ $statusDistribution['not_started'] }}</h3>
                    <span class="badge rounded-pill mt-1">{{ $percentages['not_started'] }}%</span>
                </div>
            </div>

            <div class="col-md-3">
                <div class="stat-card stat-card-nr">
                    <small class="text-capitalize">No longer required</small>
                    <h3 class="mb-1">{{ $statusDistribution['no_required'] }}</h3>
                    <span class="badge rounded-pill mt-1">{{ $percentages['no_required'] }}%</span>
                </div>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-lg-6 d-flex">
                <div class="card shadow-sm flex-fill h-100">
                    <div class="card-header">
                        <h6 class="mb-0 fw-bold text-capitalize">Activity status distribution</h6>
                    </div>
                    <div class="card-body d-flex flex-column">
                        <div class="mb-2 fs-6 text-muted">
                            Total planned activities ({{ $totalActivities }})
                        </div>
                        <div id="projectPmsStatusChart" class="activity-status-chart"></div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6 d-flex">
                <div class="card shadow-sm flex-fill h-100">
                    <div class="card-header">
                        <h6 class="mb-0 fw-bold text-capitalize">Project Detail</h6>
                    </div>
                    <div class="card-body">
                        <dl class="row mb-0">
                            <dt class="col-sm-4">Project</dt>
                            <dd class="col-sm-8">{{ $project->title }} ({{ $project?->short_name }})</dd>

                            <dt class="col-sm-4">Duration</dt>
                            <dd class="col-sm-8">
                                {{ optional($project->start_date)->format('M d, Y') }} –
                                {{ optional($project->completion_date)->format('M d, Y') }}
                            </dd>

                            <dt class="col-sm-4">{{ __('label.description') }}</dt>
                            <dd class="col-sm-8">
                                {{ $project->description ? Str::limit($project->description, 180) : '-' }}</dd>

                            <dt class="col-sm-4">{{ __('label.team-lead') }}</dt>
                            <dd class="col-sm-8">
                                {{ isset($users) ? $users[$project->team_lead_id] ?? '-' : $project->teamLead->name ?? '-' }}
                            </dd>

                            <dt class="col-sm-4">{{ __('label.focal-person') }}</dt>
                            <dd class="col-sm-8">
                                {{ isset($users) ? $users[$project->focal_person_id] ?? '-' : $project->focalPerson->name ?? '-' }}
                            </dd>

                            <dt class="col-sm-4">Members</dt>
                            <dd class="col-sm-8">@php($memberNames = $project->members->pluck('full_name')->filter()->implode(', '))
                                {{ $memberNames ?: '-' }} ({{ $totalMembers }})</dd>

                            <dt class="col-sm-4">Stages</dt>
                            <dd class="col-sm-8">@php($stageTitles = $project->stages->pluck('title')->filter()->implode(', '))
                                {{ $stageTitles ?: '-' }} ({{ $totalStages }})</dd>

                            {{-- <dt class="col-sm-4">Completion rate</dt>
                            <dd class="col-sm-8">{{ $completionRate }}%</dd> --}}
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
