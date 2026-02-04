@extends('layouts.container')

@section('title', 'Work Plan Details')

@section('page_css')
    <style>
        .select2-container .select2-selection--single {
            height: 38px !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 36px !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 36px !important;
        }
    </style>
@endsection

@section('page_js')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#navbarVerticalMenu').find('#weekly-plan-index').addClass('active');

            $('.project-select').select2({
                dropdownParent: $('#addPlanModal'),
                width: '100%'
            });

            $('.activity-select').select2({
                dropdownParent: $('#addPlanModal'),
                width: '100%'
            });
        });
    </script>
@endsection
@section('page-content')
    <div class="container-fluid">
        <div class="pb-3 mb-3 border-bottom">
            <div class="d-flex flex-column flex-lg-row align-items-start align-items-lg-center gap-2">
                <div class="brd-crms flex-grow-1">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}"
                                    class="text-decoration-none text-dark">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('weekly-plan.index') }}"
                                    class="text-decoration-none text-dark">Weekly Plan</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Details</li>
                        </ol>
                    </nav>
                    <h4 class="m-0 lh1 mt-1 fs-6 text-uppercase fw-bold text-primary">
                        Plan Details: {{ $week['start_date']->format('M j') }} - {{ $week['end_date']->format('M j, Y') }}
                    </h4>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addPlanModal">
                        <i class="bi bi-plus-lg"></i> Add Plan
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <div class="card shadow-sm border rounded c-tabs-content active" id="weekly-plan-table">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="WeeklyPlanTable">
                        <thead class="bg-light">
                            <tr>
                                <th>SN</th>
                                <th>Project</th>
                                <th>Activity</th>
                                <th>Planned Tasks</th>
                                <th>Status</th>
                                <th>Reason</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="7" class="text-center text-muted">No plans added for this week yet.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>

    {{-- Add Plan Modal --}}
    <div class="modal fade" id="addPlanModal" tabindex="-1" aria-labelledby="addPlanModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="addPlanModalLabel">Add Weekly Plan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addPlanForm">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Project</label>
                                <select class="form-select project-select" name="project_id">
                                    <option value="">Select Project</option>
                                    {{-- Projects loop will go here --}}
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Activity</label>
                                <select class="form-select activity-select" name="activity_id">
                                    <option value="">Select Activity</option>
                                </select>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Planned Task</label>
                                <textarea class="form-control" name="planned_task" rows="3"></textarea>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Reason</label>
                                <textarea class="form-control" name="planned_task" rows="3"></textarea>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Save Plan</button>
                </div>
            </div>
        </div>
    </div>
@stop
