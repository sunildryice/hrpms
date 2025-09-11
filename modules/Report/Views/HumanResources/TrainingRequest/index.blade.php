@extends('layouts.container')

@section('title', 'Report : Training Request')

@section('page_js')
    <script type="text/javascript">
        let start_date  = '';
        let end_date    = '';
        let employee    = '';
        let designation = '';
        let department  = '';
        let dutyStation = '';
        let trainingName  = '';

        $(document).ready(function() {

            $('#navbarVerticalMenu').find('#training-request-report-menu').addClass('active');

            var oTable = $('#trainingRequestReportTable').DataTable({
                scrollX: true,
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('report.training.request.index') }}",
                    type: 'POST',
                    data: function(d) {
                        d.start_date    = start_date;
                        d.end_date      = end_date;
                        d.employee      = employee;
                        d.designation   = designation;
                        d.department    = department;
                        d.duty_station   = dutyStation;
                        d.training_name  = trainingName;
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'ref_number',
                        name: 'ref_number'
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
                        data: 'department',
                        name: 'department'
                    },
                    {
                        data: 'duty_station',
                        name: 'duty_station'
                    },
                    {
                        data: 'name_of_course',
                        name: 'name_of_course'
                    },
                    {
                        data: 'training_organizer',
                        name: 'training_organizer'
                    },
                    {
                        data: 'date_of_course_begin',
                        name: 'date_of_course_begin'
                    },
                    {
                        data: 'date_of_course_end',
                        name: 'date_of_course_end'
                    },
                    {
                        data: 'time_of_course',
                        name: 'time_of_course'
                    },
                    {
                        data: 'project',
                        name: 'project'
                    },
                    {
                        data: 'account_code',
                        name: 'account_code'
                    },
                    {
                        data: 'activity_code',
                        name: 'activity_code'
                    },
                    {
                        data: 'donor_code',
                        name: 'dob'
                    },
                    {
                        data: 'training_cost',
                        name: 'gender'
                    },
                    {
                        data: 'approved_date',
                        name: 'approved_date'
                    },
                    {
                        data: 'training_report',
                        name: 'training_report'
                    },
                    {
                        data: 'remarks',
                        name: 'remarkste'
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

                if ($('#employee').val()) {
                    employee = $('#employee').val();
                }

                if ($('#designation').val()) {
                    designation = $('#designation').val();
                }

                if ($('#department').val()) {
                    department = $('#department').val();
                }

                if ($('#duty_station').val()) {
                    dutyStation = $('#duty_station').val();
                }

                if ($('#training_name').val()) {
                    trainingName = $('#training_name').val();
                }

                if (start_date > end_date) {
                    $('#error_message').html('\'From\' date cannot be greater than \'To\' date.');
                    return;
                } else {
                    $('#error_message').html('');
                    $('#error_message').hide();
                }

                $('#btn_export').attr('href', '');
                $('#btn_export').attr('href', $('#btn_export').attr('href') +
                    '/report/training/request/export?start_date=' + start_date + '&end_date=' + end_date
                    + '&employee=' + employee + '&designation=' + designation + '&department=' + department
                    + '&duty_station=' + dutyStation + '&training_name=' + trainingName);

                oTable.ajax.reload();
            });

            $('#btn_reset').on('click', function(e) {
                start_date      = '';
                end_date        = '';
                employee        = '';
                designation     = '';
                department      = '';
                dutyStation     = '';
                trainingName    = '';
                $('#employee').val('').trigger('change');
                $('#designation').val('').trigger('change');
                $('#department').val('').trigger('change');
                $('#duty_station').val('').trigger('change');
            });
        });

        $('[name=start_date]').datepicker({
            language: 'en-GB',
            autohide: true,
            format: 'yyyy-mm-dd'
        });

        $('[name=end_date]').datepicker({
            language: 'en-GB',
            autohide: true,
            format: 'yyyy-mm-dd'
        });

        function resetValue(name){
            let value = null;
            eval(name + "=" + value + ";");
        }
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
                    <a href="{{ route('report.training.request.export') }}" id="btn_export" class="btn btn-primary btn-sm">
                        Export
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <div class="card shadow-sm border rounded c-tabs-content active" id="employee-table" style="overflow: auto;">
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
                                <option value="" selected onclick="resetValue('employee')">Select employee...</option>
                                @foreach ($employees as $employee)
                                    @if ($employee->user)
                                        <option value="{{ $employee->user->id }}">{{ $employee->getFullName() }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label" for="designation">Designation</label>
                            <select class="form-control select2" name="designation" id="designation">
                                <option value="" selected onclick="resetValue('designation')">Select designation...</option>
                                @foreach ($designations as $designation)
                                    <option value="{{ $designation->id }}">{{ $designation->title }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label" for="department">Department</label>
                            <select class="form-control select2" name="department" id="department">
                                <option value="" selected onclick="resetValue('department')">Select department...</option>
                                @foreach ($departments as $department)
                                    <option value="{{ $department->id }}">{{ $department->title }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label" for="duty_station">Duty Station</label>
                            <select class="form-control select2" name="duty_station" id="duty_station">
                                <option value="" selected onclick="resetValue('dutyStation')">Select duty station...</option>
                                @foreach ($dutyStations as $dutyStation)
                                    <option value="{{ $dutyStation->id }}">{{ $dutyStation->getDistrictName() }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label" for="training_name">Name of course/training</label>
                            <input class="form-control" type="text" name="training_name" id="training_name">
                        </div>
                        <div class="col" style="display: flex; justify-content: end">
                            <button type="button" id="btn_search" class="btn btn-primary btn-sm m-1">Search</button>
                            <button type="reset" id="btn_reset" class="btn btn-secondary btn-sm m-1">Reset</button>
                        </div>
                    </div>
                    <span class="text-danger" id="error_message"></span>
                </form>
                <div class="table-responsive">
                    <table class="table" id="trainingRequestReportTable">
                        <thead class="bg-light">
                            <tr>
                                <th>{{ __('label.sn') }}</th>
                                <th>Ref No.</th>
                                <th>Employee Name</th>
                                <th>Designation</th>
                                <th>Department</th>
                                <th>Duty Station</th>
                                <th>Name of Course / Training</th>
                                <th>Training Organizer</th>
                                <th>Date of Course: Begin</th>
                                <th>Date of Course: End</th>
                                <th>Time of Course (Days)</th>
                                <th>Project</th>
                                <th>Account Code</th>
                                <th>Activity Code</th>
                                <th>Donor Code</th>
                                <th>Training Cost</th>
                                <th>Approved Date</th>
                                <th>Training Report</th>
                                <th>Remarks</th>
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
