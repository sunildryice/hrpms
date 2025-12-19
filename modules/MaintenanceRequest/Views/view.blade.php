@php $authUser = auth()->user();@endphp
@extends('layouts.container')

@section('title', 'Maintenance Request')

@section('page_css')
    <style>
        .wrap-text {
            white-space: normal !important;
            word-wrap: break-word;
            word-break: break-word;
        }
    </style>
@endsection

@section('page_js')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#navbarVerticalMenu').find('#maintenance-requests-menu').addClass('active');

            var oTable = $('#maintenanceRequestItemTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('maintenance.requests.items.index', $maintenanceRequest->id) }}",
                bFilter: false,
                bPaginate: false,
                bInfo: false,
                columns: [{
                        data: 'item_name',
                        name: 'item_name',
                        className: 'wrap-text'
                    },
                    {
                        data: 'problem',
                        name: 'problem'
                    },
                    // {
                    //     data: 'activity',
                    //     name: 'activity'
                    // },
                    // {
                    //     data: 'account',
                    //     name: 'account'
                    // },
                    // {
                    //     data: 'donor',
                    //     name: 'donor'
                    // },
                    // {
                    //     data: 'estimated_cost',
                    //     name: 'estimated_cost'
                    // },
                    {
                        data: 'replacement_good_needed',
                        name: 'replacement_good_needed'
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
                            <a href="{{ route('maintenance.requests.index') }}"
                                class="text-decoration-none text-dark">Maintenance Requests</a>
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
                    <div class="card-header fw-bold">Maintanance Request Details</div>
                    @include('MaintenanceRequest::Partials.detail')
                </div>
            </div>
            <div class="col-lg-9">
                <div class="card">
                    <div class="card-header fw-bold">
                        Maintenance Request Items
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table" id="maintenanceRequestItemTable">
                                <thead class="thead-light">
                                    <tr>
                                        <th scope="col">{{ __('label.item-name') }}</th>
                                        <th scope="col">{{ __('label.problem') }}</th>
                                        {{-- <th scope="col">{{ __('label.activity') }}</th>
                                        <th scope="col">{{ __('label.account') }}</th>
                                        <th scope="col">{{ __('label.donor') }}</th>
                                        <th scope="col">{{ __('label.estimate') }}</th> --}}
                                        <th scope="col">{{ __('label.replacement-good-needed') }}</th>
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
                        Maintenance Request Process
                    </div>
                    <div class="card-body">
                        <div class="c-b">
                            @foreach ($maintenanceRequest->logs as $log)
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
                                                <span class="badge bg-primary c-badge">{!! $log->createdBy->employee->latestTenure->getDesignationName() !!}</span>
                                            </div>
                                            <small
                                                title="{{ $log->created_at }}">{{ $log->created_at->format('M d, Y h:i A') }}</small>
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
