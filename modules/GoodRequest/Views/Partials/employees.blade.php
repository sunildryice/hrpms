<div class="card">
    <div class="card-header fw-bold">
       Direct Dispatch Employees
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-lg-12">
                <div class="table-responsive">
                    <table class="table" id="goodRequestItemTable">
                        <thead class="thead-light">
                            <tr>
                                <th scope="col">{{ __('label.sn') }}</th>
                                <th scope="col">{{ __('label.staff-code') }}</th>
                                <th scope="col">{{ __('label.name') }}</th>
                                <th scope="col">{{ __('label.designation') }}</th>
                                <th scope="col">{{ __('label.office') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($goodRequest?->employees as $index=> $employee)
                                <tr>
                                    <td>{{ ++$index }}</td>
                                    <td>{{ $employee->employee_code }}</td>
                                    <td>{{ $employee->getFullName() }}</td>
                                    <td>{{ $employee->getDesignationName() }}</td>
                                    <td>{{ $employee->getOfficeName() }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" @class(['text-center'])>
                                        No Records
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
