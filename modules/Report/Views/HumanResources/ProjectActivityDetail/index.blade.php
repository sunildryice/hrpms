@extends('layouts.container')

@section('title', 'Report : Project Activity Detail')

@section('page_css')
    <style>
        .table-scroll-wrapper {
            overflow-x: auto;
            border: 1px solid var(--bs-border-color);
            border-radius: 0.375rem;
        }
        .table-scroll-wrapper table {
            min-width: 1400px;
            margin-bottom: 0;
            width: 100%;
        }
        .table-scroll-wrapper thead th {
            position: sticky;
            top: 0;
            z-index: 10;
            background: #f8f9fa;
            white-space: nowrap;
        }
        .col-detail {
            min-width: 220px;
            white-space: normal;
            word-break: break-word;
        }
    </style>
@endsection

@section('page_js')
    <script>
        $(document).ready(function() {
            $('#navbarVerticalMenu').find('#project-activity-detail-report-menu').addClass('active');

            $('[name="from_date"], [name="to_date"]').datepicker({
                language: 'en-GB',
                autoHide: true,
                format: 'yyyy-mm-dd',
            });

            function filterActivitiesByProject() {
                var projectId = $('[name="project_id"]').val();
                var $activitySelect = $('[name="activity_id"]');
                var selectedVal = $activitySelect.val();

                var $options = $activitySelect.find('option');
                var $filteredOptions = $('<div></div>');
                $options.each(function() {
                    var $opt = $(this).clone();
                    if ($opt.val() === '' || projectId === '' || $opt.data('project') == projectId) {
                        $filteredOptions.append($opt);
                    }
                });

                if ($activitySelect.data('select2')) {
                    $activitySelect.select2('destroy');
                }

                $activitySelect.empty();
                $activitySelect.append($filteredOptions.children().clone());

                if ($activitySelect.find('option[value="' + selectedVal + '"]').length) {
                    $activitySelect.val(selectedVal);
                } else {
                    $activitySelect.val('');
                }

                $activitySelect.select2({
                    placeholder: 'All Activities',
                    allowClear: true
                });
            }

            $('[name="project_id"]').on('change', filterActivitiesByProject);
            filterActivitiesByProject();
        });
    </script>
@endsection

@section('page-content')
    <div class="container-fluid">
        <div class="pb-3 mb-3 border-bottom">
            <div class="d-flex flex-column flex-lg-row align-items-start align-items-lg-center gap-2">
                <div class="flex-grow-1">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}"
                                    class="text-decoration-none text-dark">Home</a></li>
                            <li class="breadcrumb-item" aria-current="page">@yield('title')</li>
                        </ol>
                    </nav>
                    <h4 class="m-0 lh1 mt-1 fs-6 text-uppercase fw-bold text-primary">@yield('title')</h4>
                </div>
                <div class="justify-content-end">
                    <a href="{{ route('report.project.activity.detail.export', request()->query()) }}"
                       class="btn btn-primary btn-sm">
                        <i class="bi bi-download"></i> Export
                    </a>
                </div>
            </div>
        </div>

        <form method="GET" action="{{ route('report.project.activity.detail.index') }}">
            <div class="card shadow-sm border rounded mb-3">
                <div class="card-body">
                    <div class="row g-3 align-items-end">
                        <div class="col-lg-3">
                            <label class="form-label">Project</label>
                            <select name="project_id" class="form-select select2">
                                <option value="">All Projects</option>
                                @foreach ($projects as $project)
                                    <option value="{{ $project->id }}" {{ request('project_id') == $project->id ? 'selected' : '' }}>
                                        {{ $project->short_name ?: $project->title }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-3">
                            <label class="form-label">Project Activity</label>
                            <select name="activity_id" class="form-select select2">
                                <option value="">All Activities</option>
                                @foreach ($activities as $activity)
                                    <option value="{{ $activity->id }}" data-project="{{ $activity->project_id }}" {{ request('activity_id') == $activity->id ? 'selected' : '' }}>
                                        {{ $activity->title }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-2">
                            <label class="form-label">From</label>
                            <input class="form-control" type="text" name="from_date" value="{{ request('from_date') }}" placeholder="yyyy-mm-dd" autocomplete="off">
                        </div>
                        <div class="col-lg-2">
                            <label class="form-label">To</label>
                            <input class="form-control" type="text" name="to_date" value="{{ request('to_date') }}" placeholder="yyyy-mm-dd" autocomplete="off">
                        </div>
                        <div class="col-lg-2 d-flex align-items-end gap-2 pb-1">
                            <button type="submit" class="btn btn-primary btn-sm">Search</button>
                            <a href="{{ route('report.project.activity.detail.index') }}" class="btn btn-secondary btn-sm">Reset</a>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <section>
            <div class="card shadow-sm border rounded">
                <div class="card-body">
                    <div class="table-scroll-wrapper">
                        <table class="table table-sm table-bordered align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th style="width:50px">{{ __('label.sn') }}</th>
                                    <th style="min-width:180px">Project</th>
                                    <th style="min-width:200px">Activity Title</th>
                                    <th>Status</th>
                                    <th class="col-detail">Key Accomplishments</th>
                                    <th class="col-detail">Challenges</th>
                                    <th class="col-detail">Lessons Learned</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($details as $detail)
                                    <tr>
                                        <td>{{ $details->firstItem() + $loop->index }}</td>
                                        <td>{{ $detail->projectActivity?->project?->short_name ?: $detail->projectActivity?->project?->title ?? 'N/A' }}</td>
                                        <td>{{ $detail->projectActivity?->title ?? 'N/A' }}</td>
                                        <td><span class="badge {{ $detail->projectActivity?->statusBgColor() ?? 'bg-secondary' }}">{{ $detail->projectActivity?->statusLabel() ?? 'N/A' }}</span></td>
                                        <td class="text-wrap">{{ $detail->key_accomplishment ?: '-' }}</td>
                                        <td class="text-wrap">{{ $detail->challenge ?: '-' }}</td>
                                        <td class="text-wrap">{{ $detail->lesson_learned ?: '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-3">No records found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">
                        {{ $details->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
