@extends('layouts.container')

@section('title', 'Report : Project Summary')

@section('page_css')
    <style>
        .dt-container {
            position: relative;
        }

        .table-scroll-wrapper {
            overflow-x: auto;
            max-height: 620px;
            /* Fixed height */
            overflow-y: auto;
            border: 1px solid var(--bs-border-color);
            border-radius: 0.375rem;
        }

        .table-scroll-wrapper table {
            min-width: 1600px;
            /* Prevent shrinking */
            margin-bottom: 0;
            width: 100%;
        }

        /* Sticky Header */
        .table-scroll-wrapper thead th {
            position: sticky;
            top: 0;
            z-index: 10;
            background: #f8f9fa;
            white-space: nowrap;
            box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.1);
            /* subtle shadow for separation */
        }

        /* Make header bolder when scrolling */
        .table-scroll-wrapper thead {
            background: #f8f9fa;
        }

        /* Column widths */
        .col-sn {
            min-width: 50px;
            width: 50px;
        }

        .col-project {
            min-width: 200px;
            width: 200px;
            white-space: normal;
            word-break: break-word;
        }

        .col-count {
            min-width: 80px;
            width: 80px;
            text-align: center;
        }

        .col-date {
            min-width: 110px;
            width: 110px;
            white-space: nowrap;
        }

        .col-person {
            min-width: 130px;
            width: 130px;
            white-space: nowrap;
        }

        .col-status {
            min-width: 110px;
            width: 110px;
            white-space: nowrap;
        }

        .col-wrap {
            min-width: 160px;
            width: 160px;
            white-space: normal;
            word-break: break-word;
        }

        .col-budget {
            min-width: 120px;
            width: 120px;
            text-align: right;
            white-space: nowrap;
        }
    </style>
@endsection

@section('page_js')
    <script>
        $(document).ready(function() {
            $('#navbarVerticalMenu').find('#project-summary-report-menu').addClass('active');

            $('[name="request_date"], [name="off_day_date"]').datepicker({
                language: 'en-GB',
                autoHide: true,
                format: 'yyyy-mm-dd',
            });
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
                    <h4 class="m-0 mt-1 lh1 fs-6 text-uppercase fw-bold text-primary">@yield('title')</h4>
                </div>
                <div>
                    <a href="{{ route('report.project.summary.export', request()->query()) }}"
                        class="btn btn-primary btn-sm">Export</a>
                </div>
            </div>
        </div>

        <div class="card shadow-sm border rounded">
            <div class="card-body">
                <form action="{{ route('report.project.summary.index') }}" method="GET">
                    <div class="row mb-4" style="align-items: flex-end;">
                        {{-- <div class="col-md-2 col-lg-6">
                            <label class="form-label">Projects</label>
                            <select name="projects[]" class="form-control select2" multiple="multiple">
                                @foreach ($allProjects as $proj)
                                    <option value="{{ $proj->id }}"
                                        {{ in_array($proj->id, (array) old('projects', request('projects'))) ? 'selected' : '' }}>
                                        {{ $proj->short_name ?: $proj->title }}
                                    </option>
                                @endforeach
                            </select>
                        </div> --}}

                        <div class="col-md-2">
                            <label class="form-label">Team Lead</label>
                            <select name="team_lead_id" class="form-control select2">
                                <option value=""> All Team Leads </option>
                                @foreach ($teamLeads as $user)
                                    <option value="{{ $user->id }}"
                                        {{ request('team_lead_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->full_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">Focal Person</label>
                            <select name="focal_person_id" class="form-control select2">
                                <option value=""> All Focal Persons </option>
                                @foreach ($focalPersons as $user)
                                    <option value="{{ $user->id }}"
                                        {{ request('focal_person_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->full_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">Project Theme</label>
                            <select name="project_theme_id" class="form-control select2">
                                <option value=""> All Themes </option>
                                @foreach ($projectThemes as $theme)
                                    <option value="{{ $theme->id }}"
                                        {{ request('project_theme_id') == $theme->id ? 'selected' : '' }}>
                                        {{ $theme->title }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">Approach</label>
                            <select name="approach_id" class="form-control select2">
                                <option value=""> All Approaches </option>
                                @foreach ($approaches as $approach)
                                    <option value="{{ $approach->id }}"
                                        {{ request('approach_id') == $approach->id ? 'selected' : '' }}>
                                        {{ $approach->title }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-1">
                            <label class="form-label">District</label>
                            <select name="district_id" class="form-control select2">
                                <option value=""> All Districts </option>
                                @foreach ($districts as $district)
                                    <option value="{{ $district->id }}"
                                        {{ request('district_id') == $district->id ? 'selected' : '' }}>
                                        {{ $district->district_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-1">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-control select2">
                                <option value=""> All Status </option>
                                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active
                                </option>
                                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive
                                </option>
                            </select>
                        </div>

                        <div class="col-auto mt-4">
                            <button type="submit" class="btn btn-primary btn-sm me-2">Search</button>
                            <a href="{{ route('report.project.summary.index') }}"
                                class="btn btn-secondary btn-sm">Reset</a>
                        </div>
                    </div>
                </form>

                <div class="table-scroll-wrapper">
                    <table class="table table-bordered table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="col-sn">S.N.</th>
                                <th class="col-project">Project</th>
                                <th class="col-count">Total</th>
                                <th class="col-count">Completed</th>
                                <th class="col-count">Under Progress</th>
                                <th class="col-count">Not Started</th>
                                <th class="col-count">Not Required</th>
                                <th class="col-date">Start Date</th>
                                <th class="col-date">Completion Date</th>
                                <th class="col-person">Team Lead</th>
                                <th class="col-person">Focal Person</th>
                                <th class="col-status">Status</th>
                                <th class="col-wrap">Primary Funder</th>
                                <th class="col-wrap">Contracting Agency</th>
                                <th class="col-budget">Budget (USD)</th>
                                <th class="col-wrap">Working Areas (Districts)</th>
                                <th class="col-wrap">Project Theme</th>
                                <th class="col-wrap">Approaches</th>
                                <th class="col-wrap">Sector</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($projects as $index => $p)
                                <tr>
                                    <td>{{ $projects->perPage() * ($projects->currentPage() - 1) + $index + 1 }}</td>
                                    <td class="col-wrap">
                                        <a class="text-decoration-none" href="{{ route('project.dashboard', $p->id) }}">
                                            {{ $p->short_name }}
                                        </a>
                                    </td>
                                    <td class="col-count">{{ $p->total_activities }}</td>
                                    <td class="col-count">{{ $p->completed_count }}</td>
                                    <td class="col-count">{{ $p->under_progress_count }}</td>
                                    <td class="col-count">{{ $p->not_started_count }}</td>
                                    <td class="col-count">{{ $p->no_required_count }}</td>
                                    <td class="col-date">{{ $p->formatted_start_date ?: '-' }}</td>
                                    <td class="col-date">{{ $p->formatted_completion_date ?: '-' }}</td>
                                    <td class="col-person">{{ $p->teamLead?->full_name ?? '-' }}</td>
                                    <td class="col-person">{{ $p->focalPerson?->full_name ?? '-' }}</td>
                                    <td class="col-status">{{ $p->getActiveStatus() }}</td>
                                    <td class="col-wrap">{{ $p->primary_funder ?: '-' }}</td>
                                    <td class="col-wrap">{{ $p->contracting_agency ?: '-' }}</td>
                                    <td class="col-budget">{{ $p->budget_usd ? number_format($p->budget_usd, 2) : '-' }}
                                    </td>
                                    <td class="col-wrap">{{ $p->districts->pluck('district_name')->join(', ') ?: '-' }}
                                    </td>
                                    <td class="col-wrap">{{ $p->projectTheme->title ?? '-' }}</td>
                                    <td class="col-wrap">{{ $p->approaches->pluck('title')->join(', ') ?: '-' }}</td>
                                    <td class="col-wrap">{{ $p->sector->title ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="19" class="text-center py-4">No project summary records found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $projects->withQueryString()->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
