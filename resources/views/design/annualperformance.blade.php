@extends('layouts.container-report')

@section('title', 'Annual Performance Review Form')
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
            <div class="fs-8"> Annual Performance Review Form</div>
        </div>

        <div class="print-header">
            <div class="row">
                <div class="col-lg-8">

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
        </div>

        <div class="print-body mb-5">

            <div class="row">
                <div class="col-lg-12">
                    <table class="table border mb-4">
                        <thead>
                            <tr>
                                <th scope="col" colspan="4">A. EMPLOYEE AND SUPERVISOR DETAILS</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <th scope="row">Employee Name:</th>
                                <td></td>
                                <th scope="row">Employee Title:</th>
                                <td></td>
                            </tr>
                            <tr>
                                <th scope="row">Supervisor Name:</th>
                                <td></td>
                                <th scope="row">Supervisor Title: </th>
                                <td></td>
                            </tr>
                            <tr>
                                <th scope="row">Technical Supervisor Name:</th>
                                <td></td>
                                <th scope="row">Technical Supervisor Title: </th>
                                <td></td>
                            </tr>
                            <tr>
                                <th scope="row">Date of joining:</th>
                                <td></td>
                                <th scope="row">In current position since: </th>
                                <td></td>
                            </tr>
                            <tr>
                                <th scope="row">Duty Station:</th>
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
                                <th scope="col" colspan="4">B.EMPLOYEE FEEDBACK FOR THIS REVIEW PERIOD</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <th scope="row">List major achievements including new or increased responsibilities:</th>
                                <td></td>
                            </tr>
                            <tr>
                                <th scope="row">List any major challenges or difficulties you faced:</th>
                                <td></td>
                            </tr>
                        </tbody>
                    </table>
                    <table class="table border">
                        <thead>
                            <tr>
                                <th scope="col" colspan="4">C. KEY GOALS REVIEW </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <th scope="col">(Insert key goals agreed upon during previous performance review):</th>
                                <th scope="col">To be completed by Employee:</th>
                                <th scope="col">To be completed by Supervisor:</th>
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
                                <th scope="col" colspan="4">D. KEY SKILLS EVALUATION (to be completed by supervisor)
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <th scope="row">Communication/working relationships</th>
                                <td></td>
                            </tr>
                            <tr>
                                <th scope="row">Productivity/job effectiveness/organization & planning </th>
                                <td></td>
                            </tr>
                            <tr>
                                <th scope="row">Leadership/developing others</th>
                                <td></td>
                            </tr>
                            <tr>
                                <th scope="row">Problem solving/judgment/analytics</th>
                                <td></td>
                            </tr>
                            <tr>
                                <th scope="row">Accountability</th>
                                <td></td>
                            </tr>
                        </tbody>
                    </table>
                    <table class="table border">
                        <thead>
                            <tr>
                                <th scope="col" colspan="4">E. EMPLOYEE COMMENTS (optional)</th>
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
                                <th scope="col" colspan="4">F. OVERALL PERFORMANCE EVALUATION (to be completed by
                                    supervisor)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <th scope="row">Substantially exceeds expectations:</th>
                                <td>Has mastered all job-related skills and consistently delivers outstanding results in all
                                    areas of responsibility</td>
                                <td>5</td>
                            </tr>
                            <tr>
                                <th scope="row">Exceeds expectations</th>
                                <td>Highly skilled in relation to technical requirements of the job and regularly produces
                                    above-average results in all areas of responsibility.</td>
                                <td>4</td>
                            </tr>
                            <tr>
                                <th scope="row">Meets all expectations</th>
                                <td>Is fully qualified to perform job duties with the appropriate amount of direction and
                                    regularly delivers expected results in all areas of responsibility.</td>
                                <td>3</td>
                            </tr>
                            <tr>
                                <th scope="row">Partially meets expectations</th>
                                <td>Demonstrates beginner knowledge / skill level and does not yet consistently deliver
                                    expected results in all areas of responsibility. Needs improvement.</td>
                                <td>2</td>
                            </tr>
                            <tr>
                                <th scope="row">Below expectations</th>
                                <td>Demonstrates insufficient skills and does not deliver expected results in all areas of
                                    responsibility. Significant and immediate improvement required.</td>
                                <td>1</td>
                            </tr>
                        </tbody>
                    </table>
                    <table class="table border">
                        <thead>
                            <tr>
                                <th scope="col" colspan="4">G. IDENTIFY AT LEAST 3 KEY GOALS FOR THE NEXT REVIEW
                                    PERIOD: (To be completed jointly)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>1</td>
                            </tr>
                            <tr>
                                <td>2</td>
                            </tr>
                            <tr>
                                <td>3</td>
                            </tr>
                        </tbody>
                    </table>
                    <table class="table border">
                        <thead>
                            <tr>
                                <th scope="col" colspan="4">H. EMPLOYEE COMMENTS (optional)</th>
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
                                <th scope="col" colspan="4">I. SUPERVISOR /NEXT LINE MANAGER COMMENTS (OPTIONAL)</th>
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
                                <th scope="col" colspan="4">J. AKNOWLEDGEMENTS</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <th scope="row">Employee signature:</th>
                                <td></td>
                                <th scope="row">Date:</th>
                                <td></td>
                            </tr>
                            <tr>
                                <th scope="row">Supervisor signature:</th>
                                <td></td>
                                <th scope="row">Date:</th>
                                <td></td>
                            </tr>
                            <tr>
                                <th scope="row">Next Line Manager Signature:</th>
                                <td></td>
                                <th scope="row">Date:</th>
                                <td></td>
                            </tr>
                            <tr>
                                <th scope="row">Executive Director Signature:</th>
                                <td></td>
                                <th scope="row">Date:</th>
                                <td></td>
                            </tr>


                        </tbody>
                    </table>
                </div>
            </div>




        </div>
    </section>

@endsection
