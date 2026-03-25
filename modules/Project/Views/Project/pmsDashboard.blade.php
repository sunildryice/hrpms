@extends('layouts.container')

@section('title', 'PMS Dashboard')

@section('page_css')
    <style>
        .chart-container {
            min-height: 500px;
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

            const timelineOptions = {
                series: @json($seriesTimeline),

                chart: {
                    type: 'rangeBar',
                    height: Math.max(100, {{ count($projectNames) * 40 }}),
                    zoom: {
                        enabled: true,
                        allowMouseWheelZoom: false
                    },
                    toolbar: {
                        show: true
                    }
                },

                plotOptions: {
                    bar: {
                        horizontal: true,
                        barHeight: '60%',
                        rangeBarGroupRows: true
                    }
                },

                colors: [
                    '#27ae60', // Completed
                    '#f8c90c', // Under Progress
                    '#eb7d1d', // Not Started
                    '#e74c3c', // No Required
                    '#01aef0', // ← extra color for "no activities" 
                ],

                dataLabels: {
                    enabled: true,
                    formatter: function(val, opts) {
                        const d = opts.w.config.series[opts.seriesIndex].data[opts.dataPointIndex];
                        const pct = d.meta?.percentage;
                        if (d.meta?.status === 'no_activities') return ' ';
                        return pct ? pct.toFixed(1) + '%' : '';
                    },
                    style: {
                        colors: ['#fff'],
                        fontSize: '11px',
                        fontWeight: 600
                    }
                },

                xaxis: {
                    type: 'datetime',
                    min: new Date('{{ $minYear->format('Y-m-d') }}').getTime(),
                    max: new Date('{{ $maxYear->format('Y-m-d') }}').getTime(),
                    labels: {
                        datetimeFormatter: {
                            year: 'yyyy'
                        }
                    },
                    title: {
                        text: 'Project Timeline'
                    }
                },

                yaxis: {
                    categories: @json($projectNames)
                },

                grid: {
                    borderColor: '#eee',
                    strokeDashArray: 4
                },

                legend: {
                    position: 'top',
                },

                tooltip: {
                    custom: function({
                        seriesIndex,
                        dataPointIndex,
                        w
                    }) {
                        const d = w.config.series[seriesIndex].data[dataPointIndex];
                        const m = d.meta || {};

                        if (m.status === 'no_activities') {
                            return `
                                <div class="p-2 bg-white border rounded shadow-sm">
                                    <strong>${d.x}</strong><br>
                                    <b>No activities / status data</b><br>
                                </div>`;
                        }

                        const start = new Date(d.y[0]);
                        const end = new Date(d.y[1]);

                        const format = (date) => date.toLocaleDateString('en-GB', {
                            day: '2-digit',
                            month: 'short',
                            year: 'numeric'
                        });

                        return `
                                <div class="p-2 bg-white border rounded shadow-sm">
                                    <strong>${d.x}</strong><br>
                                    <b>Status:</b> ${m.status.replaceAll('_', ' ').replace(/\b\w/g, c => c.toUpperCase())}<br>
                                    <b>${m.percentage.toFixed(1)}%</b><br>
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

        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('pms.dashboard') }}" class="row g-3 align-items-end">
                    <!-- Projects -->
                    <div class="col-md-6">
                        <label for="project_ids" class="form-label">Projects</label>
                        <select name="project_ids[]" id="project_ids" class="form-control select2" multiple>
                            {{-- <option value="" {{ empty($projectIds) ? 'selected' : '' }}>Select project</option> --}}
                            @foreach ($allProjects as $proj)
                                <option value="{{ $proj->id }}"
                                    {{ in_array($proj->id, $projectIds ?? []) ? 'selected' : '' }}>
                                    {{ $proj->short_name ? $proj->short_name : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Start Date -->
                    <div class="col-md-2">
                        <label for="start_date" class="form-label">Start Date</label>
                        <input type="text" name="start_date" id="start_date" class="form-control"
                            value="{{ $startDateFilter ?? '' }}" placeholder="yyyy-mm-dd">
                    </div>

                    <!-- End Date -->
                    <div class="col-md-2">
                        <label for="end_date" class="form-label">End Date</label>
                        <input type="text" name="end_date" id="end_date" class="form-control"
                            value="{{ $endDateFilter ?? '' }}" placeholder="yyyy-mm-dd">
                    </div>

                    <div class="col-md-2 d-flex align-items-end gap-2">
                        <button type="submit" class="btn btn-primary btn-sm">Search</button>
                        <a href="{{ route('pms.dashboard') }}" class="btn btn-secondary btn-sm">Reset</a>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h6 class="mb-0 fw-bold">Project Timeline</h6>
            </div>
            <div class="card-body chart-container">
                <div id="timeline-chart"></div>
            </div>
        </div>

    </div>
@endsection
