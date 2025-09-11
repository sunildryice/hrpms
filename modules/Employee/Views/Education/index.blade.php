<div class="card-header fw-bold">Educational Details</div>
<div class="card-body">
    <div class="table-responsive">
        <table class="table table-borderedless">
            <thead class="bg-light">
                <tr>
                    <th>{{ __('label.education-level') }}</th>
                    <th>{{ __('label.degree') }}</th>
                    <th style="width:25%">{{ __('label.institution') }}</th>
                    <th>{{ __('label.passed-year') }}</th>
                    <th>{{ __('label.document') }}</th>
                    <th>{{ __('label.action') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($employee->education as $emp_education)
                    <tr>
                        <td>{{ $emp_education->getEducationLevel() }}</td>
                        <td>{{ $emp_education->getDegree() }}</td>
                        <td style="width: 25%">{{ $emp_education->getInstitution() }}</td>
                        <td>{{ $emp_education->getPassedYear() }}</td>
                        <td>
                            @if (file_exists('storage/' . $emp_education->attachment) && $emp_education->attachment != '')
                                <a href="{!! asset('storage/' . $emp_education->attachment) !!}" target="_blank" class="btn btn-outline-primary btn-sm"
                                    title="View Attachment">
                                    <i class="bi-download"></i>
                                </a>
                            @endif
                        </td>
                        <td>
                            <a href="javascript:;" class="btn btn-outline-primary btn-sm"
                                onclick="displayEducationEditForm(this, '{!! route('employees.education.edit', [$emp_education->employee_id, $emp_education->id]) !!}')">
                                <i class="bi-pencil-square"></i></a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
