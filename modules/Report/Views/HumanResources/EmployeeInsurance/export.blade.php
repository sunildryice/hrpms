<table>
    <thead>
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
        @foreach ($employees as $key=>$employee)
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
                    <td rowspan={{$rowCount}}>{{ $nominee->getFullName() }}</td>
                    <td rowspan={{$rowCount}}></td>
                    <td rowspan={{$rowCount}}>{{ $nominee->getRelationName() }}</td>
                    <td rowspan={{$rowCount}}>{{ $nominee->getDateOfBirth() }}</td>
                    <td rowspan={{$rowCount}}>{{ $nominee->getAge() }}</td>
                @else
                    <td rowspan={{$rowCount}}></td>
                    <td rowspan={{$rowCount}}></td>
                    <td rowspan={{$rowCount}}></td>
                    <td rowspan={{$rowCount}}></td>
                    <td rowspan={{$rowCount}}></td>
                @endif



                @if ($familyDetails && $familyDetails->isNotEmpty())
                    @foreach ($familyDetails as $familyDetail)
                        @if($loop->first)
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