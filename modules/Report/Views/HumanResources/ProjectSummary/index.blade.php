@extends('layouts.container')

@section('title', 'Report : Project Summary')

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
                        <div class="col-md-2 col-lg-3">
                            <label class="form-label">Projects</label>
                            <select name="projects[]" class="form-control select2" multiple="multiple">
                                @foreach ($allProjects as $proj)
                                    <option value="{{ $proj->id }}"
                                        {{ in_array($proj->id, (array) old('projects', request('projects'))) ? 'selected' : '' }}>
                                        {{ $proj->title }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-auto mt-4">
                            <button type="submit" class="btn btn-primary btn-sm me-2">Search</button>
                            <a href="{{ route('report.project.summary.index') }}" class="btn btn-secondary btn-sm">Reset</a>
                        </div>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>S.N.</th>
                                <th>Project</th>
                                <th>Activities</th>
                                <th>Completed</th>
                                <th>Under Progress</th>
                                <th>Not Started</th>
                                <th>No Longer Req.</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($projects as $index => $p)
                                <tr>
                                    <td>{{ $projects->perPage() * ($projects->currentPage() - 1) + $index + 1 }}</td>
                                    <td>
                                        <a class="text-decoration-none"
                                            href="{{ route('project.dashboard', $p->id) }}">{{ $p->title }}</a>
                                    </td>
                                    <td>{{ $p->total_activities }}</td>
                                    <td>{{ $p->completed_count }}</td>
                                    <td>{{ $p->under_progress_count }}</td>
                                    <td>{{ $p->not_started_count }}</td>
                                    <td>{{ $p->no_required_count }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4">No project summary records found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    {{ $projects->withQueryString()->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
