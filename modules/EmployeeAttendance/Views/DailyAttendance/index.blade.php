@extends('layouts.container')

@section('title', 'Daily Attendance')


@section('page_js')
    <script type="text/javascript">
        let selected_date = '';
        let office_id = '';

        $(document).ready(function() {

            $('#navbarVerticalMenu').find('#daily-attendance-index').addClass('active');

            $('[name=selected_date]').datepicker({
                language: 'en-GB',
                autohide: true,
                format: 'yyyy-mm-dd',
                endDate: '0d'
            });

            // Set default to today
            const today = new Date().toISOString().split('T')[0];
            $('#selected_date').val(today);
            selected_date = new Date(today).getTime();

            var table = $('#dailyAttendanceTable').DataTable({
                scrollX: true,
                processing: true,
                serverSide: true,
                bFilter: false,
                bPaginate: false,
                bInfo: true,
                ajax: {
                    url: "{{ route('daily.attendance.index') }}",
                    type: 'POST',
                    data: function(d) {
                        d.selected_date = selected_date;
                        d.office_id = office_id;
                    }
                },
                columns: [{
                        data: 'staff_id',
                        name: 'staff_id'
                    },
                    {
                        data: 'employee_name',
                        name: 'employee_name',
                        className: 'name-col'
                    },
                    {
                        data: 'time_in',
                        name: 'time_in'
                    },
                    {
                        data: 'time_out',
                        name: 'time_out'
                    },
                    {
                        data: 'hours_worked',
                        name: 'hours_worked'
                    },
                    {
                        data: 'remarks',
                        name: 'remarks',
                        className: 'remarks-col'
                    },
                ],
            });

            // Auto-load today's data on page load
            table.ajax.reload();

            $('#btn_search').on('click', function() {
                if ($('#selected_date').val()) {
                    let sd = new Date($('#selected_date').val());
                    selected_date = sd.getTime();
                } else {
                    selected_date = '';
                }

                office_id = $('#office_id').val() || '';

                table.ajax.reload();
            });

            $('#btn_reset').on('click', function() {
                const today = new Date().toISOString().split('T')[0];
                $('#selected_date').val(today);
                selected_date = new Date(today).getTime();
                $('#office_id').val('').trigger('change.select2');
                office_id = '';
                table.ajax.reload();
            });
        });
    </script>
@endsection

@section('page-content')

    <div class="container-fluid">
        <div class="pb-3 mb-3 border-bottom">
            <div
                class="d-flex flex-column flex-lg-row align-items-start align-items-lg-center justify-content-between gap-3">
                <div class="flex-grow-1">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item">
                                <a href="{{ route('dashboard.index') }}" class="text-decoration-none text-dark">Home</a>
                            </li>
                            <li class="breadcrumb-item" aria-current="page">@yield('title')</li>
                        </ol>
                    </nav>
                    <h4 class="m-0 lh1 mt-1 fs-6 text-uppercase fw-bold text-primary">@yield('title')</h4>
                </div>
            </div>
        </div>

        <div class="card shadow-sm border rounded">
            <div class="card-body">
                <form>
                    <div class="row mb-4 align-items-end">
                        <div class="col-md-2">
                            <label class="form-label" for="selected_date">Date</label>
                            <input class="form-control" type="text" name="selected_date" id="selected_date"
                                placeholder="Select Date">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label" for="office_id">Office</label>
                            <select class="form-control select2" name="office_id" id="office_id">
                                <option value="">Select Office</option>
                                @foreach ($offices as $office)
                                    <option value="{{ $office->id }}">{{ $office->getOfficeName() }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-auto">
                            <button type="button" id="btn_search" class="btn btn-primary btn-sm m-1">Search</button>
                            <button type="button" id="btn_reset" class="btn btn-secondary btn-sm m-1">Reset</button>
                        </div>
                    </div>
                    <span class="text-danger small" id="error_message"></span>
                </form>

                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="dailyAttendanceTable">
                        <thead class="bg-light">
                            <tr>
                                <th>Staff ID</th>
                                <th>Employee Name</th>
                                <th>Time In (hh:mm)</th>
                                <th>Time Out (hh:mm)</th>
                                <th>Hours Worked (hh.mm)</th>
                                <th style="width: 40%;">Remarks</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

@endsection
