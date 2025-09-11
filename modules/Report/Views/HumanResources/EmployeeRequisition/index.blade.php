@extends('layouts.container')

@section('title', 'Report : Employee Requisition')

@section('page_js')
    <script type="text/javascript">
        let start_date      = '';
        let end_date        = '';
        let position        = '';
        let duty_station    = '';
        let fiscal_year     = '';

        $(document).ready(function() {
            $('#navbarVerticalMenu').find('#employee-requisition-report-menu').addClass('active');

            var oTable = $('#employeeRequisitionReportTable').DataTable({
                scrollX: true,
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('report.employee.requisition.index') }}",
                    type: 'POST',
                    data: function(d) {
                        d.start_date    = start_date;
                        d.end_date      = end_date;
                        d.position      = position;
                        d.duty_station  = duty_station;
                        d.fiscal_year   = fiscal_year;
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
                        data: 'position_title',
                        name: 'position_title'
                    },
                    {
                        data: 'duty_station',
                        name: 'duty_station'
                    },
                    {
                        data: 'requested_level',
                        name: 'requested_level'
                    },
                    {
                        data: 'requested_date',
                        name: 'requested_date'
                    },
                    {
                        data: 'type_of_employement',
                        name: 'type_of_employement'
                    },
                    {
                        data: 'for_fiscal_year',
                        name: 'for_fiscal_year'
                    },
                    {
                        data: 'replacement_for',
                        name: 'replacement_for'
                    },
                    {
                        data: 'date_required_from',
                        name: 'date_required_from'
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
                        name: 'donor_code'
                    },
                    {
                        data: 'requested_by',
                        name: 'requested_by'
                    },
                    {
                        data: 'requested_date',
                        name: 'requested_date'
                    },
                    {
                        data: 'approved',
                        name: 'approved'
                    },
                    {
                        data: 'approved_date',
                        name: 'approved_date'
                    },
                    {
                        data: 'vacancy_type',
                        name: 'vacancy_type'
                    },
                    {
                        data: 'vacancy_portfolio',
                        name: 'vacancy_portfolio'
                    },
                    {
                        data: 'vacancy_date',
                        name: 'vacancy_date'
                    },
                    {
                        data: 'vacancy_deadline',
                        name: 'vacancy_deadline'
                    },
                    {
                        data: 'recruitment_process',
                        name: 'recruitment_process'
                    },
                    {
                        data: 'recruited',
                        name: 'recruited'
                    },
                    {
                        data: 'joined_date',
                        name: 'joined_date'
                    },
                    {
                        data: 'remarks',
                        name: 'remarks'
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

                if ($('#position').val()) {
                    position = $('#position').val();
                }

                if ($('#duty_station').val()) {
                    duty_station = $('#duty_station').val();
                }

                if ($('#fiscal_year').val()) {
                    fiscal_year = $('#fiscal_year').val();
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
                    '/report/employee/requisition/export?start_date=' + start_date + '&end_date=' +
                    end_date + '&duty_station=' + duty_station + '&position=' + position + '&fiscal_year=' + fiscal_year);

                oTable.ajax.reload();
            });

            $('#btn_reset').on('click', function(e) {
                // e.preventDefault();
                // $('form')[0].reset();
                start_date      = '';
                end_date        = '';
                position        = '';
                duty_station    = '';
                fiscal_year     = '';
                $('#fiscal_year').val('').trigger('change');
                $('#position').val('').trigger('change');
                $('#duty_station').val('').trigger('change');
                $("select option").removeAttr('selected');
                $("input[type=text]").removeAttr('value');
            });
        });

        $('[name="start_date"]').datepicker({
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
                    <a href="{{ route('report.employee.requisition.export') }}" id="btn_export"
                        class="btn btn-primary btn-sm">
                        Export
                    </a>
                </div>
            </div>
        </div>
        <div class="card" id="employee-table" style="overflow: auto;">
            <div class="card-body">
                <form>
                    <div class="row mb-4" style="align-items: flex-end">
                        <div class="col-md-2">
                            <label class="form-label" for="start_date">From</label>
                            <input class="form-control" type="text" name="start_date" id="start_date">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label" for="end_date">To</label>
                            <input class="form-control" type="text" name="end_date" id="end_date">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label" for="fiscal_year">Fiscal Year</label>
                            <select class="form-control select2" name="fiscal_year" id="fiscal_year">
                                <option value="" onclick="resetValue('fiscal_year')">Select year...</option>
                                @foreach ($fiscalYears as $fiscalYear)
                                    <option value="{{$fiscalYear->id}}">{{$fiscalYear->title}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label" for="position">Position</label>
                            <select class="form-control select2" name="position" id="position">
                                <option value="" onclick="resetValue('position')">Select position...</option>
                                <option value="all">All</option>
                                @foreach ($positions as $position)
                                    <option value="{{ $position }}">{{ $position }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label" for="duty_station">Duty Station</label>
                            <select class="form-control select2" name="duty_station" id="duty_station">
                                <option value="" onclick="resetValue('duty_station')">Select duty station...</option>
                                <option value="all">All</option>
                                @foreach ($dutyStations as $dutyStation)
                                    <option value="{{ $dutyStation->id }}">{{ $dutyStation->district_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col">
                            <button type="button" id="btn_search" class="btn btn-primary btn-sm m-1">Search</button>
                            <button type="reset" id="btn_reset" class="btn btn-secondary btn-sm m-1">Reset</button>
                        </div>
                    </div>
                    <span class="text-danger" id="error_message"></span>
                </form>
                <div class="table-responsive">
                    <table class="table" id="employeeRequisitionReportTable">
                        <thead class="bg-light">
                            <tr>
                                <th>{{ __('label.sn') }}</th>
                                <th>Ref. No.</th>
                                <th>Position Title</th>
                                <th>Duty Station</th>
                                <th>Requested Level</th>
                                <th>Requested Date</th>
                                <th>Type of Employement</th>
                                <th>For Fiscal Year</th>
                                <th>Replacement For</th>
                                <th>Date Required From</th>
                                <th>Project</th>
                                <th>Account Code</th>
                                <th>Activity Code</th>
                                <th>Donor Code</th>
                                <th>Requested By</th>
                                <th>Requested Date</th>
                                <th>Approved (Yes/No)</th>
                                <th>Approved Date</th>
                                <th>Vacancy Type</th>
                                <th>Vacancy Portfolio</th>
                                <th>Vacancy Date</th>
                                <th>Vacancy Deadline</th>
                                <th>Recuitment Process</th>
                                <th>Recruited</th>
                                <th>Joined Date</th>
                                <th>Remarks</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
@stop
