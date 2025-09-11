@extends('layouts.container-report')

@section('title', 'Payment Sheet')
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

    <!-- CSS only -->

    <div class="print-title fw-bold mb-3 translate-middle text-center ">
        <div class="fs-5"> One Heart Worldwide</div>
        <div class="fs-8">{{$paymentSheet->requester->office->getOfficeName()}}</div>
        <div class="fs-8"> Payment Sheet</div>
    </div>

    <div class="print-header">
        <div class="row">
            <div class="col-lg-8">
                <div class="print-code fs-6 fw-bold">
                    OWH Payment Sheet No. {{ $paymentSheet->getpaymentSheetNumber() }}
                </div>

                <div class="print-header-info my-3">
                    <ul class="list-unstyled m-0 p-0 fs-7">
                        <li><span class="fw-bold me-2"> Shipper's Name & Address.
                                :</span><span>{{ $paymentSheet->shipper_name . ' ' . $paymentSheet->shipper_address }}</span>
                        </li>
                        <li><span class="fw-bold me-2"> Consignee's Name & Address.
                                :</span><span>{{ $paymentSheet->consignee_name . ' ' . $paymentSheet->consignee_address }}</span>
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
                    <ul class="list-unstyled m-0 p-0 fs-7 align-self-end">
                        <li><span class="fw-bold me-2">Date :</span><span>{{ $paymentSheet->getSheetDate() }}</span>
                        </li>


                    </ul>
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
                @foreach($paymentSheet->paymentSheetDetails as $detail)
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
                    <span><strong>Name & Signature of Shipper:</strong> </span> <span class="d-flex"><strong
                            class="me-2">Date of Signing:</strong> <span style="width: 206px;"
                            class="d-flex">&nbsp;</span> </span>
                </li>
                <li class="d-flex align-content-between flex-grow-1 justify-content-between">
                    <span><strong>Signature of transporter as confirmation of goods received in good condition before
                            Payment:</strong> </span> <span class="d-flex"><strong class="me-2">Name:</strong>
                        <span style="width: 206px;">&nbsp;</span> </span>
                </li>
                <li class="d-flex align-content-between flex-grow-1 justify-content-between">
                    <span>&nbsp;</span>
                    <span class="d-flex"><strong class="me-2">Date:</strong> <span style="width: 206px;"
                            class="d-flex">&nbsp;</span> </span>
                </li>
                <li class="d-flex align-content-between flex-grow-1 justify-content-between">
                    <span>&nbsp;</span>
                    <span class="d-flex"><strong class="me-2">Signature:</strong> <span
                            style="width: 206px;">&nbsp;</span> </span>
                </li>
                <li class="d-flex align-content-between flex-grow-1 justify-content-between pt-5">
                    <span><strong>Signature of transporter as confirmation of goods received in good condition before
                            Payment:</strong> </span> <span class="d-flex"><strong class="me-2">Name:</strong>
                        <span style="width: 206px;">&nbsp;</span> </span>
                </li>
                <li class="d-flex align-content-between flex-grow-1 justify-content-between">
                    <span>&nbsp;</span>
                    <span class="d-flex"><strong class="me-2">Date:</strong> <span style="width: 206px;"
                            class="d-flex">&nbsp;</span> </span>
                </li>
                <li class="d-flex align-content-between flex-grow-1 justify-content-between">
                    <span>&nbsp;</span>
                    <span class="d-flex"><strong class="me-2">Signature:</strong> <span
                            style="width: 206px;">&nbsp;</span> </span>
                </li>
            </ul>
            <div class="mb-3">
                <label for="">Remarks</label>
                <div class="ms-height">
                    <p>{{ $paymentSheet->remarks }}</p>
                </div>

            </div>
            <div class="mb-3">
                <label for="">Special Instruction to Transporter</label>
                <div class="ms-height">
                    <p>{{ $paymentSheet->instruction }}</p>
                </div>

            </div>
            <small>Note: Penalty will be charged in without VAT amount.</small>


        </div>
    </div>
    <div class="print-footer">
    </div>

@endsection
