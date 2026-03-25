@extends('layouts.container')

@section('title', 'View Key Goals')

@section('page_css')
    <style>
        #keygoals-table th,
        #keygoals-table td,
        #devplan-table th,
        #devplan-table td {
            border: 1px solid #dee2e6;
            padding: 10px;
            vertical-align: middle;
        }

        #keygoals-table,
        #devplan-table {
            table-layout: fixed;
            width: 100%;
        }

        .col-objective {
            width: 45%;
        }

        .col-output {
            width: 45%;
        }

        .col-plan {
            width: 90%;
        }

        .col-action {
            width: 10%;
            text-align: center;
        }

        .btn-sm {
            padding: 0.25rem 0.5rem;
            margin: 0 2px;
        }

        .table thead th {
            background-color: #f8f9fa;
            font-weight: 600;
        }

        .readonly-cell {
            background-color: #f8f9fa;
            cursor: default;
        }

        .list-group-item.readonly {
            background-color: #f8f9fa;
            border-left: 4px solid #0d6efd;
        }
    </style>
@endsection

@section('page_js')
    <script type="text/javascript">
        $(function() {
            $('#navbarVerticalMenu').find('#performance-employee-index').addClass('active');
        });
    </script>
@endsection

@section('page-content')

    <div class="page-header pb-3 mb-3 border-bottom">
        <div class="d-flex flex-column flex-lg-row align-items-start align-items-lg-center gap-2">
            <div class="brd-crms flex-grow-1">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item">
                            <a href="{!! route('dashboard.index') !!}" class="text-decoration-none text-dark">Home</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('performance.employee.index') }}"
                                class="text-decoration-none text-dark">Performance Review</a>
                        </li>
                        <li class="breadcrumb-item" aria-current="page">@yield('title')</li>
                    </ol>
                </nav>
                <h4 class="m-0 lh1 mt-1 fs-6 text-uppercase fw-bold text-primary">@yield('title')</h4>
            </div>
        </div>
    </div>
    <section>

        <div id="employeeAndSupervisorDetails" class="mb-3">
            <div class="card">
                <div class="card-header fw-bold">
                    <span class="card-title">
                        <span class="fw-bold">A.</span>
                        <span>
                            Employee and Line Manager Details
                        </span>
                    </span>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="row">
                                <div class="col-lg-4">
                                    <span class="fw-bold">Employee Name</span>
                                </div>
                                <div class="col-lg-6">
                                    <span>{{ $performanceReview->getEmployeeName() }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="row">
                                <div class="col-lg-4">
                                    <span class="fw-bold">Employee Title</span>
                                </div>
                                <div class="col-lg-6">
                                    <span>{{ $performanceReview->getEmployeeTitle() }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="row">
                                <div class="col-lg-4">
                                    <span class="fw-bold">Line Manager Name</span>
                                </div>
                                <div class="col-lg-6">
                                    <span>{{ $performanceReview->getSupervisorName() }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="row">
                                <div class="col-lg-4">
                                    <span class="fw-bold">Line Manager Title</span>
                                </div>
                                <div class="col-lg-6">
                                    <span>{{ $performanceReview->getSupervisorTitle() }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="row">
                                <div class="col-lg-4">
                                    <span class="fw-bold">Date of Joining</span>
                                </div>
                                <div class="col-lg-6">
                                    <span>{{ $performanceReview->employee->getFirstJoinedDate() }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="row">
                                <div class="col-lg-4">
                                    <span class="fw-bold">In Current Position Since</span>
                                </div>
                                <div class="col-lg-6">
                                    <span>{{ $performanceReview->getJoinedDate() }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row mb-2">
                        <div class="col-lg-6">
                            <div class="row">
                                <div class="col-lg-4">
                                    <span class="fw-bold">Review period from:</span>
                                </div>
                                <div class="col-lg-6">
                                    <span>{{ $performanceReview->getReviewFromDate() }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="row">
                                <div class="col-lg-4">
                                    <span class="fw-bold">Review period to:</span>
                                </div>
                                <div class="col-lg-6">
                                    <span>{{ $performanceReview->getReviewToDate() }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 mt-3">
                            <div class="row">
                                <div class="col-lg-4">
                                    <span class="fw-bold">Deadline:</span>
                                </div>
                                <div class="col-lg-6">
                                    <span>{{ $performanceReview->getDeadlineDate() }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- B. Key Goals -->
        <div id="setKeyGoals" class="mb-3">
            <div class="card">
                <div class="card-header fw-bold">
                    <span class="fw-bold">B.</span>Key Goals
                </div>
                <div class="card-body">
                    <table class="table table-bordered" id="keygoals-table">
                        <thead>
                            <tr>
                                <th class="col-objective">Objective</th>
                                <th class="col-output">Output / Deliverable</th>
                            </tr>
                        </thead>
                        <tbody id="keygoals-body">
                            @forelse ($currentKeyGoals as $kg)
                                <tr class="keygoal-row readonly">
                                    <td class="col-objective readonly-cell">
                                        {{ $kg->title }}
                                    </td>
                                    <td class="col-output readonly-cell">
                                        {{ $kg->output_deliverables ?? '—' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="text-center text-muted py-4">
                                        No key goals have been set yet.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- C. Professional Development Plan -->
        <div id="professionalDevelopmentPlan" class="mb-3">
            <div class="card">
                <div class="card-header fw-bold">
                    <span class="fw-bold">C.</span> Professional Development Plan
                </div>
                <div class="card-body">
                    @php
                        $devPlans = $performanceReview->developmentPlans;
                    @endphp

                    @if ($devPlans->isEmpty())
                        <div class="text-center text-muted py-4">
                            No professional development plan has been added yet.
                        </div>
                    @else
                        <table class="table table-bordered" id="devplan-table">
                            <thead>
                                <tr>
                                    <th style="width: 5%">SN</th>
                                    <th class="col-plan">Development Plan</th>
                                </tr>
                            </thead>
                            <tbody id="devplan-body">
                                @foreach ($devPlans as $plan)
                                    <tr class="devplan-row readonly">
                                        <td class="sn">{{ $loop->iteration }}</td>
                                        <td class="col-plan readonly-cell">
                                            {{ $plan->objective }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>
        </div>

    </section>

    @if ($performanceReview->status_id == config('constant.RETURNED_STATUS'))
        <section>
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header fw-bold">
                        Remarks
                    </div>
                    <div class="card-body">
                        <div class="p1">
                            <span>{{ $performanceReview->getLatestRemark() }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    @endif
@endsection
