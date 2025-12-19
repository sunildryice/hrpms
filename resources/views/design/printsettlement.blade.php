@extends('layouts.container-report')

@section('title', 'Work Log')
@section('page_css')

@endsection
@section('page_js')

@endsection

@section('page-content')


    <section class="print-info bg-white p-3" id="print-info">


        <div class="print-title fw-bold mb-3 translate-middle text-center ">
            <div class="fs-5"> HERD International</div>
            <div class="fs-8"> Purchase Order</div>
        </div>

        <div class="print-header">
            <div class="row">
                <div class="col-lg-8">
                    <div class="print-code fs-7 fw-bold">
                        Purchase Order No: PO-09
                    </div>

                    <div class="print-header-info my-3">
                        <ul class="list-unstyled m-0 p-0 fs-7">
                            <li><span class="fw-bold me-2"> Staff Name :</span><span>Ram Prasad</span></li>
                            <li><span class="fw-bold me-2"> Title :</span><span>Ram Prasad</span></li>
                            <li><span class="fw-bold me-2">Original Advance Issue Date :</span><span>Kathmandu</span>
                            </li>
                            <li><span class="fw-bold me-2">Ref # :</span><span>6502</span></li>
                            <li><span class="fw-bold me-2">Program Compeletion Date :</span><span>Kathmandu</span></li>
                            <li><span class="fw-bold me-2">Program Settelment Date :</span><span>Kathmandu</span></li>
                            <li><span class="fw-bold me-2">Total days:</span><span>Kathmandu</span></li>

                        </ul>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="d-flex flex-column justify-content-end">
                        <div class="d-flex flex-column justify-content-end brand-logo mb-4 flex-grow-1">
                            <div class="d-flex flex-column justify-content-end float-right">
                                <img src="{{ asset('img/logonp.png') }}" alt=""
                                    class="align-self-end pe-5 logo-img">
                            </div>

                        </div>
                    </div>

                </div>
            </div>
            <div class="row my-3 fs-i-s">
                <div class="col-lg-4">
                    <table class="table table-bordered">
                        <tbody>
                            <tr>
                                <td>Amount of Original Advance</td>
                                <td class="amount-td">30,000.00</td>
                            </tr>
                            <tr>
                                <td>Amount of Original Advance</td>
                                <td class="amount-td">30,000.00</td>
                            </tr>
                            <tr>
                                <td>Amount of Original Advance</td>
                                <td class="amount-td">30,000.00</td>
                            </tr>

                        </tbody>
                    </table>
                </div>
                <div class="col-lg-7 offset-lg-1">
                    <table class="table table-bordered ">
                        <tbody>
                            <tr>
                                <td style="width: 15%;" rowspan="4">Activity details</td>
                                <td>1 day refresher traingin BPP/MISO for HW fromm 16th Jan to 17 Jan 2022</td>
                            </tr>
                            <tr>

                                <td>1 day refresher traingin BPP/MISO for HW fromm 16th Jan to 17 Jan 2022</td>
                            </tr>
                            <tr>

                                <td>1 day refresher traingin BPP/MISO for HW fromm 16th Jan to 17 Jan 2022</td>
                            </tr>

                        </tbody>
                    </table>
                </div>
            </div>


        </div>
        <div class="print-body ">
            <h5 class="fs-7 text-uppercase fw-bold">Advance Use details</h5>
            <table class="table mb-3 fs-i-s">
                <thead>
                    <tr>
                        <th>SN.</th>
                        <th style="width: 15%;">Narrative</th>
                        <th>Project</th>
                        <th>District</th>
                        <th>Location</th>
                        <th>Actvt. Code</th>
                        <th>Donor Code</th>
                        <th>Total Expenses</th>
                        <th>Less TDS</th>
                        <th>Net Settelment</th>
                        <th>Trgt Achived</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1.</td>
                        <td>Narrative</td>
                        <td>PRO2023</td>
                        <td>Kathmandu</td>
                        <td>Kathmandu</td>
                        <td>AC1222</td>
                        <td>D192</td>
                        <td class="amount-td">120000</td>
                        <td class="amount-td">Less TDS</td>
                        <td class="amount-td">Net Settelment</td>
                        <td>Trgt Achived</td>
                    </tr>
                    <tr>
                        <td>1.</td>
                        <td>Narrative</td>
                        <td>PRO2023</td>
                        <td>Kathmandu</td>
                        <td>Kathmandu</td>
                        <td>AC1222</td>
                        <td>D192</td>
                        <td class="amount-td">120000</td>
                        <td class="amount-td">Less TDS</td>
                        <td class="amount-td">Net Settelment</td>
                        <td>Trgt Achived</td>
                    </tr>
                    <tr>
                        <td>1.</td>
                        <td>Narrative</td>
                        <td>PRO2023</td>
                        <td>Kathmandu</td>
                        <td>Kathmandu</td>
                        <td>AC1222</td>
                        <td>D192</td>
                        <td class="amount-td">120000</td>
                        <td class="amount-td">Less TDS</td>
                        <td class="amount-td">Net Settelment</td>
                        <td>Trgt Achived</td>
                    </tr>
                    <tr>
                        <td>1.</td>
                        <td>Narrative</td>
                        <td>PRO2023</td>
                        <td>Kathmandu</td>
                        <td>Kathmandu</td>
                        <td>AC1222</td>
                        <td>D192</td>
                        <td class="amount-td">120000</td>
                        <td class="amount-td">Less TDS</td>
                        <td class="amount-td">Net Settelment</td>
                        <td>Trgt Achived</td>
                    </tr>
                    <tr>
                        <td>1.</td>
                        <td>Narrative</td>
                        <td>PRO2023</td>
                        <td>Kathmandu</td>
                        <td>Kathmandu</td>
                        <td>AC1222</td>
                        <td>D192</td>
                        <td class="amount-td">120000</td>
                        <td class="amount-td">Less TDS</td>
                        <td class="amount-td">Net Settelment</td>
                        <td>Trgt Achived</td>
                    </tr>
                    <tr>
                        <td>1.</td>
                        <td>Narrative</td>
                        <td>PRO2023</td>
                        <td>Kathmandu</td>
                        <td>Kathmandu</td>
                        <td>AC1222</td>
                        <td>D192</td>
                        <td class="amount-td">120000</td>
                        <td class="amount-td">Less TDS</td>
                        <td class="amount-td">Net Settelment</td>
                        <td>Trgt Achived</td>
                    </tr>
                    <tr>
                        <td>1.</td>
                        <td>Narrative</td>
                        <td>PRO2023</td>
                        <td>Kathmandu</td>
                        <td>Kathmandu</td>
                        <td>AC1222</td>
                        <td>D192</td>
                        <td class="amount-td">120000</td>
                        <td class="amount-td">Less TDS</td>
                        <td class="amount-td">Net Settelment</td>
                        <td>Trgt Achived</td>
                    </tr>
                    <tr>
                        <td>1.</td>
                        <td>Narrative</td>
                        <td>PRO2023</td>
                        <td>Kathmandu</td>
                        <td>Kathmandu</td>
                        <td>AC1222</td>
                        <td>D192</td>
                        <td class="amount-td">120000</td>
                        <td class="amount-td">Less TDS</td>
                        <td class="amount-td">Net Settelment</td>
                        <td>Trgt Achived</td>
                    </tr>
                    <tr>
                        <td>1.</td>
                        <td>Narrative</td>
                        <td>PRO2023</td>
                        <td>Kathmandu</td>
                        <td>Kathmandu</td>
                        <td>AC1222</td>
                        <td>D192</td>
                        <td class="amount-td">120000</td>
                        <td class="amount-td">Less TDS</td>
                        <td class="amount-td">Net Settelment</td>
                        <td>Trgt Achived</td>
                    </tr>
                    <tr>
                        <td>1.</td>
                        <td>Narrative</td>
                        <td>PRO2023</td>
                        <td>Kathmandu</td>
                        <td>Kathmandu</td>
                        <td>AC1222</td>
                        <td>D192</td>
                        <td class="amount-td">120000</td>
                        <td class="amount-td">Less TDS</td>
                        <td class="amount-td">Net Settelment</td>
                        <td>Trgt Achived</td>
                    </tr>
                    <tr>
                        <td>1.</td>
                        <td>Narrative</td>
                        <td>PRO2023</td>
                        <td>Kathmandu</td>
                        <td>Kathmandu</td>
                        <td>AC1222</td>
                        <td>D192</td>
                        <td class="amount-td">120000</td>
                        <td class="amount-td">Less TDS</td>
                        <td class="amount-td">Net Settelment</td>
                        <td>Trgt Achived</td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <th class="amount-td" colspan="6"></th>
                        <th class="amount-td">Total</th>
                        <th class="amount-td">125000</th>
                        <th class="amount-td">6000</th>
                        <th class="amount-td">6000</th>
                        <th></th>
                    </tr>

                </tfoot>
            </table>
            <h5 class="fs-7 text-uppercase fw-bold">Authorization and Checking</h5>
            <p class="mb-4">Reason for over/underspending and agreed by approver: <span><strong>Employee
                        Name</strong></span></p>
        </div>

        <div class="print-footer pt-5">
            <div class="row">
                <div class="col-lg-4">
                    <h5 class="fs-7 text-uppercase fw-bold">Checked By</h5>
                    <div class="fot-info w-100">
                        <div class="d-flex flex-grow-1 mb-2">
                            <span class="w-25">Name </span><span class="border-bottom d-flex flex-grow-1 w-75 pb-1">ram
                                bhadur</span>
                        </div>
                        <div class="d-flex flex-grow-1 mb-2">
                            <span class="w-25">Title </span><span class="border-bottom d-flex flex-grow-1 w-75 pb-1">ram
                                bhadur</span>
                        </div>
                        <div class="d-flex flex-grow-1 mb-2">
                            <span class="w-25">Date </span><span class="border-bottom d-flex flex-grow-1 w-75 pb-1">ram
                                bhadur</span>
                        </div>
                        <div class="d-flex flex-grow-1 mb-2">
                            <span class="w-25">Comments </span><span class="d-flex flex-grow-1 w-75 pb-1">ram bhadur
                                Lorem ipsum dolor sit amet consectetur adipisicing elit. Sed saepe inventore amet aliquam
                                fugit
                                ratione delectus optio, est enim sequi fugiat quidem quae harum eaque necessitatibus, autem,
                                a
                                commodi dolor. </span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <h5 class="fs-7 text-uppercase fw-bold">Recommended By</h5>
                    <div class="fot-info w-100">
                        <div class="d-flex flex-grow-1 mb-2">
                            <span class="w-25">Name </span><span class="border-bottom d-flex flex-grow-1 w-75 pb-1">ram
                                bhadur</span>
                        </div>
                        <div class="d-flex flex-grow-1 mb-2">
                            <span class="w-25">Title </span><span class="border-bottom d-flex flex-grow-1 w-75 pb-1">ram
                                bhadur</span>
                        </div>
                        <div class="d-flex flex-grow-1 mb-2">
                            <span class="w-25">Date </span><span class="border-bottom d-flex flex-grow-1 w-75 pb-1">ram
                                bhadur</span>
                        </div>
                        <div class="d-flex flex-grow-1 mb-2">
                            <span class="w-25">Comments </span><span class="d-flex flex-grow-1 w-75 pb-1">ram bhadur
                                Lorem ipsum dolor sit amet consectetur adipisicing elit. Sed saepe inventore amet aliquam
                                fugit
                                ratione delectus optio, est enim sequi fugiat quidem quae harum eaque necessitatibus, autem,
                                a
                                commodi dolor. </span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <h5 class="fs-7 text-uppercase fw-bold">Authorized By</h5>
                    <div class="fot-info w-100">
                        <div class="d-flex flex-grow-1 mb-2">
                            <span class="w-25">Name </span><span class="border-bottom d-flex flex-grow-1 w-75 pb-1">ram
                                bhadur</span>
                        </div>
                        <div class="d-flex flex-grow-1 mb-2">
                            <span class="w-25">Title </span><span class="border-bottom d-flex flex-grow-1 w-75 pb-1">ram
                                bhadur</span>
                        </div>
                        <div class="d-flex flex-grow-1 mb-2">
                            <span class="w-25">Date </span><span class="border-bottom d-flex flex-grow-1 w-75 pb-1">ram
                                bhadur</span>
                        </div>
                        <div class="d-flex flex-grow-1 mb-2">
                            <span class="w-25">Comments </span><span class="d-flex flex-grow-1 w-75 pb-1">ram bhadur
                                Lorem ipsum dolor sit amet consectetur adipisicing elit. Sed saepe inventore amet aliquam
                                fugit
                                ratione delectus optio, est enim sequi fugiat quidem quae harum eaque necessitatibus, autem,
                                a
                                commodi dolor. </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </section>

@stop
