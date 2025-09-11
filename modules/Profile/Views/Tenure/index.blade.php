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
                    <th>Date of Joining</th>
                    <th>Duty Station</th>
                    <th>Supervisor</th>
                    <th>Cross Supervisor</th>
                    <th>Next Line Manager</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($employee->tenures as $tenure)
                    <tr>
                        <td>{{ $tenure->getDesignationName() }}</td>
                        <td>{{ $tenure->getDepartmentName() }}</td>
                        <td>{{ $tenure->getJoinedDate() }}</td>
                        <td>{{ $tenure->getDutyStation() }}</td>
                        <td>{{ $tenure->getSupervisorName() }}</td>
                        <td>{{ $tenure->getCrossSupervisorName() }}</td>
                        <td>{{ $tenure->getNextLineManagerName() }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

</div>
@push('scripts')
@endpush
