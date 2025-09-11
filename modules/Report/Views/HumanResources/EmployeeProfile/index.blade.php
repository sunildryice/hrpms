@extends('layouts.container')

@section('title', 'Report : Employee Profile')

@section('page_js')
    <script type="text/javascript">
        $(document).ready(function() {

            let start_date = '';
            let end_date = '';
            let office = '';
            let gender = '';
            let active = null;

            $('#navbarVerticalMenu').find('#employee-profile-report-menu').addClass('active');

            var oTable = $('#employeeProfileReportTable').DataTable({
                scrollX: true,
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('report.employee.profile.index') }}",
                    type: 'POST',
                    data: function(d) {
                        d.start_date = start_date;
                        d.end_date = end_date;
                        d.office = office;
                        d.gender = gender;
                        d.active = active;
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'staff_name',
                        name: 'staff_name'
                    },
                    {
                        data: 'staff_id',
                        name: 'staff_id'
                    },
                    {
                        data: 'joined_date',
                        name: 'joined_date'
                    },
                    {
                        data: 'position',
                        name: 'position'
                    },
                    {
                        data: 'duty_station',
                        name: 'duty_station'
                    },
                    {
                        data: 'district',
                        name: 'district'
                    },
                    {
                        data: 'supervisor_name',
                        name: 'supervisor_name'
                    },
                    {
                        data: 'current_address',
                        name: 'current_address'
                    },
                    {
                        data: 'mobile',
                        name: 'mobile'
                    },
                    {
                        data: 'office_email',
                        name: 'office_email'
                    },
                    {
                        data: 'citizenship_no',
                        name: 'citizenship_no'
                    },
                    {
                        data: 'pan_no',
                        name: 'pan_no'
                    },
                    {
                        data: 'ssf_no',
                        name: 'ssf_no'
                    },
                    {
                        data: 'cit_no',
                        name: 'cit_no'
                    },
                    {
                        data: 'dob',
                        name: 'dob'
                    },
                    {
                        data: 'gender',
                        name: 'gender'
                    },
                    {
                        data: 'blood_group',
                        name: 'blood_group'
                    },
                    {
                        data: 'marital_status',
                        name: 'marital_status'
                    },
                    {
                        data: 'bank_detail',
                        name: 'bank_detail'
                    },
                    {
                        data: 'probationary_complete',
                        name: 'probationary_complete'
                    },
                    {
                        data: 'active_employee',
                        name: 'active_employee'
                    },
                    {
                        data: 'last_working_date',
                        name: 'last_working_date'
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

                if ($('#office').val()) {
                    office = $('#office').val();
                }

                gender = $('#gender').find(':selected').val();

                active = $('#active').find(':selected').val();

                if (start_date > end_date) {
                    $('#error_message').html('\'From\' date cannot be greater than \'To\' date.');
                    return;
                } else {
                    $('#error_message').html('');
                    $('#error_message').hide();
                }

                $('#btn_export').attr('href', '');
                let url = '/report/employee/profile/export?start_date=' + start_date + '&end_date=' +
                    end_date + '&office=' + office;
                if (gender) {
                    url += '&gender=' + gender;
                }
                    console.log(active);
                if (active === '1' || active === '0') {
                    url += '&active=' + active;
                }
                $('#btn_export').attr('href', $('#btn_export').attr('href') +
                    url);

                oTable.ajax.reload();
            });

            $('#btn_reset').on('click', function(e) {
                start_date = '';
                end_date = '';
                office = '';
                $('#office').val('').trigger('change');
            });
        });

        function resetValue(name) {
            let value = null;
            eval(name + "=" + value + ";");
        }
    </script>
@endsection
@section('page-content')
    <div class="container-fluid">
        <div class="pb-3 mb-3 border-bottom">
            <div class="gap-2 d-flex flex-column flex-lg-row align-items-start align-items-lg-center">
                <div class="brd-crms flex-grow-1">
                    <nav aria-label="breadcrumb">
                        <ol class="m-0 breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}"
                                    class="text-decoration-none text-dark">Home</a></li>
                            <li class="breadcrumb-item" aria-current="page">@yield('title')</li>
                        </ol>
                    </nav>
                    <h4 class="m-0 mt-1 lh1 fs-6 text-uppercase fw-bold text-primary">@yield('title')</h4>
                </div>
                <div class="add-info justify-content-end">
                    <a href="{{ route('report.employee.profile.export') }}" id="btn_export" class="btn btn-primary btn-sm">
                        Export
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">

        <form>
            <div class="row" style="align-items: flex-end">
                <div class="col-md-2 d-none">
                    <label class="form-label" for="start_date">From</label>
                    <input class="form-control" type="date" name="start_date" id="start_date">
                </div>
                <div class="col-md-2 d-none">
                    <label class="form-label" for="end_date">To</label>
                    <input class="form-control" type="date" name="end_date" id="end_date">
                </div>
                <div class="col-md-2">
                    <label class="form-label" for="office">Office</label>
                    <select class="form-control select2" name="office" id="office">
                        <option value="">Select Office...</option>
                        @foreach ($offices as $office)
                            <option value="{{ $office->id }}">{{ $office->office_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label" for="office">Gender</label>
                    <select name="gender" id="gender" class="select2 form-control">
                        <option value="">Select a Gender</option>
                        @foreach ($genders as $gender)
                            <option value="{{ $gender->id }}" @if ($gender->id == old('gender')) selected @endif>
                                {{ $gender->title }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label" for="office">Active/Inactive</label>
                    <select class="form-control select2" name="active" id="active">
                        <option value="">Select Active/Inactive</option>
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>
                <div class="col">
                    <button type="button" id="btn_search" class="m-1 btn btn-primary btn-sm">Search</button>
                    <button type="reset" id="btn_reset" class="m-1 btn btn-secondary btn-sm">Reset</button>
                </div>
            </div>
            <span class="text-danger" id="error_message"></span>
        </form>

        <hr>

        <div class="rounded border shadow-sm card c-tabs-content active" id="employee-table" style="overflow: auto;">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table" id="employeeProfileReportTable">
                        <thead class="bg-light">
                            <tr>
                                <th>{{ __('label.sn') }}</th>
                                <th>Name of Staff</th>
                                <th>Staff ID</th>
                                <th>Joined Date</th>
                                <th>Position (Latest)</th>
                                <th>Duty Station (Latest)</th>
                                <th>District (Latest)</th>
                                <th>Supervisor Name (Latest)</th>
                                <th>Current Address</th>
                                <th>Mobile</th>
                                <th>Office Email</th>
                                <th>Citizenship No.</th>
                                <th>PAN No.</th>
                                <th>SSF No.</th>
                                <th>CIT No.</th>
                                <th>DOB</th>
                                <th>Gender</th>
                                <th>Blood Group</th>
                                <th>Marital Status</th>
                                <th>Bank Details</th>
                                <th>Probationary Complete</th>
                                <th>Active Employee</th>
                                <th>Last Working Date</th>
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
