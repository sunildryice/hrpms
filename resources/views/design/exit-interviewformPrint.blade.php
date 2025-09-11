@extends('layouts.container-report')

@section('title', 'Exit Interview Form Print')
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

    <!-- CSS only -->

    <section class="print-info bg-white p-3" id="print-info">


        <div class="print-title fw-bold mb-3 translate-middle text-center ">
            <div class="fs-5"> One Heart Worldwide</div>
            <div class="fs-8"> Exit Interview Form</div>
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
            <div>The objective of this questionnaire is to elicit your honest feedback using which the organization can
                learn
                from its shortcomings. All information provided by you will be kept confidential and used only for the
                purpose
                of organizational improvement. Consider this feedback as a parting gift to us.
            </div>
            <div class="my-4 fw-bold fs-6">Probation Record </div>
            <table class="table border">
                <tbody>
                    <tr>
                        <td>Employee Name</td>
                        <td></td>
                        <td>Immediate Head</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>Division / Dept.</td>
                        <td></td>
                        <td>Job Title</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>Hire Date</td>
                        <td></td>
                        <td>Resignation/Termination Date</td>
                    </tr>

                </tbody>
            </table>
            <table class="table border">
                <tbody>
                    <tr>
                        <td>1.What is the main reason for your leaving?</td>
                    </tr>
                    <tr>
                        <td class="content"></td>
                    </tr>
                    <tr>
                        <td>2. What did you like most about your job?</td>
                    </tr>
                    <tr>
                        <td class="content"></td>
                    </tr>
                    <tr>
                        <td>3. What did you like least about your job?</td>
                    </tr>
                    <tr>
                        <td class="content"></td>
                    </tr>
                    <tr>
                        <td>4. What has been good/enjoyable/satisfying for you in your time with us?

                        </td>
                    </tr>
                    <tr>
                        <td class="content"></td>
                    </tr>
                </tbody>

            </table>
            <table class="table border">
                <tbody>
                    <tr>
                        <td colspan="5">5. What did you think of your immediate head / supervisor on the following
                            points?
                        </td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>Almost</td>
                        <td>Always</td>
                        <td>Usually</td>
                        <td>Sometimes Never</td>
                    </tr>
                    <tr>
                        <td>Was consistently fair</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>Provided recognition</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>Resolved complaints</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>Was sensitive to employees' needs</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>Provided feedback on performance</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>Was receptive to open communication</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                </tbody>

            </table>
            <table class="table border">
                <tbody>
                    <tr>
                        <td colspan="5">6. How would you rate the following?</td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>Excellent</td>
                        <td>Good</td>
                        <td>Fair</td>
                        <td>Poor</td>
                    </tr>
                    <tr>
                        <td>Cooperation within your department</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>Cooperation with other department</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>Personal job training</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>Organization's performance review system</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>Company's new employee orientation program</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>Fairly compensated for your position and benefits</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>Career development/Advancement opportunities</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>Working conditions</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>Organization Culture</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>Management Response in discrimination /harassment </td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td colspan="5" class="fw-bold content">Comments:</td>
                    </tr>

                </tbody>
            </table>
            <table class="table border">
                <tbody>
                    <tr>
                        <td>7. Was the work you were doing approximately what you expected it would be?</td>
                    </tr>
                    <tr>
                        <td>Yes <br> No</td>
                    </tr>
                    <tr>
                        <td colspan="5" class="fw-bold content">Comments:</td>
                    </tr>
                    <tr>
                        <td>8. Was your workload usually:</td>
                    </tr>
                    <tr>
                        <td>Too heavy <br> About right <br> Too light</td>
                    </tr>
                    <tr>
                        <td>9. Would you recommend this organization to a friend as a good organization to work for?</td>
                    </tr>
                    <tr>
                        <td>Most definitely <br> With reservations <br> No</td>
                    </tr>
                    <tr>
                        <td>10: What would you say about how you were motivated, and how that could have been improved?</td>
                    </tr>
                    <tr>
                        <td class="content"></td>
                    </tr>
                    <tr>
                        <td>11. What were the positive elements you have seen in the organization?</td>
                    </tr>
                    <tr>
                        <td class="content"></td>
                    </tr>
                    <tr>
                        <td>12. What was your most important lesson learned during your work period?</td>
                    </tr>
                    <tr>
                        <td class="content"></td>
                    </tr>
                    <tr>
                        <td>13. What suggestions do you have to make this organization a better place to work?</td>
                    </tr>
                    <tr>
                        <td class="content"></td>
                    </tr>
                </tbody>
            </table>
            <div class="row mb-4">
                <div class="col-lg-6">
                    <div> Human Resources Representative:</div>
                    <div>Date:</div>
                </div>
                <div class="col-lg-6">
                    <div>Employee: </div>
                    <div>Date:</div>
                </div>
            </div>
        </div>

    </section>

@endsection
