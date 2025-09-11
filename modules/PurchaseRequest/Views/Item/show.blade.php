@extends('layouts.container')

@section('title','Purchase Request Items')

@section('page_js')
    <script type="text/javascript">
     $(document).ready(function() {
            $('#navbarVerticalMenu').find('#approved-purchase-requests-menu').addClass('active');

            $('#prItemsTable').DataTable({});
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
                        <a href="{{ route('approved.purchase.requests.index') }}"
                            class="text-decoration-none text-dark">Purchase
                            Requests</a>
                    </li>
                    <li class="breadcrumb-item" aria-current="page">@yield('title')</li>
                </ol>
            </nav>
            <h4 class="m-0 lh1 mt-1 fs-6 text-uppercase fw-bold text-primary">@yield('title')</h4>
        </div>
    </div>
</div>

    <div class="container-fluid">
        <div class="card shadow-sm border rounded c-tabs-content active" id="item-table" style="overflow: auto;">
            <div class="card-header">
                {{$purchaseRequest->getPurchaseRequestNumber()}}
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-responsive table-bordered" id="prItemsTable">
                        <thead>
                            <tr>
                                <th>{{ __('label.sn') }}</th>
                                <th>PR Items</th>
                                <th>PO Items</th>
                                <th>GRN Items</th>
                                <th>Inventory Items</th>
                               
                            </tr>
                        </thead>
                        

                        <tbody>
                            @foreach ($items as $key=>$item)
                                <tr>
                                    <td>{{ ++$key }}</td>
                                    <td>{{ $item->getItemName() }}</td>
                                    <td>{{ $item->getPOItemName() }}</td>
                                    <td>{!! $item->getGrnItemsCsv() !!}</td>
                                    <td>{!! $item->getInventoryItemCsv()  !!}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@stop
