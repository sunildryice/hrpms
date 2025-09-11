@extends('layouts.container')

@section('title', 'Approve Travel Request')

@section('page_js')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#navbarVerticalMenu').find('#approve-ta-request-menu').addClass('active');
        });

        var officialTable = $('#officialTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('ta.requests.official.index', $travel->id) }}",
            bFilter: false,
            bPaginate: false,
            bInfo: false,
            columns: [{
                    data: 'name',
                    name: 'name'
                },
                {
                    data: 'post',
                    name: 'post'
                },
                {
                    data: 'level',
                    name: 'level'
                },
                {
                    data: 'office',
                    name: 'office'
                },
                {
                    data: 'district',
                    name: 'district',
                    orderable: false,
                    searchable: false
                },
            ]
        });

        var itineraryTable = $('#itineraryTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('ta.requests.itinerary.index', $travel->id) }}",
            bFilter: false,
            bPaginate: false,
            bInfo: false,
            columns: [{
                    data: 'travel_date',
                    name: 'travel_date'
                },
                {
                    data: 'place_from',
                    name: 'place_from'
                },
                {
                    data: 'place_to',
                    name: 'place_to'
                },
                {
                    data: 'activities',
                    name: 'activities'
                },
            ]
        });

        var estimateTable = $('#estimateTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('ta.requests.estimate.index', $travel->id) }}",
            bFilter: false,
            bPaginate: false,
            bInfo: false,
            columns: [{
                    data: 'particulars',
                    name: 'particulars'
                },
                {
                    data: 'quantity',
                    name: 'quantity'
                },
                {
                    data: 'days',
                    name: 'days'
                },
                {
                    data: 'unit_price',
                    name: 'unit_price'
                },
                {
                    data: 'total_amount',
                    name: 'total_amount'
                },
                {
                    data: 'account_code',
                    name: 'account_code'
                },
                {
                    data: 'activity_code',
                    name: 'activity_code'
                },
                {
                    data: 'donor_code',
                    name: 'donor_code'
                },
            ],
            drawCallback: function() {
                let table = this[0]
                let footer = table.getElementsByTagName('tfoot')[0];
                if (!footer) {
                    footer = document.createElement('tfoot');
                    table.appendChild(footer);
                }

                let total_amount = this.api().column(4).data().reduce(function(a, b) {
                    return parseFloat(a) + parseFloat(b);
                }, 0);

                total_amount = new Intl.NumberFormat('en-US').format(total_amount);

                footer.innerHTML = '';
                footer.innerHTML = `<tr>
                    <td colspan='3'></td>
                    <td >Total Amount</td>
                    <td colspan='3'>${total_amount}</td>
                </tr>`

            }
        });

        document.addEventListener('DOMContentLoaded', function(e) {
            const form = document.getElementById('travelRequestAddForm');
            const fv = FormValidation.formValidation(form, {
                fields: {
                    status_id: {
                        validators: {
                            notEmpty: {
                                message: 'Status is required',
                            },
                        },
                    },
                    recommended_to: {
                        validators: {
                            notEmpty: {
                                message: 'Recommended to is required.',
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
                            return (field === 'recommended_to' && statusId !== 4) || (field ===
                                'status_id' && statusId === 4);
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
                if (this.value == 4) {
                    $(form).find('#recommendBlock').show();
                } else {
                    $(form).find('#recommendBlock').hide();
                }
            }).on('change', '[name="recommended_to"]', function(e) {
                fv.revalidateField('recommended_to');
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
                            <a href="{{ route('approve.ta.requests.index') }}" class="text-decoration-none text-dark">Travel
                                Request</a>
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
                        Travel Information
                    </div>
                    <div class="card-body">
                        @include('TravelAuthorization::Partials.detail')
                    </div>
                </div>
            </div>
            <div class="col-lg-9">
                <div class="card">
                    <div class="card-header fw-bold">
                        Details of visiting officials
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table" id="officialTable">
                                <thead class="thead-light">
                                    <tr>
                                        <th scope="col">Name</th>
                                        <th scope="col">Post</th>
                                        <th scope="col">Level</th>
                                        <th scope="col">Office</th>
                                        <th scope="col">District</th>
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
                        Itinerary of visit
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table" id="itineraryTable">
                                <thead class="thead-light">
                                    <tr>
                                        <th scope="col">{{ __('label.date') }}</th>
                                        <th scope="col">{{ __('label.from-place') }}</th>
                                        <th scope="col">{{ __('label.to-place') }}</th>
                                        <th scope="col">{{ __('label.activity') }}</th>
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
                        Detail Cost Estimation
                    </div>
                    <div class='card-body'>
                        <div class="table-responsive">
                            <table class="table" id="estimateTable">
                                <thead class="thead-light">
                                    <tr>
                                        <th scope="col">{{ __('label.particulars') }}</th>
                                        <th scope="col">{{ __('label.quantity') }}</th>
                                        <th scope="col">{{ __('label.days') }}</th>
                                        <th scope="col">{{ __('label.rate') }}</th>
                                        <th scope="col">{{ __('label.total-amount') }}</th>
                                        <th scope="col">{{ __('label.activity') }}</th>
                                        <th scope="col">{{ __('label.account') }}</th>
                                        <th scope="col">{{ __('label.donor') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header fw-bold"></div>
                <div class="card-body">
                    <div class="c-b">
                        @foreach ($travel->logs as $log)
                            <div class="d-flex py-2 flex-row gap-2 mb-2 border-bottom ">
                                <div width="40" height="40" class="rounded-circle mr-3 user-icon">
                                    <i class="bi-person-circle fs-5"></i>
                                </div>
                                <div class="w-100">
                                    <div
                                        class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                                        <div
                                            class="d-flex flex-column flex-md-row align-items-start align-items-md-center gap-md-2 mb-2 mb-md-0">
                                            <label class="form-label mb-0">{{ $log->getCreatedBy() }}</label>
                                            <span class="badge bg-primary c-badge">{!! $log->createdBy->employee->latestTenure->getDesignationName() !!}</span>
                                        </div>
                                        <small>{{ $log->getCreatedAt() }}</small>
                                    </div>
                                    <p class="text-justify comment-text mb-0 mt-1">
                                        {{ $log->log_remarks }}
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="border-top pt-4">
                        <form action="{{ route('approve.ta.requests.store', $travel->id) }}" id="travelRequestAddForm"
                            method="post" enctype="multipart/form-data" autocomplete="off">
                            <div class="card-body">
                                <div class="row mb-2">
                                    <div class="col-lg-3">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="validationleavetype"
                                                class="form-label required-label">Status</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-9">
                                        <select name="status_id" class="select2 form-control" data-width="100%">
                                            <option value="">Select a Status</option>
                                            <option value="2" @if (old('status_id') == '2') selected @endif>Return
                                                to Requester
                                            </option>
                                            <option value="8" @if (old('status_id') == '8') selected @endif>Reject
                                            </option>
                                            @if ($travel->status_id == 3)
                                                <option value="4">Recommend</option>
                                            @endif
                                            <option value="6" @if (old('status_id') == '6') selected @endif>
                                                Approve
                                            </option>
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

                                <div class="row mb-2" id="recommendBlock" style="display: none;">
                                    <div class="col-lg-3">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="validationleavetype" class="form-label required-label">Recommended
                                                To</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-9">
                                        <select name="recommended_to" class="select2 form-control" data-width="100%">
                                            <option value="">Select Recommended To</option>
                                            @foreach ($approvers as $approver)
                                                <option value="{{ $approver->id }}">
                                                    {{ $approver->getFullName() }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @if ($errors->has('approver_id'))
                                            <div class="fv-plugins-message-container invalid-feedback">
                                                <div data-field="approver_id">
                                                    {!! $errors->first('approver_id') !!}
                                                </div>
                                            </div>
                                        @endif
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
                            <div class=" justify-content-end d-flex gap-2">
                                <button type="submit" name="btn" value="submit" class="btn btn-success btn-sm">
                                    Submit
                                </button>
                                <a href="{!! route('approve.ta.requests.index') !!}" class="btn btn-danger btn-sm">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@stop
