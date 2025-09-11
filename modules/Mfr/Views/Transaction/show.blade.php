@extends('layouts.container')
@section('title', 'View Transaction')

@section('page_js')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#navbarVerticalMenu').find('#agreement-index').addClass('active');
        });

    </script>
@endsection
@section('page-content')


    <div class="pb-3 mb-3 page-header border-bottom">
        <div class="d-flex align-items-center">
            <div class="brd-crms flex-grow-1">
                <nav aria-label="breadcrumb">
                    <ol class="m-0 breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{!! route('dashboard.index') !!}" class="text-decoration-none text-dark">Home</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('mfr.agreement.show.transactions', $transaction->agreement->id) }}" class="text-decoration-none text-dark">Show
                                Transaction
                            </a>
                        </li>
                        <li class="breadcrumb-item" aria-current="page">@yield('title')</li>
                    </ol>
                </nav>
                <h4 class="m-0 mt-1 lh1 fs-6 text-uppercase fw-bold text-primary">@yield('title')</h4>
            </div>
            <div class="mb-2">
                {{-- @if (auth()->user()->can('create', $agreement)) --}}
                    <a type="button" class="btn btn-primary btn-sm open-transaction-modal-form"
                        href="{{ route('mfr.transaction.print', $transaction->id) }}">
                        <i class="bi bi-printer"></i>
                        Print
                    </a>
                {{-- @endif --}}
            </div>
        </div>
    </div>

    <section class="registration">
        <div class="row">
            <div class="col-lg-3">
                <div class="card">
                    <div class="card-header fw-bold">
                        Agreement Details
                    </div>
                    @include('Mfr::Partials.agreement-detail')
                </div>
                <div class="card">
                    <div class="card-header fw-bold">
                        Current Transaction Details
                    </div>
                    @include('Mfr::Partials.transaction-detail')
                </div>
            </div>
            <div class="col-lg-9">
                @include('Mfr::Partials.transactions')

                <div class="card">
                    <div class="card-header">Process</div>
                    <div class="card-body">
                        <div class="c-b">
                            @foreach ($transaction->logs as $log)
                                <div class="flex-row gap-2 py-2 mb-2 d-flex border-bottom">
                                    <div width="40" height="40" class="mr-3 rounded-circle user-icon">
                                        <i class="bi-person"></i>
                                    </div>
                                    <div class="w-100">
                                        <div
                                            class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                                            <div
                                                class="mb-2 d-flex flex-column flex-md-row align-items-start align-items-md-center gap-md-2 mb-md-0">
                                                <label class="mb-0 form-label">{{ $log->getCreatedBy() }}</label>
                                                <span class="badge bg-primary c-badge">
                                                    {!! $log->createdBy->employee->latestTenure->getDesignationName() !!}
                                                </span>
                                            </div>
                                            <small>{{ $log->created_at->diffForHumans() }}</small>
                                        </div>
                                        <p class="mt-1 mb-0 text-justify comment-text">
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
