@extends('layouts.container')

@section('title', 'Report : Consultant Profile')

@section('page_js')
    <script type="text/javascript">
        $(document).ready(function() {

            let start_date = '';
            let end_date = '';
            let office = '';

            $('#navbarVerticalMenu').find('#consultant-profile-report-menu').addClass('active');

            var oTable = $('#consultantProfileReportTable').DataTable({
                scrollX: true,
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('report.consultant.profile.index') }}",
                    type: 'POST',
                    data: function(d) {
                        d.start_date = start_date;
                        d.end_date = end_date;
                        d.office = office;
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'consultant_name',
                        name: 'consultant_name'
                    },
                    {
                        data: 'consultant_company',
                        name: 'consultant_company'
                    },
                    {
                        data: 'id_number',
                        name: 'id_number'
                    },
                    {
                        data: 'joined_date',
                        name: 'joined_date'
                    },
                    {
                        data: 'consultant_type',
                        name: 'consultant_type'
                    },
                    {
                        data: 'position_latest',
                        name: 'position_latest'
                    },
                    {
                        data: 'duty_station_latest',
                        name: 'duty_station_latest'
                    },
                    {
                        data: 'supervisor_name_latest',
                        name: 'supervisor_name_latest'
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
                        data: 'citizenship_number',
                        name: 'citizenship_number'
                    },
                    {
                        data: 'pan_vat_number',
                        name: 'pan_vat_number'
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
                        data: 'bank_details',
                        name: 'bank_details'
                    },
                    {
                        data: 'leave_applicable',
                        name: 'leave_applicable'
                    },
                    {
                        data: 'contract_end_date',
                        name: 'contract_end_date'
                    },
                    {
                        data: 'contract_amendment_tenure',
                        name: 'contract_amendment_tenure'
                    },
                    {
                        data: 'contract_ending_notice_period',
                        name: 'contract_ending_notice_period'
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

                if (start_date > end_date) {
                    $('#error_message').html('\'From\' date cannot be greater than \'To\' date.');
                    return;
                } else {
                    $('#error_message').html('');
                    $('#error_message').hide();
                }

                $('#btn_export').attr('href', '');
                $('#btn_export').attr('href', $('#btn_export').attr('href') +
                    '/report/consultant/profile/export?start_date=' + start_date + '&end_date=' +
                    end_date + '&office=' + office);

                oTable.ajax.reload();
            });

            $('#btn_reset').on('click', function(e) {
                start_date = '';
                end_date = '';
                office = '';
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
                    <a href="{{ route('report.consultant.profile.export') }}" id="btn_export"
                        class="btn btn-primary btn-sm">
                        Export
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">

        <form class="d-none">
            <div class="row" style="align-items: flex-end">
                <div class="col-md-2">
                    <label class="form-label" for="start_date">From</label>
                    <input class="form-control" type="date" name="start_date" id="start_date">
                </div>
                <div class="col-md-2">
                    <label class="form-label" for="end_date">To</label>
                    <input class="form-control" type="date" name="end_date" id="end_date">
                </div>
                <div class="col-md-2">
                    <label class="form-label" for="office">Office</label>
                    <select class="form-control" name="office" id="office">
                        <option value="" selected disabled>Select Office...</option>
                        @foreach ($offices as $office)
                            <option value="{{ $office->id }}">{{ $office->office_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col">
                    <button type="button" id="btn_search" class="btn btn-primary btn-sm m-1">Search</button>
                    <button type="reset" id="btn_reset" class="btn btn-secondary btn-sm m-1">Reset</button>
                </div>
            </div>
            <span class="text-danger" id="error_message"></span>
            <hr>
        </form>


        <div class="card shadow-sm border rounded c-tabs-content active" id="employee-table" style="overflow: auto;">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table" id="consultantProfileReportTable">
                        <thead class="bg-light">
                            <tr>
                                <th>{{ __('label.sn') }}</th>
                                <th>Consultant Name</th>
                                <th>Consultant Company</th>
                                <th>ID No.</th>
                                <th>Joined Date</th>
                                <th>Consultant Type</th>
                                <th>Position (Latest)</th>
                                <th>Duty Station (Latest)</th>
                                <th>Supervisor Name (Latest)</th>
                                <th>Current Address</th>
                                <th>Mobile</th>
                                <th>Office Email</th>
                                <th>Citizenship No.</th>
                                <th>PAN/VAT No.</th>
                                <th>DOB</th>
                                <th>Gender</th>
                                <th>Bank Details</th>
                                <th>Leave Applicable</th>
                                <th>Contract End Date</th>
                                <th>Contract Ammendment/Tenure</th>
                                <th>Contract Ending Notice Period</th>
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
