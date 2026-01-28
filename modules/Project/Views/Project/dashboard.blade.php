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
            background: #01AEF0;
        }

        .stat-card-completed {
            background: #0198d6;
        }

        .stat-card-progress {
            background: #0288c0;
        }

        .stat-card-pending {
            background: #7ad7f7;
            color: #053246;
        }

        .stat-card-nr {
            background:  #2f4f4f;
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
            var rootStyles = getComputedStyle(document.documentElement);
            var brand = (rootStyles.getPropertyValue('--ohw-blue') || '').trim() || '#01AEF0';
            var secondary = (rootStyles.getPropertyValue('--ohw--secondary') || '').trim() || '#997a5b';
            var brandDark = '#0288c0';
            var brandMid = '#0198d6';
            var brandLight = '#7ad7f7';
            var neutral = '#2f4f4f';

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
                colors: [brandMid, brandDark, brandLight, secondary || neutral],
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
                            <li class="breadcrumb-item active" aria-current="page">PMS Dashboard</li>
                        </ol>
                    </nav>
                    <h4 class="m-0 lh1 mt-1 fs-6 text-uppercase fw-bold text-primary">
                        PMS Dashboard – {{ $project->short_name ?? $project->title }}
                    </h4>
                </div>
            </div>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-md-3">
                <div class="stat-card stat-card-total">
                    <small>Total planned activities</small>
                    <h3 class="mb-1">{{ $totalActivities }}</h3>
                    <span class="badge rounded-pill mt-1">
                        {{ optional($project->start_date)->format('M d, Y') }} –
                        {{ optional($project->completion_date)->format('M d, Y') }}
                    </span>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card stat-card-completed">
                    <small>Completed</small>
                    <h3 class="mb-1">{{ $statusDistribution['completed'] }}</h3>
                    <span class="badge rounded-pill mt-1">{{ $completionRate }}% completion</span>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card stat-card-progress">
                    <small>Under progress</small>
                    <h3 class="mb-1">{{ $statusDistribution['under_progress'] }}</h3>
                    <span class="badge rounded-pill mt-1">{{ $totalStages }} stages</span>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card stat-card-pending mb-2">
                    <small>Not started</small>
                    <h3 class="mb-1">{{ $statusDistribution['not_started'] }}</h3>
                    <span class="badge rounded-pill mt-1">{{ $totalMembers }} project members</span>
                </div>
                <div class="stat-card stat-card-nr">
                    <small>No longer required</small>
                    <h3 class="mb-0">{{ $statusDistribution['no_required'] }}</h3>
                </div>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-lg-6">
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h6 class="mb-0 fw-bold">Activity status distribution</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-2 fs-6 text-muted">
                            Total planned activities ({{ $totalActivities }})
                        </div>
                        <div id="projectPmsStatusChart" class="activity-status-chart"></div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card shadow-sm h-100">
                    <div class="card-header">
                        <h6 class="mb-0 fw-bold">Project Detail</h6>
                    </div>
                    <div class="card-body">
                        <dl class="row mb-0">
                            <dt class="col-sm-4">Project</dt>
                            <dd class="col-sm-8">{{ $project->title }}</dd>

                            <dt class="col-sm-4">Short name</dt>
                            <dd class="col-sm-8">{{ $project->short_name ?? '—' }}</dd>

                            <dt class="col-sm-4">Duration</dt>
                            <dd class="col-sm-8">
                                {{ optional($project->start_date)->format('M d, Y') }} –
                                {{ optional($project->completion_date)->format('M d, Y') }}
                            </dd>

                            <dt class="col-sm-4">Stages</dt>
                            <dd class="col-sm-8">{{ $totalStages }}</dd>

                            <dt class="col-sm-4">Members</dt>
                            <dd class="col-sm-8">{{ $totalMembers }}</dd>

                            <dt class="col-sm-4">Completion rate</dt>
                            <dd class="col-sm-8">{{ $completionRate }}%</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

