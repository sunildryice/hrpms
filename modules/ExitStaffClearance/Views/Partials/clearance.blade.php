<div class="card">
    <form action="{{ route('clearance.record.store', $staffClearance->id) }}" method="POST" id="groupCForm">
        @csrf
        <div class="card-header fw-bold">
            <span class="card-title">
                <span class="fw-bold">A.</span>
                <span>
                    Clearance
                </span>
            </span>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-lg-12">
                    <table id="keyGoalTable" class="mb-3" style="width: 100%">
                        <thead>
                            <tr>
                                <th style="width: 20%" rowspan="2">Departments </th>
                                <th style="width: 50%" colspan="3">Cleared By:</th>
                                <th style="width: 30%" rowspan="2">Remarks</th>
                            </tr>
                            <tr>
                                <th style="width: 20%">Name </th>
                                <th style="width: 10%">Signature</th>
                                <th style="width: 20%">Cleared Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($departments as $department)
                                <tr>
                                    <td>
                                        <span style="width: 100%" class="fw-bold">{{ $department->title }}</span>
                                    </td>
                                    <td colspan="5"></td>
                                </tr>
                                @foreach ($department->childrens as $children)
                                    <tr>
                                        <td>
                                            <span style="width: 100%">{{ $children->title }}</span>
                                        </td>
                                        @php
                                            $record = $records->firstWhere('clearance_department_id', $children->id);
                                        @endphp
                                        <td class="text-center">
                                                <span style="width: 100%"
                                                    class="fw-bold">{{ $record?->getClearedByName() }}</span>
                                        </td>
                                        <td></td>
                                        <td class="text-center">
                                                <span style="width: 100%"
                                                    class="fw-bold">{{ $record?->getClearedDate() }}</span>
                                        </td>
                                        <td class="text-center">
                                                <span style="width: 100%" class="fw-bold">{{ $record?->remarks }}</span>
                                        </td>
                                    </tr>
                                @endforeach
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </form>
</div>
