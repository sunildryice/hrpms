@extends('layouts.container-report')

@section('title', 'Training Report Print')
@section('page_css')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Mukta:wght@200;500;600;800&display=swap" rel="stylesheet">
    <style>
        .print-info {
            font-family: 'Mukta', sans-serif;
        }

        table {
            border: 1px solid;
        }

        .table thead th {
            font-size: 0.94375rem;

        }

        tbody,
        td,
        tfoot,
        th,
        thead,
        tr {
            font-weight: 600;
        }

        tbody,
        td,
        tfoot,
        th,
        thead,
        tr {
            border-width: 0.1px;
        }

        .content {
            height: 70px;
        }
    </style>
@endsection
@section('page_js')

@endsection

@section('page-content')

    <!-- CSS only -->

    <section class="print-info bg-white p-3" id="print-info">


        <div class="print-title fw-bold mb-3 translate-middle text-center ">
            <div class="fs-5"> One Heart Worldwide</div>
            <div class="fs-8"> Training Report Form</div>
        </div>

        <div class="print-header">
            <div class="row">
                <div class="col-lg-8">

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
            <table class="table mb-3 border">
                <thead>
                    <tr>
                        <th colspan="2" class="text-center">One Heart Worldwide - Training Report Form <br>
                            (To be filled after the Training)
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Name Of Staff:</td>
                        <td>Position:</td>
                    </tr>
                    <tr>
                        <td colspan="2"> A. Introduction (Brief mention of what course was undertaken, where it was
                            undertaken, how long the training, who else participated & facilitated)</td>
                    </tr>
                    <tr>
                        <td class="content" colspan="2"></td>
                    </tr>
                    <tr>
                        <td colspan="2"> B. Key Outcomes & Learnings (What were the key learnings for the trainee & OHW
                            Nepal).</td>
                    </tr>
                    <tr>
                        <td class="content" colspan="2"></td>
                    </tr>
                    <tr>
                        <td colspan="2"> C. Utility (How do you intend to use learning in practice within the
                            organization?)
                        </td>
                    </tr>
                    <tr>
                        <td class="content" colspan="2"></td>
                    </tr>
                    <tr>
                        <td colspan="2"> D. Conclusion/Other Comments</td>
                    </tr>
                    <tr>
                        <td class="content" colspan="2"></td>
                    </tr>
                    <tr>
                        <th colspan="2">Submitted by:</th>
                    </tr>
                    <tr>
                        <td>Name:</td>
                        <td>Signature:</td>
                    </tr>
                    <tr>
                        <td>Position:</td>
                        <td>Date:</td>
                    </tr>

                    <tr>
                        <td colspan="5"> Report Received by:</td>
                    </tr>
                    <tr>
                        <td class="content" colspan="5"></td>
                    </tr>
                    <tr>
                        <td colspan="5"> Line Manager:</td>
                    </tr>
                    <tr>
                        <td class="content" colspan="5"></td>
                    </tr>
                    <tr>
                        <td colspan="5"> Admin and Finance Director</td>
                    </tr>
                    <tr>
                        <td colspan="5" > <strong>Note: Prepare an article of not more than 700 words about the training &
                                learning achieved within 7 days of return from the training (Submit this to the
                                HR).</strong>
                        </td>
                    </tr>


                </tbody>





            </table>


        </div>


    </section>



@endsection
