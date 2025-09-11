@php $authUser = auth()->user();@endphp
@extends('layouts.container')

@section('title', 'Local Travel Detail: '. $localTravel->getLocalTravelNumber())

@section('page_js')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#navbarVerticalMenu').find('#local-travel-reimbursements-menu').addClass('active');

            var oTable = $('#localTravelItineraryTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('local.travel.reimbursements.itineraries.index', $localTravel->id) }}",
                bFilter: false,
                bPaginate: false,
                bInfo: false,
                columns: [{
                        data: 'travel_date',
                        name: 'travel_date'
                    },
                    {
                        data: 'purpose',
                        name: 'purpose'
                    },
                    {
                        data: 'travel_mode',
                        name: 'travel_mode'
                    },
                    {
                        data: 'total_distance',
                        name: 'total_distance'
                    },
                    {
                        data: 'departure_place',
                        name: 'departure_place'
                    },
                    {
                        data: 'arrival_place',
                        name: 'arrival_place'
                    },
                    {
                        data: 'total_fare',
                        name: 'total_fare'
                    },
                    {
                        data: 'activity',
                        name: 'activity',
                    },
                    {
                        data: 'account',
                        name: 'account',
                    },
                    {
                        data: 'remarks',
                        name: 'remarks'
                    },
                ]
            });
        });
    </script>
@endsection
@section('page-content')


            <div class="page-header pb-3 mb-3 border-bottom">
                <div class="d-flex align-items-center">
                    <div class="brd-crms flex-grow-1">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item">
                                    <a href="{!! route('dashboard.index') !!}" class="text-decoration-none text-dark">Home</a>
                                </li>
                                @php
                                    $url = url()->previous();
                                    $route = app('router')->getRoutes($url)->match(app('request')->create($url))->getName();
                                    $parentRoute = $route == 'approved.local.travel.reimbursements.index' ? 'approved.local.travel.reimbursements.index' : 'local.travel.reimbursements.index';
                                @endphp
                                <li class="breadcrumb-item">
                                    <a href="{{ route($parentRoute) }}" class="text-decoration-none text-dark">
                                        Local Travel Reimbursements
                                    </a>
                                </li>
                                <li class="breadcrumb-item" aria-current="page">@yield('title')</li>
                            </ol>
                        </nav>
                        <h4 class="m-0 lh1 mt-1 fs-6 text-uppercase fw-bold text-primary">@yield('title')</h4>
                    </div>
                </div>
            </div>

            <section class="registration">
                <div class="row">
                    <div class="col-lg-3">
                        <div class="card">
                            <div class="card-header fw-bold">
                                Local Travel Reimbursement Details
                            </div>
                            @include('TravelRequest::LocalTravel.Partials.detail')
                        </div>
                        @isset($localTravel->paid_at)
                            <div class="card">
                                <div class="card-header fw-bold">
                                    Payment Details
                                </div>
                                @include('TravelRequest::LocalTravel.Partials.paymentDetails')
                            </div>
                        @endisset
                    </div>
                    <div class="col-lg-9">
                        <div class="card">
                            <div class="card-header fw-bold">
                                Travel Details
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table" id="localTravelItineraryTable">
                                        <thead class="thead-light">
                                            <tr>
                                                <th scope="col">{{ __('label.date') }}</th>
                                                <th scope="col">{{ __('label.purpose') }}</th>
                                                <th scope="col">{{ __('label.mode') }}</th>
                                                <th scope="col">{{ __('label.km') }}</th>
                                                <th scope="col">{{ __('label.from') }}</th>
                                                <th scope="col">{{ __('label.to') }}</th>
                                                <th scope="col">{{ __('label.fare') }}</th>
                                                <th scope="col">{{ __('label.activity') }}</th>
                                                <th scope="col">{{ __('label.account') }}</th>
                                                <th scope="col">{{ __('label.remarks') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header fw-bold">
                                Local Travel Process
                            </div>
                            <div class="card-body">
                                    <div class="c-b">
                                        @foreach ($localTravel->logs as $log)
                                            <div class="d-flex py-2 flex-row gap-2 mb-2 border-bottom ">
                                                <div width="40" height="40" class="rounded-circle mr-3 user-icon">
                                                    <i class="bi-person-circle fs-5"></i>
                                                </div>
                                                <div class="w-100">
                                                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                                                        <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center gap-md-2 mb-2 mb-md-0">
                                                            <label class="form-label mb-0">{{ $log->getCreatedBy() }}</label>
                                                            <span
                                                                class="badge bg-primary c-badge">{!! $log->createdBy->employee->latestTenure->getDesignationName() !!}</span>
                                                        </div>
                                                        <small title="{{$log->created_at}}">{{ $log->created_at->format('M d, Y h:i A') }}</small>
                                                    </div>
                                                    <p class="text-justify comment-text mb-0 mt-1">
                                                        {{ $log->log_remarks }}
                                                    </p>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                            </div>

                        </div>
                    </div>
                </div>
            </section>
@stop
