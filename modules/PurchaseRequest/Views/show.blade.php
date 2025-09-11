@php $authUser = auth()->user();@endphp
@extends('layouts.container')

@section('title', 'Purchase Request Detail')

@section('page_css')
    <style>
        .sticky-col {
            position: -webkit-sticky;
            position: sticky;
            background: white;
            z-index: 99;

        }

        .table-container {
            /* height: calc(100vh - 215px); */
            overflow: auto;
        }

        .wrap-col {
            min-width: 300px;
            max-width: 350px;
            white-space: pre-line;
            left: 0px;
        }
    </style>
@endsection

@section('page_js')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#navbarVerticalMenu').find('#purchase-requests-menu').addClass('active');

            var oTable = $('#purchaseRequestItemTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('purchase.requests.items.index', $purchaseRequest->id) }}",
                bFilter: false,
                bPaginate: false,
                bInfo: false,
                columns: [{
                        data: 'item',
                    name: 'item',
                    className: 'sticky-col wrap-col'

                    },
                    {
                        data: 'unit',
                        name: 'unit'
                    },
                    {
                        data: 'quantity',
                        name: 'quantity'
                    },
                    {
                        data: 'unit_price',
                        name: 'unit_price'
                    },
                    {
                        data: 'total_price',
                        name: 'total_price'
                    },
                    {
                        data: 'office',
                        name: 'office'
                    },
                    {
                        data: 'activity',
                        name: 'activity'
                    },
                    {
                        data: 'account',
                        name: 'account'
                    },
                    {
                        data: 'donor',
                        name: 'donor'
                    },
                    {
                        data: 'specification',
                        name: 'specification',
                        className: 'wrap-col',
                    },
                ],
                initComplete: () => {
                    const table = $('#purchaseRequestItemTable');
                    const tableContainer = $('.table-container');
                    const tableHeight = table[0].clientHeight;
                    if (tableHeight > 682) {
                        tableContainer.css('height', 'calc(100vh - 215px)');
                    }
                },
            });
        });
    </script>
@endsection
@section('page-content')


    <div class="page-header pb-3 mb-3 border-bottom">
        <div class="d-flex flex-column flex-lg-row align-items-start align-items-lg-center gap-2">
            <div class="brd-crms flex-grow-1">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item">
                            <a href="{!! route('dashboard.index') !!}" class="text-decoration-none text-dark">Home</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('purchase.requests.index') }}" class="text-decoration-none text-dark">Purchase
                                Requests</a>
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
                        Purchase Request Details
                    </div>
                    @include('PurchaseRequest::Partials.detail')
                </div>
            </div>
            <div class="col-lg-9">
                <div class="card">
                    <div class="card-header fw-bold">
                        Purchase Request Items
                    </div>
                    <div class="card-body">
                                            <div class="table-responsive table-container">
                            <table class="table" id="purchaseRequestItemTable">
                                        <thead class="thead-light sticky-top">
                                    <tr>
                                            <th scope="col" class="sticky-col wrap-col">{{ __('label.item') }}</th>
                                        <th scope="col">{{ __('label.unit') }}</th>
                                        <th scope="col">{{ __('label.quantity') }}</th>
                                        <th scope="col">{{ __('label.estimated-rate') }}</th>
                                        <th scope="col">{{ __('label.estimated-amount') }}</th>
                                        <th scope="col">{{ __('label.office') }}</th>
                                        <th scope="col">{{ __('label.activity') }}</th>
                                        <th scope="col">{{ __('label.account') }}</th>
                                        <th scope="col">{{ __('label.donor') }}</th>
                                        <th scope="col">{{ __('label.specification') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                                <tr>
                                    <td colspan="4">Total Tentative Amount</td>
                                    <td>{{ $purchaseRequest->total_amount }}</td>
                                    <td colspan="3"></td>
                                </tr>
                                {{-- <tr>
                                            <td colspan="4">Balance Budget</td>
                                            <td>{{ $purchaseRequest->balance_budget }}</td>
                                            <td colspan="3"></td>
                                        </tr>
                                        <tr>
                                            <td colspan="4">Commitment Amount</td>
                                            <td>{{ $purchaseRequest->commitment_amount }}</td>
                                            <td colspan="3"></td>
                                        </tr>
                                        <tr>
                                            <td colspan="4">Estimated Balance Amount</td>
                                            <td>{{ $purchaseRequest->estimated_balance_budget }}</td>
                                            <td colspan="3"></td>
                                        </tr>
                                        <tr>
                                            <td colspan="4">Budgeted</td>
                                            <td colspan="4">{{ $purchaseRequest->getBudgeted() }}</td>
                                        </tr>
                                        @if (!$purchaseRequest->budgeted)
                                            <tr>
                                                <td colspan="4">Budget Description</td>
                                                <td colspan="4">{{ $purchaseRequest->budget_description }}</td>
                                            </tr>
                                        @endif --}}
                            </table>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header fw-bold">Purchase Request Budget</div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-responsive">
                                <thead class="thead-light">
                                    <tr>
                                        <th scope="col">District</th>
                                        <th scope="col">Activity</th>
                                        <th scope="col">Total Est. Amount</th>
                                        <th scope="col">Balance Budget</th>
                                        <th scope="col">Commitment Amount</th>
                                        <th scope="col">Estimated Balance Budget</th>
                                        <th scope="col">Budgeted</th>
                                        <th scope="col" >Justification (if not budgeted)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($purchaseRequest->purchaseRequestBudgets as $prBudget)
                                        <tr>
                                            {{-- <td>{{ $prBudget->getDistrict() }}</td> --}}
                                            <td>{{ $prBudget->getOffice() }}</td>
                                            <td>{{ $prBudget->activityCode?->getActivityCode() }}</td>
                                            <td>{{ $purchaseRequest->purchaseRequestItems()->select('total_price')
                                                        ->where('activity_code_id',$prBudget->activity_code_id)
                                                        ->where('office_id',$prBudget->office_id)
                                                        ->sum('total_price') }}</td>
                                            <td>{{ $prBudget->balance_budget }}</td>
                                            <td>{{ $prBudget->commitment_amount }}</td>
                                            <td>{{ $prBudget->estimated_balance_budget }}</td>
                                            <td>{{ (bool) $prBudget->budgeted ? 'Yes' : 'No' }}</td>
                                            <td class="wrap-col">{{ $prBudget->description }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                @include('Attachment::list', [
                    'modelType' => 'Modules\PurchaseRequest\Models\PurchaseRequest',
                    'modelId' => $purchaseRequest->id,
                ])

                <div class="card">
                    <div class="card-header fw-bold">Purchase Process</div>
                    <div class="card-body">
                        <div class="c-b">
                            @foreach ($purchaseRequest->logs as $log)
                                <div class="d-flex py-2 flex-row gap-2 mb-2 border-bottom ">
                                    <div width="40" height="40" class="rounded-circle mr-3 user-icon">
                                        <i class="bi-person-circle fs-4"></i>
                                    </div>
                                    <div class="w-100">
                                        <div
                                            class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                                            <div
                                                class="d-flex flex-column flex-md-row align-items-start align-items-md-center gap-md-2 mb-2 mb-md-0">
                                                <label class="form-label mb-0">{{ $log->getCreatedBy() }}</label>
                                                <span class="badge bg-primary c-badge">{!! $log->createdBy->employee->latestTenure->getDesignationName() !!}</span>
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
