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
                                <th scope="col">{{ __('label.office') }}</th>
                                <th scope="col">{{ __('label.activity-code') }}</th>
                                <th scope="col">{{ __('label.donor') }}</th>
                                <th scope="col">{{ __('label.amount') }}</th>
                                <th scope="col">{{ __('label.vat-amount') }}</th>
                                <th scope="col">{{ __('label.total-amount') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($purchaseOrder->getSummary() as $index => $item)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $item->getOffice() }}</td>
                                    <td>{{ $item->activityCode?->title }}</td>
                                    <td>{{ $item->getDonorCode() }}</td>
                                    <td>{{ $item->total_price }}</td>
                                    <td>{{ $item->vat_amount }}</td>
                                    <td>{{ $item->total_amount }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            @if ($purchaseOrder->purchaseOrderItems()->count())
                                <tr>
                                    <td colspan="4">{!! __('label.total-amount') !!}</td>
                                    <td>{{ $purchaseOrder->sub_total }}</td>
                                    <td>{{ $purchaseOrder->vat_amount }}</td>
                                    <td>{{ $purchaseOrder->total_amount }}</td>
                                </tr>
                            @endif
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
