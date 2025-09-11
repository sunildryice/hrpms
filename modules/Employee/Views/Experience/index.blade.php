<div class="card-header fw-bold">
    <h3 class="m-0 fs-6">Experiences</h3>
</div>
<div class="card-body">
    <div class="table-responsive">
        <table class="table">
            <thead class="thead-light">
                <tr>
                    <th style="width: 25%">{{ __('label.institution') }}</th>
                    <th>{{ __('label.position') }}</th>
                    <th>{{ __('label.from-date') }}</th>
                    <th>{{ __('label.to-date') }}</th>
                    <th>{{ __('label.document') }}</th>
                    <th>{{ __('label.action') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($employee->experiences as $experience)
                    <tr>
                        <td>{{ $experience->institution }}</td>
                        <td>{{ $experience->position }}</td>
                        <td>{{ $experience->getPeriodFrom() }}</td>
                        <td>{{ $experience->getPeriodTo() }}</td>
                        <td>
                            @if (file_exists('storage/' . $experience->attachment) && $experience->attachment != '')
                                <a href="{!! asset('storage/' . $experience->attachment) !!}" target="_blank" class="btn btn-outline-primary btn-sm"
                                    title="View Attachment">
                                    <i class="bi-download"></i>
                                </a>
                            @endif
                        </td>
                        <td>
                            <a href="javascript:;" class="btn btn-outline-primary btn-sm"
                                onclick="displayExperienceEditForm(this, '{!! route('employees.experiences.edit', [$experience->employee_id, $experience->id]) !!}')">
                                <i class="bi-pencil-square"></i></a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@push('scripts')
@endpush
