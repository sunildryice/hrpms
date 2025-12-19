@extends('layouts.container-report')

@section('title', 'Good Receive Note')
@section('page_css')

@endsection
@section('page_js')

@endsection

@section('page-content')
    <script type="text/javascript">
        window.print();
    </script>

    <section class="p-3 bg-white print-info" id="print-info">
        <div class="mb-3 text-center print-title fw-bold translate-middle">
            <div class="fs-5"> HERD International</div>
            <div class="fs-8">{{ $grn->getOfficeName() }}</div>
            <div class="fs-8"> Good Receive Note</div>
        </div>
        <div class="print-header">
            <div class="row">
                <div class="col-lg-8">
                    <div class="print-code fs-7 fw-bold">
                        GRN No: {!! $grn->getGrnNumber() !!}
                    </div>
                    <div class="my-3 print-header-info">
                        <ul class="p-0 m-0 list-unstyled fs-7">
                            <li>
                                <span class="fw-bold me-2">
                                    PO/PR No: </span>{{ $grn->grnable?->getGrnableNumber() }}</span>
                                </span>
                            </li>
                            <li><span class="fw-bold me-2">Vendor's / Suppliers' Name
                                    :</span><span>{!! $grn->getSupplierName() !!}</span>
                            </li>
                            <li><span class="fw-bold me-2">Received Date :</span><span>{!! $grn->getReceivedDate() !!}</span>
                            </li>
                            {{-- <li><span class="fw-bold me-2">Activity Code :</span><span>CR 1.1</span></li> --}}

                        </ul>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="d-flex flex-column justify-content-end">
                        <div class="mb-4 d-flex flex-column justify-content-end brand-logo flex-grow-1">
                            <div class="float-right d-flex flex-column justify-content-end">
                                <img src="{{ asset('img/logonp.png') }}" alt=""
                                    class="align-self-end pe-5 logo-img">
                            </div>

                        </div>
                        <ul class="p-0 m-0 list-unstyled fs-7 align-self-end">
                            {{-- <li><span class="fw-bold me-2">Account Code :</span><span>6502</span></li> --}}
                            <li>
                                <span class="fw-bold me-2">Activity Codes :</span>
                                <span>{{ $grn->getActivityCodes() }}</span>
                            </li>
                            <li>
                                <span class="fw-bold me-2">Donor / Grant :</span>
                                <span>{{ $grn->getDonorCodes() }}</span>
                            </li>
                            <li>
                                <span class="fw-bold me-2">Account Codes:</span>
                                <span>{{ $grn->getAccountCodes() }}</span>
                            </li>

                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="print-body">
            <div class="my-3 fw-bold">The following items(s) have been received in good
                conditions</div>
            <table class="table mb-3 fs-i-s">
                <thead>
                    @php
                        $assetFlag = $grn->hasAssets();
                    @endphp
                    <tr>
                        <th>SN.</th>
                        <th>Particular</th>
                        <th>Specification</th>
                        <th>Unit</th>
                        @if ($assetFlag)
                            <th>Assets</th>
                        @endif
                        <th>Qty.</th>
                        <th>Rate</th>
                        <th>Amount</th>
                        <th>Discount</th>
                        <th>Vat</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($grn->grnItems as $grnItem)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $grnItem->getItemName() }}</td>
                            <td>{{ $grnItem->grnitemable?->specification ?: $grnItem->specification }}</td>
                            <td>{{ $grnItem->getUnitName() }}</td>
                            @if ($assetFlag)
                                <td>{!! $grnItem->inventoryItem->getAssetCodes()->implode(',<br>') !!}</td>
                            @endif
                            <td>{{ $grnItem->quantity }}</td>
                            <td>{{ $grnItem->unit_price }}</td>
                            <td>{{ $grnItem->total_price }}</td>
                            <td>{{ $grnItem->discount_amount }}</td>
                            <td>{{ $grnItem->vat_amount }}</td>
                            <td>{{ $grnItem->total_amount }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    @php
                        $subTotal = $grn->sub_total ?: $grn->grnItems->sum('total_price');
                        $discountAmount = $grn->discount_amount ?: $grn->grnItems->sum('discount_amount');
                        $vatAmount = $grn->vat_amount ?: $grn->grnItems->sum('vat_amount');
                        $totalAmount = $grn->total_amount ?: $subTotal + $vatAmount - $discountAmount;
                        $tdsAmount = $grn->tds_amount ?: $grn->grnItems->sum('tds_amount');
                        $grnAmount = $grn->grn_amount ?: $totalAmount - $tdsAmount;
                    @endphp
                    <tr>
                        <th colspan="@if ($assetFlag) 7 @else 6 @endif">{!! __('label.total-amount') !!} (Total of
                            Bill)</th>
                        <th>{{ $subTotal }}</th>
                        <th>{{ $discountAmount }}</th>
                        <th>{{ $vatAmount }}</th>
                        <th>{{ $totalAmount }}</th>
                    </tr>
                    <tr>
                        {{-- <th colspan="8">{!! __('label.tds-amount-less') !!}</th> --}}
                        <th colspan="@if ($assetFlag) 10 @else 9 @endif">Less: 1.5% TDS on before VAT
                            amount</th>
                        <th>{{ $tdsAmount }}</th>
                    </tr>
                    {{-- <tr> --}}
                    {{-- <th colspan="8">Other Cost</th> --}}
                    {{-- <th>{{ $grn->other_charge_amount }}</th> --}}
                    {{-- </tr> --}}
                    <tr>
                        <th colspan="@if ($assetFlag) 10 @else 9 @endif">Net Payable Amount (NPR)</th>
                        <th>{{ $grnAmount }}</th>
                    </tr>
                </tfoot>
            </table>
            <p>In words : <span
                    class="border-bottom text-capitalize">{{ \App\Helper::convertNumberToWords($grnAmount) }}</span>
                Only.</p>

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

                $summaries = $items
                    ->groupBy(['activity_code_id', 'donor_code_id', 'office_id'])
                    ->flatten(2)
                    ->map(function ($grnItem) {
                        $summary = (object) [];
                        foreach ($grnItem as $index => $item) {
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
            <table class="table border">
                <thead>
                    <tr>
                        <th colspan="7">Summary</th>
                    </tr>
                    <tr>
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
                    @foreach ($summaries as $summary)
                        <tr>
                            <td>{{ $summary->activityCode?->title }}</td>
                            <td>{{ $summary->getOffice() }}</td>
                            <td>{{ $summary->getDonorCode() }}</td>
                            <td>{{ $summary->total_price }}</td>
                            <td>{{ $summary->discount_amount }}</td>
                            <td>{{ $summary->vat_amount }}</td>
                            <td>{{ $summary->total_amount }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    @if ($grn->grnItems->count())
                        <tr>
                            <td colspan="3">{!! __('label.total-amount') !!}</td>
                            <td>{{ $grn->sub_total }}</td>
                            <td>{{ $grn->discount_amount }}</td>
                            <td>{{ $grn->vat_amount }}</td>
                            <td>{{ $grn->total_amount }}</td>
                        </tr>
                    @endif
                </tfoot>
            </table>

        </div>
        <div class="print-footer">
            <div class="my-3 row justify-content-between">
                <div class="col-lg-4">
                    <ul class="list-unstyled w-100">
                        <li><strong>Received By:</strong></li>
                        <li><strong class="me-2">Name:</strong>{{ $grn->getApproverName() }}</li>
                        <li><strong class="me-2">Title:</strong> {{ $grn->approvedLog?->getDesignation() }}
                        </li>
                        {{-- <li><strong class="me-2">Date:</strong> {{ $grn->approvedLog?->created_at }}</li> --}}
                    </ul>
                </div>
            </div>
        </div>
    </section>

@stop
