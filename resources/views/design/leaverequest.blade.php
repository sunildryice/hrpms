@extends('layouts.container-report')

@section('title', 'Leave Request')

@section('page_css')

    <style>
        .table thead th {
            font-size: 0.8rem;
            font-weight: 700;
        }
    </style>
@endsection

@section('page-content')

    <section class="print-info bg-white p-3" id="print-info">
        <div class="print-title fw-bold mb-3 translate-middle text-center ">
            <div class="fs-5"> One Heart Worldwide</div>
            <div class="fs-8">Office Name</div>
            <div class="fs-8"> Leave Request</div>
        </div>
        <div class="print-header">
            <div class="row">
                <div class="col-lg-8">
                    <div class="print-code fs-7 fw-bold mb-2">
                        Leave Request No:12345
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
        <div class="print-body border-top">
            <div class="card-body">
                <div class="row">

                    <div class="col-9 offset-1">
                        <h4 class="m-0 lh1 mt-0 mb-2 fs-6 text-uppercase fw-bold">Leave Request Details:
                        </h4>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-lg-3">
                        <div class="d-flex align-items-start  h-100">
                            <label for="validationfullname" class="m-0 text-end flex-grow-1 fw-bold">Date </label>
                        </div>
                    </div>
                    <div class="col-lg-9">
                        2022-11-09

                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-lg-3">
                        <div class="d-flex align-items-start  h-100">
                            <label for="" class="m-0 text-end flex-grow-1 fw-bold">Leave Type </label>
                        </div>
                    </div>
                    <div class="col-lg-9">
                        Sick Leave
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-lg-3">
                        <div class="d-flex align-items-start  h-100">
                            <label for="" class="m-0 text-end flex-grow-1 fw-bold">Leave </label>
                        </div>
                    </div>
                    <div class="col-lg-9">
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="d-inline-flex gap-2">
                                    <span> <strong> From</strong></span>
                                    <span>2022-11-09</span>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="d-inline-flex gap-2">
                                    <span> <strong> To</strong></span>
                                    <span>2022-11-09</span>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-lg-3">
                        <div class="d-flex align-items-start  h-100">
                            <label for="" class="m-0 text-end flex-grow-1 fw-bold">Remarks </label>
                        </div>
                    </div>
                    <div class="col-lg-9">
                        Remarks Details
                    </div>
                </div>
                <div class="row">

                    <div class="col-9 offset-1">
                        <h4 class="m-0 lh1 mt-4 mb-2 fs-6 text-uppercase fw-bold">Leave Request Details:
                        </h4>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-lg-3">
                        <div class="d-flex align-items-start  h-100">
                            <label for="" class="m-0 text-end flex-grow-1 fw-bold">Substitutes </label>
                        </div>
                    </div>
                    <div class="col-lg-9">
                        Bhagwati Shrestha
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-lg-3">
                        <div class="d-flex align-items-start  h-100">
                            <label for="" class="m-0 text-end flex-grow-1 fw-bold">Send To
                            </label>
                        </div>
                    </div>
                    <div class="col-lg-9">
                        Bhagwati Shrestha
                    </div>
                </div>
                <div class="row">

                    <div class="col-9 offset-1">
                        <h4 class="m-0 lh1 mt-4 mb-2 fs-6 text-uppercase fw-bold">Leave Request Details:
                        </h4>
                    </div>
                </div>
                <div class="row">

                    <div class="col-9 offset-1">
                        <table class="table table-bordered  my-4">
                            <thead>
                                <tr>
                                    <th width="10%">DAY</th>
                                    <th>DATE</th>
                                    <th>LEAVE SLOTS</th>
                                </tr>

                            </thead>
                            <tbody>
                                <tr>
                                    <td>1</td>
                                    <td>2022-11-09</td>
                                    <td>2 Hours</td>
                                </tr>
                                <tr>
                                    <td>2</td>
                                    <td>2022-11-09</td>
                                    <td>2 Hours</td>
                                </tr>
                                <tr>
                                    <td>3</td>
                                    <td>2022-11-09</td>
                                    <td>2 Hours</td>
                                </tr>

                            </tbody>
                        </table>
                    </div>
                </div>

                <hr>

                <div class="row justify-content-between my-3">
                    <div class="col-lg-4">
                        <ul class="list-unstyled">
                            <li><strong>Prepared By:</strong></li>
                            <li><strong class="me-1">Name:</strong> xxx </li>
                            <li><strong class="me-1">Title:</strong>
                                xxx </li>
                            <li> <strong class="me-1">Date:</strong>xxx
                            </li>
                        </ul>
                    </div>
                    <div class="col-lg-4">
                        <ul class="list-unstyled">
                            <li><strong>Recommended By:</strong></li>
                            <li><strong class="me-1">Name:</strong> xxx</li>
                            <li><strong class="me-1">Title:</strong>
                                xxx
                            </li>
                            <li><strong class="me-1">Date:</strong>xxx
                            </li>
                        </ul>
                    </div>
                    <div class="col-lg-4">
                        <ul class="list-unstyled">
                            <li><strong>Approved By:</strong></li>
                            <li><strong class="me-1">Name:</strong> xxx</li>
                            <li><strong class="me-1">Title:</strong>
                                xxx</li>
                            <li><strong class="me-1">Date:</strong>xxx
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

        </div>

        </div>
        <div class="print-footer">
        </div>

    </section>


@endsection
