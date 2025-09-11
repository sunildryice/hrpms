@extends('layouts.container')

@section('title', 'Edit Distribution Handover')

@section('page_js')
    <script type="text/javascript">
        document.addEventListener('DOMContentLoaded', function(e) {
            $('#navbarVerticalMenu').find('#distribution-handovers-menu').addClass('active');
            const form = document.getElementById('distributionHandoverEditForm');
            const fv = FormValidation.formValidation(form, {
                fields: {
                    local_level_id: {
                        validators: {
                            notEmpty: {
                                message: 'Local level is required',
                            },
                        },
                    },
                    to_name: {
                        validators: {
                            notEmpty: {
                                message: 'To is required',
                            },
                        },
                    },
                    date_of_handover: {
                        validators: {
                            notEmpty: {
                                message: 'Date of handover is required',
                            },
                        },
                    },
                    letter_body: {
                        validators: {
                            notEmpty: {
                                message: 'Letter body is required',
                            },
                        },
                    },
                    approver_id: {
                        validators: {
                            notEmpty: {
                                message: 'Approver is required',
                            }
                        }
                    },
                    receiver_id: {
                        validators: {
                            notEmpty: {
                                message: 'Receiver is required',
                            }
                        }
                    }
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

            $(form).find('[name="date_of_handover"]').datepicker({
                language: 'en-GB',
                autoHide: true,
                format: 'yyyy-mm-dd',
                zIndex: 2048,
            }).on('change', function(e) {
                fv.revalidateField('date_of_handover');
            });


        });

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
                                    <a href="{{ route('distribution.requests.handovers.index') }}"
                                        class="text-decoration-none">Distribution
                                        Handover</a>
                                </li>
                                <li class="breadcrumb-item" aria-current="page">@yield('title')</li>
                            </ol>
                        </nav>
                        <h4 class="m-0 mt-1 lh1 fs-6 text-uppercase fw-bold text-primary">@yield('title')</h4>
                    </div>
                </div>
            </div>

            <section class="registration">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <form action="{{ route('distribution.requests.handovers.update', $distributionHandover->id) }}"
                                id="distributionHandoverEditForm" method="post" enctype="multipart/form-data"
                                autocomplete="off">
                                <div class="card-body">
                                    <div class="mb-2 row">
                                        <div class="col-lg-2">
                                            <div class="d-flex align-items-start h-100">
                                                <label for="validationdistributiontype" class="m-0">District</label>
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <input type="text" class="form-control" readonly="readonly"
                                                value="{{ $distributionHandover->getDistrictName() }}" />
                                        </div>
                                        <div class="col-lg-2">
                                            <div class="d-flex align-items-start h-100">
                                                <label for="validationdistributiontype" class="m-0">Office</label>
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <input type="text" class="form-control" readonly="readonly"
                                                value="{{ $distributionHandover->distributionRequest->getOfficeName() }}" />
                                        </div>
                                    </div>
                                    <div class="mb-2 row">
                                        <div class="col-lg-2">
                                            <div class="d-flex align-items-start h-100">
                                                <label for="validationdistributiontype" class="m-0">Project</label>
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <input type="text" class="form-control" readonly="readonly"
                                                value="{{ $distributionHandover->getProjectCode() }}" />
                                        </div>
                                        <div class="col-lg-2">
                                            <div class="d-flex align-items-start h-100">
                                                <label for="validationdistributiontype" class="m-0">Health
                                                    Facility</label>
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <input type="text" class="form-control" readonly="readonly"
                                                value="{{ $distributionHandover->health_facility_name }}" />
                                        </div>
                                    </div>

                                    <div class="mb-2 row">
                                        <div class="col-lg-2">
                                            <div class="d-flex align-items-start h-100">
                                                <label for="validationdistributiontype" class="m-0 required-label">Date of
                                                    Handover</label>
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <input type="text" class="form-control" readonly="readonly"
                                                name="date_of_handover"
                                                value="{{ $distributionHandover->date_of_handover }}" />
                                        </div>
                                    </div>

                                    <div class="mb-2 row">
                                        <div class="col-lg-2">
                                            <div class="d-flex align-items-start h-100">
                                                <label for="validationRemarks" class="form-label required-label">Local
                                                    Level</label>
                                            </div>
                                        </div>
                                        <div class="col-lg-10">
                                            <select type="text" name="local_level_id"
                                                class="form-control select2 @if ($errors->has('local_level_id')) is-invalid @endif">
                                                <option value="">Select Local Level</option>
                                                @foreach ($localLevels as $localLevel)
                                                    <option value="{{ $localLevel->id }}"
                                                        @if ($localLevel->id == $distributionHandover->local_level_id) selected @endif>
                                                        {{ $localLevel->getLocalLevelName() }}</option>
                                                @endforeach
                                            </select>
                                            @if ($errors->has('local_level_id'))
                                                <div class="fv-plugins-message-container invalid-feedback">
                                                    <div data-field="local_level_id">{!! $errors->first('local_level_id') !!}</div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="mb-2 row">
                                        <div class="col-lg-2">
                                            <div class="d-flex align-items-start h-100">
                                                <label for="validationRemarks" class="form-label required-label">To</label>
                                            </div>
                                        </div>
                                        <div class="col-lg-10">
                                            <input type="text" name="to_name"
                                                class="form-control @if ($errors->has('to_name')) is-invalid @endif"
                                                value="{{ old('to_name') ?: $distributionHandover->to_name }}">
                                            @if ($errors->has('to_name'))
                                                <div class="fv-plugins-message-container invalid-feedback">
                                                    <div data-field="to_name">{!! $errors->first('to_name') !!}</div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="mb-2 row">
                                        <div class="col-lg-2">
                                            <div class="d-flex align-items-start h-100">
                                                <label for="validationRemarks" class="form-label required-label">Letter
                                                    Body</label>
                                            </div>
                                        </div>
                                        <div class="col-lg-10">
                                            <textarea type="text" rows="15" class="form-control @if ($errors->has('letter_body')) is-invalid @endif"
                                                name="letter_body">{{ old('letter_body') ?: $distributionHandover->letter_body }}</textarea>
                                            @if ($errors->has('remarks'))
                                                <div class="fv-plugins-message-container invalid-feedback">
                                                    <div data-field="remarks">{!! $errors->first('letter_body') !!}</div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="mb-2 row">
                                        <div class="col-lg-2">
                                            <div class="d-flex align-items-start h-100">
                                                <label for="validationRemarks" class="m-0">CC</label>
                                            </div>
                                        </div>
                                        <div class="col-lg-10">
                                            <textarea type="text" rows="3" class="form-control @if ($errors->has('cc_name')) is-invalid @endif"
                                                name="cc_name">{!! old('cc_name') ?: $distributionHandover->cc_name !!}</textarea>
                                            @if ($errors->has('cc_name'))
                                                <div class="fv-plugins-message-container invalid-feedback">
                                                    <div data-field="cc_name">{!! $errors->first('cc_name') !!}</div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="mb-2 row">
                                        <div class="col-lg-2">
                                            <div class="d-flex align-items-start h-100">
                                                <label for="validationRemarks"
                                                    class="form-label required-label">Approver</label>
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            @php $selectedApproverId = old('approver_id') ?: $distributionHandover->approver_id; @endphp
                                            <select name="approver_id"
                                                class="select2 form-control
                                                @if ($errors->has('approver_id')) is-invalid @endif"
                                                data-width="100%">
                                                <option value="">Select an Approver</option>
                                                @foreach ($approvers as $approver)
                                                    <option value="{{ $approver->id }}"
                                                        {{ $approver->id == $selectedApproverId ? 'selected' : '' }}>
                                                        {{ $approver->full_name }}</option>
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
                                    <div class="mb-2 row">
                                        <div class="col-lg-2">
                                            <div class="d-flex align-items-start h-100">
                                                <label for="validationRemarks" class="form-label required-label">Send
                                                    to</label>
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            @php $selectedReceiverId = old('receiver_id') ?: $distributionHandover->receiver_id; @endphp
                                            <select name="receiver_id"
                                                class="select2 form-control
                                                @if ($errors->has('receiver_id')) is-invalid @endif"
                                                data-width="100%">
                                                <option value="">Select a Receiver</option>
                                                @foreach ($receivers as $receiver)
                                                    <option value="{{ $receiver->getUserId() }}"
                                                        {{ $receiver->getUserId() == $selectedReceiverId ? 'selected' : '' }}>
                                                        {{ $receiver->full_name }}</option>
                                                @endforeach
                                            </select>
                                            @if ($errors->has('receiver_id'))
                                                <div class="fv-plugins-message-container invalid-feedback">
                                                    <div data-field="receiver_id">
                                                        {!! $errors->first('receiver_id') !!}
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    {!! csrf_field() !!}
                                    {!! method_field('PUT') !!}
                                    <div class="gap-2 border-0 card-footer justify-content-end d-flex">
                                        <button type="submit" name="btn" value="save"
                                            class="btn btn-primary btn-sm">
                                            Update
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="card">
                                        <div class="card-header fw-bold">
                                            Distribution Request Items
                                        </div>
                                        <div class="p2">
                                            <div class="d-flex align-items-center add-info justify-content-end">
                                            </div>
                                        </div>
                                        <div class="container-fluid-s">
                                            <div class="card">
                                                <div class="card-body">
                                                    <div class="table-responsive">
                                                        <table class="table" id="distributionHandoverItemTable">
                                                            <thead class="thead-light">
                                                                <tr>
                                                                    <th scope="col">{{ __('label.item-name') }}</th>
                                                                    <th scope="col">{{ __('label.unit') }}</th>
                                                                    <th scope="col">{{ __('label.quantity') }}</th>
                                                                    <th scope="col">{{ __('label.unit-price') }}</th>
                                                                    <th scope="col">{{ __('label.total-price') }}</th>
                                                                    <th scope="col">{{ __('label.activity') }}</th>
                                                                    <th scope="col">{{ __('label.account') }}</th>
                                                                    <th scope="col">{{ __('label.donor') }}</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                            </tbody>
                                                            <tfoot>
                                                                <tr>
                                                                    <td colspan="4">Total Amount</td>
                                                                    <td id="total_amount">
                                                                        {{ $distributionHandover->total_amount }}</td>
                                                                    <td colspan="3"></td>
                                                                </tr>
                                                            </tfoot>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                                <div class="gap-2 border-0 card-footer justify-content-end d-flex">
                                    <button type="submit" name="btn" value="submit" class="btn btn-success btn-sm">
                                        Submit
                                    </button>
                                    <a href="{!! route('distribution.requests.handovers.index') !!}" class="btn btn-danger btn-sm">Cancel</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </section>

        </div>
    </div>

@stop
