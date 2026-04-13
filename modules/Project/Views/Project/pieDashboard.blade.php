@extends('layouts.container')

@section('title', 'PMS Pie Dashboard')

@section('page_css')
    <style>
        .pie-card {
            min-height: 360px;
        }
        .pie-card .card-title {
            font-size: 0.85rem;
            font-weight: 600;
            text-align: center;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .no-activity-box {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 260px;
            color: #aaa;
            font-size: 0.85rem;
        }
    </style>
@endsection

@section('page_js')
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    <script>
        $(document).ready(function() {
            $('#project_ids').select2({
                allowClear: true,
                width: '100%'
            });

            $('#start_date').datepicker({
                language: 'en-GB',
                autoHide: true,
                format: 'yyyy-mm-dd',
                todayHighlight: true,
                clearBtn: true
            });

            $('#end_date').datepicker({
                language: 'en-GB',
                autoHide: true,
                format: 'yyyy-mm-dd',
                todayHighlight: true,
                clearBtn: true
            });
        });

        document.addEventListener("DOMContentLoaded", function() {
            const statusColors = {
                'Completed': '#27ae60',
                'Under Progress': '#f8c90c',
                'Not Started': '#eb7d1d',
                'Not Required': '#e74c3c',
            };

            const projects = @json($projectsData);

            projects.forEach(function(project) {
                const el = document.getElementById('pie-chart-' + project.id);
                if (!el) return;

                const series = [
                    project.completed,
                    project.under_progress,
                    project.not_started,
                    project.not_required,
                ];

                const total = series.reduce((a, b) => a + b, 0);

                if (total === 0) {
                    el.innerHTML = '<div class="no-activity-box">No activities</div>';
                    return;
                }

                const options = {
                    series: series,
                    labels: ['Completed', 'Under Progress', 'Not Started', 'Not Required'],
                    colors: [
                        statusColors['Completed'],
                        statusColors['Under Progress'],
                        statusColors['Not Started'],
                        statusColors['Not Required'],
                    ],
                    chart: {
                        type: 'donut',
                        height: 280,
                        toolbar: { show: false },
                    },
                    plotOptions: {
                        pie: {
                            donut: {
                                size: '60%',
                                labels: {
                                    show: true,
                                    total: {
                                        show: true,
                                        label: 'Total',
                                        formatter: function(w) {
                                            return w.globals.seriesTotals.reduce((a, b) => a + b, 0);
                                        }
                                    }
                                }
                            }
                        }
                    },
                    dataLabels: {
                        enabled: true,
                        formatter: function(val) {
                            return val.toFixed(1) + '%';
                        },
                        style: {
                            fontSize: '11px',
                        },
                        dropShadow: { enabled: false },
                    },
                    legend: {
                        position: 'bottom',
                        fontSize: '11px',
                    },
                    tooltip: {
                        y: {
                            formatter: function(val, opts) {
                                const t = opts.w.globals.seriesTotals.reduce((a, b) => a + b, 0);
                                const pct = t > 0 ? ((val / t) * 100).toFixed(1) : 0;
                                return val + ' (' + pct + '%)';
                            }
                        }
                    },
                };

                new ApexCharts(el, options).render();
            });
        });
    </script>
@endsection

@section('page-content')
    <div class="container-fluid py-4">

        {{-- Filter Form --}}
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('pms.pie-dashboard') }}" class="row g-3 align-items-end">
                    <div class="col-md-6">
                        <label for="project_ids" class="form-label">Projects</label>
                        <select name="project_ids[]" id="project_ids" class="form-control select2" multiple>
                            @foreach ($allProjects as $proj)
                                <option value="{{ $proj->id }}"
                                    {{ in_array($proj->id, $projectIds ?? []) ? 'selected' : '' }}>
                                    {{ $proj->short_name ?: $proj->title }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label for="start_date" class="form-label">Start Date</label>
                        <input type="text" name="start_date" id="start_date" class="form-control"
                            value="{{ $startDateFilter ?? '' }}" placeholder="yyyy-mm-dd">
                    </div>

                    <div class="col-md-2">
                        <label for="end_date" class="form-label">End Date</label>
                        <input type="text" name="end_date" id="end_date" class="form-control"
                            value="{{ $endDateFilter ?? '' }}" placeholder="yyyy-mm-dd">
                    </div>

                    <div class="col-md-2 d-flex align-items-end gap-2">
                        <button type="submit" class="btn btn-primary btn-sm">Search</button>
                        <a href="{{ route('pms.pie-dashboard') }}" class="btn btn-secondary btn-sm">Reset</a>
                    </div>
                </form>
            </div>
        </div>

        {{-- Pie Charts Grid --}}
        @if ($projects->isEmpty())
            <div class="alert alert-info">No projects found for the selected filters.</div>
        @else
            <div class="row g-3">
                @foreach ($projects as $project)
                    <div class="col-xl-3 col-lg-4 col-md-6">
                        <div class="card pie-card h-100">
                            <div class="card-body d-flex flex-column">
                                <p class="card-title mb-1" title="{{ $project->title }}">
                                    {{ $project->short_name ?: $project->title }}
                                </p>
                                <small class="text-muted text-center d-block mb-2" style="font-size:0.75rem;">
                                    Total: {{ $project->total_activities }} activities
                                </small>
                                <div id="pie-chart-{{ $project->id }}" class="flex-grow-1"></div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

    </div>
@endsection