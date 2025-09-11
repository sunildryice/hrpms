<table class="table table-responsive table-bordered" id="stockBookTable">
    <thead>
        <tr>
            <th colspan="34" style="text-align: center">Stock Book Report</th>
        </tr>
        <tr>
            <th colspan="14" style="text-align: center;">IN</th>
            <th colspan="18" style="text-align: center;">OUT</th>
            <th rowspan="2">Balance Quantity</th>
            <th rowspan="2">Balance Amount</th>
        </tr>
        <tr>
            <th>{{ __('label.sn') }}</th>
            <th>Items</th>
            <th>Description</th>
            <th>Inventory Type</th>
            <th>Item Category</th>
            <th>Unit</th>
            <th>Purchased Quantity</th>
            <th>Rate</th>
            <th>Amount</th>
            <th>VAT Amount</th>
            <th>Total Amount with VAT</th>
            <th>GRN No.</th>
            <th>Purchased Date</th>
            <th>Goods Source/Vendor</th>


            <th>{{__('label.sn')}}</th>
            <th>Items</th>
            <th>Description</th>
            <th>Unit</th>
            <th>Used Quantity</th>
            <th>Rate</th>
            <th>Amount</th>
            <th>VAT</th>
            <th>Total Amount</th>
            <th>Issued To</th>
            <th>Location</th>
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
        @foreach ($data as $key=>$inventoryItem)
            <tr>
                <td>{{ ++$key }}</td>
                <td>{{$inventoryItem->item->title}}</td>
                <td></td>
                <td>{{$inventoryItem->item->category->inventoryType->title}}</td>
                <td>{{$inventoryItem->item->category->title}}</td>
                <td>{{$inventoryItem->unit->title}}</td>
                <td>{{$inventoryItem->quantity}}</td>
                <td>{{$inventoryItem->unit_price}}</td>
                <td>{{$inventoryItem->total_price}}</td>
                <td>{{$inventoryItem->vat_amount}}</td>
                <td>{{$inventoryItem->total_price + $inventoryItem->vat_amount}}</td>
                <td>{{$inventoryItem->grn->getGrnNumber()}}</td>
                <td>{{$inventoryItem->grn->getReceivedDate()}}</td>
                <td>{{$inventoryItem->grn->getSupplierName()}}</td>

                @php
                    $items = $inventoryItem->goodRequestAndDistributionItems();
                @endphp

                <td>
                    @if ($items->isNotEmpty())
                        @foreach ($items as $key=>$item)
                            <div>{{++$key}}</div>
                        @endforeach
                    @else
                        <div></div>
                    @endif
                </td>
                <td>
                    @foreach ($items as $item)
                        <div>{{$item->get('item')}}</div>
                    @endforeach
                </td>
                <td>
                    @foreach ($items as $item)
                        <div>{{$item->get('description')}}</div>
                    @endforeach
                </td>
                <td>
                    @foreach ($items as $item)
                        <div>{{$item->get('unit')}}</div>
                    @endforeach
                </td>
                <td>
                    @foreach ($items as $item)
                        <div>{{$item->get('used_quantity')}}</div>
                    @endforeach
                </td>
                <td>
                    @foreach ($items as $item)
                        <div>{{$item->get('rate')}}</div>
                    @endforeach
                </td>
                <td>
                    @foreach ($items as $item)
                        <div>{{$item->get('amount')}}</div>
                    @endforeach
                </td>
                <td>
                    @foreach ($items as $item)
                        <div>{{$item->get('vat')}}</div>
                    @endforeach
                </td>
                <td>
                    @foreach ($items as $item)
                        <div>{{$item->get('total_amount')}}</div>
                    @endforeach
                </td>
                <td>
                    @foreach ($items as $item)
                        <div>{{$item->get('issued_to')}}</div>
                    @endforeach
                </td>
                <td>
                    @foreach ($items as $item)
                        <div>{{$item->get('location')}}</div>
                    @endforeach
                </td>
                <td>
                    @foreach ($items as $item)
                        <div>{{$item->get('project')}}</div>
                    @endforeach
                </td>
                <td>
                    @foreach ($items as $item)
                        <div>{{$item->get('account_code')}}</div>
                    @endforeach
                </td>
                <td>
                    @foreach ($items as $item)
                        <div>{{$item->get('activity_code')}}</div>
                    @endforeach
                </td>
                <td>
                    @foreach ($items as $item)
                        <div>{{$item->get('donor_code')}}</div>
                    @endforeach
                </td>
                <td>
                    @foreach ($items as $item)
                        <div>{{$item->get('grn_number')}}</div>
                    @endforeach
                </td>
                <td>
                    @foreach ($items as $item)
                        <div>{{$item->get('stock_requisition_number')}}</div>
                    @endforeach
                </td>
                <td>
                    @foreach ($items as $item)
                        <div>{{$item->get('issued_date')}}</div>
                    @endforeach
                </td>
                <td>
                    <div>{{$inventoryItem->quantity - $inventoryItem->assigned_quantity}}</div>
                </td>
                <td>
                    <div>{{$inventoryItem->unit_price * ($inventoryItem->quantity - $inventoryItem->assigned_quantity)}}</div>
                </td>

            </tr>
        @endforeach
    </tbody>
</table>