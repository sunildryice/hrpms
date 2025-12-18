@extends('layouts.container-report')

@section('title', 'Advance Request Print')

@section('page-content')
    <script type="text/javascript">
        window.print();
    </script>

    <section class="print-info bg-white p-3" id="print-info">
        <div class="print-title fw-bold mb-3 translate-middle text-center ">
            <div class="fs-5"> HERD International</div>
            <div class="fs-8">{{ $advanceRequest->office->getOfficeName() }}</div>
            <div class="fs-8">Cash Advance Request</div>
        </div>

        <div class="print-header">
            <div class="row">
                <div class="col-lg-8">
                    <div class="print-code fs-6 fw-bold mb-3">

                    </div>

                    <div class="print-header-info mb-3">
                        <ul class="list-unstyled m-0 p-0 fs-7">
                            <li><span class="fw-bold me-2">Ref.
                                    #</span><span>{{ $advanceRequest->getAdvanceRequestNumber() }}</span></li>
                            <li><span class="fw-bold me-2">Required
                                    Date:</span><span>{{ $advanceRequest->getRequiredDate() }}</span></li>
                            <li><span class="fw-bold me-2"> Project
                                    :</span><span>{{ $advanceRequest->getProjectCode() }}</span>
                            </li>
                            {{-- <li><span class="fw-bold me-2">Activity Code:</span><span>{{$advanceRequest->getProjectCode()}}</span>
                        </li> --}}

                        </ul>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="d-flex flex-column justify-content-end">
                        <div class="d-flex flex-column justify-content-end brand-logo mb-4 flex-grow-1">
                            <div class="d-flex flex-column justify-content-end float-right">
                                <img src="{{ asset('img/logonp.png') }}" alt="" class="align-self-end pe-5">
                            </div>

                        </div>
                        <ul class="list-unstyled m-0 p-0 fs-7 align-self-end">
                            {{-- <li><span class="fw-bold me-2">Ref. #</span><span>{{$advanceRequest->getAdvanceRequestNumber()}}</span></li>
                        <li><span class="fw-bold me-2">Account Code :</span><span>{{$advanceRequest->getProjectCode()}}</span></li>
                        <li><span class="fw-bold me-2">Donor Code:</span><span>{{$advanceRequest->getProjectCode()}}</span></li> --}}

                        </ul>
                    </div>

                </div>
            </div>
        </div>

        <div class="print-body mb-5">
            <div class="row">
                <div class="col-lg-6">
                    <table class="table border" style="margin-top: 2.3rem;">
                        <tbody>
                            <tr>
                                <th scope="row">Employee Name:</th>
                                <td>{{ $advanceRequest->getRequesterName() }}</td>
                            </tr>
                            <tr>
                                <th scope="row">Title:</th>
                                <td>{{ $advanceRequest->requester->employee->getDesignationName() }}</td>
                            </tr>
                            <tr>
                                <th scope="row">District/Office:</th>
                                <td>{{ $advanceRequest->requestForOffice->getOfficeName() }}</td>
                            </tr>
                            <tr>
                                <th scope="row">Signature: </th>
                                <td></td>
                            </tr>

                        </tbody>
                    </table>
                </div>
                <div class="col-lg-6">
                    <table class="table border caption-top">
                        <caption>Activity start and end dates:</caption>
                        <tbody>
                            <tr>
                                <th scope="row">Start Date:</th>
                                <td>{{ $advanceRequest->getStartDate() }}</td>
                            </tr>
                            <tr>
                                <th scope="row">End Date:</th>
                                <td>{{ $advanceRequest->getEndDate() }}</td>
                            </tr>
                            <tr>
                                <th scope="row">Tentative Settlement Date:</th>
                                <td>{{ $advanceRequest->getSettlementDate() }}</td>
                            </tr>

                        </tbody>
                    </table>

                </div>
            </div>
            <table class="table border">
                <tbody>
                    <tr>
                        <th colspan="3" scope="row">Requested Amount in Figure: {{ $advanceRequest->getEstimatedAmount() }}</th>
                    </tr>
                    <tr>
                        <th colspan="3" scope="row" class="text-capitalize">Amount in Words: {{ $digit }}</th>
                    </tr>
                    <tr>
                        <td>Purpose:</td>
                        <td colspan="2">{{ $advanceRequest->purpose }}</td>
                    </tr>
                    <tr>
                        <td colspan="2"></td>
                        <td>Amount (Rs.)</td>
                    </tr>
                    <tr>
                        <td>Activity Code</td>
                        <td class="text-center">Description</td>
                        <td></td>
                    </tr>
                    @foreach ($advanceRequest->advanceRequestDetails as $details)
                        <tr>
                            <td>{{ $details->getActivityCode() }}</td>
                            <td>{{ $details->description }}</td>
                            <td>{{ $details->amount }} </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="2" class="text-end">Total Amount</td>
                        <td>{{ $advanceRequest->getEstimatedAmount() }}</td>
                    </tr>
                </tfoot>
            </table>
            <p>To be filled by Finance Section</p>
            <p>Outstanding Advance, if any: {{ $advanceRequest->outstanding_advance }}</p>


            {{-- @if (!empty($advanceRequest->verifiedLog))
                <div class="mb-3">
                    <div><strong>Finance Comment:</strong></div>
                    <div class="ms-height">
                        <p>{{ $advanceRequest->verifiedLog->log_remarks }}</p>
                    </div>
                </div>
            @endif --}}

            <div class="row mt-4">
                <div class="col-lg-4 mb-4">
                    <div><strong>Checked By:</strong></div>
                    <div><strong>Name:</strong> {{ $advanceRequest->getVerifierName() }} </div>
                    <div><strong>Title:</strong> {{ $advanceRequest->verifier->employee->getDesignationName() }} </div>
                    <div>
                        <strong>Date:</strong> {{ $advanceRequest->verifiedLog ? $advanceRequest->verifiedLog->created_at : '' }}
                    </div>
                </div>
                <div class="col-lg-4 mb-4">
                    <div><strong>Recommended By:</strong></div>
                    <div><strong>Name:</strong> {{ $advanceRequest->recommendedLog ? $advanceRequest->getReviewerName() : '' }} </div>
                    <div><strong>Title:</strong> {{ $advanceRequest->recommendedLog ? $advanceRequest->reviewer->employee->getDesignationName() : '' }} </div>
                    <div>
                        <strong>Date:</strong> {{ $advanceRequest->recommendedLog ? $advanceRequest->recommendedLog->created_at : '' }}
                    </div>
                </div>
                {{-- <div class="col-lg-6 mb-4">
                    <div><strong>Reviewed By:</strong></div>
                    <div><strong>Name:</strong></div>
                    <div><strong>Title:</strong></div>
                    <div>
                        <strong>Date:</strong>
                    </div>
                </div> --}}

                <div class="col-lg-4 mb-4">
                    <div><strong>Approved By:</strong></div>
                    <div><strong>Name:</strong> {{ $advanceRequest->getApproverName() }} </div>
                    <div><strong>Title:</strong> {{ $advanceRequest->approver->employee->getDesignationName() }} </div>
                    <div>
                        <strong>Date:</strong> {{ $advanceRequest->approvedLog ? $advanceRequest->approvedLog->created_at : '' }}
                    </div>
                </div>
            </div>
            <i>Advance should be settled as soon as the task completes but not later than 30 days from the receipt. Any
                outstanding advance should be settled
                before taking any new advance.</i>
            <div>Finance team: Cash Advance released on Date and Time (Button)</div>
        </div>
    </section>

@endsection
