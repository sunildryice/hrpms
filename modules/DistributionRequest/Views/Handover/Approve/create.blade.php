@php $authUser = auth()->user();@endphp
@extends('layouts.container')

@section('title', 'Approve Distribution Handover')

@section('page_js')
    <script type="text/javascript">
        $(document).ready(function () {
            $('#navbarVerticalMenu').find('#approve-distribution-handovers-menu').addClass('active');

            var oTable = $('#distributionHandoverItemTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('distribution.requests.handovers.items.index', $distributionHandover->id) }}",
                bFilter: false,
                bPaginate: false,
                bInfo: false,
                columns: [
                    {data: 'item_name', name: 'item_name'},
                    {data: 'unit', name: 'unit'},
                    {data: 'quantity', name: 'quantity'},
                    {data: 'unit_price', name: 'unit_price'},
                    {data: 'total_amount', name: 'total_amount'},
                    {data: 'vat_amount', name: 'vat_amount'},
                    {data: 'net_amount', name: 'net_amount'},
                    {data: 'activity', name: 'activity'},
                    {data: 'account', name: 'account'},
                    {data: 'donor', name: 'donor'},
                ]
            });
        });

        document.addEventListener('DOMContentLoaded', function (e) {
            const form = document.getElementById('distributionRequestApproveForm');
            const fv = FormValidation.formValidation(form, {
                fields: {
                    status_id: {
                        validators: {
                            notEmpty: {
                                message: 'Status is required',
                            },
                        },
                    },
                    log_remarks: {
                        validators: {
                            notEmpty: {
                                message: 'The remarks is required',
                            },
                        },
                    },
                },
                plugins: {
                    trigger: new FormValidation.plugins.Trigger(),
                    bootstrap5: new FormValidation.plugins.Bootstrap5(),
                    submitButton: new FormValidation.plugins.SubmitButton(),
                    defaultSubmit: new FormValidation.plugins.DefaultSubmit(),
                    icon: new FormValidation.plugins.Icon({
                        valid: 'bi bi-check2-square',
                        invalid: 'bi bi-x-lg',
                        validating: 'bi bi-arrow-repeat',
                    }),
                },
            });
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
                                    <a href="{{ route('approve.distribution.requests.index') }}"
                                       class="text-decoration-none">Distribution Requests</a>
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
                                Distribution Request Details
                            </div>
                            @include("DistributionRequest::Partials.detail")
                        </div>
                        <div class="card">
                            <div class="card-header fw-bold">
                                Distribution Handover Details
                            </div>
                            @include("DistributionRequest::Handover.Partials.detail")
                        </div>
                    </div>
                    <div class="col-lg-9">
                        <div class="card">
                            <div class="card-header fw-bold">
                                Distribution Request Items
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="table-responsive">
                                            <table class="table" id="distributionHandoverItemTable">
                                                <thead class="thead-light">
                                                <tr>
                                                    <th scope="col">{{ __('label.item-name') }}</th>
                                                    <th scope="col">{{ __('label.unit') }}</th>
                                                    <th scope="col">{{ __('label.quantity') }}</th>
                                                    <th scope="col">{{ __('label.unit-price') }}</th>
                                                    <th scope="col">{{ __('label.total-price') }}</th>
                                                    <th scope="col">{{ __('label.vat-amount') }}</th>
                                                    <th scope="col">{{ __('label.net-amount') }}</th>
                                                    <th scope="col">{{ __('label.activity') }}</th>
                                                    <th scope="col">{{ __('label.account') }}</th>
                                                    <th scope="col">{{ __('label.donor') }}</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                </tbody>
                                                <tfoot>
                                                <tr>
                                                    <td colspan="6">Total Amount</td>
                                                    <td id="total_amount">{{ $distributionHandover->total_amount }}</td>
                                                    <td colspan="3"></td>
                                                </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header fw-bold">
                                Distribution Handover Process
                            </div>
                            <form action="{{ route('approve.distribution.requests.handovers.store', $distributionHandover->id) }}"
                                  id="distributionRequestApproveForm" method="post"
                                  enctype="multipart/form-data" autocomplete="off">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-lg-5">
                                            @foreach($distributionHandover->logs as $log)
                                                <div class="d-flex py-2 flex-row gap-2 mb-2 border-bottom ">
                                                    <div width="40" height="40"
                                                         class="rounded-circle mr-3 user-icon">
                                                        <i class="bi-person-circle fs-5"></i>
                                                    </div>
                                                    <div class="w-100">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center gap-md-2 mb-2 mb-md-0">
                                                                <span class="me-2">{{ $log->getCreatedBy() }}</span>
                                                                <span
                                                                    class="badge bg-primary c-badge">{!! $log->createdBy->employee->latestTenure->getDesignationName() !!}</span>
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
                                        <div class="col-lg-7">
                                            <div class="row mb-2">
                                                <div class="col-lg-3">
                                                    <div class="d-flex align-items-start h-100">
                                                        <label for="validationleavetype" class="form-label required-label">Status</label>
                                                    </div>
                                                </div>
                                                <div class="col-lg-9">
                                                    <select name="status_id" class="select2 form-control"
                                                            data-width="100%">
                                                        <option value="">Select a Status</option>
                                                        <option value="2">Return to Requester</option>
                                                        <option value="6">Approve</option>
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

                                            <div class="row mb-2">
                                                <div class="col-lg-3">
                                                    <div class="d-flex align-items-start h-100">
                                                        <label for="validationRemarks" class="form-label required-label">Remarks</label>
                                                    </div>
                                                </div>
                                                <div class="col-lg-9">
                                                    <textarea type="text"
                                                              class="form-control @if ($errors->has('log_remarks')) is-invalid @endif"
                                                              name="log_remarks">{{ old('log_remarks') }}</textarea>
                                                    @if ($errors->has('log_remarks'))
                                                        <div class="fv-plugins-message-container invalid-feedback">
                                                            <div
                                                                data-field="log_remarks">{!! $errors->first('log_remarks') !!}</div>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                            {!! csrf_field() !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer border-0 justify-content-end d-flex gap-2">
                                    <button type="submit" name="btn" value="submit" class="btn btn-success btn-sm">
                                        Submit
                                    </button>
                                    <a href="{!! route('approve.distribution.requests.handovers.index') !!}"
                                       class="btn btn-danger btn-sm">Cancel</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </section>

        </div>
    </div>
@stop
