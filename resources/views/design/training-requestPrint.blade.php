@extends('layouts.container-report')

@section('title', 'Training Request Print')
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
            width: 25%;
        }

        tbody,
        td,
        tfoot,
        th,
        thead,
        tr {
            border-width: 0.1px;
        }
    </style>
@endsection
@section('page_js')

@endsection

@section('page-content')

    <!-- CSS only -->

    <div class="print-title fw-bold mb-3 translate-middle text-center ">
        <div class="fs-5"> HERD International</div>
        <div class="fs-8">XYZ Office</div>
        <div class="fs-8"> Training Request</div>
    </div>



    <div class="print-header">
        <div class="row">
            <div class="col-lg-8">

            </div>
            <div class="col-lg-4">
                <div class="d-flex flex-column justify-content-end">
                    <div class="d-flex flex-column justify-content-end brand-logo mb-4 flex-grow-1">
                        <div class="d-flex flex-column justify-content-end float-right">
                            <img src="{{ asset('img/logonp.png') }}" alt="" class="align-self-end pe-5 logo-img">
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>
    <div class="print-body mb-5">
        <table class="table mb-5">
            <tbody>
                <tr>
                    <td>Date of request:</td>
                    <td colspan="3"></td>
                </tr>
                <tr>
                    <td>Requesting Employee Name / Designation/ Department:</td>
                    <td colspan="3"></td>
                </tr>
                <tr>
                    <td>Name of Course(s) or Training requested Training Institution Name</td>
                    <td colspan="3"></td>
                </tr>
                <tr>
                    <td>Date of Course: Begin</td>
                    <td>
                    <td>Own Time<br> Work Time</td>
                    <td></td>
                </tr>
                <tr>
                    <td>Date of Course: End</td>
                    <td></td>
                    <td>Time of Course</td>
                    <td></td>
                </tr>
            </tbody>
        </table>

        <div>
            <div>Course Fee(s) <span> _____________ </span>(attach receipt for fees) </div>
            <table class="table">
                <tbody>
                    <tr>
                        <td>Project:</td>
                        <td>Activity Code:</td>
                        <td>Account Code:</td>
                        <td>Donor Code:</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div>
            <div>Brief description of course(s) (attach a copy of course(s) description): </div>
            <div class="border" style="min-height: 200px;"></div>
        </div>
        <table class="table my-4">
            <thead>
                <th>To be filled by Requester</th>
                <th>Response</th>
            </thead>
            <tbody>
                <tr>
                    <td>1. Why do you think you feel you need this training?</td>
                    <td></td>
                </tr>
                <tr>
                    <td>2. What have you already done to attempt to address this issue?</td>
                    <td></td>
                </tr>
                <tr>
                    <td>3. Was it effective? Why or why not?</td>
                    <td></td>
                </tr>
                <tr>
                    <td>4. How will you measure the success of this new training initiative?</td>
                    <td></td>
                </tr>
                <tr>
                    <td>5. Is training the appropriate solution? Hint: The problem must be caused by a skill/knowledge
                        deficiency.</td>
                    <td></td>
                </tr>
                <tr>
                    <td>6. Will the knowledge/skills be used frequently enough to justify the training?</td>
                    <td></td>
                </tr>
            </tbody>
        </table>
        <table class="table">
            <tbody>
                <tr>
                    <td rowspan="2">I meet the criteria for this application and I have: </td>
                    <td>_____________completed all information as requested,</td>
                </tr>
                <tr>
                    <td>_____________attached the course description.</td>
                </tr>
                <tr>
                    <td colspan="2">I shall submit Training Report within Seven (7) days after completion of the training
                    </td>
                </tr>
                <tr>
                    <td>Employee Signature: </td>
                    <td>Date: </td>
                </tr>
            </tbody>
        </table>

        <table class="table">
            <thead>
                <th>To be filled by HR Officer</th>
                <th>Response</th>
            </thead>
            <tbody>
                <tr>
                    <td>1. Is training the appropriate solution? Hint: The problem must be caused by a skill/knowledge
                        deficiency.</td>
                    <td></td>
                </tr>
                <tr>
                    <td>2. Is the request aligned with our current organizational objectives and priorities?</td>
                    <td></td>
                </tr>
                <tr>
                    <td>3. Are resources available to meet the request?</td>
                    <td></td>
                </tr>
                <tr>
                    <td>Name and Signature of HR </td>
                    <td></td>
                </tr>
            </tbody>
        </table>
        <table class="table mb-5">
            <tbody>
                <tr>
                    <td>Department of Recommendation:</td>
                </tr>
                <tr>
                    <td>Recommended By (Name and Title):</td>
                </tr>
                <tr>
                    <td>Signature and Date:</td>
                </tr>
            </tbody>
        </table>
        <div>
            <strong>Approval:</strong>
            <table class="table">
                <tbody>
                    <tr>
                        <th colspan="2">Approved / Not Approved for this time</th>
                    </tr>
                    <tr>
                        <td rowspan="2">TIME FOR COURSE ATTENDANCE:</td>
                        <td>Approved as time worked.</td>
                    </tr>
                    <tr>
                        <td>Temporary work schedule adjustment.</td>
                    </tr>
                    <tr>
                        <td>Approved Amount for Training:</td>
                        <td></td>
                    </tr>
                    <tr>
                        <th colspan="2">Approved By: </th>
                    </tr>
                    <tr>
                        <td colspan="2">Name and Title: </td>
                    </tr>
                    <tr>
                        <td colspan="2">Signature: </td>
                    </tr>
                    <tr>
                        <td colspan="2">Date: </td>
                    </tr>
                </tbody>

            </table>
        </div>



    </div>



@endsection
