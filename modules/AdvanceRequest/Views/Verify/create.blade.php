{{-- @php $authUser = auth()->user();@endphp --}}
@extends('layouts.container')

@section('title', 'Verify Advance Request')

@section('page_js')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#navbarVerticalMenu').find('#verify-advance-requests-menu').addClass('active');

            var oTable = $('#advanceRequestItemTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('advance.requests.details.index', $advanceRequest->id) }}",
                bFilter: false,
                bPaginate: false,
                bInfo: false,
                columns: [{
                        data: 'amount',
                        name: 'amount'
                    },
                    {
                        data: 'activity',
                        name: 'activity'
                    },
                    {
                        data: 'description',
                        name: 'description'
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
                        data: 'attachment',
                        name: 'attachment'
                    },
                    @if ($authUser->can('update', $advanceRequest))
                        {
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: false
                        },
                    @endif
                ]
            });
        });

        document.addEventListener('DOMContentLoaded', function(e) {
            const form = document.getElementById('advanceRequestApproveForm');
            const fv = FormValidation.formValidation(form, {
                fields: {
                    status_id: {
                        validators: {
                            notEmpty: {
                                message: 'Status is required',
                            },
                        },
                    },
                    approver_id: {
                        validators: {
                            notEmpty: {
                                message: 'Recommended to is required.',
                            },
                        },
                    },
                    outstanding_advance: {
                        validators: {
                            greaterThan: {
                                min: 0,
                                message: 'Outstanding advance should not be less than 0.',
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
                    excluded: new FormValidation.plugins.Excluded({
                        excluded: function(field, ele, eles) {
                            const statusId = parseInt(form.querySelector('[name="status_id"]')
                                .value);
                            return (field === 'approver_id' && statusId !== 11) || (field ===
                                'status_id' && statusId === 11);
                        },
                    }),
                    icon: new FormValidation.plugins.Icon({
                        valid: 'bi bi-check2-square',
                        invalid: 'bi bi-x-lg',
                        validating: 'bi bi-arrow-repeat',
                    }),
                },
            });

            $(form).on('change', '[name="status_id"]', function(e) {
                fv.revalidateField('status_id');
                if (this.value == 11) {
                    $(form).find('#recommendBlock').show();
                } else {
                    $(form).find('#recommendBlock').hide();
                }
            }).on('change', '[name="approver_id"]', function(e) {
                fv.revalidateField('approver_id');
            });
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
                                    <a href="{{ route('verify.advance.requests.index') }}"
                                        class="text-decoration-none text-dark">Advance
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
                                Advance Request Details
                            </div>
                            @include('AdvanceRequest::Partials.advance')
                        </div>
                    </div>
                    <div class="col-lg-9">
                        <div class="card">
                            <div class="card-header fw-bold">
                                Advance Request Items
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table" id="advanceRequestItemTable">
                                        <thead class="thead-light">
                                            <tr>

                                                <th scope="col">Amount</th>
                                                <th scope="col">{{ __('label.activity') }}</th>
                                                <th scope="col">{{ __('label.description') }}</th>
                                                <th scope="col">{{ __('label.account') }}</th>
                                                <th scope="col">{{ __('label.donor') }}</th>
                                                <th scope="col">{{ __('label.attachment') }}</th>
                                                @if ($authUser->can('update', $advanceRequest))
                                                    <th style="width: 150px">{{ __('label.action') }}</th>
                                                @endif
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header fw-bold">
                                Advance Process
                            </div>
                            <form action="{{ route('verify.advance.requests.store', $advanceRequest->id) }}"
                                id="advanceRequestApproveForm" method="post" enctype="multipart/form-data"
                                autocomplete="off">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-lg-5">
                                            @foreach ($advanceRequest->logs as $log)
                                                <div class="d-flex py-2 flex-row gap-2 mb-2 border-bottom ">
                                                    <div width="40" height="40"
                                                        class="rounded-circle mr-3 user-icon">
                                                        <i class="bi-person"></i>
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
                                                        <label for="validationleavetype"
                                                            class="form-label required-label">Status</label>
                                                    </div>
                                                </div>
                                                <div class="col-lg-9">
                                                    <select name="status_id" class="select2 form-control"
                                                        data-width="100%">
                                                        <option value="">Select a Status</option>
                                                        <option value="2">Return to Requester</option>
                                                        <option value="11">Verified</option>
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
                                            <div class="mb-2" id="recommendBlock" style="display: none;">
                                                <div class="row mb-2">
                                                    <div class="col-lg-3">
                                                        <div class="d-flex align-items-start h-100">
                                                            <label for="validationleavetype" class="m-0">Outstanding
                                                                Advance</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-9">
                                                        <input type="number" class="form-control"
                                                            name="outstanding_advance" min=0 value='0.00'>
                                                        @if ($errors->has('outstanding_advance'))
                                                            <div class="fv-plugins-message-container invalid-feedback">
                                                                <div data-field="outstanding_advance">
                                                                    {!! $errors->first('outstanding_advance') !!}
                                                                </div>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row mb-2">
                                                <div class="col-lg-3">
                                                    <div class="d-flex align-items-start h-100">
                                                        <label for="validationRemarks"
                                                            class="form-label required-label">Remarks</label>
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
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer border-0 justify-content-end d-flex gap-2">
                                    <button type="submit" name="btn" value="submit" class="btn btn-success btn-sm">
                                        Submit
                                    </button>
                                    <a href="{!! route('approve.advance.requests.index') !!}" class="btn btn-danger btn-sm">Cancel</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </section>
@stop
