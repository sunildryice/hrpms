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

    <section class="print-info bg-white p-3" id="print-info">
        <div class="print-title fw-bold mb-3 translate-middle text-center">
            <div class="fs-5"> HERD International</div>
            <div class="fs-8">{{ $requester->office->getOfficeName() }}</div>
            <div class="fs-8"> Field Visit Report</div>
        </div>

        <div class="print-header">
            <div class="row">
                <div class="col-lg-8"></div>
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
                            <li><span
                                    class="fw-bold me-2">Ref:</span><span>{{ $travelRequest->getTravelRequestNumber() }}</span>
                            </li>
                            <li><span class="fw-bold me-2">Date:</span><span>{{ $travelRequest->getReportDate() }}</span>
                            </li>

                            <li class="mb-3"></li>


                            <li><span class="fw-bold me-2">Prepared
                                    by:</span><span>{{ $travelRequest->getRequesterName() }}
                                </span></li>
                            <li><span class="fw-bold me-2">Designation:</span><span>{{ $requester->getDesignationName() }}
                                </span></li>
                            <li><span class="fw-bold me-2">Submitted
                                    to:</span><span>{{ $travelRequest->getApproverName() }}
                                </span></li>
                            {{-- <li><span
                                        class="fw-bold me-2">Project:</span><span>{{ $travelRequest->getProjectCode() }}</span>
                                </li> --}}
                            {{-- <li><span class="fw-bold me-2">Visit conducted
                                    by:</span><span>{{ $travelRequest->getRequesterName() }} @if ($travelRequest->accompanyingStaffs)
                                        , {{ $travelRequest->getAccompanyingStaffs() }}
                                    @endif
                                </span></li> --}}
                            <li><span class="fw-bold me-2">Travelled
                                    District/Place:</span><span>{{ $travelRequest->final_destination }}</span>
                            </li>
                            <li><span class="fw-bold me-2">Travelling
                                    date:</span><span>{{ $travelRequest->getDepartureDate() }} to
                                    {{ $travelRequest->getReturnDate() }}</span></li>
                            <li><span class="fw-bold me-2">Total Travel
                                    Days:</span><span>{{ $travelRequest->getTotalDays() }}</span></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="print-body mb-5">
            <div class="row">
                <div class="col-lg-12">

                    <div class="my-2 fw-bold">1. General Objective/Purpose of Travel</div>
                    <div class="mb-4" style="min-height: 20px;">{!! nl2br(e($travelReport->objectives)) !!}</div>

                    <div class="my-2 fw-bold">2. Major Achievement</div>
                    <div class="mb-4" style="min-height: 20px;">{!! nl2br(e($travelReport->major_achievement)) !!}</div>

                    <div class="my-2 fw-bold">3. Daily Carried Activities / Completed Tasks</div>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                {{-- <th style="width: 10%">Day</th> --}}
                                <th style="width: 15%">Date</th>
                                {{-- <th>{{ __('label.activity') }}</th> --}}
                                <th>Planned Activities</th>
                                <th>Carried Activities / Completed Tasks</th>
                                <th style="width: 20%">Remarks</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $itineraries = $travelRequest?->travelRequestDayItineraries ?? collect();
                            @endphp

                            @forelse($itineraries as $index => $itinerary)
                                @php
                                    $date = \Carbon\Carbon::parse($itinerary->date);
                                    $weekday = $date->format('l');
                                    $formattedDate = $date->format('d M Y');
                                @endphp

                                <tr>
                                    {{-- <td class="text-center fw-bold">{{ $weekday }}</td> --}}
                                    <td class="text-nowrap">{{ $formattedDate }}</td>
                                    {{-- <td class="text-nowrap">{{ $itinerary?->activity?->title }}</td> --}}
                                    <td class="text-nowrap">{{ $itinerary?->planned_activities }}</td>
                                    <td>{!! $itinerary->completed_tasks ? nl2br(e($itinerary->completed_tasks)): '<em class="text-muted">Not filled</em>' !!}</td>
                                    <td>{!! $itinerary->remarks ? nl2br(e($itinerary->remarks)) : '' !!}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">
                                        No itinerary days available for this travel request.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div class="my-2 fw-bold">4. Not Completed Activities & Reasons</div>
                    <div class="mb-4" style="min-height: 20px;">{!! nl2br(e($travelReport->not_completed_activities)) !!}</div>

                    <div class="my-2 fw-bold">5. Conclusion & Recommendations</div>
                    <div class="mb-5" style="min-height: 100px;">{!! nl2br(e($travelReport->conclusion_recommendations)) !!}</div>

                    <div class="row">
                        <div class="col-lg-6 mb-4">
                            <div><strong>Submitted By:</strong></div>
                            <div><strong>Name:</strong> {{ $travelReport->getReporterName() }}</div>
                            <div><strong>Position:</strong> {{ $requester->getDesignationName() }}</div>
                            <div><strong>Signature:</strong></div>
                            <div><strong>Date:</strong> {{ $dates['submitted_date'] ?? '' }}</div>
                        </div>
                        <div class="col-lg-6 mb-4">
                            <div><strong>Approved By:</strong></div>
                            <div><strong>Name:</strong> {{ $travelReport->getApproverName() }}</div>
                            <div><strong>Position:</strong> {{ $approver?->getDesignationName() }}</div>
                            <div><strong>Signature:</strong></div>
                            <div><strong>Date:</strong> {{ $dates['approved_date'] ?? '' }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
