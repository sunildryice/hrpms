@extends('layouts.container')

@section('title', 'PMS Dashboard')

@section('page_css')
    <style>
        .chart-container {
            min-height: 500px;
        }

        .timeline-labels {
            display: flex;
            justify-content: space-between;
            font-size: 0.9rem;
            color: #555;
            margin: 0 1rem 0.5rem;
            font-weight: 500;
        }
    </style>
@endsection

@section('page_js')
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        $(document).ready(function() {
            $('#project_ids').select2({
                placeholder: "Select projects ",
                allowClear: true,
                width: '100%'
            });
        });
        document.addEventListener("DOMContentLoaded", function() {

            // Chart 1: Percentage Stacked Bar
            const percentOptions = {
                series: @json($seriesPercent),
                chart: {
                    type: 'bar',
                    height: Math.max(400, {{ count($projectNames) * 60 }}),
                    toolbar: {
                        show: true
                    },
                    stacked: true,
                },
                plotOptions: {
                    bar: {
                        horizontal: true,
                        borderRadius: 4,
                        barHeight: '50%',
                        dataLabels: {
                            position: 'center'
                        }
                    },
                },
                dataLabels: {
                    enabled: true,
                    formatter: val => val > 5 ? val.toFixed(1) + "%" : "",
                    style: {
                        fontSize: '12px',
                        colors: ['#fff']
                    }
                },
                xaxis: {
                    categories: @json($projectNames),
                    min: 0,
                    max: 100,
                    tickAmount: 10,
                    title: {
                        text: 'Status Distribution (%)'
                    }
                },
                colors: ['#27ae60', '#f8c90c', '#eb7d1d', '#e74c3c'],
                legend: {
                    position: 'top',
                    horizontalAlign: 'left',
                    offsetX: 40
                },
                tooltip: {
                    y: {
                        formatter: val => val.toFixed(1) + "%"
                    }
                },
            };
            new ApexCharts(document.querySelector("#percent-chart"), percentOptions).render();

            // Chart 2: Timeline RangeBar 
            const timelineOptions = {
                series: [{
                    name: 'Project Duration',
                    data: @json($seriesTimeline[0]['data'])
                }],
                chart: {
                    type: 'rangeBar',
                    zoom: {
                        enabled: true,
                        allowMouseWheelZoom: false
                    },
                    height: Math.max(400, {{ count($projectNames) * 70 }}),
                    toolbar: {
                        show: true
                    },
                },
                plotOptions: {
                    bar: {
                        horizontal: true,
                        barHeight: '50%',
                        distributed: false,
                    }
                },
                colors: ['#01aef0'],
                xaxis: {
                    type: 'datetime',
                    min: new Date('{{ $minYear->format('Y-m-d') }}').getTime(),
                    max: new Date('{{ $maxYear->format('Y-m-d') }}').getTime(),
                    labels: {
                        format: 'yyyy'
                    },
                    title: {
                        text: 'Project Timeline (Years)'
                    }
                },
                yaxis: {
                    categories: @json($projectNames),
                },
                // tooltip: {
                //     custom: function({
                //         dataPointIndex,
                //         w
                //     }) {
                //         const d = w.config.series[0].data[dataPointIndex];
                //         const m = d.meta || {};
                //         return `
            // <div class="p-3 bg-white border rounded shadow-sm">
            //     <strong>${d.x}</strong><br>
            //     ${m.title || ''}<br>
            //     Completed: ${m.percentages?.completed ?? 0}%<br>
            //     Under Progress: ${m.percentages?.under_progress ?? 0}%<br>
            //     Not Started: ${m.percentages?.not_started ?? 0}%<br>
            //     No Longer Required: ${m.percentages?.no_required ?? 0}%
            // </div>`;
                //     }
                // },
                // legend: {
                //     show: false
                // }
                tooltip: {
                    custom: function({
                        series,
                        seriesIndex,
                        dataPointIndex,
                        w
                    }) {
                        const d = w.config.series[seriesIndex].data[dataPointIndex];
                        const m = d.meta || {};
                        const total = (m.percentages?.completed || 0) +
                            (m.percentages?.under_progress || 0) +
                            (m.percentages?.not_started || 0) +
                            (m.percentages?.no_required || 0);

                        return `
            <div class="p-3 bg-white border rounded shadow" style="min-width:220px;">
                <div class="fw-bold mb-2">${d.x} — ${m.title || 'Project'}</div>
                <div class="progress" style="height:10px; margin-bottom:8px;">
                    <div class="progress-bar bg-success" style="width: ${m.percentages?.completed || 0}%"></div>
                    <div class="progress-bar bg-warning" style="width: ${m.percentages?.under_progress || 0}%"></div>
                    <div class="progress-bar bg-danger" style="width: ${m.percentages?.not_started || 0}%"></div>
                    <div class="progress-bar bg-secondary" style="width: ${m.percentages?.no_required || 0}%"></div>
                </div>
                <small>
                    Completed: ${m.percentages?.completed ?? 0}%<br>
                    Under Progress: ${m.percentages?.under_progress ?? 0}%<br>
                    Not Started: ${m.percentages?.not_started ?? 0}%<br>
                    No Longer Required: ${m.percentages?.no_required ?? 0}%
                </small>
            </div>`;
                    }
                }
            };

            new ApexCharts(document.querySelector("#timeline-chart"), timelineOptions).render();
        });
    </script>
@endsection

@section('page-content')
    <div class="container-fluid py-4">
        <div class="pb-3 mb-3 border-bottom">
            <div class="d-flex flex-column flex-lg-row align-items-start align-items-lg-center gap-2 dashboard-header">
                <div class="brd-crms flex-grow-1">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item">
                                <a href="{{ route('dashboard.index') }}" class="text-decoration-none text-dark">Home</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">PMS Dashboard</li>
                        </ol>
                    </nav>
                    <h4 class="m-0 lh1 mt-1 fs-6 text-uppercase fw-bold text-primary">
                        PMS Dashboard
                    </h4>
                </div>
            </div>
        </div>

        {{-- <div class="card shadow-sm mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('pms.dashboard') }}" class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label for="project_ids" class="form-label">Select Projects</label>
                        <select name="project_ids[]" id="project_ids" class="form-select select2" >
                            <option value="" {{ empty($projectIds) ? 'selected' : '' }}>Select project</option>
                            @foreach ($allProjects as $proj)
                                <option value="{{ $proj->id }}"
                                    {{ in_array($proj->id, $projectIds ?? []) ? 'selected' : '' }}>
                                    {{ $proj->short_name ? $proj->short_name : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 d-flex align-items-end gap-2">
                        <button type="submit" class="btn btn-primary">Search</button>
                        <a href="{{ route('pms.dashboard') }}" class="btn btn-secondary">Reset</a>
                    </div>
                </form>
            </div>
        </div> --}}

        <!-- Chart 1: Timeline -->
        <div class="card shadow-sm">
            <div class="card-header bg-light">
                <h6 class="mb-0 fw-bold">Project Timeline (Duration by Year)</h6>
            </div>
            <div class="card-body chart-container">
                <div class="timeline-labels">
                </div>
                <div id="timeline-chart"></div>
            </div>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-header bg-light">
                <h6 class="mb-0 fw-bold">Project Status Distribution (%)</h6>
            </div>
            <div class="card-body chart-container">
                <div id="percent-chart"></div>
            </div>
        </div>

    </div>
@endsection
