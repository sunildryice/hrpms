<table>
    <thead>
        <tr>
            <th colspan="10" style="text-align: center;">Stock Book Report (Office Use)</th>
        </tr>
        <tr>
            <th colspan="20" style="text-align: center;">IN</th>
            <th colspan="9" style="text-align: center;">OUT</th>
            <th rowspan="2">Balance Quantity</th>
            <th rowspan="2">Balance Amount</th>
        </tr>
        <tr>
            <th>{{ __('label.sn') }}</th>
            <th>Items</th>
            <th>Description</th>
            <th>Inventory Type</th>
            <th>Item Category</th>
            <th>Batch No.</th>
            <th>Unit</th>
            <th>Purchased Quantity</th>
            <th>Rate</th>
            <th>Amount</th>
            <th>Discount</th>
            <th>Amount after Discount</th>
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
            <th>Location</th>
            <th>GRN No.</th>
            <th>Stock Requisition No.</th>
            <th>Handover Date</th>
            <th>Issued Date</th>
        </tr>
    </thead>

    <tbody>
        @php $index = 0; @endphp
        @foreach ($data as $key => $inventoryItem)
            @php
                //if ($key == 203) {
                //    dd($inventoryItem->goodRequestAndDistributionItems(true));
                //}
                $items = $inventoryItem->goodRequestAndDistributionItems(true);
                $usedQuantity = $items->sum('used_quantity');
                //if ($inventoryItem->item->title == 'Pen') {
                //    dd($items->unique('location')->implode('location', ', '), $inventoryItem);
                //}
            @endphp
            <tr>
                <td>{{ ++$index }}</td>
                <td>{{ $inventoryItem->item->title }}</td>
                <td>{{ $inventoryItem->specification }}</td>
                <td>{{ $inventoryItem->item->category->inventoryType->title }}</td>
                <td>{{ $inventoryItem->item->category->title }}</td>
                <td>{{ $inventoryItem->batch_number }}</td>
                <td>{{ $inventoryItem->unit->title }}</td>
                <td>{{ $inventoryItem->quantity }}</td>
                <td>{{ $inventoryItem->getUnitPrice() }}</td>
                <td>{{ $inventoryItem->getTotalPrice() }}</td>
                <td>{{ $inventoryItem->getDiscountAmount() }}</td>
                <td>{{ $inventoryItem->getTotalAmountAfterDiscount() }}</td>
                <td>{{ $inventoryItem->getVatAmount() }}</td>
                <td>{{ $inventoryItem->total_price + $inventoryItem->vat_amount }}</td>
                <td>{{ $inventoryItem->grn->getGrnNumber() }}</td>
                <td>{{ $inventoryItem->getPurchaseDate() }}</td>
                <td>{{ $inventoryItem->getExecutionType() }}</td>
                <td>{{ $inventoryItem->accountCode->getAccountCode() }}</td>
                <td>{{ $inventoryItem->activityCode->getActivityCode() }}</td>
                <td>{{ $inventoryItem->donorCode->getDescription() }}</td>
                <td>{{ $inventoryItem->getSupplierName() }}</td>

                @php
                    //$usedQuantity = 0;
                    $assignedQuantity = 0;
                    $amount = 0;
                    $vat = 0;
                    $totalAmount = 0;
                    $balanceAmount = 0;
                    $officeCode = '';
                    $location = $items->unique('location')->implode('location', ', ');
                    $project = '';
                    $accountCode = '';
                    $activityCode = '';
                    $donorCode = '';
                    $grnNumber = '';
                    $stockRequisitionNumber = '';
                    $handoverDate = '';
                    $issuedDate = '';
                @endphp

                @if ($items && $items->isNotEmpty())
                    @foreach ($items as $key => $item)
                        @php
                            //$usedQuantity += $item->get('used_quantity');
                            $assignedQuantity += $item->get('assigned_quantity');
                            $amount += $item->get('amount');
                            $vat += $item->get('vat');
                            $totalAmount += $item->get('total_amount');
                            $officeCode .= $item->get('office_code') . ', ';
                            $project .= $item->get('project') . ', ';
                            $accountCode .= $item->get('account_code') . ', ';
                            $activityCode .= $item->get('activity_code') . ', ';
                            $donorCode = $item->get('donor_code') . ', ';
                            $grnNumber = $item->get('grn_number') . ', ';
                            $stockRequisitionNumber .= $item->get('stock_requisition_number') . ', ';
                            $handoverDate .= $item->get('handover_date')
                                ? $item->get('handover_date')->format('Y-m-d') . ', '
                                : '';
                            $issuedDate .= $item->get('issued_date') . ', ';
                            $balanceAmount += $inventoryItem->unit_price * ($inventoryItem->quantity - $usedQuantity);
                            $outVat = $inventoryItem->vat_amount
                                ? $item->get('rate') * (config('constant.VAT_PERCENTAGE') / 100) * $usedQuantity
                                : 0;
                        @endphp
                    @endforeach
                    <td>{{ $usedQuantity }}</td>
                    <td>{{ $item->get('rate') }}</td>
                    <td>{{ $usedQuantity * $item->get('rate') }}</td>
                    <td>{{ $outVat }}</td>
                    <td>{{ $usedQuantity * $item->get('rate') + $outVat }}</td>
                    <td>{{ $location }}</td>
                    <td>{{ $grnNumber }}</td>
                    <td>{{ $stockRequisitionNumber }}</td>
                    <td>{{ $inventoryItem->quantity - $usedQuantity }}</td> <!-- balance qty-->
                    <td>{{ $inventoryItem->unit_price * ($inventoryItem->quantity - $usedQuantity) }}</td>
                    <td>{{ $handoverDate }}</td>
                    <td>{{ $issuedDate }}</td>
                @else
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td>{{ $inventoryItem->quantity - $assignedQuantity }}</td>
                    <td>{{ $inventoryItem->unit_price * ($inventoryItem->quantity - $assignedQuantity) }}</td>
                    <td></td>
                @endif
            </tr>
        @endforeach
    </tbody>
</table>
{{-- @dd('t') --}}
