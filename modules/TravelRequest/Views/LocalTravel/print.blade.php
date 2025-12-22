@extends('layouts.container-report')

@section('title', 'Local Travel Print')
@section('page_css')
    <style>


        table {
            border: 1px solid;
        }

        .table tbody th,
        .table tbody th {
            font-size: 1rem;

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
            <div class="fs-8">{{ $localTravel->getOfficeName() }}</div>
            <div class="fs-8"> Local Travel Reimbursement</div>
        </div>

        <div class="print-header">
            <div class="row">
                <div class="col-lg-8">

                </div>
                <div class="col-lg-4">
                    <div class="d-flex flex-column justify-content-end">
                        <div class="d-flex flex-column justify-content-end brand-logo mb-4 flex-grow-1">
                            <div class="d-flex flex-column justify-content-end float-right">
                                <img src="{{ asset('img/logonp.png') }}" style="width: 200px" alt="" class="align-self-end pe-5">
                            </div>

                        </div>
                    </div>

                </div>
                <div class="col-lg-4">
                    <div class="print-header-info mb-3">
                        <ul class="list-unstyled m-0 p-0 fs-7">
                            <li><span class="fw-bold me-2">Ref.
                                    #</span><span>{{ $localTravel->getLocalTravelNumber() }} :
                                    {{ $localTravel->getTravelRequestNumber() }}</span>
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
                                <td>{{ $localTravel->getTravellerName() }}</td>
                                <th scope="row">Title:</th>
                                <td>{{ $localTravel->getTravellerDesignation() }}</td>
                            </tr>
                            <tr>
                                <th scope="row">Duty Station:</th>
                                <td>{{ $localTravel->getTravellerDutyStation() }}</td>
                                <th scope="row">Phone:</th>
                                <td>{{ $localTravel->getTravellerPhone() }}</td>
                            </tr>
                                <th scope="row">Date:</th>
                                <td colspan="3">{{ $localTravel->submittedLog ? $localTravel->submittedLog->created_at->toFormattedDateString() : '' }}
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">Purpose:</th>
                                <td colspan="3">{{ $localTravel->title }}</td>
                            </tr>

                        </tbody>
                    </table>
                    <table class="table border mb-4">
                        <tbody>
                            <tr>
                                <th>Date</th>
                                <th>Travel Mode</th>
                                <th>Pickup Location</th>
                                <th>{{ __('label.fare') }}</th>
                                <th>Reason</th>
                            </tr>
                            @foreach ($localTravel->localTravelItineraries as $itinerary)
                                <tr>
                                    <td>{{ $itinerary->getTravelDate() }}</td>
                                    <td>{{ $itinerary->getTravelMode() }}</td>
                                    <td>{{ $itinerary->pickup_location }}</td>
                                    <td>{{ $itinerary->total_fare }}</td>
                                    <td>{{ $itinerary->remarks }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="row mt-4">
                        <div class="col-lg-4 mb-4">
                            <div><strong>{{$localTravel->isConsultantTravel() ? "Prepared By: (On Belalf of Consultant)" : 'Requested By:'}}</strong></div>
                            <div><strong>Name:</strong> {{ $localTravel->getRequesterName() }} </div>
                            <div><strong>Title:</strong> {{ $requester->getDesignationName() }} </div>
                            <div><strong>Date:</strong> {{ $localTravel->submittedLog?->created_at }}</div>
                        </div>

                        <div class="col-lg-4 mb-4">
                            <div><strong>Recommended By:</strong></div>
                            <div><strong>Name:</strong>
                                {{ $localTravel->recommendedLog ? $localTravel->getReviewerName() : '' }} </div>
                            <div><strong>Title:</strong>
                                {{ $localTravel->recommendedLog ? $localTravel->reviewer->employee->getDesignationName() : '' }}
                            </div>
                            <div><strong>Date:</strong> {{ $localTravel->recommendedLog?->created_at }} </div>
                        </div>
                        <div class="col-lg-4 mb-4">
                            <div><strong>Authorized By:</strong></div>
                            <div><strong>Name:</strong> {{ $localTravel->getApproverName() }} </div>
                            <div><strong>Title:</strong>
                                {{ $localTravel->approvedLog ? $localTravel->approver->employee->getDesignationName() : '' }}
                            </div>
                            <div><strong>Date:</strong> {{ $localTravel->approvedLog?->created_at }} </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection
