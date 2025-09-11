@extends('layouts.container')

@section('title', 'Report : Employee Family Detail')

@section('page_js')
    <script type="text/javascript">
        $(document).ready(function() {
            let office_id = '';

            $('#navbarVerticalMenu').find('#employee-insurance-report-menu').addClass('active');

            $('#btn_search').on('click', function(e) {
                if ($('#office_id').val()) {
                    office_id = $('#office_id').val();
                }

                $('#btn_export').attr('href', '');
                $('#btn_export').attr('href', $('#btn_export').attr('href') +
                    '/report/employee/family/detail/export?office_id=' + office_id);

                oTable.ajax.reload();
            });

            $('#btn_reset').on('click', function(e) {
                office_id = '';
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
                    <a href="{{ route('report.employee.insurance.export', ['office_id' => request()->has('office_id') ? request()->get('office_id') : null]) }}" id="btn_export"
                        class="btn btn-primary btn-sm">
                        Export
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">

        <form class="flex" method="POST" action="{{route('report.employee.insurance.index')}}">
            @csrf
            <div class="row" style="align-items: flex-end">
                <div class="col-md-2">
                    <label class="form-label" for="office_id">Office</label>
                    @php
                        $selectedOfficeId = request()->has('office_id') ? request()->get('office_id') : '';
                    @endphp
                    <select class="form-control select2" name="office_id" id="office_id">
                        <option value="">All Offices</option>
                        @foreach ($offices as $office)
                            <option value="{{ $office->id }}" {{ $office->id == $selectedOfficeId ? 'selected' : ''}}>{{ $office->getOfficeName() }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col">
                    <button type="submit" id="btn_search" class="btn btn-primary btn-sm m-1">Search</button>
                </div>
            </div>
            <span class="text-danger" id="error_message"></span>
            <hr>
        </form>


        <div class="card shadow-sm border rounded c-tabs-content active" id="employee-table" style="overflow: auto;">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="employeeInsuranceReportTable">
                        <thead class="bg-light">
                            <tr>
                                <th rowspan="2">{{ __('label.sn') }}</th>
                                <th rowspan="2">Staff Name</th>
                                <th rowspan="2">Designation</th>
                                <th rowspan="2">Duty Station</th>
                                <th rowspan="2">Date of Birth</th>
                                <th rowspan="2">Age</th>
                                <th colspan="5" style="text-align: center;">Emergency Contact</th>
                                <th colspan="5" style="text-align: center;">Nominee</th>
                                <th colspan="5" style="text-align: center;">Family Members</th>
                            </tr>
                            <tr>
                                <th>Name</th>
                                <th>Gender</th>
                                <th>Relationship</th>
                                <th>DOB</th>
                                <th>Age</th>

                                <th>Name</th>
                                <th>Gender</th>
                                <th>Relationship</th>
                                <th>DOB</th>
                                <th>Age</th>

                                <th>Name</th>
                                <th>Gender</th>
                                <th>Relationship</th>
                                <th>DOB</th>
                                <th>Age</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($employees as $key => $employee)
                                @php
                                    $rowCount = $employee->familyDetails->count() == 0 ? 1 : $employee->familyDetails->count();
                                    $familyDetails = $employee->familyDetails;
                                    $emergencyContact = $familyDetails->whereNotNull('emergency_contact_at')->count() == 0 ? false : $familyDetails->whereNotNull('emergency_contact_at')->first();
                                    $nominee = $familyDetails->whereNotNull('nominee_at')->count() == 0 ? false : $familyDetails->whereNotNull('nominee_at')->first();
                                @endphp

                                <tr>
                                    <td rowspan={{$rowCount}}>{{ ++$key }}</td>
                                    <td rowspan={{$rowCount}}>{{ $employee->getFullName() }}</td>
                                    <td rowspan={{$rowCount}}>{{ $employee->latestTenure->getDesignationName() }}</td>
                                    <td rowspan={{$rowCount}}>{{ $employee->latestTenure->getDutyStation() }}</td>
                                    <td rowspan={{$rowCount}}>{{ $employee->getDateOfBirth() }}</td>
                                    <td rowspan={{$rowCount}}>{{ $employee->getAge() }}</td>


                                    @if ($emergencyContact)
                                        <td rowspan={{$rowCount}}>{{ $emergencyContact->getFullName() }}</td>
                                        <td rowspan={{$rowCount}}></td>
                                        <td rowspan={{$rowCount}}>{{ $emergencyContact->getRelationName() }}</td>
                                        <td rowspan={{$rowCount}}>{{ $emergencyContact->getDateOfBirth() }}</td>
                                        <td rowspan={{$rowCount}}>{{ $emergencyContact->getAge() }}</td>
                                    @else
                                        <td rowspan={{$rowCount}}></td>
                                        <td rowspan={{$rowCount}}></td>
                                        <td rowspan={{$rowCount}}></td>
                                        <td rowspan={{$rowCount}}></td>
                                        <td rowspan={{$rowCount}}></td>
                                    @endif


                                    @if ($nominee)
                                        <td rowspan="{{$rowCount}}">{{ $nominee->getFullName() }}</td>
                                        <td rowspan="{{$rowCount}}"></td>
                                        <td rowspan="{{$rowCount}}">{{ $nominee->getRelationName() }}</td>
                                        <td rowspan="{{$rowCount}}">{{ $nominee->getDateOfBirth() }}</td>
                                        <td rowspan="{{$rowCount}}">{{ $nominee->getAge() }}</td>
                                    @else
                                        <td rowspan={{$rowCount}}></td>
                                        <td rowspan={{$rowCount}}></td>
                                        <td rowspan={{$rowCount}}></td>
                                        <td rowspan={{$rowCount}}></td>
                                        <td rowspan={{$rowCount}}></td>
                                    @endif



                                    @if ($familyDetails->isNotEmpty())
                                        @foreach ($familyDetails as $familyDetail)
                                            @if ($loop->first)
                                                <td>{{ $familyDetail->getFullName() }}</td>
                                                <td></td>
                                                <td>{{ $familyDetail->getRelationName() }}</td>
                                                <td>{{ $familyDetail->getDateOfBirth() }}</td>
                                                <td>{{ $familyDetail->getAge() }}</td>
                                            @else
                                            <tr>
                                                <td>{{ $familyDetail->getFullName() }}</td>
                                                <td></td>
                                                <td>{{ $familyDetail->getRelationName() }}</td>
                                                <td>{{ $familyDetail->getDateOfBirth() }}</td>
                                                <td>{{ $familyDetail->getAge() }}</td>
                                            </tr>
                                            @endif
                                        @endforeach
                                    @else
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    @endif

                                </tr>
                            @endforeach

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@stop
