@extends('layouts.container-report')

@section('title', 'Distribution Handover Print')
@section('page_css')
    <style>
        @media print {
            .print-header {
                margin-top: 1rem;
            }

        }

        .letter-body {
            white-space: pre-line;
        }
    </style>
@endsection
@section('page-content')
    <script type="text/javascript">
        window.print();
    </script>

    <section class="p-3 bg-white print-info" id="print-info">
        <div class="print-header">
            <div class="row">
                <div class="col-lg-8">
                    <div class="print-code fw-bold">
                        Ref No: {!! $distributionHandover->getDistributionHandoverNumber() !!}
                    </div>
                    <div class="print-code fw-bold">
                        Date: {!! $distributionHandover->date_of_handover ?? $distributionHandover->getApprovedDate() !!}
                    </div>

                    <div class="mb-3 print-header-info">
                        <div>
                            <span class="fw-bold me-2">To:</span>
                        </div>
                        <ul class="p-0 m-0 list-unstyled fs-7">
                            {{-- <li>{!! $distributionHandover->to_name !!}</li> --}}
                            <li>{!! $distributionHandover->healthFacility->title !!}</li>
                            <li> {!! $distributionHandover->healthFacility->getLocalLevel() !!},
                                {!! $distributionHandover->healthFacility->ward !!}
                            </li>
                            {!! $distributionHandover->healthFacility->getDistrict() !!}
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="d-flex flex-column justify-content-end">
                        <ul class="p-0 m-0 list-unstyled fs-7 align-self-end">
                            {{-- <li><span class="fw-bold me-2">Account Code :</span><span>6502</span></li> --}}
                            {{-- <li><span class="fw-bold me-2">Donor / Grant :</span><span>Kathmandu</span></li> --}}

                        </ul>
                    </div>
                </div>
            </div>
        </div>

        {{-- Subject --}}
        <div class="print-subject">
            <div class="my-3 text-center fw-bold fs-6">Letter of Handover </div>
        </div>
        <div class="letter-body">
            <p>{!! $distributionHandover->letter_body !!}</p>
        </div>
        {{-- <div> --}}
        {{-- <strong class="fs-7">{{$distributionHandover->projectCode->getProjectCode()}} </strong> --}}
        {{-- provides the equipment per attached list. --}}
        {{-- <div class="fw-bold fs-7">Specification Detail: </div> --}}

        {{-- </div> --}}

        <div class="print-body">
            <table class="table my-4">
                <thead>
                    <tr>
                        <th>SN.</th>
                        <th>Particular</th>
                        <th>Unit</th>
                        <th>Qty.</th>
                        <th>Rate</th>
                        <th>Amount</th>
                        <th>Vat</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($distributionHandover->distributionHandoverItems as $handoverItem)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $handoverItem->item->title }}</td>
                            <td>{{ $handoverItem->getUnit() }}</td>
                            <td>{{ $handoverItem->quantity }}</td>
                            <td>{{ $handoverItem->unit_price }}</td>
                            <td>{{ $handoverItem->total_amount }}</td>
                            <td>{{ $handoverItem->vat_amount }}</td>
                            <td>{{ $handoverItem->net_amount }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="5">{!! __('label.total-amount') !!}</th>
                        <th>{{ $distributionHandover->distributionHandoverItems->sum('total_amount') }}</th>
                        <th>{{ $distributionHandover->distributionHandoverItems->sum('vat_amount') }}</th>
                        <th>{{ $distributionHandover->distributionHandoverItems->sum('net_amount') }}</th>
                    </tr>
                </tfoot>
            </table>

            <div class="print-code">
                Thank You
            </div>
        </div>

        <div class="print-footer">

            <div class="mt-5 mb-2 row justify-content-between">
                <div class="col-lg-4">
                    <div class="fot-info w-100">
                        <div class="mb-2 d-flex flex-grow-1">
                            <span class="pb-1 border-bottom d-flex flex-grow-1 w-75">
                            </span>
                        </div>
                        <div class="mb-2 d-flex flex-grow-1">
                            <span class="d-flex flex-grow-1 w-75">
                                {{ $distributionHandover->getApproverName() }}
                            </span>
                        </div>
                        <div class="mb-2 d-flex flex-grow-1">
                            <span class="pb-1 d-flex flex-grow-1 w-75" style="font-weight: bold;">
                                {{ $distributionHandover->approver->employee->getDesignationName() }}
                            </span>
                        </div>

                    </div>
                </div>
                <div class="fw-bold fs-7">Acknowledgment: </div>
                <p>
                    On behalf of {{ $distributionHandover->healthFacility->title }} hereby I confirm that I have received
                    the equipment/material.
                </p>
                <div class="mt-5 col-lg-4">
                    <div class="fot-info w-100">
                        <div class="mb-2 d-flex flex-grow-1">
                            <span class="pb-1 border-bottom d-flex flex-grow-1 w-75">
                            </span>
                        </div>
                        <div class="mb-2 d-flex flex-grow-1">
                            <span class="d-flex flex-grow-1 w-75">
                                Name:
                            </span>
                        </div>
                        <div class="mb-2 d-flex flex-grow-1">
                            <span class="pb-1 d-flex flex-grow-1 w-75" style="font-weight: bold;">
                                Designation:
                            </span>
                        </div>
                        <div class="mb-2 d-flex flex-grow-1">
                            <span class="pb-1 d-flex flex-grow-1 w-75" style="font-weight: bold;">
                                Date:
                            </span>
                        </div>

                    </div>
                </div>
            </div>
            <div class="col-lg-8">
                <div class="print-code fw-bold d-flex">
                    <div>
                        <span class="fw-bold me-2">CC:</span>
                    </div>
                    <ul class="p-0 m-0 list-unstyled fs-7">
                        <li class="letter-body">{{ $distributionHandover->cc_name }}</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

@endsection
