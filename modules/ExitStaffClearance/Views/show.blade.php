@extends('layouts.container')

@section('title', 'Show Exit Staff Clearance')

@section('page_js')
    <script type="text/javascript">
        $(function() {
            $('#navbarVerticalMenu').find('#staff-clearance-menu').addClass('active');


        });
    </script>
@endsection

@section('page-content')

    <style>
        td,
        th {
            border: 1px solid grey;
            padding: 8px;
            text-align: left;
        }
    </style>


    <div class="pb-3 mb-3 page-header border-bottom">
        <div class="gap-2 d-flex flex-column flex-lg-row align-items-start align-items-lg-center">
            <div class="brd-crms flex-grow-1">
                <nav aria-label="breadcrumb">
                    <ol class="m-0 breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{!! route('dashboard.index') !!}" class="text-decoration-none text-dark">Home</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('staff.clearance.index') }}" class="text-decoration-none text-dark">Exit Staff
                                Clearance</a>
                        </li>
                        <li class="breadcrumb-item" aria-current="page">@yield('title')</li>
                    </ol>
                </nav>
                <h4 class="m-0 mt-1 lh1 fs-6 text-uppercase fw-bold text-primary">@yield('title')</h4>
            </div>
            <div class="add-info justify-content-end">
                @if($authUser->can('print', $staffClearance))
                <a href="{!! route('staff.clearance.print', $staffClearance) !!}" class="btn btn-primary btn-sm" rel="tooltip" title="Print staff clearanc">
                    <i class="bi-printer"></i> Print</a>
                @endif
            </div>
        </div>
    </div>

    <section>

        <div id="employee-details" class="mb-3">
            @include('ExitStaffClearance::Partials.employee-details')
        </div>

        <div id="clearance" class="mb-3">
            @include('ExitStaffClearance::Partials.clearance')
        </div>

        <div id="payable" class="mb-3">
            @include('ExitStaffClearance::Partials.payable')
        </div>

    </section>

    <div class="card">
        <div class="card-header fw-bold">
            Clearance Process
        </div>
        <div class="card-body">
            <div class="row">
                <div @class([
                    'col-lg-12' => true,
                ])>
                @include('ExitStaffClearance::Partials.logs')
                </div>
            </div>
        </div>
    </div>

@stop
