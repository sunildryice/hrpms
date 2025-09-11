@php $authUser = auth()->user();@endphp
@extends('layouts.container')

@section('title', 'Receive Distribution Handover')

@section('page_js')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#navbarVerticalMenu').find('#receive-distribution-handovers-menu').addClass('active');

            var oTable = $('#distributionHandoverItemTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('distribution.requests.handovers.items.index', $distributionHandover->id) }}",
                bFilter: false,
                bPaginate: false,
                bInfo: false,
                columns: [{
                        data: 'item_name',
                        name: 'item_name'
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
                        data: 'total_amount',
                        name: 'total_amount'
                    },
                    {
                        data: 'vat_amount',
                        name: 'vat_amount'
                    },
                    {
                        data: 'net_amount',
                        name: 'net_amount'
                    },
                    {
                        data: 'activity',
                        name: 'activity'
                    },
                    {
                        data: 'account',
                        name: 'account'
                    },
                    {
                        data: 'donor',
                        name: 'donor'
                    },
                ]
            });
        });

        document.addEventListener('DOMContentLoaded', function(e) {

            let receivedDate = null;
            if(@json($distributionHandover->received_date != null)){
                receivedDate = @json($receivedDate);
            }
            const form = document.getElementById('distributionRequestApproveForm');
            const fv = FormValidation.formValidation(form, {
                fields: {
                    received_date: {
                        validators: {
                            notEmpty: {
                                message: 'Received date is required',
                            },
                            date: {
                                format: 'YYYY-MM-DD',
                                message: 'The value is not a valid date',
                            }
                        },
                    },
                    handover_date: {
                        validators: {
                            notEmpty: {
                                message: 'Handover date is required',
                            },
                            date: {
                                format: 'YYYY-MM-DD',
                                message: 'The value is not a valid date',
                            },
                            callback: {
                                message: 'Handover date cannot be less than received date',
                                callback: function(value, validator, $field) {
                                    const receivedDateValue = new Date(form.querySelector(
                                        '[name="received_date"]').value);
                                    return new Date(value.value) >= receivedDateValue;
                                }
                            }
                        }
                    },
                    receiver_remarks: {
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
                            const submitButton = $('[name="btn"]:focus').data('submit');
                            const handoverDate =  (form.querySelector('[name="handover_date"]')).value;
                            return field === 'handover_date' && submitButton === 'save' && handoverDate === '';
                        }
                    }),
                    icon: new FormValidation.plugins.Icon({
                        valid: 'bi bi-check2-square',
                        invalid: 'bi bi-x-lg',
                        validating: 'bi bi-arrow-repeat',
                    }),
                },
            });


            $(form.querySelector('[name="received_date"]')).datepicker({
                langauge: 'en',
                autoHide: true,
                format: 'yyyy-mm-dd',
                endDate: '{!! date('Y-m-d') !!}',
            }).on('change', function(e) {
                fv.revalidateField('received_date');
                if(form.querySelector('[name="handover_date"]').value){
                    fv.revalidateField('handover_date');
                }
                date = e.target.value;
                $(form.querySelector('[name="handover_date"]')).datepicker('setStartDate', date)
            })

            $(form.querySelector('[name="handover_date"]')).datepicker({
                langauge: 'en',
                autoHide: true,
                format: 'yyyy-mm-dd',
                startDate: receivedDate,
                endDate: '{!! date('Y-m-d') !!}',
            }).on('change', function(e) {
                fv.revalidateField('handover_date');
            })
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
                            @include('DistributionRequest::Partials.detail')
                        </div>
                        <div class="card">
                            <div class="card-header fw-bold">
                                Distribution Handover Details
                            </div>
                            @include('DistributionRequest::Handover.Partials.detail')
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
                                                        <td id="total_amount">{{ $distributionHandover->total_amount }}
                                                        </td>
                                                        <td colspan="3"></td>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @include('Attachment::index', [
                            'modelType' => 'Modules\DistributionRequest\Models\DistributionHandover',
                            'modelId' => $distributionHandover->id,
                        ])
                        <div class="card">
                            <div class="card-header fw-bold">
                                Distribution Receive Details
                            </div>
                            <form
                                action="{{ route('receive.distribution.requests.handovers.update', $distributionHandover->id) }}"
                                id="distributionRequestApproveForm" method="post" enctype="multipart/form-data"
                                autocomplete="off">
                                @csrf
                                @method('PUT')
                                <div class="card-body">
                                    <div class="row">

                                        <div class="col-lg-7">
                                            <div class="row mb-2">
                                                <div class="col-lg-3">
                                                    <div class="d-flex align-items-start h-100">
                                                        <label for="validationleavetype"
                                                            class="form-label required-label">Received Date</label>
                                                    </div>
                                                </div>
                                                <div class="col-lg-9">
                                                    <input type="text"
                                                        class="form-control
                                                            @if ($errors->has('received_date')) is-invalid @endif"
                                                        readonly name="received_date"
                                                        value="{{ old('received_date') ?? $distributionHandover->received_date?->format('Y-m-d') }}" />
                                                    @if ($errors->has('received_date'))
                                                        <div class="fv-plugins-message-container invalid-feedback">
                                                            <div data-field="received_date">{!! $errors->first('received_date') !!}
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="row mb-2">
                                                <div class="col-lg-3">
                                                    <div class="d-flex align-items-start h-100">
                                                        <label for="validationleavetype" class="form-label">Handover
                                                            Date</label>
                                                    </div>
                                                </div>
                                                <div class="col-lg-9">
                                                    <input type="text"
                                                        class="form-control
                                                            @if ($errors->has('handover_date')) is-invalid @endif"
                                                        readonly name="handover_date"
                                                        value="{{ old('handover_date') ?? $distributionHandover->handover_date?->format('Y-m-d') }}" />
                                                    @if ($errors->has('handover_date'))
                                                        <div class="fv-plugins-message-container invalid-feedback">
                                                            <div data-field="handover_date">{!! $errors->first('handover_date') !!}
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
                                                    <textarea type="text" class="form-control @if ($errors->has('receiver_remarks')) is-invalid @endif"
                                                        name="receiver_remarks">{{ old('receiver_remarks') ?? $distributionHandover->receiver_remarks }}</textarea>
                                                    @if ($errors->has('receiver_remarks'))
                                                        <div class="fv-plugins-message-container invalid-feedback">
                                                            <div data-field="receiver_remarks">{!! $errors->first('receiver_remarks') !!}</div>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer border-0 justify-content-end d-flex gap-2">
                                    <button type="submit" name="btn" value="save" class="btn btn-primary btn-sm"
                                        data-submit="save">
                                        Save
                                    </button>
                                    <button type="submit" name="btn" value="submit" class="btn btn-success btn-sm"
                                        data-submit="submit">
                                        Submit
                                    </button>
                                    <a href="{!! route('receive.distribution.requests.handovers.index') !!}" class="btn btn-danger btn-sm">Cancel</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </section>

        </div>
    </div>
@stop
