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
    </style>
@endsection

@section('page_js')

    <!-- Frappe Gantt CDN -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/frappe-gantt@0.6.1/dist/frappe-gantt.min.js"></script>

    <script type="text/javascript">
        document.addEventListener('DOMContentLoaded', function(e) {
            $('#navbarVerticalMenu').find('#project-index').addClass('active');

            // Build tasks from project activities (exclude overall project duration)
            const tasks = [];
            @foreach ($projectActivity as $activity)
                @php
                    $start = $activity->start_date ? $activity->start_date->format('Y-m-d') : null;
                    $end = $activity->completion_date ? $activity->completion_date->format('Y-m-d') : null;
                @endphp
                @if ($start && $end)
                    @php
                        $members = $activity->members->pluck('full_name')->join(', ');
                        $taskData = [
                            'id' => 'act-' . $activity->id,
                            'name' => $activity->title,
                            'start' => $start,
                            'end' => $end,
                            'progress' => 0,
                            'members' => $members,
                        ];
                        if ($activity->parent_id) {
                            $taskData['dependencies'] = 'act-' . $activity->parent_id;
                        }
                    @endphp
                    tasks.push(@json($taskData));
                @endif
            @endforeach

            if (document.querySelector('#gantt')) {
                new Gantt('#gantt', tasks, {
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
                        return (
                            '<div class="details-container" style="padding: 12px; min-width: 220px; background: #ffffff; border-radius: 6px; box-shadow: 0 4px 12px rgba(0,0,0,0.15);">' +
                            '<div class="title" style="font-weight: 600; margin-bottom: 8px; color: #1a202c; font-size: 14px;">' +
                            task.name + '</div>' +
                            '<div class="dates" style="color: #4a5568; font-size: 12px; margin-bottom: 8px;">' +
                            startDate + ' - ' + endDate + '</div>' +
                            '<div class="members" style="color: #2d3748; font-size: 11px; border-top: 1px solid #e2e8f0; padding-top: 8px; margin-top: 8px;"><strong style="color: #1a202c;">Members:</strong> ' +
                            members + '</div>' +
                            '</div>'
                        );
                    }
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
                        <li class="breadcrumb-item active" aria-current="page">Project Details</li>
                    </ol>
                </nav>
                <h4 class="m-0 lh1 mt-1 fs-6 text-uppercase fw-bold text-primary">Project Details</h4>
            </div>

            <div>
                <button class="btn btn-primary btn-sm">Gantt</button>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card h-100">
                <div class="card-header">
                    <div class="d-flex align-items-center justify-content-between">
                        <span class="fw-bold">Project Gantt : {{ $project->short_name }}</span>
                        <div class="justify-content-end d-flex gap-2">


                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div id="gantt"></div>
                </div>
            </div>
        </div>
    </div>

@endsection
