<div class="card-header fw-bold">
    <h3 class="m-0 fs-6">Working Hours</h3>
</div>
<div class="card-body">
    <div class="table-responsive">
        <table class="table table-borderedless">
            <thead class="bg-light">
            <tr>
                <th>Start Date</th>
                <th>End Date</th>
                <th>Work Percentile</th>
                <th>Remarks</th>
                <th class="sticky-col">Action</th>
            </tr>
            </thead>
            <tbody>
            @foreach($employee->hours as $hour)
                <tr>
                    <td>{{ $hour->getStartDate() }}</td>
                    <td>{{ $hour->getEndDate() }}</td>
                    <td>{{ $hour->work_percentile }}%</td>
                    <td>{{ $hour->remarks }}</td>
                    <td class="sticky-col">
                        @if($authUser->can('update', $hour))
                            <a href="javascript:;" class="btn btn-outline-primary btn-sm"
                               onclick="displayHourEditForm(this, '{!! route('employees.hours.edit', [$hour->employee_id, $hour->id]) !!}')">
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
