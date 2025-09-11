@extends('layouts.container')

@section('title', 'View Paid Travel Claim')

@section('page_js')
    <script type="text/javascript">
        document.addEventListener('DOMContentLoaded', function(e) {
            $('#navbarVerticalMenu').find('#paid-travel-claims-menu').addClass('active');
        });

        var expenseTable = $('#expenseTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('travel.claims.expenses.index', $travelClaim->id) }}",
            bFilter: false,
            bPaginate: false,
            bInfo: false,
            columns: [{
                    data: 'activity',
                    name: 'activity'
                },
                {
                    data: 'expense_date',
                    name: 'expense_date'
                },
                {
                    data: 'expense_description',
                    name: 'expense_description'
                },
                {
                    data: 'expense_amount',
                    name: 'expense_amount'
                },
                {
                    data: 'attachment',
                    name: 'attachment'
                },
            ]
        });

        var itineraryTable = $('#itineraryTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('travel.claims.itineraries.index', $travelClaim->id) }}",
            bFilter: false,
            bPaginate: false,
            bInfo: false,
            columns: [{
                    data: 'departure_date',
                    name: 'departure_date'
                },
                {
                    data: 'departure_place',
                    name: 'departure_place'
                },
                {
                    data: 'arrival_date',
                    name: 'arrival_date'
                },
                {
                    data: 'arrival_place',
                    name: 'arrival_place'
                },
                {
                    data: 'overnights',
                    name: 'overnights'
                },
                {
                    data: 'dsa_unit_price',
                    name: 'dsa_unit_price'
                },
                {
                    data: 'percentage_charged',
                    name: 'percentage_charged'
                },
                {
                    data: 'total_amount',
                    name: 'total_amount'
                },
                {
                    data: 'attachment',
                    name: 'attachment'
                },
            ]
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
                        <li class="breadcrumb-item">
                            <a href="{{ route('paid.travel.claims.index') }}" class="text-decoration-none text-dark">Paid
                                Travel
                                Claims
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
                        Travel Request Details
                    </div>
                    @include('TravelRequest::Partials.detail')
                </div>
                <div class="card">
                    <div class="card-header fw-bold">
                        Travel Claim Details
                    </div>
                    @include('TravelRequest::Partials.claim-detail')
                </div>
                @if (isset($travelClaim->paid_at))
                    <div class="card">
                        <div class="card-header fw-bold">
                            Payment Details
                        </div>
                        @include('TravelRequest::Partials.paymentDetails')
                    </div>
                @endif
            </div>
            <div class="col-lg-9">
                <div>
                    <div class="card">
                        <div class="card-header fw-bold">
                            Travel Expenses
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="expenseTable">
                                    <thead class="thead-light">
                                        <tr>
                                            <th scope="col">{{ __('label.activity') }}</th>
                                            <th scope="col">{{ __('label.date') }}</th>
                                            <th scope="col">{{ __('label.description') }}</th>
                                            <th scope="col">{{ __('label.amount') }}</th>
                                            <th scope="col">{{ __('label.attachment') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="3">{{ __('label.sub-total') }}</td>
                                            <td id="total_expense_amount">
                                                {{ $travelClaim->total_expense_amount }}</td>
                                            <td></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>

                    </div>
                    <div class="card">
                        <div class="card-header fw-bold">
                            Travel Itineraries
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="itineraryTable">
                                    <thead class="thead-light">
                                        <tr>
                                            <th scope="col" colspan="2" class="text-center">
                                                {{ __('label.date') }}</th>
                                            <th scope="col" colspan="2" class="text-center">
                                                {{ __('label.destination') }}</th>
                                            <th scope="col" rowspan="2">{{ __('label.overnights') }}
                                            </th>
                                            <th scope="col" rowspan="2">{{ __('label.dsa-rate') }}
                                            </th>
                                            <th scope="col" rowspan="2">{{ __('label.percentage') }}
                                            </th>
                                            <th scope="col" rowspan="2">{{ __('label.total-dsa') }}
                                            </th>
                                            <th scope="col" rowspan="2">{{ __('label.attachment') }}
                                            </th>
                                        </tr>
                                        <tr>
                                            <th scope="col">{{ __('label.from') }}</th>
                                            <th scope="col">{{ __('label.to') }}</th>
                                            <th scope="col">{{ __('label.from') }}</th>
                                            <th scope="col">{{ __('label.to') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="7">{{ __('label.sub-total') }}</td>
                                            <td id="total_itinerary_amount">
                                                {{ $travelClaim->total_itinerary_amount }}</td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td colspan="7">{{ __('label.grand-total') }}</td>
                                            <td id="grand_total_amount">
                                                {{ $travelClaim->total_amount }}
                                            </td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td colspan="7">{{ __('label.advance-amount') }}</td>
                                            <td id="advance_amount">
                                                {{ $travelClaim->advance_amount }}
                                            </td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td colspan="7">
                                                {{ __('label.refundable-reimbursable-amount') }}</td>
                                            <td id="refundable_amount">
                                                {{ $travelClaim->refundable_amount }}
                                            </td>
                                            <td></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header fw-bold">Process</div>
                        <div class="card-body">
                            <div class="c-b">
                                @foreach ($travelClaim->logs as $log)
                                    <div class="d-flex py-2 flex-row gap-2 mb-2 border-bottom ">
                                        <div width="40" height="40" class="rounded-circle mr-3 user-icon">
                                            <i class="bi-person-circle fs-5"></i>
                                        </div>
                                        <div class="w-100">
                                            <div
                                                class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                                                <div
                                                    class="d-flex flex-column flex-md-row align-items-start align-items-md-center gap-md-2 mb-2 mb-md-0">
                                                    <label class="form-label mb-0">{{ $log->getCreatedBy() }}</label>
                                                    <span class="badge bg-primary c-badge">
                                                        {!! $log->createdBy->employee->latestTenure->getDesignationName() !!}
                                                    </span>
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
        </div>
    </section>
@stop
