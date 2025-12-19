@extends('layouts.container-report')

@section('title', 'Training Report Print')
@section('page_css')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Mukta:wght@200;500;600;800&display=swap" rel="stylesheet">
    <style>
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

    <div class="print-title fw-bold mb-3 translate-middle text-center ">
        <div class="fs-5"> HERD International</div>
        <div class="fs-8">{{ $requester->getOfficeName() }}</div>
        <div class="fs-8"> Training Report Form </div>
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
    <div class="print-body">
        <table class="table mb-3 border">
            <thead>
                <tr>
                    <th colspan="2" class="text-center">
                        (To be filled after the Training)
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Name of Staff: {{ $trainingReport->getCreatedBy() }}</td>
                    <td>Position: {{ $requester->getDesignationName() }}</td>
                </tr>

                @if ($trainingReportQuestions->count() > 0)
                    @foreach ($trainingReportQuestions as $trainingReportQuestion)
                        @if (
                            $trainingReportQuestion->trainingQuestion->answer_type == 'textarea' &&
                                $trainingReportQuestion->trainingQuestion->type == '6')
                            <tr>
                                <td colspan="2">{{ $trainingReportQuestion->trainingQuestion->question }}</td>
                            </tr>
                            <tr>
                                <td class="content" colspan="2">{{ $trainingReportQuestion->answer }}</td>
                            </tr>
                        @endif
                    @endforeach
                @endif

                <tr>
                    <th colspan="2">Submitted by:</th>
                </tr>
                <tr>
                    <td>Name: {{ $trainingReport->getCreatedBy() }}</td>
                    <td>Signature:</td>
                </tr>
                <tr>
                    <td>Position: {{ $requester->getDesignationName() }}</td>
                    <td>Date: {{ @array_key_exists('submitted_date', $dates) ? $dates['submitted_date'] : '' }}</td>
                </tr>

                <tr>
                    <th colspan="2"> Report Received by:</th>
                </tr>
                <tr>
                    <td>Name: {{ $trainingReport->getReviewerName() }}</td>
                    <td>Signature:</td>
                </tr>
                <tr>
                    <td>Position: {{ $reviewer->getDesignationName() }}</td>
                    <td>Date: {{ @array_key_exists('reviewed_date', $dates) ? $dates['reviewed_date'] : '' }}</td>
                </tr>

                <tr>
                    <th colspan="2">Approved by:</th>
                </tr>
                <tr>
                    <td>Name: {{ $trainingReport->getApproverName() }}</td>
                    <td>Signature:</td>
                </tr>
                <tr>
                    <td>Position: {{ $approver->getDesignationName() }}</td>
                    <td>Date: {{ @array_key_exists('approved_date', $dates) ? $dates['approved_date'] : '' }}</td>
                </tr>

                <tr>
                    <td colspan="5"> <strong>Note: Prepare an article of not more than 700 words about the training &
                            learning achieved within 7 days of return from the training (Submit this to the HR).</strong>
                    </td>
                </tr>


            </tbody>





        </table>


    </div>



@endsection
