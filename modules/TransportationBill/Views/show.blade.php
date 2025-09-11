@php $authUser = auth()->user();@endphp
@extends('layouts.container')

@section('title', 'Transportation Bill Detail')

@section('page_js')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#navbarVerticalMenu').find('#transportation-bills-menu').addClass('active');

            var oTable = $('#transportationBillItemTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('transportation.bills.details.index', $transportationBill->id) }}",
                bFilter: false,
                bPaginate: false,
                bInfo: false,
                columns: [{
                        data: 'item_description',
                        name: 'item_description'
                    },
                    {
                        data: 'quantity',
                        name: 'quantity'
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
                                <li class="breadcrumb-item">
                                    <a href="{{ route('transportation.bills.index') }}"
                                        class="text-decoration-none text-dark">Transportation
                                        Bills</a>
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
                            @include('TransportationBill::Partials.detail')
                        </div>
                    </div>
                    <div class="col-lg-9">
                        <div class="card">
                            <div class="card-header fw-bold">
                                Transportation Bill Details
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table" id="transportationBillItemTable">
                                        <thead class="thead-light">
                                            <tr>
                                                <th scope="col">{{ __('label.items') }}</th>
                                                <th scope="col">{{ __('label.quantity') }}</th>
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
                                Transportation Bill Process
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-12">
                                        @foreach ($transportationBill->logs as $log)
                                            <div class="d-flex py-2 flex-row gap-2 mb-2 border-bottom ">
                                                <div width="40" height="40" class="rounded-circle mr-3 user-icon">
                                                    <i class="bi-person-circle fs-5"></i>
                                                </div>
                                                <div class="w-100">
                                                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center gap-md-2 mb-2 mb-md-0">
                                                            <span class="me-2">{{ $log->getCreatedBy() }}</span>
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
                </div>
            </section>
@stop
