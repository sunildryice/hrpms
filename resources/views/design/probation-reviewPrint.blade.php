@extends('layouts.container-report')

@section('title', 'Probation Review Print')
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

        .table tr th,
        .table tr td {
            padding: 0.25rem 0.75rem;
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

    <div class="print-title fw-bold mb-3 translate-middle text-center ">
        <div class="fs-5"> One Heart Worldwide</div>
        <div class="fs-8">XYZ Office</div>
        <div class="fs-8"> Probation Review</div>
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
        <div>The Supervisor/ line manager should ensure that the employee is given a copy of this document at each stage of
            their probation and should retain the original to monitor progress against set objectives at follow-up meetings.
        </div>

        <div class="my-4 fw-bold fs-6">Probation Record </div>

    </div>
    <div class="print-body">
        <table class="table border">
            <tbody>
                <tr>
                    <td>Employee Name:</td>
                    <td colspan="2"></td>
                </tr>
                <tr>
                    <td>Designation:</td>
                    <td colspan="2"></td>
                </tr>
                <tr>
                    <td>Level:</td>
                    <td colspan="2"></td>
                </tr>
                <tr>
                    <td>Department / Section:</td>
                    <td colspan="2"></td>
                </tr>
                <tr>
                    <td>Date Of Joining:</td>
                    <td colspan="2"></td>
                </tr>
                <tr>
                    <td>Immediate Supervisor/ Line Manager’s Name:</td>
                    <td colspan="2"></td>
                </tr>
                <tr>
                    <td>Designation:</td>
                    <td colspan="2"></td>
                </tr>
                <tr>
                    <td>Working District:</td>
                    <td colspan="2"></td>
                </tr>
                <tr>
                    <td></td>
                    <td>Date</td>
                    <td>Please tick when completed</td>
                </tr>
                <tr>
                    <td>Initial Meeting</td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td>3-month review:</td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td>6-month review:</td>
                    <td></td>
                    <td></td>
                </tr>
            </tbody>
        </table>
        <div>First review (3 months) – To be completed by Supervisor/ Line Manager in discussion with the employee.</div>

        <table class="table">
            <tbody>
                <tr>
                    <td>Indicators</td>
                    <td>Improvement required</td>
                    <td>Satisfactory</td>
                    <td>Good</td>
                    <td>Excellent</td>

                </tr>
                <tr>
                    <td>Quality and accuracy of work</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td>Work Efficiency</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td>Attendance & Punctuality</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td>Honesty & Integrity</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td>Interpersonal Relationships</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td>Effective Communications</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td>Competency in the role</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td colspan="5">If any areas of performance, conduct or attendance require improvement please provide
                        details below. </td>
                </tr>
                <tr>
                    <td class="content" colspan="5"></td>
                </tr>
                <tr>
                    <td colspan="5">Where concerns have been identified, please summarise how these will be addressed
                        during the remaining period of probation.</td>
                </tr>
                <tr>
                    <td class="content" colspan="5"></td>
                </tr>
                <tr>
                    <td colspan="5">Summarise the employee’s performance and progress over the period.</td>
                </tr>
                <tr>
                    <td class="content" colspan="5"></td>
                </tr>
                <tr>
                    <td colspan="2"></td>
                    <td colspan="2">If NO, what further action is required?</td>
                    <td>Review Date</td>
                </tr>
                <tr>
                    <td>Have the objectives identified for this period of the probation been met?</td>
                    <td>
                        Yes <br> No
                    </td>
                    <td colspan="2"></td>
                    <td></td>
                </tr>
                <tr>
                    <td>Have the training / development needs identified for this period of the probation been addressed?
                    </td>
                    <td>
                        Yes <br> No
                    </td>
                    <td colspan="2"></td>
                    <td></td>
                </tr>
                <tr>
                    <td>Employee’s Name:</td>
                    <td colspan="4">Supervisor’s Name:</td>
                </tr>
                <tr>
                    <td>Employee’s Signature:</td>
                    <td colspan="4">Supervisor’s Signature:</td>
                </tr>
                <tr>
                    <td>Date:</td>
                    <td colspan="4">Date:</td>
                </tr>
                <tr>
                    <td>Recommendation from Supervisor:</td>
                    <td colspan="4"></td>
                </tr>
                <tr>
                    <td>Discussion and Confirmation by Executive Director</td>
                    <td colspan="4"></td>
                </tr>
                <tr>
                    <td colspan="4">Is the employee’s appointment to be confirmed?</td>
                    <td>
                        Yes <br> No
                    </td>
                </tr>
                <tr>
                    <td colspan="5">If NO, please provide reasons below and summarise what action has been taken to
                        address any difficulties which have arisen during the probationary period.</td>
                </tr>
                <tr>
                    <td class="content" colspan="5"></td>
                </tr>
                <tr>
                    <td colspan="5">The employee may provide any comments about their experience of the probationary
                        process here.</td>
                </tr>
                <tr>
                    <td class="content" colspan="5"></td>
                </tr>
                <tr>
                    <td colspan="4">Should the employee’s probationary period be extended?</td>
                    <td>
                        Yes <br> No
                    </td>
                </tr>
                <tr>
                    <td colspan="5">If YES, please provide reasons and, where appropriate, specify any areas of
                        improvement required and how these will be monitored.</td>
                </tr>
                <tr>
                    <td class="content" colspan="5"></td>
                </tr>
                <tr>
                    <td>Length of the extension (max 3 months):</td>
                    <td colspan="4"></td>
                </tr>
                <tr>
                    <td>New Probation Period completion date:</td>
                    <td colspan="4"></td>
                </tr>
                <tr>
                    <td>Employee’s signature:</td>
                    <td colspan="4"></td>
                </tr>
                <tr>
                    <td>Supervisor’s signature:</td>
                    <td colspan="4"></td>
                </tr>
                <tr>
                    <td>Date:</td>
                    <td colspan="4"></td>
                </tr>
            </tbody>
        </table>
    </div>



@endsection
