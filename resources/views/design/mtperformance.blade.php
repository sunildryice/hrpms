@extends('layouts.container-report')

@section('title', 'Mid-Term Performance Review Form')
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
            width: 10%;
        }


        tbody,
        td,
        tfoot,
        th,
        thead,
        tr {
            border-width: 0.1px;
        }

        .table tr th,
        .table tr td {
            padding: 0.25rem 0.75rem;
        }
    </style>
@endsection
@section('page_js')

@endsection

@section('page-content')


    <section class="print-info bg-white p-3" id="print-info">
        <div class="print-title fw-bold mb-3 translate-middle text-center ">
            <div class="fs-5"> HERD International</div>
            <div class="fs-8"> Mid-Term Performance Review Form</div>
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

        <div class="print-body mb-5">

            <div class="row">
                <div class="col-lg-12">
                    <table class="table border mb-4">
                        <thead>
                            <tr>
                                <th scope="col" colspan="4" >A. EMPLOYEE AND SUPERVISOR DETAILS</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <th  scope="row">Employee Name:</th>
                                <td></td>
                                <th  scope="row">Employee Title:</th>
                                <td></td>
                            </tr>
                            <tr>
                                <th  scope="row">Supervisor Name:</th>
                                <td></td>
                                <th  scope="row">Supervisor Title: </th>
                                <td></td>
                            </tr>
                            <tr>
                                <th  scope="row">Technical Supervisor Name:</th>
                                <td></td>
                                <th  scope="row">Technical Supervisor Title: </th>
                                <td></td>
                            </tr>
                            <tr>
                                <th  scope="row">Date of joining:</th>
                                <td></td>
                                <th  scope="row">In current position since: </th>
                                <td></td>
                            </tr>
                            <tr>
                                <th  scope="row">Duty Station:</th>
                                <td colspan="3"></td>
                            </tr>
                            <tr>
                                <th scope="row">Review period from:</th>
                                <td></td>
                                <th scope="row">Review period to: </th>
                                <td></td>
                            </tr>

                        </tbody>
                    </table>
                    <table class="table border">
                        <thead>
                            <tr>
                                <th scope="col" colspan="4" >B.EMPLOYEE FEEDBACK FOR THIS REVIEW PERIOD</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <th  scope="row">List major achievements including new or increased responsibilities:</th>
                                <td></td>
                            </tr>
                            <tr>
                                <th  scope="row">List any major challenges or difficulties you faced:</th>
                                <td></td>
                            </tr>
                        </tbody>
                    </table>
                    <table class="table border">
                        <thead>
                            <tr>
                                <th scope="col" colspan="4" >C.	KEY GOALS REVIEW </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <th  scope="col">(Insert key goals agreed upon during previous performance review):</th>
                                <th  scope="col">To be completed by Employee:</th>
                                <th  scope="col">To be completed by Supervisor:</th>
                            </tr>
                            <tr>
                               <td>Employee</td>
                               <td></td>
                               <td></td>
                            </tr>
                            <tr>
                               <td>Employee</td>
                               <td></td>
                               <td></td>
                            </tr>
                            <tr>
                               <td>Employee</td>
                               <td></td>
                               <td></td>
                            </tr>
                        </tbody>
                    </table>
                    <table class="table border">
                        <thead>
                            <tr>
                                <th scope="col" colspan="4" >D.	STRENGHTS AND AREAS FOR GROWTH (to be completed by supervisor)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <th  scope="row">Identify strengths/critical accomplishments:</th>
                                <td></td>
                            </tr>
                            <tr>
                                <th  scope="row">Identify areas for growth/improvement: (List Professional Development Plan with timeframe)
                                    </th>
                                <td></td>
                            </tr>
                        </tbody>
                    </table>
                    <table class="table border">
                        <thead>
                            <tr>
                                <th scope="col" colspan="4" >E.	EMPLOYEE COMMENTS (optional)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>COMMENTS</td>
                            </tr>
                        </tbody>
                    </table>
                    <table class="table border">
                        <thead>
                            <tr>
                                <th scope="col" colspan="4" >F.	SUPERVISOR /NEXT LINE MANAGER COMMENTS (OPTIONAL)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>COMMENTS</td>
                            </tr>
                        </tbody>
                    </table>
                    <table class="table border mb-4">
                        <thead>
                            <tr>
                                <th scope="col" colspan="4" >G.	AKNOWLEDGEMENTS</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <th scope="col" colspan="4" >A. EMPLOYEE AND SUPERVISOR DETAILS</th>
                            </tr>
                            <tr>
                                <th  scope="row">Employee signature:</th>
                                <td></td>
                                <th  scope="row">Date:</th>
                                <td></td>
                            </tr>
                            <tr>
                                <th  scope="row">Supervisor signature:</th>
                                <td></td>
                                <th  scope="row">Date:</th>
                                <td></td>
                            </tr>
                            <tr>
                                <th  scope="row">Next Line Manager Signature:</th>
                                <td></td>
                                <th  scope="row">Date:</th>
                                <td></td>
                            </tr>
                            <tr>
                                <th  scope="row">Executive Director Signature:</th>
                                <td></td>
                                <th  scope="row">Date:</th>
                                <td></td>
                            </tr>


                        </tbody>
                    </table>
                </div>
            </div>




        </div>
    </section>

@endsection

