<table class="table" id="constructionReportTable">
    <thead class="bg-light">
        <tr>
            <th colspan="10" style="text-align: center">Construction Report</th>
        </tr>
    <tr>
        <th>{{ __('label.sn') }}</th>
        <th>Year</th>
        <th>Health Facility Name</th>
        <th>District</th>
        <th>Location</th>
        <th>Category</th>
        <th>MOU Start Date</th>
        <th>MOU End Date</th>
        <th>Work Completion Date</th>
        <th>Amendment Effective Date</th>
        <th>Extension To Date</th>
        <th>Total Project Cost NPR</th>
        <th>OHW Commitment Value NPR</th>
        <th>Other Party's Contribution NPR</th>
        <th>Total Fund Transferred NPR</th>
        <th>Expense Settled NPR</th>
        <th>Advance/Payable NPR</th>
        <th>Physical Work Progress %</th>
        <th>Current Status</th>
        <th>Donor tagging</th>
        <th>Metal Plaque Text</th>
    </tr>
    </thead>
    <tbody>
        @foreach ($constructions as $key=>$construction)
            <tr>
                <td>{{ ++$key }}</td>
                <td>{{ $construction->signed_date->format('Y') }}</td>
                <td>{{ $construction->health_facility_name }}</td>
                <td>{{ $construction->district->district_name }}</td>
                <td>{{ $construction->district->province->province_name .', '. $construction->getDistrictName() .', '. $construction->getLocalName() }}</td>
                <td>{{ $construction->type_of_work }}</td>
                <td>{{ $construction->effective_date_from?->toFormattedDateString() }}</td>
                <td>{{ $construction->effective_date_to?->toFormattedDateString() }}</td>
                <td>{{ $construction->work_completion_date?->toFormattedDateString() }}</td>
                <td>{{ $construction->latestAmendment?->effective_date?->toFormattedDateString() }}</td>
                <td>{{ $construction->latestAmendment?->extension_to_date?->toFormattedDateString() }}</td>
                <td>{{ $construction->total_contribution_amount }}</td>
                <td>{{ $construction->ohw_contribution }}</td>
                <td>{{ $construction->getOtherPartiesContribution() }}</td>
                <td>{{ $construction->getTotalFundTransferred() }}</td>
                <td>{{ $construction->getTotalExpenseSettled() }}</td>
                <td>{{ $construction->getTotalFundTransferred() - $construction->getTotalExpenseSettled() }}</td>
                <td>{{ $construction->latestConstructionProgress?->progress_percentage }}</td>
                <td></td>
                <td>{{ $construction->donor }}</td>
                <td>{{ $construction->metal_plaque_text }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
