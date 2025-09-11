<div class="card-header fw-bold">
    <h3 class="m-0 fs-6">Trainings</h3>
</div>
<div class="card-body">
    <div class="table-responsive">
        <table class="table table-borderedless">
            <thead class="bg-light">
                <tr>
                    <th style="width: 25%">{{ __('label.institution') }}</th>
                    <th>{{ __('label.training-topic') }}</th>
                    <th>{{ __('label.from-date') }}</th>
                    <th>{{ __('label.to-date') }}</th>
                    <th>{{ __('label.document') }}</th>
                    <th>{{ __('label.action') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($employee->trainings as $training)
                    <tr>
                        <td>{{ $training->institution }}</td>
                        <td>{{ $training->training_topic }}</td>
                        <td>{{ $training->getPeriodFrom() }}</td>
                        <td>{{ $training->getPeriodTo() }}</td>
                        <td>
                            @if (file_exists('storage/' . $training->attachment) && $training->attachment != '')
                                <a href="{!! asset('storage/' . $training->attachment) !!}" target="_blank" class="btn btn-outline-primary btn-sm"
                                    title="View Attachment">
                                    <i class="bi-download"></i>
                                </a>
                            @endif
                        </td>
                        <td>
                            <a href="javascript:;" class="btn btn-outline-primary btn-sm"
                                onclick="displayTrainingEditForm(this, '{!! route('employees.trainings.edit', [$training->employee_id, $training->id]) !!}')">
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
