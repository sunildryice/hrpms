@extends('layouts.container')

@section('title', 'Report : Performance Review')

@section('page_js')
    <script type="text/javascript">
        $(document).ready(function() {
            let start_date          = '';
            let end_date            = '';
            let employee            = '';
            let duty_station        = '';
            let goal_setting_date   = '';
            let mid_term_per_date   = '';
            let final_per_date      = '';

            $('#navbarVerticalMenu').find('#performance-review-menu').addClass('active');

            var oTable = $('#performanceReviewReportTable').DataTable({
                scrollX: true,
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('report.performance.review.index') }}",
                    type: 'POST',
                    data: function(d) {
                        d.start_date        = start_date;
                        d.end_date          = end_date;
                        d.employee          = employee;
                        d.duty_station      = duty_station;
                        d.goal_setting_date = goal_setting_date;
                        d.mid_term_per_date = mid_term_per_date;
                        d.final_per_date    = final_per_date;
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'employee_name',
                        name: 'employee_name'
                    },
                    {
                        data: 'designation',
                        name: 'designation'
                    },
                    {
                        data: 'duty_station',
                        name: 'duty_station'
                    },
                    {
                        data: 'supervisor',
                        name: 'supervisor'
                    },
                    {
                        data: 'fiscal_year',
                        name: 'fiscal_year'
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },
                    {
                        data: 'goal_setting_date',
                        name: 'goal_setting_date'
                    },
                    {
                        data: 'mid_term_per_date',
                        name: 'mid_term_per_date'
                    },
                    {
                        data: 'final_per_date',
                        name: 'final_per_date'
                    },
                    {
                        data: 'major_achievements',
                        name: 'major_achievements'
                    },
                    {
                        data: 'major_challenges',
                        name: 'major_challenges'
                    },
                    {
                        data: 'working_relationship',
                        name: 'working_relationship'
                    },
                    {
                        data: 'productivity',
                        name: 'productivity'
                    },
                    {
                        data: 'leadership',
                        name: 'leadership'
                    },
                    {
                        data: 'accountability',
                        name: 'accountability'
                    },
                    {
                        data: 'problem_solving',
                        name: 'problem_solving'
                    },
                    {
                        data: 'identified_strengths',
                        name: 'identified_strengths'
                    },
                    {
                        data: 'identified_growth_areas',
                        name: 'identified_growth_areas'
                    },
                    {
                        data: 'performance_evaluation',
                        name: 'performance_evaluation'
                    },
                    {
                        data: 'employee_comment',
                        name: 'employee_comment'
                    },
                    {
                        data: 'supervisor_comment',
                        name: 'supervisor_comment'
                    },
                ],
                scrollX: true
            });

            $('#btn_search').on('click', function(e) {
                if ($('#start_date').val()) {
                    let start = new Date($('#start_date').val());
                    start_date = start.getTime();
                }

                if ($('#end_date').val()) {
                    let end = new Date($('#end_date').val());
                    end_date = end.getTime();
                }

                if (start_date > end_date) {
                    $('#error_message').html('\'From\' date cannot be greater than \'To\' date.');
                    return;
                } else {
                    $('#error_message').html('');
                    $('#error_message').hide();
                }

                if ($('#employee').val()) {
                    employee = $('#employee').val();
                }

                if ($('#duty_station').val()) {
                    duty_station = $('#duty_station').val();
                }

                if ($('#goal_setting_date').val()) {
                    let goal_setting = new Date($('#goal_setting_date').val());
                    goal_setting_date = goal_setting.getTime();
                }

                if ($('#mid_term_per_date').val()) {
                    let midTermPer = new Date($('#mid_term_per_date').val());
                    mid_term_per_date = midTermPer.getTime();
                }

                if ($('#final_per_date').val()) {
                    let finalPer = new Date($('#final_per_date').val());
                    final_per_date = finalPer.getTime();
                }

                $('#btn_export').attr('href', '');
                $('#btn_export').attr('href', $('#btn_export').attr('href') +
                    '/report/performance/review/export?start_date=' + start_date
                    + '&end_date=' + end_date
                    + '&employee=' + employee
                    + '&duty_station=' + duty_station
                    + '&goal_setting_date=' + goal_setting_date
                    + '&mid_term_per_date=' + mid_term_per_date
                    + '&final_per_date=' + final_per_date
                    );

                oTable.ajax.reload();
            });

            $('[name="start_date"]').datepicker({
                language: 'en-GB',
                autoHide: true,
                format: 'yyyy-mm-dd',
            });

            $('[name="end_date"]').datepicker({
                language: 'en-GB',
                autoHide: true,
                format: 'yyyy-mm-dd',
            });

            $('[name="goal_setting_date"]').datepicker({
                language: 'en-GB',
                autoHide: true,
                format: 'yyyy-mm-dd',
            });

            $('[name="mid_term_per_date"]').datepicker({
                language: 'en-GB',
                autoHide: true,
                format: 'yyyy-mm-dd',
            });

            $('[name="final_per_date"]').datepicker({
                language: 'en-GB',
                autoHide: true,
                format: 'yyyy-mm-dd',
            });

            $('#btn_reset').on('click', function(e) {
                start_date          = '';
                end_date            = '';
                employee            = '';
                duty_station        = '';
                goal_setting_date   = '';
                mid_term_per_date   = '';
                final_per_date      = '';
                $('#employee').val('').trigger("change");
                $('#duty_station').val('').trigger("change");
                $("input[type=text]").removeAttr('value');
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
                            <li class="breadcrumb-item" aria-current="page">@yield('title')</li>
                        </ol>
                    </nav>
                    <h4 class="m-0 lh1 mt-1 fs-6 text-uppercase fw-bold text-primary">@yield('title')</h4>
                </div>
                <div class="add-info justify-content-end">
                    <a href="{{ route('report.performance.review.export') }}" id="btn_export" class="btn btn-primary btn-sm">
                        Export
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <div class="card shadow-sm border rounded c-tabs-content active" id="performance-review-table" style="overflow: auto;">
            <div class="card-body">
                <form>
                    <div class="row mb-4" style="align-items: flex-end">
                        <div class="col-md-2 mb-2">
                            <label class="form-label" for="start_date">From</label>
                            <input class="form-control" type="text" name="start_date" id="start_date">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label" for="end_date">To</label>
                            <input class="form-control" type="text" name="end_date" id="end_date">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label" for="employee">Employee</label>
                            <select class="form-control select2" name="employee" id="employee">
                                <option value="" selected disabled>Select employee...</option>
                                @foreach ($employees as $employee)
                                    @if ($employee->user)
                                        <option value="{{ $employee->user->id }}">{{ $employee->getFullName() }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label" for="duty_station">Duty Station</label>
                            <select class="form-control select2" name="duty_station" id="duty_station">
                                <option value="" selected onclick="resetValue('duty_station')">Select duty station...</option>
                                @foreach ($dutyStations as $dutyStation)
                                    <option value="{{ $dutyStation->id }}">{{ $dutyStation->getDistrictName() }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label" for="goal_setting_date">Goal Setting Date</label>
                            <input class="form-control" type="text" name="goal_setting_date" id="goal_setting_date">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label" for="mid_term_per_date">Mid Term PER Date</label>
                            <input class="form-control" type="text" name="mid_term_per_date" id="mid_term_per_date">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label" for="final_per_date">Final PER Date</label>
                            <input class="form-control" type="text" name="final_per_date" id="final_per_date">
                        </div>
                        <div class="col">
                            <button type="button" id="btn_search" class="btn btn-primary btn-sm m-1">Search</button>
                            <button type="reset" id="btn_reset" class="btn btn-secondary btn-sm m-1">Reset</button>
                        </div>
                    </div>
                    <span class="text-danger" id="error_message"></span>
                </form>
                <div class="table-responsive">
                    <table class="table" id="performanceReviewReportTable">
                        <thead class="bg-light">
                            <tr>
                                <th>{{ __('label.sn') }}</th>
                                <th>Employee Name</th>
                                <th>Designation</th>
                                <th>Duty Station</th>
                                <th>Supervisor</th>
                                <th>Fiscal Year</th>
                                <th>Status</th>
                                <th>Goal Setting Date</th>
                                <th>Mid-Term PER Date</th>
                                <th>Final PER Date</th>
                                <th>Major Achievements</th>
                                <th>Major Challenges</th>
                                <th>Communication/Wrokking</th>
                                <th>Productivity & Planning</th>
                                <th>Leadership</th>
                                <th>Problem Solving</th>
                                <th>Accountability</th>
                                <th>Identified strengths</th>
                                <th>Identified Growth Area</th>
                                <th>Performance Evaluation</th>
                                <th>Employee Comment</th>
                                <th>Supervisor Comment</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@stop
