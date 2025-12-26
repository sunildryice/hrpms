@extends('layouts.container-report')

@section('title', 'Staff Personal Information')
@section('page_css')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Mukta:wght@200;500;600;800&display=swap" rel="stylesheet">
    <style>
        .print-info {
            font-family: 'Mukta', sans-serif;
        }

        table {
            border: 1px solid;
        }

        .table thead th {
            font-size: 0.94375rem;

        }

        tbody,
        td,
        tfoot,
        th,
        thead,
        tr {
            border-width: 0.1px;
        }

        .table tr th {
            padding: 0.45rem 0.75rem;
            width: 18%;
        }

        .table tr td {
            padding: 0.25rem 0.75rem;
        }

        .staff-image {
            width: 180px;
        }


        .staff-image img {
            height: 100px;
            object-fit: contain;
        }
    </style>
@endsection
@section('page_js')
@endsection

@section('page-content')

    <section class="print-info bg-white p-3" id="print-info">

        <div class="print-title fw-bold mb-3 translate-middle text-center">
            <div class="fs-5">HERD International</div>
            <div class="fs-8">
                {{ $employee->employee_type_id == config('constant.FULL_TIME_EMPLOYEE') ? 'Staff' : 'Consultant/STE' }}
                Personal information</div>
        </div>
        <div class="print-header">
            <div class="row">
                <div class="col-lg-8">
                </div>
                <div class="col-lg-4">
                    <div class="d-flex flex-column justify-content-end">
                        <div class="d-flex flex-column justify-content-end brand-logo mb-4 flex-grow-1">
                            <div class="d-flex flex-column justify-content-end float-right">
                                <img src="{{ asset('img/logonp.png') }}" alt="" class="align-self-end pe-5" style="width: 200px">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="print-body mb-5">
            <table class="table border">
                <thead>
                    <tr>
                        <th colspan="5">
                            {{ $employee->employee_type_id == config('constant.FULL_TIME_EMPLOYEE') ? 'Staff' : 'Consultant/STE' }}
                            Personal Information </th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th scope="row">Staff ID </th>
                        <td colspan="3">{{ $employee->employee_code }}</td>
                        <td rowspan="5" class="staff-image text-center bg-white ">
                            @if (file_exists('storage/' . $employee->profile_picture) && $employee->profile_picture != '')
                                <img src="{{ asset('storage/' . $employee->profile_picture) }}" alt=""
                                    class="w-100 ">
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">Official Email Address </th>
                        <td colspan="3">{{ $employee->official_email_address }}</td>
                    </tr>
                    <tr>
                        <th scope="row">Full Name: </th>
                        <td colspan="3">{{ $employee->getFullName() }}</td>
                    </tr>
                    <tr>
                        <th scope="row">Position: </th>
                        <td colspan="3">{{ $employee->getDesignationName() }}</td>
                    </tr>
                    <tr>
                        <th scope="row">Joining Date: </th>
                        <td colspan="3">
                            @if ($employee->latestTenure->joined_date != null)
                                {{ $employee->latestTenure->joined_date->format('M d, Y') }}
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">Duty Station:</th>
                        <td colspan="4">{{ $employee->getDutyStation() }}</td>
                    </tr>
                    <tr>
                        <th scope="row">Line Manager Name:</th>
                        <td colspan="4">{{ $employee->latestTenure->getSupervisorName() }}</td>
                    </tr>
                    <tr>
                        <th scope="row">Reviewer Name:</th>
                        <td colspan="4">{{ $employee->latestTenure->getNextLineManagerName() }}</td>
                    </tr>
                    <tr>
                        <th scope="row" rowspan="4">Current Address</th>
                        <td colspan="4">Province: {{ $employee->address->temporary_province->province_name }}</td>
                    </tr>
                    <tr>
                        <td colspan="4">District: {{ $employee->address->temporary_district->district_name }}</td>
                    </tr>
                    <tr>
                        <td colspan="4">Municipality: {{ $employee->address->temporary_local_level->local_level_name }}
                        </td>
                    </tr>
                    <tr>
                        <td>Ward: {{ $employee->address->temporary_ward }}</td>
                        <td colspan="3">Tole: {{ $employee->address->temporary_tole }}</td>
                    </tr>
                    <tr>
                        <th scope="row" rowspan="4">Permanent Address</th>
                        <td colspan="4">Province: {{ $employee->address->permanent_province->province_name }}</td>
                    </tr>
                    <tr>
                        <td colspan="4">District: {{ $employee->address->permanent_district->district_name }}</td>
                    </tr>
                    <tr>
                        <td colspan="4">Municipality: {{ $employee->address->permanent_local_level->local_level_name }}
                        </td>
                    </tr>
                    <tr>
                        <td>Ward: {{ $employee->address->permanent_ward }}</td>
                        <td colspan="4">Tole: {{ $employee->address->permanent_tole }}</td>
                    </tr>
                    <tr>
                        <th scope="row">Telephone ( Mobile)</th>
                        <td colspan="4">{{ $employee->mobile_number }}</td>
                    </tr>
                    <tr>
                        <th scope="row">Date of Birth (DD/MM/YYYY) AD*</th>
                        <td colspan="4">{{ $employee->getDateOfBirth() }}</td>
                    </tr>
                    <tr>
                        <th scope="row">ID Numbers</th>
                        <td>Citizenship Number: {{ $employee->citizenship_number }}</td>
                        <td>PAN Number: {{ $employee->pan_number }}</td>
                        <td>SSF No. : {{ $employee->finance->ssf_number }}</td>
                        <td>CIT No. : {{ $employee->finance->cit_number }}</td>
                    </tr>
                    <tr>
                        <th scope="row" rowspan="4">Bank Account Details:</th>
                        <td colspan="4">Account Name: {{ $employee->finance->employee->getFullName() }}</td>
                    </tr>
                    <tr>
                        <td colspan="4">Account No. : {{ $employee->finance->account_number }}</td>
                    </tr>
                    <tr>
                        <td colspan="4">Bank Name : {{ $employee->finance->bank_name }}</td>
                    </tr>
                    <tr>
                        <td colspan="4">Branch : {{ $employee->finance->branch_name }}</td>
                    </tr>
                    <tr>
                        <th>Marital Status: </th>
                        <td colspan="5">{{ $employee->getMaritalStatus() }}</td>
                    </tr>
                    <tr>
                        <th>Gender:</th>
                        <td colspan="5">{{ $employee->genderName->title }}</td>
                    </tr>
                    <tr>
                        <th>If married:</th>
                        <td>Spouse Name : {{ $employee->isMarried() ? $employee->spouse->getFullName() : '' }}</td>
                        <td colspan="3">Date of Birth AD* :
                            {{ $employee->isMarried() ? $employee->spouse->getDateOfBirth() : '' }}</td>
                    </tr>
                    {{-- <tr>
                        <td colspan="3">Attach Citizenship</td>
                    </tr> --}}
                    <tr>
                        @if ($employee->isMarried())
                            @foreach ($employee->childrens as $key => $child)
                                <th>Child {{ ++$key }} Name: </th>
                                <td>{{ $child->getFullName() }}</td>
                            @endforeach
                        @else
                            <th>Child 1 Name:</th>
                            <td></td>
                        @endif
                        {{-- <td colspan="3">Attach Birth certificate</td> --}}
                    </tr>
                </tbody>
            </table>









            <table class="table border">
                <thead>
                    <tr>
                        <th colspan="2">Medical/ Health Information</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th scope="row">Blood Group</th>
                        <td>{{ $employee->medicalCondition->bloodGroup->title }}</td>
                    </tr>
                    <tr>
                        <th scope="row">Medical Condition</th>
                        <td>{{ $employee->medicalCondition->medical_condition }}</td>
                    </tr>
                    <tr>
                        <th scope="row">Remarks</th>
                        <td>{{ $employee->medicalCondition->remarks }}</td>
                    </tr>

                </tbody>
            </table>
            <table class="table border">
                <thead>
                    <tr>
                        <th colspan="2">Educational Details</th>
                    </tr>
                </thead>
                <tbody>
                    @if ($employee->education->isNotEmpty())
                        @foreach ($employee->education as $education)
                            <tr>
                                <th scope="row">Education Level</th>
                                <td>{{ $education->getEducationLevel() }}</td>
                            </tr>
                            <tr>
                                <th scope="row">Name of Degree</th>
                                <td>{{ $education->getDegree() }}</td>
                            </tr>
                            <tr>
                                <th scope="row">Institution</th>
                                <td>{{ $education->getInstitution() }}</td>
                            </tr>
                            <tr>
                                <th scope="row">Passed Year</th>
                                <td>{{ $education->getPassedYear() }}</td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <th scope="row">Education Level</th>
                            <td></td>
                        </tr>
                        <tr>
                            <th scope="row">Name of Degree</th>
                            <td></td>
                        </tr>
                        <tr>
                            <th scope="row">Institution</th>
                            <td></td>
                        </tr>
                        <tr>
                            <th scope="row">Passed Year</th>
                            <td></td>
                        </tr>
                    @endif

                </tbody>
            </table>
            <table class="table border">
                <thead>
                    <tr>
                        <th colspan="4">Experience</th>
                    </tr>
                </thead>
                <tbody>
                    @if ($employee->experiences->isNotEmpty())
                        @foreach ($employee->experiences as $experience)
                            <tr>
                                <th scope="row">Institution</th>
                                <td colspan="3">{{ $experience->institution }}</td>
                            </tr>
                            <tr>
                                <th scope="row">Position</th>
                                <td colspan="3">{{ $experience->position }}</td>
                            </tr>
                            <tr>
                                <th scope="row">Period From</th>
                                <td>{{ $experience->getPeriodFrom() }}</td>

                                <th scope="row">Period To</th>
                                <td>{{ $experience->getPeriodTo() }}</td>
                            </tr>
                            <tr>
                                <th scope="row">Remarks</th>
                                <td colspan="3">{{ $experience->remarks }}</td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <th scope="row">Institution</th>
                            <td colspan="3"></td>
                        </tr>
                        <tr>
                            <th scope="row">Position</th>
                            <td colspan="3"></td>
                        </tr>
                        <tr>
                            <th scope="row">Period From</th>
                            <td></td>

                            <th scope="row">Period To</th>
                            <td></td>
                        </tr>
                        <tr>
                            <th scope="row">Remarks</th>
                            <td colspan="3"></td>
                        </tr>
                    @endif

                </tbody>
            </table>
            <table class="table border">
                <thead>
                    <tr>
                        <th colspan="4">Training</th>
                    </tr>
                </thead>
                <tbody>
                    @if ($employee->trainings->isNotEmpty())
                        @foreach ($employee->trainings as $training)
                            <div>
                                <tr>
                                    <th scope="row">Institution</th>
                                    <td colspan="3">{{ $training->institution }}</td>
                                </tr>
                                <tr>
                                    <th scope="row">Training Topic</th>
                                    <td colspan="3">{{ $training->training_topic }}</td>
                                </tr>
                                <tr>
                                    <th scope="row">Period From</th>
                                    <td>{{ $training->getPeriodFrom() }}</td>

                                    <th scope="row">Period To</th>
                                    <td>{{ $training->getPeriodTo() }}</td>
                                </tr>
                                <tr>
                                    <th scope="row">Remarks</th>
                                    <td colspan="3">{{ $training->remarks }}</td>
                                </tr>
                            </div>
                        @endforeach
                    @else
                        <tr>
                            <th scope="row">Institution</th>
                            <td colspan="3"></td>
                        </tr>
                        <tr>
                            <th scope="row">Training Topic</th>
                            <td colspan="3"></td>
                        </tr>
                        <tr>
                            <th scope="row">Period From</th>
                            <td></td>
                            <th scope="row">Period To</th>
                            <td></td>
                        </tr>
                        <tr>
                            <th scope="row">Remarks</th>
                            <td colspan="3"></td>
                        </tr>
                    @endif
                </tbody>
            </table>
            <table class="table border">
                <thead>
                    <tr>
                        <th colspan="2">Emergency Contact Information </th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th scope="row">Full Name</th>
                        <td>{{ $employee->emergencyContact->getFullName() }}</td>
                    </tr>

                    <tr>
                        <th scope="row">Relationship</th>
                        <td>{{ $employee->emergencyContact->getRelationName() }}</td>
                    </tr>
                    <tr>
                        <th scope="row">Address</th>
                        <td colspan="5">{{ $employee->emergencyContact->getAddress() }}</td>
                    </tr>
                    <tr>
                        <th scope="row">Telephone ( Mobile)</th>
                        <td colspan="5">{{ $employee->emergencyContact->contact_number }}</td>
                    </tr>
                    <tr>
                        <th scope="row" rowspan="3">Nominee:</th>
                        <td>Name: {{ $employee->nominee->getFullName() }}</td>
                    </tr>
                    <tr>
                        <td>Relationship: {{ $employee->nominee->getRelationName() }}</td>
                    </tr>
                    <tr>
                        <td>Nominee contact no. : {{ $employee->nominee->contact_number }}</td>
                    </tr>

                    <tr>
                        <th scope="row">Probation Completion date/ Period:</th>
                        <td colspan="5">{{ $employee->probation_complete_date }}</td>
                    </tr>
                    <tr>
                        <th scope="row">Last working date:</th>
                        <td colspan="5">{{ $employee->last_working_date }}</td>
                    </tr>

                </tbody>
            </table>
        </div>
    </section>

    <script>
        window.onload = print;
    </script>

@endsection
