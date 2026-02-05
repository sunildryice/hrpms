@extends('layouts.container')

@section('title', 'Employee Work Plan Details')

@section('page_js')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#navbarVerticalMenu').find('#employee-work-plan-index').addClass('active');

            var oTable = $('#WeeklyPlanTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: window.location.href,
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'project.short_name',
                        name: 'project.short_name',
                        defaultContent: ''
                    },
                    {
                        data: 'activity.title',
                        name: 'activity.title',
                        defaultContent: ''
                    },
                    {
                        data: 'plan_tasks',
                        name: 'plan_tasks',
                        defaultContent: ''
                    },
                    {
                        data: 'status',
                        name: 'status',
                        defaultContent: ''
                    },
                    {
                        data: 'reason',
                        name: 'reason',
                        defaultContent: '',
                    },
                ],
            });
        });
    </script>
@endsection
@section('page-content')
    <div class="pb-3 mb-3 border-bottom">
        <div class="d-flex flex-column flex-lg-row align-items-start align-items-lg-center gap-2">
            <div class="brd-crms flex-grow-1">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}"
                                class="text-decoration-none text-dark">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('employee-work-plan.index') }}"
                                class="text-decoration-none text-dark">Employee Work Plan</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Details</li>
                    </ol>
                </nav>
                <h4 class="m-0 lh1 mt-1 fs-6 text-uppercase fw-bold text-primary">
                    Plan Details: {{ $week['start_date']->format('M j') }} - {{ $week['end_date']->format('M j, Y') }} -
                    {{ $workPlan->employee->full_name }}
                </h4>
            </div>
        </div>
    </div>
    <div class="card shadow-sm border rounded c-tabs-content active" id="weekly-plan-table">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table" id="WeeklyPlanTable">
                    <thead class="bg-light">
                        <tr>
                            <th>SN</th>
                            <th>Project</th>
                            <th>Activity</th>
                            <th>Planned Tasks</th>
                            <th>Status</th>
                            <th>Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>

        </div>
    @stop
