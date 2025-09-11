@extends('layouts.container')

@section('title', 'Approve Exit Staff Clearance')

@section('page_js')
    <script type="text/javascript">
        document.addEventListener("DOMContentLoaded", function() {
            $('#navbarVerticalMenu').find('#staff-clearance-approve-menu').addClass('active');
            const clearanceId = "{{ $staffClearance->id }}";
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
                            <a href="{{ route('staff.clearance.approve.index') }}"
                                class="text-decoration-none text-dark">Approve Staff
                                Clearance</a>
                        </li>
                        <li class="breadcrumb-item" aria-current="page">@yield('title')</li>
                    </ol>
                </nav>
                <h4 class="m-0 mt-1 lh1 fs-6 text-uppercase fw-bold text-primary">@yield('title')</h4>
            </div>
        </div>
    </div>

    <section>

        <div id="employee-details" class="mb-3">
            @include('ExitStaffClearance::Partials.employee-details')
        </div>

        <div class="card">
            <div class="card-body">
                <span class="fw-bold">
                    The above mentioned staff member is leaving OHW and under clearance So Please indicate outstanding, if
                    any against his / her name.
                </span>
            </div>
        </div>

        <div id="clearance" class="mb-3">
            @include('ExitStaffClearance::Partials.clearance')
        </div>

        <div id="payable" class="mb-3">
            @include('ExitStaffClearance::Partials.payable')
        </div>
    </section>

    <section>
        @if ($staffClearance->status_id == config('constant.RETURNED_STATUS'))
            <div class="col-lg-6">
                <div class="p-3 mb-2 border row">
                    <div>
                        <div class="d-flex align-items-start h-100">
                            <span class="fw-bold" style="text-decoration: underline">Remarks:</span>
                        </div>
                    </div>
                    <div class="mt-2">
                        <span>{{ $staffClearance->getLatestRemark() }}</span>
                    </div>
                </div>
            </div>
        @endif
    </section>

    <section>
        <div class="card">
            <div class="card-header fw-bold">
                Clearance Process
            </div>
            <div class="card-body">
                <div class="row">
                    <div @class([
                        'col-lg-6' => true,
                    ])>
                        @include('ExitStaffClearance::Partials.logs')
                    </div>
                    <div class="col-lg-6">
                        <form action="{{ route('staff.clearance.approve.store', $staffClearance->id) }}" id="editForm"
                            method="post" enctype="multipart/form-data" autocomplete="off" {{-- onsubmit="return confirm('Have you saved all the forms? Are you sure to submit?');" --}}>
                            <input type="hidden" name="staff_clearance_id" value="{{ $staffClearance->id }}">
                            <div class="mb-2 row">
                                <div class="col-lg-3">
                                    <div class="d-flex align-items-start h-100">
                                        <label for="status_id" class="form-label required-label">Status </label>
                                    </div>
                                </div>
                                <div class="col-lg-9">
                                    <select name="status_id" class="select2 form-control" data-width="100%">
                                        <option value="">Select Status</option>
                                        <option value="{{ config('constant.VERIFIED_STATUS') }}"
                                            {{ old('status_id') == config('constant.VERIFIED_STATUS') ? 'selected' : '' }}>
                                            Return</option>
                                        <option value="{{ config('constant.APPROVED_STATUS') }}"
                                            {{ old('status_id') == config('constant.APPROVED_STATUS') ? 'selected' : '' }}>
                                            Approve</option>
                                    </select>
                                    @if ($errors->has('status_id'))
                                        <div class="fv-plugins-message-container invalid-feedback">
                                            <div data-field="status_id">
                                                {!! $errors->first('status_id') !!}
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="mb-2 row">
                                <div class="col-lg-3">
                                    <div class="d-flex align-items-start h-100">
                                        <label for="log_remarks" class="form-label required-label">Remarks </label>
                                    </div>
                                </div>
                                <div class="col-lg-9">
                                    <textarea type="text" class="form-control @if ($errors->has('log_remarks')) is-invalid @endif" name="log_remarks">{{ old('log_remarks') }}</textarea>
                                    @if ($errors->has('log_remarks'))
                                        <div class="fv-plugins-message-container invalid-feedback">
                                            <div data-field="log_remarks">{!! $errors->first('log_remarks') !!}</div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            {!! csrf_field() !!}
                            <div class="gap-2 border-0 justify-content-end d-flex">
                                <button type="submit" name="btn" value="submit" class="btn btn-success btn-sm">
                                    Submit
                                </button>
                                <a href="{!! route('staff.clearance.index') !!}" class="btn btn-danger btn-sm">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

@stop
