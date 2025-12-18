@extends('layouts.container-report')

@section('title', 'Staff Exit Print')
@section('page_css')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Mukta:wght@200;500;600;800&display=swap" rel="stylesheet">
    <style>
        .print-info {
            font-family: 'Mukta', sans-serif;
        }
    </style>
@endsection
@section('page_js')

@endsection

@section('page-content')

    <!-- CSS only -->

    <section class="print-info bg-white p-3" id="print-info">

        <div class="print-title fw-bold mb-3 translate-middle text-center ">
            <div class="fs-5"> HERD International</div>
            <div class="fs-8">   Staff Exit Clearence Form</div>
        </div>

        <div class="print-header">
            <div class="row">
                <div class="col-lg-8">

                    <div class="print-header-info my-3">
                        <ul class="list-unstyled">
                            <li><span class="fw-bold me-2"> Name of Leaving :</span><span>Ram Krishna Dhakal</span></li>
                            <li><span class="fw-bold me-2"> Designation :</span><span>Ram Krishna Dhakal</span></li>
                            <li><span class="fw-bold me-2">Duty Station :</span><span>Kathmandu</span>
                            </li>
                            <li><span class="fw-bold me-2">Joined Date :</span><span>Jan 14, 2022</span></li>
                            <li><span class="fw-bold me-2">Resigned Date :</span><span>Jan 14, 2022</span></li>
                            <li><span class="fw-bold me-2">Date :</span><span>Jan 14, 2022</span></li>


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
            <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Dolore aut voluptatum facilis distinctio eveniet
                cupiditate fugiat temporibus totam, quo dolorum, dignissimos sunt impedit, minus mollitia aperiam unde
                doloribus. Quae, magnam.</p>


        </div>
        <div class="print-body">
            <div class="heading -js">
                A. Clearance
            </div>
            <table class="table mb-3 table-bordered">
                <thead>
                    <tr>
                        <th rowspan="2">Depatment</th>
                        <th colspan="3">Created By: <strong class="text-dark">Ram Krishna Gimire</strong> </th>
                        <th rowspan="2">Remarks</th>


                    </tr>
                    <tr>
                        <th>Name</th>
                        <th>Sign</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th colspan="5">Logistic</th>
                    </tr>
                    <tr class="child-content-table">
                        <td>- Computer Accessories</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <th colspan="5">HR/Admin</th>
                    </tr>
                    <tr class="child-content-table">
                        <td>- Computer Accessories</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                </tbody>
                <tfoot>

                </tfoot>
            </table>

            <small>Note: Please deliver by <span class="border-bottom text-capitalize">Date</span> at OHW Office Bagdol
                - 4 Lalitpur </small>
            <div class="mt-5 ">
                <ul class="list-unstyled w-50">
                    <li class="d-flex align-content-between flex-grow-1 justify-content-between">
                        <span><strong class="me-2">Name :</strong> Ram Krishna Shrestha </span>

                    </li>
                </ul>
                <p class=" mb-1 fw-bold  mb-3">Submited by (Staff Member)</p>
                <p class="mt-1 mb-1 "><strong>Supervisor Clearance :</strong> I hereby clearify that <strong>Mr. Developer
                        name
                    </strong> has properly handover the all documents as per attached handover note. </p>
                <ul class="list-unstyled w-50">

                    <li class="d-flex align-content-between flex-grow-1 justify-content-between">
                        <span><strong class="me-2">Signature :</strong> Ram Krishna Shrestha </span>

                    </li>
                    <li class="d-flex align-content-between flex-grow-1 justify-content-between">
                        <span><strong class="me-2">Date:</strong> Ram Krishna Shrestha </span>

                    </li>
                    <li class="d-flex align-content-between flex-grow-1 justify-content-between">
                        <span><strong class="me-2">Stamp:</strong> Ram Krishna Shrestha </span>

                    </li>
                </ul>

            </div>
        </div>
        <div class="print-footer">
        </div>
    </section>


@endsection
