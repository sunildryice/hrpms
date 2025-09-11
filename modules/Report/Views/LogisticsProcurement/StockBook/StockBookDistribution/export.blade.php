<table>
    <thead>
        <tr>
            <th colspan="10" style="text-align: center;">Stock Book Report (Distribution)</th>
        </tr>
        <tr>
            <th colspan="17" style="text-align: center;">IN</th>
            <th colspan="15" style="text-align: center;">OUT</th>
            <th rowspan="2">Balance Quantity</th>
            <th rowspan="2">Balance Amount</th>
        </tr>
        <tr>
            <th>{{ __('label.sn') }}</th>
            <th>Items</th>
            <th>Description</th>
            <th>Inventory Type</th>
            <th>Item Category</th>
            <td>Batch No.</td>
            <th>Unit</th>
            <th>Purchased Quantity</th>
            <th>Rate</th>
            <th>Amount</th>
            <th>VAT Amount</th>
            <th>Total Amount with VAT</th>
            <th>GRN No.</th>
            <th>Purchased Date</th>
            <th>Execution Type</th>
            <th>Account Code</th>
            <th>Activity Code</th>
            <th>Donor Code</th>
            <th>Goods Source/Vendor</th>


            <th>Used Quantity</th>
            <th>Rate</th>
            <th>Amount</th>
            <th>VAT</th>
            <th>Total Amount</th>
            <th>Office Code</th>
            <th>Location</th>
            <th>Health Facility</th>
            <th>Project</th>
            <th>Account Code</th>
            <th>Activity Code</th>
            <th>Donor Code</th>
            <th>GRN No.</th>
            <th>Stock Requisition No.</th>
            <th>Issued Date</th>
        </tr>
    </thead>

    <tbody>
        @foreach ($data as $key => $inventoryItem)
            @php
                $items = $inventoryItem->goodRequestAndDistributionItems();
            @endphp
            <tr>
                <td>{{ ++$key }}</td>
                <td>{{ $inventoryItem->item->title }}</td>
                <td>{{ $inventoryItem->specification }}</td>
                <td>{{ $inventoryItem->item->category->inventoryType->title }}</td>
                <td>{{ $inventoryItem->item->category->title }}</td>
                <td>{{ $inventoryItem->batch_number }}</td>
                <td>{{ $inventoryItem->unit->title }}</td>
                <td>{{ $inventoryItem->quantity }}</td>
                <td>{{ $inventoryItem->getUnitPrice() }}</td>
                <td>{{ $inventoryItem->getTotalPrice() }}</td>
                <td>{{ $inventoryItem->getVatAmount() }}</td>
                <td>{{ $inventoryItem->total_price + $inventoryItem->vat_amount }}</td>
                <td>{{ $inventoryItem->grn->getGrnNumber() }}</td>
                <td>{{ $inventoryItem->getPurchaseDate() }}</td>
                <td>{{ $inventoryItem->getExecutionType() }}</td>
                <td>{{ $inventoryItem->accountCode->getAccountCode() }}</td>
                <td>{{ $inventoryItem->activityCode->getActivityCode() }}</td>
                <td>{{ $inventoryItem->donorCode->getDonorCode() }}</td>
                <td>{{ $inventoryItem->getSupplierName() }}</td>

                @php
                    $usedQuantity = 0;
                    $amount = 0;
                    $vat = 0;
                    $totalAmount = 0;
                    $officeCode = '';
                    $location = '';
                    $healthFacility = '';
                    $project = '';
                    $accountCode = '';
                    $activityCode = '';
                    $donorCode = '';
                    $grnNumber = '';
                    $stockRequisitionNumber = '';
                    $issuedDate = '';
                    $balanceQuantity = 0;
                    $balanceAmount = 0;
                @endphp

                @if ($items && $items->isNotEmpty())
                    @foreach ($items as $key => $item)
                        @php
                            $usedQuantity += $item->get('used_quantity');
                            $amount += $item->get('amount');
                            $vat += $item->get('vat');
                            $totalAmount += $item->get('total_amount');
                            $officeCode .= $item->get('office_code') . ', ';
                            $location .= $item->get('location') . ', ';
                            $healthFacility .= $item->get('health_facility') . ', ';
                            $project .= $item->get('project') . ', ';
                            $accountCode .= $item->get('account_code') . ', ';
                            $activityCode .= $item->get('activity_code') . ', ';
                            $donorCode = $item->get('donor_code') . ', ';
                            $grnNumber = $item->get('grn_number') . ', ';
                            $stockRequisitionNumber .= $item->get('stock_requisition_number') . ', ';
                            $issuedDate .= $item->get('issued_date') . ', ';
                            $balanceQuantity += $inventoryItem->quantity - $inventoryItem->assigned_quantity;
                            $balanceAmount +=
                                $inventoryItem->unit_price *
                                ($inventoryItem->quantity - $inventoryItem->assigned_quantity);
                        @endphp
                    @endforeach
                    <td>{{ $usedQuantity }}</td>
                    <td>{{ $item->get('rate') }}</td>
                    <td>{{ $amount }}</td>
                    <td>{{ $vat }}</td>
                    <td>{{ $totalAmount }}</td>
                    <td>{{ $officeCode }}</td>
                    <td>{{ $location }}</td>
                    <td>{{ $healthFacility }}</td>
                    <td>{{ $project }}</td>
                    <td>{{ $accountCode }}</td>
                    <td>{{ $activityCode }}</td>
                    <td>{{ $donorCode }}</td>
                    <td>{{ $grnNumber }}</td>
                    <td>{{ $inventoryItem->quantity - $usedQuantity }}</td>
                    <td>{{ $inventoryItem->unit_price * ($inventoryItem->quantity - $usedQuantity) }}</td>
                    <td>{{ $stockRequisitionNumber }}</td>
                    <td>{{ $issuedDate }}</td>
                @endif
            </tr>
        @endforeach
    </tbody>
</table>
