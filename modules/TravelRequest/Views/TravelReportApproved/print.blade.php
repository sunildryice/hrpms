@extends('layouts.container-report')

@section('title', 'Travel Report')
@section('page_css')
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
            border-width: 0.1px;
        }

        .table tr th,
        .table tr td {
            padding: 0.25rem 0.75rem;
            vertical-align: top;
        }
    </style>
@endsection

@section('page-content')
    <script type="text/javascript">
        window.print();
    </script>

    <!-- CSS only -->

    <section class="print-info bg-white p-3" id="print-info">
        <div class="print-title fw-bold mb-3 translate-middle text-center ">
            <div class="fs-5"> HERD International</div>
            <div class="fs-8">{{ $requester->office->getOfficeName() }}</div>
            <div class="fs-8"> Field Visit Report Form</div>
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
                <div class="col-lg-4">
                    <div class="print-header-info mb-3">
                        <ul class="list-unstyled m-0 p-0 fs-7">
                            <li><span
                                    class="fw-bold me-2">Ref:</span><span>{{ $travelRequest->getTravelRequestNumber() }}</span>
                            </li>
                            <li><span
                                    class="fw-bold me-2 ">Project:</span><span>{{ $travelRequest->getProjectCode() }}</span>
                            </li>
                            <li><span class="fw-bold me-2">Visit conducted
                                    by:*</span><span>{{ $travelRequest->getRequesterName() }}
                                    @if ($travelRequest->accompanyingStaffs)
                                        , {{ $travelRequest->getAccompanyingStaffs() }}
                                    @endif
                                </span></li>
                            <li><span class="fw-bold me-2">Visit duration with
                                    date*:</span><span>{{ $travelRequest->getDepartureDate() }} to
                                    {{ $travelRequest->getReturnDate() }}</span>
                            </li>
                            <li><span class="fw-bold me-2">Visit Location*
                                </span><span>{{ $travelRequest->final_destination }}</span>
                            </li>


                            <li><span class="fw-bold me-2">Overview of specific objectives and expected outputs: <br>
                                    *(identified in
                                    TOR format for field visit)</span><span>{{ $travelReport->objectives }}</span></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="print-body mb-5">
            <div class="row">
                <div class="col-lg-12">
                    <div class="my-3">A. Observations</div>
                    <table class="table border">
                        <tbody>
                            <tr>
                                <td width="3%">1</td>
                                <td>{{ $travelReport->observation }}</td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="my-3">B. Activities conducted *</div>
                    <table class="table border">
                        <tbody>
                            <tr>
                                <td>1</td>
                                <td>{{ $travelReport->activities }}</td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="my-3">C. Recommendations and Plan of Action </div>
                    <table class="table border">
                        <thead>
                            <tr>
                                <th width="3%">SN </th>
                                <th>What?</th>
                                <th>When? </th>
                                <th>Who? </th>
                                <th>Remarks </th>
                            </tr>
                        </thead>
                        <tbody>

                            @forelse($travelReport->travelReportRecommendations as $index=>$recommendation)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $recommendation['recommendation_subject'] }}</td>
                                    <td>{{ $recommendation['recommendation_date'] }}</td>
                                    <td>{{ $recommendation['recommendation_responsible'] }}</td>
                                    <td>{{ $recommendation['recommendation_remarks'] }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div class="my-3">D. Other comments (if any)</div>
                    <div style="min-height: 100px;" class="border mb-5">{{ $travelReport->other_comments }}</div>

                    <div class="row">
                        <div class="col-lg-6 mb-4">
                            <div><strong>Submitted By:</strong></div>
                            <div><strong>Name:</strong> {{ $travelReport->getReporterName() }} </div>
                            <div><strong>Position:</strong> {{ $requester->getDesignationName() }} </div>
                            <div><strong>Signature:</strong></div>
                            <div><strong>Date:</strong>{{ $dates['submitted_date'] }}</div>
                        </div>
                        <div class="col-lg-6 mb-4">
                            <div><strong>Approved By:</strong></div>
                            <div><strong>Name:</strong> {{ $travelReport->getApproverName() }} </div>
                            <div><strong>Position:</strong> {{ $approver->getDesignationName() }} </div>
                            <div><strong>Signature:</strong> </div>
                            <div><strong>Date:</strong> {{ $dates['approved_date'] }} </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
