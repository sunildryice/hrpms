@php $authUser = auth()->user();@endphp
@extends('layouts.container')

@section('title', 'GRN Detail')

@section('page_js')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#navbarVerticalMenu').find('#grns-menu').addClass('active');
        });
    </script>
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
                                    <a href="{{ route('grns.index') }}" class="text-decoration-none">Good Receive Notes</a>
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
                                GRN Details
                            </div>
                            @include('Grn::Partials.detail')
                        </div>
                    </div>
                    <div class="col-lg-9">

                        <div class="card">
                            <div class="card-header fw-bold">
                                GRN Items
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="table-responsive">
                                            <table class="table" id="grnItemTable">
                                                <thead class="thead-light">
                                                    <tr>
                                                        <th>{{ __('label.sn') }}</th>
                                                        <th scope="col">{{ __('label.item') }}</th>
                                                        <th scope="col">{{ __('label.specification') }}</th>
                                                        <th scope="col">{{ __('label.unit') }}</th>
                                                        <th scope="col">{{ __('label.quantity') }}</th>
                                                        <th scope="col">{{ __('label.unit-price') }}</th>
                                                        <th scope="col">{{ __('label.amount') }}</th>
                                                        <th scope="col">{{ __('label.discount') }}</th>
                                                        <th scope="col">{{ __('label.vat-amount') }}</th>
                                                        <th scope="col">{{ __('label.total-amount') }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($grn->grnItems as $index => $grnItem)
                                                        <tr>
                                                            <td>{{ $index + 1 }}</td>
                                                            <td>{{ $grnItem->getItemName() }}</td>
                                                            <td>{{ $grnItem->specification }}</td>
                                                            <td>{{ $grnItem->getUnitName() }}</td>
                                                            <td>{{ $grnItem->quantity }}</td>
                                                            <td>{{ $grnItem->unit_price }}</td>
                                                            <td>{{ $grnItem->total_price }}</td>
                                                            <td>{{ $grnItem->discount_amount }}</td>
                                                            <td>{{ $grnItem->vat_amount }}</td>
                                                            <td>{{ $grnItem->total_amount }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                                <tfoot>
                                                    @if ($grn->grnItems->count())
                                                        <tr>
                                                            <td colspan="6">{!! __('label.total-amount') !!}</td>
                                                            <td>{{ $grn->sub_total }}</td>
                                                            <td>{{ $grn->discount_amount }}</td>
                                                            <td>{{ $grn->vat_amount }}</td>
                                                            <td>{{ $grn->total_amount }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="9">{!! __('label.tds-amount-less') !!}</td>
                                                            <td>{{ $grn->tds_amount }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="9">Net Payable Amount</td>
                                                            <td>{{ $grn->grn_amount }}</td>
                                                        </tr>
                                                    @endif
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @include('Grn::Partials.summary')

                    </div>
                </div>
            </section>

        </div>
    </div>
@stop
