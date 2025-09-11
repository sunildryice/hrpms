<table class="table table-responsive table-bordered" id="assetDispositionTable">
    <thead>
        <tr>
            <th colspan="18" style="text-align: center">Asset Disposition Report</th>
        </tr>
        <tr>
            <th>{{ __('label.sn') }}</th>
            <th>Items</th>
            <th>Coding (Item Code)</th>
            <th>Assets Code</th>
            <th>Description</th>
            <th>Disposition Type</th>
            <th>Disposed Date</th>
            <th>Disposed By</th>
            <th>Office</th>
            <th>Serial Number</th>
            <th>Price</th>
            <th>Purchasing Date</th>
            <th>Execution</th>
            <th>Voucher Number</th>
            <th>Staff Name</th>
            <th>Designation</th>
            <th>Location</th>
            <th>District Code</th>
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
                <td>{{ $asset->getItemName() }}</td>
                <td>{{ $asset->getItemCode() }}</td>
                <td>{{ $asset->getAssetNumber() }}</td>
                <td>{{ $asset->getSpecification() }}</td>
                <td>{{ $asset->getDispositionType() }}</td>
                <td>{{ $asset->getDispositionDate() }}</td>
                <td>{{ $asset->getDisposedBy() }}</td>
                <td>{{ $asset->getDispositionOffice() }}</td>
                <td>{{ $asset->getSerialNumber() }}</td>
                <td>{{ $asset->getPrice() }}</td>
                <td>{{ $asset->getPurchaseDate() }}</td>
                <td></td>
                <td></td>
                <td>{{ $asset->getAssignedUserName() }}</td>
                <td>{{ $asset->getAssignedUserDesignation() }}</td>
                <td>{{ $asset->getAssignedUserOfficeLocation() }}</td>
                <td>{{ $asset->getAssignedUserOfficeDistrict() }}</td>
                <td>{{ $asset->getAssetCondition() }}</td>
                <td></td>
                <td>{{ $asset->inventoryItem->getSupplierName() }}</td>
                <td></td>
            </tr>
        @endforeach
    </tbody>
</table>
