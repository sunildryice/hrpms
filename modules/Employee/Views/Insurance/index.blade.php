<div class="card-header fw-bold">Insurance</div>
<div class="card-body">
    <div class="table-responsive">
        <table class="table table-borderedless">
            <thead class="bg-light">
                <tr>
                    <th style="width: 25%">{{ __('label.insurer') }}</th>
                    <th>{{ __('label.amount') }}</th>
                    <th>{{ __('label.fy') }}</th>
                    <th>{{ __('label.paid-date') }}</th>
                    <th>{{ __('label.attachment') }}</th>
                    @if ($actionMode == 'edit')
                        <th>{{ __('label.action') }}</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @foreach ($employee->insurances as $insurance)
                    <tr>
                        <td>{{ $insurance->insurer }}</td>
                        <td>{{ $insurance->amount }}</td>
                        <td>{{ $insurance->getPayrollFiscalYear() }}</td>
                        <td>{{ $insurance->getPaidDate() }}</td>
                        <td>
                            @if (file_exists('storage/' . $insurance->attachment) && $insurance->attachment != '')
                                <a href="{!! asset('storage/' . $insurance->attachment) !!}" target="_blank" class="btn btn-outline-primary btn-sm"
                                    title="View Attachment">
                                    <i class="bi-download"></i>
                                </a>
                            @endif
                        </td>
                        @if ($actionMode == 'edit')
                            <td>
                                <a href="javascript:;" class="btn btn-outline-primary btn-sm"
                                    onclick="displayInsuranceEditForm(this, '{!! route('employees.insurance.edit', [$insurance->employee_id, $insurance->id]) !!}')">
                                    <i class="bi-pencil-square"></i></a>
                            </td>
                        @endif
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
