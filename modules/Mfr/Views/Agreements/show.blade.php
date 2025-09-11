@extends('layouts.container')

@section('title', 'View MFR')

@section('page_css')
    <style>

    </style>
@endsection

@section('page_js')
    <script type="text/javascript">
        document.addEventListener('DOMContentLoaded', function(e) {
            $('#navbarVerticalMenu').find('#agreement-index').addClass('active');
        });
    </script>
@endsection
@section('page-content')
    <div class="p-3 m-content">
        <div class="container-fluid">

            <div class="pb-3 mb-3 page-header border-bottom">
                <div class="d-flex align-items-center">
                    <div class="brd-crms flex-grow-1">
                        <nav aria-label="breadcrumb">
                            <ol class="m-0 breadcrumb">
                                <li class="breadcrumb-item">
                                    <a href="{!! route('dashboard.index') !!}" class="text-decoration-none text-dark">Home</a>
                                </li>
                                <li class="breadcrumb-item">
                                    <a href="{{ route('mfr.agreement.index') }}"
                                        class="text-decoration-none text-dark">MFR</a>
                                </li>
                                <li class="breadcrumb-item" aria-current="page">@yield('title')</li>
                            </ol>
                        </nav>
                        <h4 class="m-0 mt-1 lh1 fs-6 text-uppercase fw-bold text-primary">@yield('title')</h4>
                    </div>
                    <div class="add-info justify-content-end">
                        <a class="btn btn-primary btn-sm" href="{!! route('mfr.agreement.edit', [$agreement->id]) !!}">
                            <i class="bi-pencil"></i> Edit Agreeemnt
                        </a>
                        <a class="btn btn-primary btn-sm" href="{!! route('mfr.agreement.show.transactions', [$agreement->id]) !!}">
                            <i class="bi-eye"></i> View Transactions
                        </a>
                    </div>
                </div>
            </div>

            <section class="registration">
                <div class="row">

                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="mb-2 row">
                                    <div class="col-lg-3">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="validationdistrict" class="form-label">Partner
                                                organization
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-lg-9">
                                        <input readonly class="form-control"
                                            value={{ $agreement->partnerOrganization->name }} />
                                    </div>
                                </div>

                                <div class="mb-2 row">
                                    <div class="col-lg-3">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="validationdistrict" class="form-label">District
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-lg-9">
                                        <input readonly class="form-control"
                                            value={{ $agreement->district->district_name }} />
                                    </div>
                                </div>

                                <div class="mb-2 row">
                                    <div class="col-lg-3">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="validationdistrict" class="form-label">Project
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-lg-9">
                                        <input readonly class="form-control" value={{ $agreement->project->title }} />
                                    </div>
                                </div>

                                <div class="mb-2 row">
                                    <div class="col-lg-3">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="validationGrant" class="form-label">Grant Agreement
                                                Number</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-9">
                                        <input type="text"
                                            class="form-control @if ($errors->has('grant_number')) is-invalid @endif"
                                            name="grant_number" value="{{ $agreement->grant_number }}" readonly>
                                    </div>
                                </div>

                                <div class="mb-2 row">
                                    <div class="col-lg-3">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="validationpd" class="form-label">Agreement Period
                                                (from)
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-lg-9">
                                        <input type="text"
                                            class="form-control
                                        @if ($errors->has('effective_from')) is-invalid @endif"
                                            readonly name="effective_from"
                                            value="{{ $agreement->getEffectiveFromDate() }}" />
                                    </div>
                                </div>

                                <div class="mb-2 row">
                                    <div class="col-lg-3">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="validationpd" class="form-label">Agreement Period
                                                (to)
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-lg-9">
                                        <input type="text"
                                            class="form-control
                                        @if ($errors->has('effective_to')) is-invalid @endif"
                                            readonly name="effective_to" value="{{ $agreement->getEffectiveToDate() }}" />
                                    </div>
                                </div>

                                <div class="mb-2 row">
                                    <div class="col-lg-3">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="validationGrant" class="form-label">Approved Budget
                                                NPR</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-9">
                                        <input type="number"
                                            class="form-control @if ($errors->has('approved_budget')) is-invalid @endif"
                                            name="approved_budget" value="{{ $agreement->getApprovedBudget() }}" disabled>
                                    </div>
                                </div>

                            </div>
                        </div>

                        @include('Attachment::list', [
                            'modelType' => 'Modules\Mfr\Models\Agreement',
                            'modelId' => $agreement->id,
                        ])

                        @include('Mfr::Partials.approvedTransactions')
                    </div>
                    <div class="gap-2 border-0 card-footer justify-content-end d-flex">
                        <a href="{!! route('mfr.agreement.index') !!}" class="btn btn-danger btn-sm">Cancel</a>
                    </div>
                </div>

        </div>
    </div>
    </section>

    </div>
    </div>

@stop
