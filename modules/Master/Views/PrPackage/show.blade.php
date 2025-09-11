@extends('layouts.container')

@section('title', 'PR Package Detail')

@section('page_js')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#navbarVerticalMenu').find('#packages-menu').addClass('active');
        });

        var oTable = $('#packageItemsTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('master.packages.items.index', $package->id) }}",
            bFilter: false,
            bPaginate: false,
            bInfo: false,
            columns: [{
                    data: 'item',
                    name: 'item',
                    className: "item-column"
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
            ],
            drawCallback: function() {
                let table = this[0];
                let footer = table.getElementsByTagName('tfoot')[0];
                if (!footer) {
                    footer = document.createElement("tfoot");
                    table.appendChild(footer);
                }

                let estimated_amount = this.api().column(4).data().reduce(function(a, b) {
                    return parseFloat(a) + parseFloat(b);
                }, 0);

                estimated_amount = new Intl.NumberFormat('en-US').format(estimated_amount);

                footer.innerHTML = '';
                footer.innerHTML = `<tr>
                                        <td colspan='4'>Total Tentative Amount</td>
                                        <td colspan='6'>${estimated_amount}</td>
                                    </tr>`;
            },
        });

       
    </script>
@endsection
@section('page_css')
    <style>
        .item-column {
            max-width: 500px;
            overflow: hidden;
            white-space: nowrap;
            text-overflow: ellipsis;
        }
    </style>
@endsection
@section('page-content')
    <div class="m-content p-3">
        <div class="container-fluid">

            <div class="page-header pb-3 mb-3 border-bottom">
                <div class="d-flex align-items-center">
                    <div class="brd-crms flex-grow-1">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item">
                                    <a href="{!! route('dashboard.index') !!}" class="text-decoration-none text-dark">Home</a>
                                </li>
                                <li class="breadcrumb-item">
                                    <a href="{{ route('master.packages.index') }}" class="text-decoration-none">PR Package</a>
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
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-header fw-bold">
                                Package Details
                            </div>
                            <div class="card-body">
                                <div class="row">
                                        <div class="table-responsive">
                                            <table class="display table table-bordered table-condensed">
                                                <tr>
                                                    <td class="gray-bg col-2">Package Name</td>
                                                    <td colspan="3">{{ $package->package_name }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="gray-bg col-2">Package Description</td>
                                                    <td colspan="3">{!! $package->package_description !!}</td>
                                                </tr>
                                            </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header fw-bold">
                                <div class="d-flex align-items-center add-info justify-content-between">
                                    <span>Items</span>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered" id="packageItemsTable">
                                        <thead class="thead-light">
                                            <tr>
                                                <th scope="col">{{ __('label.item') }}</th>
                                                <th scope="col">{{ __('label.unit') }}</th>
                                                <th scope="col">{{ __('label.quantity') }}</th>
                                                <th scope="col">{{ __('label.estimated-rate') }}</th>
                                                <th scope="col">{{ __('label.estimated-amount') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
@stop
