@php
    if ($grn->grnable_type == config('constant.PURCHASE_ORDER')) {
        $items = $grn
            ->grnItems()
            ->with([
                'grnitemable' => function ($q) {
                    $q->select('id', 'purchase_request_item_id')->with([
                        'purchaseRequestItem' => function ($q) {
                            $q->select(['id', 'office_id']);
                        },
                    ]);
                },
            ])
            ->get()
            ->map(function ($item) {
                $item->office_id = $item->grnitemable->purchaseRequestItem->office_id;
                return $item;
            });
    } elseif ($grn->grnable_type == config('constant.PURCHASE_REQUEST')) {
        $items = $grn
            ->grnItems()
            ->with([
                'grnitemable' => function ($q) {
                    $q->select('id', 'office_id');
                },
            ])
            ->get()
            ->map(function ($item) {
                $item->office_id = $item->grnitemable->office_id;
                return $item;
            });
    } else {
        $items = $grn->grnItems;
    }
    $items = $items
        ->groupBy(['activity_code_id', 'donor_code_id', 'office_id'])
        ->flatten(2)
        ->map(function ($grnitems) {
            $summary = (object) [];
            foreach ($grnitems as $index => $item) {
                if ($index == 0) {
                    $summary = $item;
                    continue;
                }
                $summary->total_price += $item->total_price;
                $summary->vat_amount += $item->vat_amount;
                $summary->discount_amount += $item->discount_amount;
                $summary->tds_amount += $item->tds_amount;
                $summary->total_amount += $item->total_amount;
            }
            return $summary;
        });

@endphp
<div class="card">
    <div class="card-header fw-bold">
        Summary
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-lg-12">
                <div class="table-responsive">
                    <table class="table" id="grnItemTable">
                        <thead class="thead-light">
                            <tr>
                                <th>{{ __('label.sn') }}</th>
                                <th scope="col">{{ __('label.activity-code') }}</th>
                                <th scope="col">{{ __('label.office') }}</th>
                                <th scope="col">{{ __('label.donor') }}</th>
                                <th scope="col">{{ __('label.amount') }}</th>
                                <th scope="col">{{ __('label.discount') }}</th>
                                <th scope="col">{{ __('label.vat-amount') }}</th>
                                <th scope="col">{{ __('label.total-amount') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($items as $index => $item)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $item->activityCode?->title }}</td>
                                    <td>{{ $item->getOffice() }}</td>
                                    <td>{{ $item->getDonorCode() }}</td>
                                    <td>{{ $item->total_price }}</td>
                                    <td>{{ $item->discount_amount }}</td>
                                    <td>{{ $item->vat_amount }}</td>
                                    <td>{{ $item->total_amount }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            @if ($grn->grnItems->count())
                                <tr>
                                    <td colspan="4">{!! __('label.total-amount') !!}</td>
                                    <td>{{ $grn->sub_total }}</td>
                                    <td>{{ $grn->discount_amount }}</td>
                                    <td>{{ $grn->vat_amount }}</td>
                                    <td>{{ $grn->total_amount }}</td>
                                </tr>
                            @endif
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
