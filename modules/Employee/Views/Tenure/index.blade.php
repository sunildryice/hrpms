<div class="card-header fw-bold">
    <h3 class="m-0 fs-6">Tenure</h3>
</div>
<div class="card-body">
    <div class="table-responsive">
        <table class="table table-borderedless">
            <thead class="bg-light">
                <tr>
                    <th>Designation</th>
                    <th>Department</th>
                    <th>From Date</th>
                    <th>To Date</th>
                    <th>Contract End Date</th>
                    <th>Duty Station</th>
                    <th>District</th>
                    <th>Line Manager</th>
                    <th>Reviewer</th>
                    <th class="sticky-col">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($employee->tenures as $tenure)
                    <tr>
                        <td>{{ $tenure->getDesignationName() }}</td>
                        <td>{{ $tenure->getDepartmentName() }}</td>
                        <td>{{ $tenure->getJoinedDate() }}</td>
                        <td>{{ $tenure->getToDate() }}</td>
                        <td>{{ $tenure->getContractEndDate() }}</td>
                        <td>{{ $tenure->duty_station }}</td>
                        <td>{{ $tenure->getDutyStation() }}</td>
                        <td>{{ $tenure->getSupervisorName() }}</td>
                        <td>{{ $tenure->getNextLineManagerName() }}</td>
                        <td class="sticky-col">
                            @if ($authUser->can('update', $tenure))
                                <a href="javascript:;" class="btn btn-outline-primary btn-sm"
                                    onclick="displayTenureEditForm(this, '{!! route('employees.tenures.edit', [$tenure->employee_id, $tenure->id]) !!}')">
                                    <i class="bi-pencil-square"></i> Edit</a>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

</div>
@push('scripts')
@endpush
