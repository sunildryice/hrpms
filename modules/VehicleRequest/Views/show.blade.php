@extends('layouts.container')

@section('title', 'Show Vehicle Request')

@section('page_css')

@endsection
@section('page_js')
    <script type="text/javascript">
        $(document).ready(function () {
            $('#navbarVerticalMenu').find('#vehicle-requests-menu').addClass('active');
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
                                <a href="{{ route('vehicle.requests.index') }}" class="text-decoration-none text-dark">Vehicle
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
            <div class="card">
                <div class="card-header fw-bold">Vehicle Request Details</div>
                @include('VehicleRequest::Partials.detail')
            </div>

            <div class="card">
                <div class="card-header fw-bold">Vehicle Request Process</div>
                <div class="card-body">
                        <div class="c-b">
                            @include('VehicleRequest::Partials.log')
                        </div>
                </div>
            </div>
        </section>
@stop
