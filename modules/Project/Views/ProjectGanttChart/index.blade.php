@extends('layouts.container')

@section('title', 'Project Gantt Chart')

@section('page_css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/frappe-gantt@0.6.1/dist/frappe-gantt.css">
    <style>
        #gantt {
            min-height: 420px;
        }

        .gantt .grid-background {
            fill: #ffffff;
            stroke: var(--ohw-blue);
            stroke-width: 1;
        }

        .gantt .bar-progress,
        .gantt .bar,
        .gantt .bar:hover {
            fill: var(--ohw-blue) !important;
        }

        .gantt .arrow {
            stroke: var(--ohw-blue) !important;
            stroke-width: 1.4px;
            fill: none;
            opacity: 0.4;
        }

        .gantt .arrow-head {
            fill: var(--ohw-blue) !important;
            opacity: 0.5;
        }

        .gantt .popup-wrapper .title::before {
            background: var(--ohw-blue) !important;
        }

        .gantt .gantt-month-clickable {
            cursor: pointer;
            background: var(--ohw-blue) !important;
        }

        .gantt .gantt-month-selected {
            font-weight: 600;
            text-decoration: underline;
            fill: var(--ohw-blue);
        }
    </style>
@endsection

@section('page_js')

    <!-- Frappe Gantt CDN -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/frappe-gantt@0.6.1/dist/frappe-gantt.min.js"></script>

    <script type="text/javascript">
        document.addEventListener('DOMContentLoaded', function(e) {
            $('#navbarVerticalMenu').find('#project-index').addClass('active');

            const projectYear = {{ $project->start_date ? $project->start_date->format('Y') : now()->format('Y') }};

            // Build tasks from project activities (exclude overall project duration)
            // and clamp them within the project's own start/completion dates
            const tasks = [];
            @foreach ($projectActivity as $activity)
                @php
                    $projectStart = $project->start_date;
                    $projectEnd = $project->completion_date;

                    $startDate = $activity->start_date;
                    $endDate = $activity->completion_date;

                    if ($projectStart && $startDate && $startDate->lt($projectStart)) {
                        $startDate = $projectStart->copy();
                    }

                    if ($projectEnd && $endDate && $endDate->gt($projectEnd)) {
                        $endDate = $projectEnd->copy();
                    }

                    $start = $startDate ? $startDate->format('Y-m-d') : null;
                    $end = $endDate ? $endDate->format('Y-m-d') : null;
                @endphp
                @if ($start && $end && $start <= $end)
                    @php
                        $members = $activity->members->pluck('full_name')->join(', ');
                        $level = $activity->activity_level ? ucfirst(str_replace('_', ' ', $activity->activity_level)) : '';
                        $taskData = [
                            'id' => 'act-' . $activity->id,
                            'name' => $activity->title,
                            'start' => $start,
                            'end' => $end,
                            'progress' => 0,
                            'members' => $members,
                            'level' => $level,
                        ];
                        if ($activity->parent_id) {
                            $taskData['dependencies'] = 'act-' . $activity->parent_id;
                        }
                    @endphp
                    tasks.push(@json($taskData));
                @endif
            @endforeach

            const allTasks = tasks.slice();
            const ganttSelector = '#gantt';

            // Precompute earliest task start for mapping month headers to real calendar months
            const allTaskStarts = allTasks.map(function(task) {
                return moment(task.start);
            }).filter(function(m) {
                return m.isValid();
            });
            const minTaskStart = allTaskStarts.length ? moment.min(allTaskStarts) : null;

            const ganttOptions = {
                view_mode: 'Month',
                language: 'en',
                bar_height: 30,
                padding: 22,
                column_width: 35,
                on_click: function(task) {
                    console.log(task);
                },
                custom_popup_html: function(task) {
                    const startDate = moment(task._start).format('MMM DD');
                    const endDate = moment(task._end).format('MMM DD');
                    const members = task.members || 'No members assigned';
                    const level = task.level || '';
                    return (
                        '<div class="details-container" style="padding: 12px; min-width: 220px; background: #ffffff; border-radius: 6px; box-shadow: 0 4px 12px rgba(0,0,0,0.15);">' +
                        '<div class="title" style="font-weight: 600; margin-bottom: 8px; color: #1a202c; font-size: 14px;">' +
                        task.name + '</div>' +
                        (level ?
                            '<div class="level" style="color: #4a5568; font-size: 11px; margin-top: -4px; margin-bottom: 6px;"><strong style="color: #1a202c;">Type:</strong> ' +
                            level + '</div>' : '') +
                        '<div class="dates" style="color: #4a5568; font-size: 12px; margin-bottom: 8px;">' +
                        startDate + ' - ' + endDate + '</div>' +
                        '<div class="members" style="color: #2d3748; font-size: 11px; border-top: 1px solid #e2e8f0; padding-top: 8px; margin-top: 8px;"><strong style="color: #1a202c;">Members:</strong> ' +
                        members + '</div>' +
                        '</div>'
                    );
                }
            };

            function attachMonthClickHandlers() {
                if (!minTaskStart) {
                    return;
                }

                // Sort month labels by x-position so we can align them with calendar months
                const monthLabels = Array.from(document.querySelectorAll('.gantt .lower-text')).sort(function(a,
                    b) {
                    const ax = parseFloat(a.getAttribute('x') || '0');
                    const bx = parseFloat(b.getAttribute('x') || '0');
                    return ax - bx;
                });

                if (!monthLabels.length) {
                    return;
                }

                const baseMonthIndex = minTaskStart.month(); // 0-based
                const baseYear = minTaskStart.year();

                monthLabels.forEach(function(labelEl, index) {
                    const text = (labelEl.textContent || '').trim();
                    if (!text) {
                        return;
                    }

                    const m = moment(text, 'MMMM', true);
                    if (!m.isValid()) {
                        return;
                    }

                    const absoluteIndex = baseMonthIndex + index; // months since base
                    const year = baseYear + Math.floor(absoluteIndex / 12);
                    const month = (absoluteIndex % 12) + 1; // 1-12

                    labelEl.style.cursor = 'pointer';
                    labelEl.classList.add('gantt-month-clickable');
                    labelEl.dataset.month = String(month);
                    labelEl.dataset.year = String(year);

                    labelEl.addEventListener('click', function() {
                        const clickedMonth = parseInt(this.dataset.month || '', 10);
                        const clickedYear = parseInt(this.dataset.year || '', 10);
                        if (!clickedMonth || !clickedYear) {
                            return;
                        }
                        filterByMonth(clickedMonth, clickedYear);
                    });
                });
            }

            function highlightSelectedMonth(month, year) {
                const labels = document.querySelectorAll('.gantt .gantt-month-clickable');
                labels.forEach(function(labelEl) {
                    labelEl.classList.remove('gantt-month-selected');

                    if (!month || !year) {
                        return;
                    }

                    const thisMonth = parseInt(labelEl.dataset.month || '', 10);
                    const thisYear = parseInt(labelEl.dataset.year || '', 10);

                    if (thisMonth === month && thisYear === year) {
                        labelEl.classList.add('gantt-month-selected');
                    }
                });
            }

            function hideNoDataMessage() {
                const msgEl = document.getElementById('gantt-no-data-message');
                if (!msgEl) {
                    return;
                }
                msgEl.textContent = '';
                msgEl.classList.add('d-none');
            }

            function showNoDataMessage() {
                const msgEl = document.getElementById('gantt-no-data-message');
                if (!msgEl) {
                    return;
                }
                msgEl.textContent =
                    'There is no data on this month. Please click Reset Filter to view Gantt chart again.';
                msgEl.classList.remove('d-none');
            }

            function updateFilterLabel(month, year) {
                const labelEl = document.getElementById('gantt-filter-label');
                if (!labelEl) {
                    return;
                }

                if (!month || !year) {
                    labelEl.textContent = '';
                    labelEl.classList.add('d-none');
                    return;
                }

                const m = moment({
                    year: year,
                    month: month - 1,
                    day: 1
                });

                labelEl.textContent = 'Filtered: ' + m.format('MMMM YYYY');
                labelEl.classList.remove('d-none');
            }

            function renderGantt(data) {
                const container = document.querySelector(ganttSelector);
                if (!container) {
                    return;
                }
                container.innerHTML = '';
                new Gantt(ganttSelector, data, ganttOptions);
                attachMonthClickHandlers();
            }

            if (document.querySelector(ganttSelector)) {
                renderGantt(allTasks);
            }

            function filterByMonth(month, year) {
                hideNoDataMessage();

                if (!month || !year) {
                    highlightSelectedMonth(null, null);
                    updateFilterLabel(null, null);
                    renderGantt(allTasks);
                    return;
                }

                const monthStart = moment({
                    year: year,
                    month: month - 1,
                    day: 1
                });
                const monthEnd = monthStart.clone().endOf('month');

                const filtered = allTasks.filter(function(task) {
                    const taskStart = moment(task.start);
                    const taskEnd = moment(task.end);

                    return taskStart.isSameOrBefore(monthEnd, 'day') &&
                        taskEnd.isSameOrAfter(monthStart, 'day');
                });

                if (!filtered.length) {
                    // No activities in this month/year; keep existing chart and show a message
                    highlightSelectedMonth(month, year);
                    updateFilterLabel(month, year);
                    showNoDataMessage();
                    return;
                }

                renderGantt(filtered);
                highlightSelectedMonth(month, year);
                updateFilterLabel(month, year);
            }

            const resetButton = document.getElementById('gantt-reset-filter');
            if (resetButton) {
                resetButton.addEventListener('click', function() {
                    filterByMonth(null, null);
                });
            }

        });
    </script>
@endsection

@section('page-content')
    <div class="pb-3 mb-3 border-bottom">
        <div class="d-flex flex-column flex-lg-row align-items-start align-items-lg-center gap-2">
            <div class="brd-crms flex-grow-1">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('dashboard.index') }}" class="text-decoration-none text-dark">Home</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('project.index') }}" class="text-decoration-none text-dark">Project</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">Project Gantt</li>
                    </ol>
                </nav>
                <h4 class="m-0 lh1 mt-1 fs-6 text-uppercase fw-bold text-primary">Project Gantt</h4>
            </div>

            @include('Project::Partials.project-header-actions', ['project' => $project])
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card h-100">
                <div class="card-header">
                    <div class="d-flex align-items-center justify-content-between">
                        <span class="fw-bold">Project Gantt : {{ $project->short_name }}</span>
                        <div class="justify-content-end d-flex gap-2 align-items-center">
                            @if ($project->start_date && $project->completion_date)
                                <span class="small text-muted me-2">
                                    {{ $project->start_date->format('M d, Y') }}
                                    &ndash;
                                    {{ $project->completion_date->format('M d, Y') }}
                                </span>
                            @endif
                            <span id="gantt-filter-label" class="small text-primary fw-semibold me-2 d-none"></span>
                            <button id="gantt-reset-filter" type="button" class="btn btn-outline-primary btn-sm">Reset
                                Filter</button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div id="gantt-no-data-message" class="alert alert-warning py-2 px-3 small d-none mb-2"></div>
                    <div id="gantt"></div>
                </div>
            </div>
        </div>
    </div>

@endsection
