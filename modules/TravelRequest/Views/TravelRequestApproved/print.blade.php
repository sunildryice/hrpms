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
                                <img src="{{ asset('img/logonp.png') }}" alt="" class="align-self-end pe-5 l-logo"
                                    style="width: 200px;">
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
                    <table class="table border mb-3">
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
                                <th scope="row">Project:</th>
                                <td colspan="3">{{ $travelRequest->getProjectCode() }}</td>
                            </tr>
                            <tr>
                                <th scope="row">Purpose of travel:</th>
                                <td colspan="3">{{ $travelRequest->purpose_of_travel }}</td>
                            </tr>

                        </tbody>
                    </table>
                    <table class="table border mb-3">
                        <tbody>
                            <tr>
                                <th scope="row">Departure Date:</th>
                                <td>{{ $travelRequest->getDepartureDate() }}</td>
                                <th scope="row">Return Date:</th>
                                <td>{{ $travelRequest->getReturnDate() }}</td>
                                <th scope="row">Issue Date:</th>
                                <td>{{ $travelRequest->approvedLog ? $travelRequest->approvedLog->created_at->toFormattedDateString() : '' }}
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    {{-- Travel Itinerary --}}
                    <div class="fw-bold mb-2">Travel Itinerary</div>
                    <table class="table border mb-3">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Activity</th>
                                <th>Planned Activities</th>
                                <th class="text-center">Accommodation</th>
                                <th class="text-center">Air Ticket</th>
                                <th class="text-center">Vehicle</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($travelRequest->travelRequestDayItineraries as $dayItinerary)
                                <tr>
                                    <td>{{ $dayItinerary->formatted_date }}</td>
                                    <td>{{ $dayItinerary->activity?->title }}</td>
                                    <td>{{ $dayItinerary->planned_activities }}</td>
                                    <td class="text-center">{{ $dayItinerary->accommodation ? 'Yes' : 'No' }}</td>
                                    <td class="text-center">{{ $dayItinerary->air_ticket ? 'Yes' : 'No' }}</td>
                                    <td class="text-center">{{ $dayItinerary->vehicle ? 'Yes' : 'No' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    {{-- Travel Advance Request --}}
                    <div class="fw-bold mb-2">Travel Advance Request</div>
                    <table class="table border mb-3">
                        <thead>
                            <tr>
                                <th scope="col">{{ __('label.estimated-dsa') }}</th>
                                <th scope="col">{{ __('label.estimated-air-fare') }}</th>
                                <th scope="col">{{ __('label.estimated-vehicle-fare') }}</th>
                                <th scope="col">{{ __('label.estimated-hotel-accommodation') }}</th>
                                <th scope="col">{{ __('label.estimated-airport-taxi') }}</th>
                                <th scope="col">{{ __('label.miscellaneous-amount') }}</th>
                                <th scope="col">{{ __('label.estimated-event-activities-cost') }}</th>
                                <th scope="col">{{ __('label.miscellaneous-remarks') }}</th>
                                <th scope="col">{{ __('label.total-amount') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if ($travelRequest->travelRequestEstimate)
                                <tr>
                                    <td>{{ $travelRequest->travelRequestEstimate->estimated_dsa }}</td>
                                    <td>{{ $travelRequest->travelRequestEstimate->estimated_air_fare }}</td>
                                    <td>{{ $travelRequest->travelRequestEstimate->estimated_vehicle_fare }}</td>
                                    <td>{{ $travelRequest->travelRequestEstimate->estimated_hotel_accommodation }}</td>
                                    <td>{{ $travelRequest->travelRequestEstimate->estimated_airport_taxi }}</td>
                                    <td>{{ $travelRequest->travelRequestEstimate->miscellaneous_amount }}</td>
                                    <td>{{ $travelRequest->travelRequestEstimate->estimated_event_activities_cost }}</td>
                                    <td>{{ $travelRequest->travelRequestEstimate->miscellaneous_remarks }}</td>
                                    <td>{{ $travelRequest->travelRequestEstimate->total_amount }}</td>
                                </tr>
                            @else
                                <tr>
                                    <td colspan="9" class="text-center">No estimate available.</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>

                    <div class="row mt-4">
                        <div class="col-lg-4 mb-3">
                            <div>
                                <strong>{{ $travelRequest->isConsultantTravel() ? 'Prepared By: (On Behalf of Consultant)' : 'Requested By:' }}</strong>
                            </div>
                            {{-- <div class="mb-2"> --}}
                            @if ($requesterSignature)
                                <img src="{{ $requesterSignature }}"
                                    alt="Signature of {{ $travelRequest->getRequesterName() }}"
                                    class="img-fluid signature-img"
                                    style="max-height: 90px; max-width: 240px; object-fit: contain;">
                            @else
                                {{-- <div class="signature-line mx-auto" style="width: 240px; height: 90px;"></div> --}}
                            @endif
                            {{-- </div> --}}
                            <div><strong>Name:</strong> {{ $travelRequest->getRequesterName() }} </div>
                            <div><strong>Title:</strong> {{ $requester->getDesignationName() }} </div>
                            <div>
                                <strong>Date:</strong>
                                {{ $travelRequest->submittedLog ? $travelRequest->submittedLog->created_at->format('Y-m-d') : '' }}
                            </div>
                        </div>
                        <div class="col-lg-4 mb-3">
                            <div><strong>Recommended By:</strong></div>
                            {{-- <div class="mb-2"> --}}
                            @if ($reviewerSignature)
                                <img src="{{ $reviewerSignature }}"
                                    alt="Signature of {{ $travelRequest->getReviewerName() }}"
                                    class="img-fluid signature-img"
                                    style="max-height: 90px; max-width: 240px; object-fit: contain;">
                            @else
                                {{-- <div class="signature-line mx-auto" style="width: 240px; height: 90px;"></div> --}}
                            @endif
                            {{-- </div> --}}
                            <div><strong>Name:</strong> {{ $travelRequest->getReviewerName() }} </div>
                            <div><strong>Title:</strong> {{ $travelRequest->reviewer->employee->getDesignationName() }}
                            </div>
                            <div><strong>Date:</strong>
                                {{ $travelRequest->recommendedLog ? $travelRequest->recommendedLog->created_at->format('Y-m-d') : '' }}
                            </div>
                        </div>
                        <div class="col-lg-4 mb-3">
                            <div><strong>Authorized By:</strong></div>
                            {{-- <div class="mb-2"> --}}
                            @if ($approverSignature)
                                <img src="{{ $approverSignature }}"
                                    alt="Signature of {{ $travelRequest->getApproverName() }}"
                                    class="img-fluid signature-img"
                                    style="max-height: 90px; max-width: 240px; object-fit: contain;">
                            @else
                                {{-- <div class="signature-line mx-auto" style="width: 240px; height: 90px;"></div> --}}
                            @endif
                            {{-- </div> --}}
                            <div><strong>Name:</strong> {{ $travelRequest->getApproverName() }} </div>
                            <div><strong>Title:</strong> {{ $travelRequest->approver->employee->getDesignationName() }}
                            </div>
                            <div><strong>Date:</strong>
                                {{ $travelRequest->approvedLog ? $travelRequest->approvedLog->created_at->format('Y-m-d') : '' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
