@extends('layouts.container-report')

@section('title', 'Travel Authorization Print')
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
    </style>
@endsection
@section('page-content')
    <script type="text/javascript">
        window.print();
    </script>

    <section class="print-info bg-white p-3" id="print-info">
        <div class="print-title fw-bold mb-3 translate-middle text-center ">
            <div class="fs-5"> HERD International</div>
            <div class="fs-8">{{ $travelRequest->office->getOfficeName() }}</div>
            <div class="fs-8"> Travel Authorization</div>
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
                            <li><span class="fw-bold me-2">Ref.
                                    #</span><span>{{ $travelRequest->gettravelRequestNumber() }}</span>
                                @if ($travelRequest->status_id == config('constant.CANCELLED_STATUS'))
                                    <span class="text-danger"><strong>({{ $travelRequest->getStatus() }})</strong></span>
                                @endif
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="print-body mb-5">

            <div class="row">
                <div class="col-lg-12">
                    <table class="table border mb-4">
                        <tbody>
                            <tr>
                                <th scope="row">Name:</th>
                                <td>{{ $travelRequest->getTravellerName() }}</td>
                                <th scope="row">Title:</th>
                                <td>{{ $travelRequest->getTravellerDesignation() }}</td>
                            </tr>
                            <tr>
                                <th scope="row">Person Type:</th>
                                <td>{{ $travelRequest->getTravellerDepartment() }}</td>
                                <th scope="row">Address:</th>
                                <td>{{ $travelRequest->getTravellerAddress() }}</td>

                            </tr>
                            <tr>
                                <th scope="row">Duty Station:</th>
                                <td>{{ $travelRequest->getTravellerDutyStation() }}</td>
                                <th scope="row">Phone:</th>
                                <td>{{ $travelRequest->getTravellerPhone() }}</td>

                            </tr>
                            <tr>
                                <th scope="row">Accompanying Staff:</th>
                                <td colspan="3">{{ $travelRequest->getAccompanyingStaffs() }}</td>

                            </tr>
                            <tr>
                                <th scope="row">Purpose of travel:</th>
                                <td colspan="3">{{ $travelRequest->purpose_of_travel }}</td>
                            </tr>

                        </tbody>
                    </table>
                    <table class="table border mb-4">
                        <tbody>
                            <tr>
                                <th scope="row">Departure Date: :</th>
                                <td>{{ $travelRequest->getDepartureDate() }}</td>
                                <th scope="row">Return Date:</th>
                                <td>{{ $travelRequest->getReturnDate() }}</td>
                                <th scope="row">Issue Date:</th>
                                <td>{{ $travelRequest->approvedLog ? $travelRequest->approvedLog->created_at->toFormattedDateString() : '' }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <table class="table border mb-4">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Itinerary</th>
                                <th>Activity Code</th>
                                {{-- <th>Account Code</th>
                                <th>Donor Code</th> --}}
                                <th>Mode of Travel</th>
                                <th>Description</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($travelRequest->travelRequestItineraries as $itenrary)
                                <tr>
                                    <td>{{ $itenrary->getDepartureDate() }} - {{ $itenrary->getArrivalDate() }}</td>
                                    <td>{{ $itenrary->departure_place }} - {{ $itenrary->arrival_place }}</td>
                                    <td>{{ $itenrary->activityCode->getActivityCode() }}</td>
                                    {{-- <td>{{ $itenrary->accountCode->getAccountCode() }}</td>
                                    <td>{{ $itenrary->donorCode->description }}</td> --}}
                                    <td>{{ $itenrary->getTravelModes() }}</td>
                                    <td>{{ $itenrary->description }}</td>
                                </tr>
                            @endforeach
                            <tr>
                                <th scope="row">Special Instructions
                                    </td>
                                <td colspan="4"></td>
                            </tr>
                            <tr>
                                <th scope="row">Excess Baggage, Air Kilos:
                                    </td>
                                <td colspan="4"></td>
                            </tr>
                            <tr>
                                <th scope="row">Estimated Cost
                                    </td>
                                <td></td>
                                {{-- <td></td>
                                <td></td> --}}
                                <th scope="col">Rate</th>
                                <th scope="col">Days</th>
                                <th scope="col">NRs</th>
                            </tr>
                            @foreach ($travelRequest->travelRequestItineraries as $dsa)
                                <tr>
                                    <td colspan="2">{{ $dsa->description }}
                                    </td>
                                    <td>{{ $dsa->dsa_unit_price }}</td>
                                    <td>{{ $dsa->getOvernights() }}</td>
                                    <td>{{ $dsa->dsa_total_price }}</td>
                                </tr>
                            @endforeach
                            @if ($travelRequest->travelRequestEstimate)
                                @php
                                    $total = $travelRequest->travelRequestItineraries->sum('dsa_total_price') + $travelRequest->travelRequestEstimate->estimated_air_fare + $travelRequest->travelRequestEstimate->estimated_vehicle_fare + $travelRequest->travelRequestEstimate->miscellaneous_amount;
                                @endphp
                                <tr>
                                    <td colspan="2">Excess Baggage</td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td colspan="2">Air Fare</td>
                                    <td></td>
                                    <td>{{ $travelRequest->travelRequestEstimate->estimated_air_fare }}</td>
                                </tr>
                                <tr>
                                    <td colspan="2">Vehicle Fare</td>
                                    <td></td>
                                    <td>{{ $travelRequest->travelRequestEstimate->estimated_vehicle_fare }}</td>
                                </tr>
                                <tr>
                                    <td colspan="2">Hotel Accommodation</td>
                                    <td></td>
                                    <td>{{ $travelRequest->travelRequestEstimate->estimated_hotel_accommodation }}</td>
                                </tr>
                                <tr>
                                    <td colspan="2">Airport Taxi</td>
                                    <td></td>
                                    <td>{{ $travelRequest->travelRequestEstimate->estimated_airport_taxi }}</td>
                                </tr>
                                <tr>
                                    <td colspan="2">Miscellaneous</td>
                                    <td></td>
                                    <td>{{ $travelRequest->travelRequestEstimate->miscellaneous_amount }}</td>
                                </tr>
                                <tr>
                                    <td colspan="2">Event/Activities Cost</td>
                                    <td></td>
                                    <td>{{ $travelRequest->travelRequestEstimate->estimated_event_activities_cost }}</td>
                                </tr>
                            @else
                                @php
                                    $total = $travelRequest->travelRequestItineraries->sum('dsa_total_price');
                                @endphp
                                <tr>
                                    <td colspan="2">Excess Baggage</td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td colspan="2">Air Fare</td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td colspan="2">Vehicle Fare</td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td colspan="2">Hotel Accommodation</td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td colspan="2">Airport Taxi</td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td colspan="2">Miscellaneous</td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td colspan="2">Event/Activities Cost</td>
                                    <td></td>
                                    <td></td>
                                </tr>
                            @endif

                            <tr>
                                <th scope="row" colspan="3" class="text-end">Total:</th>
                                <td>{{ $total }}</td>
                            </tr>
                        </tbody>
                    </table>
                    <table class="table border mb-4">
                        <thead>
                            <tr>
                                <th scope="col" colspan="2">Requested Advance</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <th scope="row">Less: Previous Advance (if Yes)</th>
                                <td>{{ $travelRequest->travelRequestEstimate ? $travelRequest->travelRequestEstimate->advance_amount : '' }}
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">Less: Direct Payment by Office</th>
                                <td></td>
                            </tr>
                            <tr>
                                <th scope="row">Net Advance</th>
                                <td>{{ $travelRequest->travelRequestEstimate ? $travelRequest->travelRequestEstimate->advance_amount : '' }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <table class="table border mb-4">
                        <tbody>
                            <tr>
                                {{-- <th scope="row">Activity Code</th>
                            <td> From Finance</td>
                            <th scope="row">Account Code</th>
                            <td> From Finance</td> --}}
                                <th scope="row">Funding Amount</th>
                                <td>-</td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="row mt-4">
                        <div class="col-lg-4 mb-4">
                            <div><strong>{{$travelRequest->isConsultantTravel() ? "Prepared By: (On Belalf of Consultant)" : 'Requested By:'}}</strong></div>
                            <div><strong>Name:</strong> {{ $travelRequest->getRequesterName() }} </div>
                            <div><strong>Title:</strong> {{ $requester->getDesignationName() }} </div>
                            <div>
                                <strong>Date:</strong>
                                {{ $travelRequest->submittedLog ? $travelRequest->submittedLog->created_at : '' }}
                            </div>
                        </div>
                        <div class="col-lg-4 mb-4">
                            <div><strong>Recommended By:</strong></div>
                            <div><strong>Name:</strong> {{ $travelRequest->getReviewerName() }} </div>
                            <div><strong>Title:</strong> {{ $travelRequest->reviewer->employee->getDesignationName() }}
                            </div>
                            <div><strong>Date:</strong>
                                {{ $travelRequest->recommendedLog ? $travelRequest->recommendedLog->created_at : '' }}
                            </div>
                        </div>
                        <div class="col-lg-4 mb-4">
                            <div><strong>Authorized By:</strong></div>
                            <div><strong>Name:</strong> {{ $travelRequest->getApproverName() }} </div>
                            <div><strong>Title:</strong> {{ $travelRequest->approver->employee->getDesignationName() }}
                            </div>
                            <div><strong>Date:</strong>
                                {{ $travelRequest->approvedLog ? $travelRequest->approvedLog->created_at : '' }} </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection
