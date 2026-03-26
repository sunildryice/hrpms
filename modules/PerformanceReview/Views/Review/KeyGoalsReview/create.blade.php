@extends('layouts.container')

@section('title', 'Review Performance Review Key Goals')

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
            $('#navbarVerticalMenu').find('#performance-review-index').addClass('active');

            $('#receiver').hide();
            let errors = {!! $errors !!};
            if (errors.receiver_id != undefined) {
                $('#receiver').show();
            }
            if ($('#status_id').val() == {{ config('constant.VERIFIED_STATUS') }}) {
                $('#receiver').show();
            }

        });

        $('#status_id').on('change', function() {
            let status = $('#status_id').val();
            if (status == {{ config('constant.VERIFIED_STATUS') }}) {
                $('#receiver').show();
            } else {
                $('#receiver').hide();
            }
        });
    </script>
@endsection

@section('page-content')

    <style>
        td,
        th {
            border: 1px solid grey;
            padding: 8px;
            text-align: left;
        }
    </style>

    <div class="m-content p-3">
        <div class="container-fluid">

            <div class="page-header pb-3 mb-3 border-bottom">
                <div class="d-flex flex-column flex-lg-row align-items-start align-items-lg-center gap-2">
                    <div class="brd-crms flex-grow-1">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item">
                                    <a href="{!! route('dashboard.index') !!}" class="text-decoration-none text-dark">Home</a>
                                </li>
                                <li class="breadcrumb-item">
                                    <a href="{{ route('performance.review.index') }}"
                                        class="text-decoration-none">Performance Review</a>
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

                <!-- B. Set Key Goals -->
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

            <section>
                <div class="card">
                    <div class="card-header fw-bold">
                        Performance Review Process
                    </div>
                    <form action="{{ route('performance.review.store') }}" id="performanceReviewProcessForm"
                        method="post" enctype="multipart/form-data" autocomplete="off">
                        <input type="hidden" name="performance_review_id" value="{{ $performanceReview->id }}"
                            onsubmit="return confirm('Have you saved all the forms? Are you sure to submit?');">
                        <div class="card-body">
                            <div class="c-b">
                                @foreach ($performanceReview->logs as $log)
                                    <div class="d-flex py-2 flex-row gap-2 mb-2 border-bottom ">
                                        <div width="40" height="40" class="rounded-circle mr-3 user-icon">
                                            <i class="bi-person"></i>
                                        </div>
                                        <div class="w-100">
                                            <div
                                                class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                                                <div
                                                    class="d-flex flex-column flex-md-row align-items-start align-items-md-center gap-md-2 mb-2 mb-md-0">
                                                    <label
                                                        class="form-label mb-0">{{ $log->createdBy->getFullName() }}</label>
                                                    <span class="badge bg-primary c-badge">
                                                        {!! $log->createdBy->employee->latestTenure->getDesignationName() !!}
                                                    </span>
                                                </div>
                                                <small
                                                    title="{{ $log->created_at }}">{{ $log->created_at->format('M d, Y h:i A') }}</small>
                                            </div>
                                            <p class="text-justify comment-text mb-0 mt-1">
                                                {{ $log->log_remarks }}
                                            </p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <div class="border-top pt-4">
                                <div class="row mb-2">
                                    <div class="col-lg-3">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="status_id" class="form-label required-label">Status </label>
                                        </div>
                                    </div>
                                    <div class="col-lg-9">
                                        <select name="status_id" id="status_id" class="select2 form-control"
                                            data-width="100%">
                                            <option value="">Select Status</option>
                                            <option value="{{ config('constant.RETURNED_STATUS') }}"
                                                {{ old('status_id') == config('constant.RETURNED_STATUS') ? 'selected' : '' }}>
                                                Return to Employee</option>
                                            {{-- <option value="{{ config('constant.VERIFIED_STATUS') }}" {{old('status_id') == config('constant.VERIFIED_STATUS') ? 'selected' : ''}}>Verify</option> --}}
                                            <option value="{{ config('constant.APPROVED_STATUS') }}"
                                                {{ old('status_id') == config('constant.APPROVED_STATUS') ? 'selected' : '' }}>
                                                Approve</option>
                                        </select>
                                        @if ($errors->has('status_id'))
                                            <div class="fv-plugins-message-container invalid-feedback">
                                                <div data-field="status_id">
                                                    {!! $errors->first('status_id') !!}
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <div class="row mb-2" id="receiver">
                                    <div class="col-lg-3">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="receiver_id" class="form-label required-label">Send To
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-lg-9">
                                        <select name="receiver_id" id="receiver_id" class="select2 form-control"
                                            data-width="100%">
                                            @foreach ($receivers as $receiver)
                                                @if ($loop->first)
                                                    <option value="{{ $receiver->id }}"
                                                        {{ old('receiver_id') == $receiver->id ? 'selected' : (empty(old('receiver_id')) ? 'selected' : '') }}>
                                                        {{ $receiver->getFullName() }}</option>
                                                @else
                                                    <option value="{{ $receiver->id }}">
                                                        {{ $receiver->getFullName() }}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                        @if ($errors->has('receiver_id'))
                                            <div class="fv-plugins-message-container invalid-feedback">
                                                <div data-field="receiver_id">
                                                    {!! $errors->first('receiver_id') !!}
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <div class="row mb-2">
                                    <div class="col-lg-3">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="log_remarks" class="form-label">Remarks
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-lg-9">
                                        <textarea type="text" class="form-control @if ($errors->has('log_remarks')) is-invalid @endif" name="log_remarks">{{ old('log_remarks') }}</textarea>
                                        @if ($errors->has('log_remarks'))
                                            <div class="fv-plugins-message-container invalid-feedback">
                                                <div data-field="log_remarks">{!! $errors->first('log_remarks') !!}</div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                {!! csrf_field() !!}
                            </div>
                        </div>
                        <div class="card-footer border-0 justify-content-end d-flex gap-2">
                            <button type="submit" name="btn" value="submit" class="btn btn-success btn-sm">
                                Submit
                            </button>
                            <a href="{!! route('performance.review.index') !!}" class="btn btn-danger btn-sm">Cancel</a>
                        </div>
                    </form>
                </div>
            </section>
        </div>
    </div>

@stop
