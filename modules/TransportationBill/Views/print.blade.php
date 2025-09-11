@extends('layouts.container-report')

@section('title', 'Transportation Bill')
@section('page_css')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Mukta:wght@200;500;600;800&display=swap" rel="stylesheet">
    <style>
         
    </style>
@endsection
@section('page_js')

@endsection

@section('page-content')

    <section class="print-info bg-white p-3" id="print-info">

        <div class="print-title fw-bold mb-3 translate-middle text-center ">
            <div class="fs-5">One Heart Worldwide</div>
            <div class="fs-8">{{ $transportationBill->getOfficeName() }}</div>
            <div class="fs-8">OHW Good Transportation Bill</div>
        </div>

        <div class="print-header">
            <div class="row">
                <div class="col-lg-8">

                    <div class="print-code fs-7 fw-bold">
                        OWH Transportation Bill No. {{ $transportationBill->getTransportationBillNumber() }}
                    </div>


                    <div class="print-header-info my-3">
                        <ul class="list-unstyled m-0 p-0 fs-7">
                            <li><span class="fw-bold me-2"> Shipper's Name & Address.
                                    :</span><span>{{ $transportationBill->shipper_name . ' ' . $transportationBill->shipper_address }}</span>
                            </li>
                            <li><span class="fw-bold me-2"> Consignee's Name & Address.
                                    :</span><span>{{ $transportationBill->consignee_name . ' ' . $transportationBill->consignee_address }}</span>
                            </li>
                            <li><span class="fw-bold me-2">Date
                                :</span><span>{{ $transportationBill->getBillDate() }}</span>
                            </li>

                        </ul>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="d-flex flex-column justify-content-end">
                        <div class="d-flex flex-column justify-content-end brand-logo mb-4 flex-grow-1">
                            <div class="d-flex flex-column justify-content-end float-right">
                                <img src="{{ asset('img/logonp.png') }}" alt="" class="align-self-end pe-5">
                            </div>

                        </div>
                    </div>

                </div>
            </div>


        </div>
        <div class="print-body">
            <table class="table mb-3">
                <thead>
                <tr>
                    <th style="width: 30px;">SN.</th>
                    <th style="width: 50%;">Description Item</th>
                    <th>Qty.</th>
                    <th>Remarks</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($transportationBill->transportationBillDetails as $detail)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $detail->item_description }}</td>
                        <td>{{ $detail->quantity }}</td>
                        <td>{{ $detail->remarks }}</td>
                    </tr>
                @endforeach
                </tbody>

            </table>

            <div class="mt-2 ">
                <ul class="list-unstyled w-100">
                    <li class="d-flex align-content-between flex-grow-1 justify-content-between">
                        <span><strong>Signature of transporter as confirmation of goods received in good condition before
                                transportation:</strong> </span> <span class="d-flex"><strong
                                class="me-2">Name:</strong>
                            <span style="width: 206px;">&nbsp;</span> </span>
                    </li>
                    <li class="d-flex align-content-between flex-grow-1 justify-content-between">
                        <span>&nbsp;</span>
                        <span class="d-flex">
                            <strong class="me-2">Date:</strong>
                            <span style="width: 206px;" class="d-flex">&nbsp;</span>
                        </span>
                    </li>
                    <li class="d-flex align-content-between flex-grow-1 justify-content-between">
                        <span>&nbsp;</span>
                        <span class="d-flex"><strong class="me-2">Signature:</strong> <span
                                style="width: 206px;">&nbsp;</span> </span>
                    </li>
                    <li class="d-flex align-content-between flex-grow-1 justify-content-between pt-5">
                        <span><strong>Consignee Name & Signatures as receipt of all listed items in good condition :</strong> </span>
                        <span class="d-flex"><strong class="me-2">Name:</strong>
                            <span style="width: 206px;">{!! $transportationBill->receivedLog ? $transportationBill->receivedLog->getCreatedBy() : '' !!}</span> </span>
                    </li>

                    <li class="d-flex align-content-between flex-grow-1 justify-content-between">
                        <span>&nbsp;</span>
                        <span class="d-flex">
                            <strong class="me-2">Datetime:</strong>
                            <span style="width: 206px;" class="d-flex">{!! $transportationBill->receivedLog ? $transportationBill->receivedLog->getCreatedAt() : '' !!}</span>
                        </span>
                    </li>
{{--                    <li class="d-flex align-content-between flex-grow-1 justify-content-between">--}}
{{--                        <span>&nbsp;</span>--}}
{{--                        <span class="d-flex">--}}
{{--                            <strong class="me-2">Signature:</strong>--}}
{{--                            <span style="width: 206px;">&nbsp;</span>--}}
{{--                        </span>--}}
{{--                    </li>--}}
                </ul>
                <p><strong>Remarks:</strong> {{ $transportationBill->remarks }} </p>
                <p><strong>Special Instruction to Transporter:</strong> {{ $transportationBill->instruction }} </p>
                <p><strong>Note:</strong>
                    The recipient must check thoroughly the quantity and condition of goods and any discrepancies found
                    must be noted in the remarks and get verified by transporter.
                    This should also be informed immediately to Logistic Officer show
                </p>
            </div>
        </div>
        <div class="print-footer">
        </div>
    </section>

@endsection
