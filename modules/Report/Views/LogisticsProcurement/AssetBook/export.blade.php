<table class="table table-responsive table-bordered" id="assetBookTable">
    <thead>
        <tr>
            <th colspan="18" style="text-align: center">Asset Book Report</th>
        </tr>
        <tr>
            <th>{{ __('label.sn') }}</th>
            <th>Items</th>
            <th>Coding (Item Code)</th>
            <th>Item Category</th>
            <th>GRN No.</th>
            <th>Asset Code</th>
            <th>Old Asset Code</th>
            <th>Description</th>
            <th>Serial Number</th>
            <th>Price</th>
            <th>Price (with VAT)</th>
            <th>Purchasing Date</th>
            <th>Execution</th>
            <th>Voucher Number</th>
            <th>Account Code</th>
            <th>Activity Code</th>
            <th>Donor Code</th>
            <th>Staff Name</th>
            <th>Designation</th>
            <th>Office Code</th>
            <th>Location</th>
            <th>Issued On</th>
            <th>Condition</th>
            <th>Room Number</th>
            <th>Vendors</th>
            <th>Remarks</th>
        </tr>
    </thead>

    <tbody>
        @foreach ($data as $key => $asset)
            <tr>
                <td>{{ ++$key }}</td>
                <td>{{ $asset->inventoryItem->item->title }}</td>
                <td>{{ $asset->getItemCode() }}</td>
                <td>{{ $asset->inventoryItem->getCategoryName() }}</td>
                <td>{{ $asset->inventoryItem?->getGrnNumber() }}</td>
                <td>{{ $asset->getAssetNumber() }}</td>
                <td>{{ $asset->old_asset_code }}</td>
                <td>{{ $asset->getSpecification() }}</td>
                <td>{{ $asset->getSerialNumber() }}</td>
                <td>{{ $asset->inventoryItem->total_price / $asset->inventoryItem->quantity }}
                <td>{{ ($asset->inventoryItem->total_price + $asset->inventoryItem->vat_amount) / $asset->inventoryItem->quantity }}
                </td>
                <td>{{ $asset->getPurchaseDate() }}</td>
                <td>{{ $asset->inventoryItem->getExecutionType() }}</td>
                <td>{{ $asset->inventoryItem->getVoucherNumber() }}</td>
                <td>{{ $asset->inventoryItem->accountCode->getAccountCode() }}</td>
                <td>{{ $asset->inventoryItem->activityCode->getActivityCode() }}</td>
                <td>{{ $asset->inventoryItem->donorCode->description }}</td>
                <td>{{ $asset->getAssignedUserName() }}</td>
                <td>{{ $asset->getAssignedUserDesignation() }}</td>
                <td>{{ $asset->inventoryItem->getOfficeCode() }}</td>
                <td>{{ $asset->getAssignedOffice() }}</td>
                <td>{{ $asset->getIssuedDate() }}</td>
                <td>{{ $asset->getAssetCondition() }}</td>
                <td></td>
                <td>{{ $asset->inventoryItem->getSupplierName() }}</td>
                <td>{{ $asset->remarks }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
