@extends('layouts.container-report')

@section('title', 'Travel Claim')

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

        .table-title {
            font-weight: bold;
            font-size: 1rem;
            margin-bottom: 0.5rem;
            margin-top: 1.5rem;
        }
    </style>
@endsection

@section('page-content')
    <script>
        window.print();
    </script>

    <section class="print-info bg-white p-3" id="print-info">

        <div class="print-title fw-bold mb-3 translate-middle text-center ">
            <div class="fs-5">HERD International</div>
            <div class="fs-8">{{ $travelClaim->travelRequest->office->getOfficeName() ?? 'Kathmandu Country Office' }}
            </div>
            <div class="fs-8">Travel Claim</div>
        </div>

        <div class="print-header">
            <div class="row">
                <div class="col-lg-8">

                </div>
                <div class="col-lg-4">
                    <div class="d-flex flex-column justify-content-end">
                        <div class="d-flex flex-column justify-content-end brand-logo mb-4 flex-grow-1">
                            <div class="d-flex flex-column justify-content-end float-right">
                                <img src="{{ asset('img/logonp.png') }}" alt="" class="align-self-end pe-5 l-logo"
                                    style="width: 200px;">
                            </div>

                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="print-header-info mb-3">
                        <ul class="list-unstyled m-0 p-0 fs-7">
                            <li> <span class="fw-bold me-2">Ref. #</span>
                                {{ $travelClaim->travelRequest->getTravelRequestNumber() ?? '' }}
                                @if ($travelClaim->status_id && $travelClaim->status_id == config('constant.CANCELLED_STATUS'))
                                    <span class="text-danger ms-2 fw-bold">({{ $travelClaim->getStatus() }})</span>
                                @endif
                            </li>
                        </ul>
                    </div>
                </div>
                {{-- <div class="fs-7 mt-1">
                    <strong>Note:</strong> All Claims must be accompanied by original Travel Authorization (TA).
                </div> --}}
            </div>
        </div>

        <!-- Claimant Info -->
        <table class="table border mb-4">
            <tbody>
                <tr>
                    <th scope="row">Name:</th>
                    <td>{{ $travelClaim->getRequesterName() ?? '' }}</td>
                    <th scope="row">Title:</th>
                    <td>{{ $travelClaim->requester?->employee?->getDesignationName() ?? '' }}</td>
                </tr>
                <tr>
                    <th scope="row">Approved Date:</th>
                    <td>{{ $travelClaim->getApprovedDate() ?? '' }}</td>
                    <th scope="row">Travel No:</th>
                    <td>{{ $travelClaim->travelRequest->getTravelRequestNumber() ?? '' }}</td>
                </tr>
            </tbody>
        </table>

        <!-- TADA / DSA Claim -->
        <strong>TADA Claim</strong>
        <table class="table border mb-4">
            <thead>
                <tr>
                    <th>DATE</th>
                    <th>BREAKFAST</th>
                    <th>LUNCH</th>
                    <th>DINNER</th>
                    <th>INCIDENTAL</th>
                    <th>TOTAL DSA</th>
                    <th>LODGING</th>
                    <th>OTHER</th>
                    <th>TOTAL</th>
                    <th>REMARKS</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($travelClaim->dsaClaims as $dsa)
                    <tr>
                        <td class="text-center">{{ $dsa->departure_date?->format('Y-m-d') ?? '' }}</td>
                        <td class="text-end">{{ number_format($dsa->breakfast ?? 0, 2) }}</td>
                        <td class="text-end">{{ number_format($dsa->lunch ?? 0, 2) }}</td>
                        <td class="text-end">{{ number_format($dsa->dinner ?? 0, 2) }}</td>
                        <td class="text-end">{{ number_format($dsa->incident_cost ?? 0, 2) }}</td>
                        <td class="text-end">{{ number_format($dsa->total_dsa ?? 0, 2) }}</td>
                        <td class="text-end">{{ number_format($dsa->lodging_expense ?? 0, 2) }}</td>
                        <td class="text-end">{{ number_format($dsa->other_expense ?? 0, 2) }}</td>
                        <td class="text-end">{{ number_format($dsa->total_amount ?? 0, 2) }}</td>
                        <td>{{ $dsa->remarks ?? '' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="text-center">No DSA/TADA claims recorded</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Local Travel Claim -->
        <strong>Local Travel Claim</strong>
        <table class="table border mb-4">
            <thead>
                <tr>
                    <th>ACTIVITY</th>
                    <th>DATE</th>
                    <th>PURPOSE</th>
                    <th>FROM</th>
                    <th>TO</th>
                    <th>TOTAL FARE</th>
                    <th>REMARKS</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($travelClaim->localTravels as $lt)
                    <tr>
                        <td>{{ $lt->activity?->title ?? ($lt->activityCode?->getActivityCodeDescription() ?? '') }}</td>
                        <td class="text-center">{{ $lt->getTravelDate() ?? '' }}</td>
                        <td>{{ $lt->purpose ?? '' }}</td>
                        <td>{{ $lt->departure_place ?? '' }}</td>
                        <td>{{ $lt->arrival_place ?? '' }}</td>
                        <td class="text-end">{{ number_format($lt->travel_fare ?? 0, 2) }}</td>
                        <td>{{ $lt->remarks ?? '' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center">No local travel claimed</td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="5" class="text-end">Total Local Travel</th>
                    <td class="text-end">{{ number_format($travelClaim->getTotalLocalTravelAmountAttribute() ?? 0, 2) }}
                    </td>
                    <td></td>
                </tr>
            </tfoot>
        </table>

        <!-- Other Expenses -->
        <strong>Other Expense</strong>
        <table class="table border mb-5">
            <thead>
                <tr>
                    <th>ACTIVITY</th>
                    <th>DATE</th>
                    <th>DESCRIPTION</th>
                    <th class="text-end">AMOUNT</th>
                    <th>INVOICE/BILL NO.</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($travelClaim->expenses as $exp)
                    <tr>
                        <td>{{ $exp->activity?->title ?? ($exp->activityCode?->getActivityCodeDescription() ?? '') }}</td>
                        <td class="text-center">{{ $exp->getExpenseDate() ?? '' }}</td>
                        <td>{{ $exp->expense_description ?? '' }}</td>
                        <td class="text-end">{{ number_format($exp->expense_amount ?? 0, 2) }}</td>
                        <td>{{ $exp->invoice_bill_number ?? '' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center">No other expenses claimed</td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="3" class="text-end">Sub-total Other Expenses</th>
                    <td class="text-end">{{ number_format($travelClaim->total_expense_amount ?? 0, 2) }}</td>
                    <td></td>
                </tr>
                <tr>
                    <th colspan="3" class="text-end">Total Local Travel</th>
                    <td class="text-end">{{ number_format($travelClaim->getTotalLocalTravelAmountAttribute() ?? 0, 2) }}
                    </td>
                    <td></td>
                </tr>
                <tr>
                    <th colspan="3" class="text-end">Total TADA/DSA</th>
                    <td class="text-end">{{ number_format($travelClaim->total_itinerary_amount ?? 0, 2) }}</td>
                    <td></td>
                </tr>
                <tr class="fw-bold">
                    <th colspan="3" class="text-end">Grand Total</th>
                    <td class="text-end">{{ number_format($travelClaim->total_amount ?? 0, 2) }}</td>
                    <td></td>
                </tr>
                <tr>
                    <th colspan="3" class="text-end">Advance Amount</th>
                    <td class="text-end">{{ number_format($travelClaim->advance_amount ?? 0, 2) }}</td>
                    <td></td>
                </tr>
                <tr class="fw-bold">
                    <th colspan="3" class="text-end">Refundable / Reimbursable Amount</th>
                    <td class="text-end">{{ number_format($travelClaim->refundable_amount ?? 0, 2) }}</td>
                    <td></td>
                </tr>
            </tfoot>
        </table>

        <!-- Certification -->
        <div class="mb-5 fs-7">
            <p>
                I certify that the above information is correct and in accordance with the approved Travel
                Authorization.<br>
                I authorize the organization to treat this as the final claim and agree to repay any overpaid amounts.<br>
                Meals/accommodation provided by the office have been deducted accordingly.
            </p>
        </div>

        <!-- Signatures -->
        @if ($travelClaim->reviewedLog?->log_remarks)
            <div class="mb-3">
                <div><strong>Finance Comment:</strong></div>
                <div class="mt-1">
                    <p class="fs-7">{{ $travelClaim->reviewedLog->log_remarks }}</p>
                </div>
            </div>
        @endif

        <div class="row mt-5">
            <div class="col-lg-6 mb-5">
                @if ($requesterSignature)
                    <img src="{{ $requesterSignature }}" alt="Signature of {{ $travelClaim->getRequesterName() }}"
                        class="img-fluid signature-img" style="max-height: 90px; max-width: 240px; object-fit: contain;">
                @else
                    <div class="signature-line mx-auto" style="width: 240px; height: 90px;"></div>
                @endif
                <div><strong>Claimed By:</strong></div>
                <div class="sign-line"></div>
                <div><strong>Name:</strong> {{ $travelClaim->getRequesterName() ?? '' }}</div>
                <div><strong>Title:</strong> {{ $travelClaim->requester?->employee?->getDesignationName() ?? '' }}</div>
                <div><strong>Date:</strong> {{ $travelClaim->submittedLog?->created_at?->format('Y-m-d') ?? '' }}</div>
            </div>

            <div class="col-lg-6 mb-5">
                <div class="mb-2">
                    @if ($reviewerSignature)
                        <img src="{{ $reviewerSignature }}" alt="Signature of {{ $travelClaim->getReviewerName() }}"
                            class="img-fluid signature-img"
                            style="max-height: 90px; max-width: 240px; object-fit: contain;">
                    @else
                        <div class="signature-line mx-auto" style="width: 240px; height: 90px;"></div>
                    @endif
                </div>
                <div><strong>Checked By:</strong></div>
                <div class="sign-line"></div>
                <div><strong>Name:</strong> {{ $travelClaim->getReviewerName() ?? '' }}</div>
                <div><strong>Title:</strong> {{ $travelClaim->reviewer?->employee?->getDesignationName() ?? '' }}</div>
                <div><strong>Date:</strong> {{ $travelClaim->reviewedLog?->created_at?->format('Y-m-d') ?? '' }}</div>
            </div>

            <div class="col-lg-6 mb-5">
                <div class="mb-2">
                    @if ($recommenderSignature)
                        <img src="{{ $recommenderSignature }}" alt="Signature of {{ $travelClaim->getRecommenderName() }}"
                            class="img-fluid signature-img"
                            style="max-height: 90px; max-width: 240px; object-fit: contain;">
                    @else
                        <div class="signature-line mx-auto" style="width: 240px; height: 90px;"></div>
                    @endif
                </div>
                <div><strong>Certified By:</strong></div>
                <div class="sign-line"></div>
                <div><strong>Name:</strong> {{ $travelClaim->getRecommenderName() ?? '' }}</div>
                <div><strong>Title:</strong> {{ $travelClaim->recommender?->employee?->getDesignationName() ?? '' }}</div>
                <div><strong>Date:</strong> {{ $travelClaim->recommendedLog?->created_at?->format('Y-m-d') ?? '' }}</div>
            </div>

            <div class="col-lg-6 mb-5">
                <div class="mb-2">
                    @if ($approverSignature)
                        <img src="{{ $approverSignature }}" alt="Signature of {{ $travelClaim->getApproverName() }}"
                            class="img-fluid signature-img"
                            style="max-height: 90px; max-width: 240px; object-fit: contain;">
                    @else
                        <div class="signature-line mx-auto" style="width: 240px; height: 90px;"></div>
                    @endif
                </div>
                <div><strong>Approved By:</strong></div>
                <div class="sign-line"></div>
                <div><strong>Name:</strong> {{ $travelClaim->getApproverName() ?? '' }}</div>
                <div><strong>Title:</strong> {{ $travelClaim->approver?->employee?->getDesignationName() ?? '' }}</div>
                <div><strong>Date:</strong> {{ $travelClaim->approvedLog?->created_at?->format('Y-m-d') ?? '' }}</div>
            </div>
        </div>

    </section>
@endsection
