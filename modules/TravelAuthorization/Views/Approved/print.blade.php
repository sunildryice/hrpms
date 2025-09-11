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
            border-width: 0.1px;
        }

        .table tr th,
        .table tr td {
            padding: 0.25rem 0.75rem;
        }


        .about-table td,
        .about-table th {
            border: none;
        }

        .main-title {
            font-size: 1.5rem;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .text {
            white-space: pre-line;
        }
    </style>
@endsection
@section('page-content')
    <script type="text/javascript">
        window.print();
    </script>

    <section class="p-3 bg-white print-info" id="print-info">
        <div class="mb-3 text-center print-title fw-bold translate-middle">
            <div class="fs-5"> One Heart Worldwide</div>
            {{-- <div class="fs-8">{{ $travel->office->getOfficeName() }}</div> --}}
            <div class="fs-8"> Travel Authorization Request</div>
        </div>

        <div class="print-header">
            <div class="row">
                <div class="col-lg-8">

                </div>
                <div class="col-lg-4">
                    <div class="d-flex flex-column justify-content-end">
                        <div class="mb-4 d-flex flex-column justify-content-end brand-logo flex-grow-1">
                            <div class="float-right d-flex flex-column justify-content-end">
                                <img src="{{ asset('img/logonp.png') }}" alt="" class="align-self-end pe-5">
                            </div>

                        </div>
                    </div>

                </div>
                <div class="col-lg-4">
                    <div class="mb-3 print-header-info">
                        <ul class="p-0 m-0 list-unstyled fs-7">
                            <li><span class="fw-bold me-2">Ref.
                                    #</span><span>{{ $travel->getTravelAuthorizationNumber() }}</span>
                                @if ($travel->status_id == config('constant.CANCELLED_STATUS'))
                                    <span class="text-danger"><strong>({{ $travel->getStatus() }})</strong></span>
                                @endif
                            </li>
                             <li><span class="fw-bold me-2">Office:
                               </span><span>{{ $travel->office->getOfficeName() }}</span>
                                @if ($travel->status_id == config('constant.CANCELLED_STATUS'))
                                    <span class="text-danger"><strong>({{ $travel->getStatus() }})</strong></span>
                                @endif
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="mb-5 print-body">

            <div class="row">
                <div class="col-lg-12">


                    <h4 class="m-0 mt-4 mb-2 lh1 fs-6 text-uppercase fw-bold">Details of visiting officials:
                    </h4>
                    <table class="table mb-4 border officialtable">
                        <thead>
                            <tr>
                                <th>S/N</th>
                                <th>Name</th>
                                <th>Post</th>
                                <th>Level</th>
                                <th>Office</th>
                                <th>District</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($travel->officials as $index => $official)
                                <tr>
                                    <td>{{ ++$index }}</td>
                                    <td>{{ $official->name }}</td>
                                    <td>{{ $official->post }}</td>
                                    <td>{{ $official->level }}</td>
                                    <td>{{ $official->office }}</td>
                                    <td>{{ $official->district->district_name }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <table class="table mb-4 border">
                        <tbody>
                            <tr>
                                <th scope="row" style="width:10%">Objectives:</th>
                                <td>{!! $travel->objectives !!}</td>

                            </tr>
                            <tr>
                                <th scope="row">Outcomes:</th>
                                <td>{!! $travel->outcomes !!}</td>
                            </tr>

                        </tbody>
                    </table>
                    <h4 class="m-0 mt-4 mb-2 lh1 fs-6 text-uppercase fw-bold">Itinerary of visit:
                    </h4>
                    <table class="table mb-4 border officialtable">
                        <thead>
                            <tr>
                                <th>Day</th>
                                <th>Date</th>
                                <th>Place from</th>
                                <th>Place To</th>
                                <th>Activies to be carried out</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($travel->itineraries as $index => $itinerary)
                                <tr>
                                    <td>{{ ++$index }}</td>
                                    <td>{{ $itinerary->travel_date->format('Y-m-d') }}</td>
                                    <td>{{ $itinerary->place_from }}</td>
                                    <td>{{ $itinerary->place_to }}</td>
                                    <td>{{ $itinerary->activities }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <h4 class="m-0 mt-4 mb-2 lh1 fs-6 text-uppercase fw-bold">Estimates:
                    </h4>
                    <table class="table mb-4 border officialtable">
                        <thead>
                            <tr>
                                <th>S.N</th>
                                <th>Particulars</th>
                                <th>Qty</th>
                                <th>Days</th>
                                <th>Rate</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($travel->estimates as $index => $estimate)
                                <tr>
                                    <td>{{ ++$index }}</td>
                                    <td>{{ $estimate->particulars }}</td>
                                    <td>{{ $estimate->quantity }}</td>
                                    <td>{{ $estimate->days }}</td>
                                    <td>{{ $estimate->unit_price }}</td>
                                    <td>{{ $estimate->total_price }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="mt-4 row">
                        @php
                            $estimates = $travel
                                ->estimates()
                                ->select(['id', 'donor_code_id', 'activity_code_id', 'account_code_id'])
                                ->with(['donorCode', 'activityCode', 'accountCode'])
                                ->get();
                            $donors = $estimates->pluck('donorCode')->unique()->implode('description', ',<br>');
                            $accounts = $estimates->pluck('accountCode')->unique()->implode('title', ',<br>');
                            $activity = $estimates->pluck('activityCode')->unique()->implode('title', ',<br>');
                        @endphp
                        <div class="mb-4 col-lg-4">
                            <div><strong>Donor:</strong></div>
                            <div><span class="stack-list">{!! $donors !!}</span></div>
                        </div>
                        <div class="mb-4 col-lg-4">
                            <div><strong>Account Code:</strong></div>
                            <div><span class="stack-list">{!! $accounts !!}</span></div>
                        </div>
                        <div class="mb-4 col-lg-4">
                            <div><strong>Activity Code:</strong></div>
                            <div><span class="stack-list">{!! $activity !!}</div>
                        </div>
                    </div>


                    <div class="mt-4 row">
                        <div class="mb-4 col-lg-4">
                            <div><strong>Requested By:</strong></div>
                            <div><strong>Name:</strong> {{ $travel->getRequesterName() }} </div>
                            <div><strong>Title:</strong> {{ $travel->submittedLog?->getDesignation() }} </div>
                            <div>
                                <strong>Date:</strong>
                                {{ $travel->submittedLog ? $travel->submittedLog->created_at : '' }}
                            </div>
                        </div>
                        <div class="mb-4 col-lg-4">
                            <div><strong>Recommended By:</strong></div>
                            <div><strong>Name:</strong> {{ $travel->getRecommenderName() }} </div>
                            <div><strong>Title:</strong> {{ $travel->recommendedLog?->getDesignation() }}
                            </div>
                            <div><strong>Date:</strong>
                                {{ $travel->recommendedLog ? $travel->recommendedLog->created_at : '' }}
                            </div>
                        </div>
                        <div class="mb-4 col-lg-4">
                            <div><strong>Authorized By:</strong></div>
                            <div><strong>Name:</strong> {{ $travel->getApproverName() }} </div>
                            <div><strong>Title:</strong> {{ $travel->approvedLog?->getDesignation() }}
                            </div>
                            <div><strong>Date:</strong>
                                {{ $travel->approvedLog ? $travel->approvedLog->created_at : '' }} </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection
