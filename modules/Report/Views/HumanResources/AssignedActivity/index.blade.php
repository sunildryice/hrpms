@extends('layouts.container')

@section('title', 'Report : Assigned Activity')

@section('page_css')
    <style>
        .wrap-text {
            white-space: normal !important;
            word-break: break-word;
            min-width: 180px;
            max-width: 350px;
        }
    </style>
@endsection

@section('page_js')
    <script>
        $(document).ready(function() {
            $('#navbarVerticalMenu').find('#assigned-activity-report-menu').addClass('active');
            $('.select2').select2();

            const form = document.getElementById('assignedActivityFilterForm');

            if (form) {
                const fv = FormValidation.formValidation(form, {
                    fields: {
                        employee: {
                            validators: {
                                notEmpty: {
                                    message: 'Employee is required',
                                },
                            },
                        },
                    },
                    plugins: {
                        trigger: new FormValidation.plugins.Trigger(),
                        bootstrap5: new FormValidation.plugins.Bootstrap5({
                            rowSelector: '.col-md-3',
                            eleInvalidClass: 'is-invalid',
                            eleValidClass: 'is-valid',
                        }),
                        submitButton: new FormValidation.plugins.SubmitButton(),
                        defaultSubmit: new FormValidation.plugins.DefaultSubmit(),
                        icon: new FormValidation.plugins.Icon({
                            valid: 'bi bi-check2-square text-success',
                            invalid: 'bi bi-x-lg text-danger',
                            validating: 'bi bi-arrow-repeat text-warning',
                        }),
                    },
                });

                $('#employeeSelect').on('change', function() {
                    fv.revalidateField('employee');
                });
            }

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
                            <li class="breadcrumb-item active" aria-current="page">@yield('title')</li>
                        </ol>
                    </nav>
                    <h4 class="m-0 mt-1 lh-1 fs-6 text-uppercase fw-bold text-primary">@yield('title')</h4>
                </div>
                <div>
                    <a href="{{ route('report.assigned.activity.export', $requestData) }}"
                        class="btn btn-primary btn-sm">Export</a>
                </div>
            </div>
        </div>

        <div class="card shadow-sm border rounded overflow-auto">
            <div class="card-body">
                <form action="{{ route('report.assigned.activity.index') }}" method="GET" id="assignedActivityFilterForm"
                    novalidate>
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <label class="form-label required-label">Employee</label>
                            <select name="employee" class="form-control select2" id="employeeSelect">
                                <option value="">Select Employee</option>
                                @foreach ($employees as $emp)
                                    @if ($emp->user)
                                        <option value="{{ $emp->user->id }}"
                                            {{ request('employee') == $emp->user->id ? 'selected' : '' }}>
                                            {{ $emp->getFullName() }}
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Project</label>
                            <select name="project" class="form-control select2">
                                <option value="">All Projects</option>
                                @foreach ($projects as $proj)
                                    <option value="{{ $proj->id }}"
                                        {{ request('project') == $proj->id ? 'selected' : '' }}>
                                        {{ $proj->title }} ({{ $proj->short_name }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-auto">
                            <button type="submit" class="btn btn-primary btn-sm mt-4">Search</button>
                            <a href="{{ route('report.assigned.activity.index') }}"
                                class="btn btn-secondary btn-sm mt-4">Reset</a>
                        </div>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>S.N.</th>
                                <th>Project</th>
                                <th>Parent Activity</th>
                                <th>Activity</th>
                                <th>Stage</th>
                                <th>Activity Level</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($activities as $index => $act)
                                <tr>
                                    <td>{{ $activities instanceof \Illuminate\Pagination\LengthAwarePaginator ? $activities->firstItem() + $index : $index + 1 }}
                                    </td>
                                    <td>{{ $act->project_short_name }}</td>
                                    <td class="wrap-text">{{ $act->parent_title ?: '-' }}</td>
                                    <td class="wrap-text">{{ $act->title }}</td>
                                    <td>{{ $act->stage_title ?: '-' }}</td>
                                    <td>
                                        @if ($act->activity_level === 'activity')
                                            <span class="badge bg-primary">Activity</span>
                                        @elseif($act->activity_level === 'sub_activity')
                                            <span class="badge bg-info">Sub Activity</span>
                                        @else
                                            {{ ucfirst($act->activity_level) }}
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">
                                        No assigned activities found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    @if ($activities instanceof \Illuminate\Pagination\LengthAwarePaginator)
                        <div class="d-flex mt-4">
                            {{ $activities->withQueryString()->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
