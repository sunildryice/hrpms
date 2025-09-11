@php
    $clearance = isset($staffClearance) ? $staffClearance : $clearance;
    $employeeExitPayable = $clearance->employeeExitPayable;
    $fiscalYear = \Modules\Master\Models\FiscalYear::select(['id', 'title'])
        ->where('title', $staffClearance->handoverNote->resignation_date?->format('Y'))
        ->first();
    $leaves = app(\Modules\Employee\Repositories\LeaveRepository::class)
        ->getEmployeeLeaves($employeeExitPayable->employee_id, $fiscalYear?->id)
        ->where('leaveType.encashment', 1);
    $totalBalance = 0;
    foreach ($leaves as $leave) {
        $balance = $leave->balance;
        $totalBalance += $balance;
    }
@endphp
<table id="payable-table" class="mb-3" style="width: 100%">
    <thead>
        <tr>
            <th style="width: 20%">Payable Details </th>
            <th style="width: 50%">Date From</th>
            <th style="width: 30%">Date to</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>Outstanding Salary</td>
            <td> {{ $employeeExitPayable->salary_date_from?->format('Y-m-d') }} </td>
            <td> {{ $employeeExitPayable->salary_date_to?->format('Y-m-d') }} </td>
        </tr>
        <tr>
            <td>Festival Bonus</td>
            <td> {{ $employeeExitPayable->festival_bonus_date_from?->format('Y-m-d') }} </td>
            <td> {{ $employeeExitPayable->festival_bonus_date_to?->format('Y-m-d') }} </td>
        </tr>
        @if ($totalBalance)
            {{-- @if ($employeeExitPayable->leave_balance) --}}
            <tr>
                <td>Leave Balance(Hours)</td>
                <td colspan="2"> {{ $totalBalance }} </td>
            </tr>
        @endif
        @if ($employeeExitPayable->gratuity_amount > 0)
            <tr>
                <td>Severence Pay/Gratuity (if any)</td>
                <td colspan="2"> {{ $employeeExitPayable->gratuity_amount }} </td>
            </tr>
        @endif
        @if ($employeeExitPayable->other_amount > 0)
            <tr>
                <td>Other</td>
                <td colspan="2"> {{ $employeeExitPayable->other_amount }} </td>
            </tr>
        @endif
        @if ($employeeExitPayable->advance_amount > 0)
            <tr>
                <td>Advance Amount</td>
                <td colspan="2"> {{ $employeeExitPayable->advance_amount }} </td>
            </tr>
        @endif
        @if ($employeeExitPayable->loan_amount > 0)
            <tr>
                <td>Loan Amount</td>
                <td colspan="2"> {{ $employeeExitPayable->loan_amount }} </td>
            </tr>
        @endif
        @if ($employeeExitPayable->other_payable_amount > 0)
            <tr>
                <td>Other Payable Amount</td>
                <td colspan="2"> {{ $employeeExitPayable->other_payable_amount }} </td>
            </tr>
        @endif
    </tbody>
</table>
