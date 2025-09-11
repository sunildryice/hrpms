@php $authUser = auth()->user();@endphp
@extends('layouts.container')

@section('title', 'Receive Good Request Assets/Items')

@section('page_js')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#navbarVerticalMenu').find('#asset-receive-menu').addClass('active');

            var oTable = $('#goodRequestItemTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('good.requests.items.index', $goodRequest->id) }}",
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
                        data: 'assigned_quantity',
                        name: 'assigned_quantity'
                    },
                    {
                        data: 'specification',
                        name: 'specification'
                    },
                ]
            });

            const form = document.getElementById('approveForm');
            const fv = FormValidation.formValidation(form, {
                fields: {
                    log_remarks: {
                        validators: {
                            notEmpty: {
                                message: 'Remarks is required.'
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
                        validating: 'bi bi-arrow-repeat'
                    }),
                }
            });
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
                                <li class="breadcrumb-item" aria-current="page">@yield('title')</li>
                            </ol>
                        </nav>
                        <h4 class="m-0 mt-1 lh1 fs-6 text-uppercase fw-bold text-primary">@yield('title')</h4>
                    </div>
                </div>
            </div>

            <section class="registration">
                <form action="{{ route('receive.good.requests.direct.assign.store', $goodRequest->id) }}" method="POST"
                    id="approveForm">
                    @csrf
                    <div class="row">
                        <div class="col-lg-3">
                            <div class="card">
                                <div class="card-header fw-bold">
                                    Good Request Details
                                </div>
                                @include('GoodRequest::Partials.detail')
                            </div>
                        </div>
                        <div class="col-lg-9">
                            <div class="card">
                                <div class="card-header fw-bold">
                                    Good Request Items
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="table-responsive">
                                                <table class="table" id="goodRequestItemTable">
                                                    <thead class="thead-light">
                                                        <tr>
                                                            <th scope="col">{{ __('label.item-name') }}</th>
                                                            <th scope="col">{{ __('label.unit') }}</th>
                                                            <th scope="col">{{ __('label.quantity') }}</th>
                                                            <th scope="col">{{ __('label.assigned-quantity') }}</th>
                                                            <th scope="col">{{ __('label.specification') }}</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @if ($goodRequestAssets->isNotEmpty())
                                <div class="card">
                                    <div class="card-header fw-bold">
                                        Assigned Assets
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="table-responsive">
                                                    <table class="table" id="assignedAssetsTable">
                                                        <thead class="thead-light">
                                                            <tr>
                                                                <th scope="col">{{ __('label.sn') }}</th>
                                                                <th scope="col">{{ __('label.asset-number') }}</th>
                                                                <th scope="col">{{ __('label.serial-number') }}</th>
                                                                <th scope="col">{{ __('label.item-name') }}</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($goodRequestAssets as $key => $goodRequestAsset)
                                                                <tr>
                                                                    <td>{{ ++$key }}</td>
                                                                    <td>{{ $goodRequestAsset->asset->getAssetNumber() }}
                                                                    </td>
                                                                    <td>{{ $goodRequestAsset->asset->getSerialNumber() }}
                                                                    </td>
                                                                    <td>{{ $goodRequestAsset->asset->getItemName() }}</td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <div class="mt-2 rounded border shadow-sm card">
                                <div class="card-header fw-bold">
                                    Good Request Process
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-lg-6">
                                            @foreach ($goodRequest->logs as $log)
                                                <div class="flex-row gap-2 py-2 mb-2 d-flex border-bottom">
                                                    <div width="40" height="40"
                                                        class="mr-3 rounded-circle user-icon">
                                                        <i class="bi-person"></i>
                                                    </div>
                                                    <div class="w-100">
                                                        <div
                                                            class="d-flex flex-column flex-md-row justify-content-between align-items-start">
                                                            <div class="mb-2 d-flex flex-column">
                                                                <span class="me-2">{{ $log->getCreatedBy() }}</span>
                                                                <span
                                                                    class="mt-1 badge bg-primary c-badge">{!! $log->createdBy->employee->latestTenure->getDesignationName() !!}</span>
                                                            </div>
                                                            <div class="mt-2 mt-md-0">
                                                                <small title="{{ $log->created_at }}">
                                                                    {{ $log->created_at->format('M d, Y h:i A') }}
                                                                </small>
                                                            </div>
                                                        </div>
                                                        <p class="mt-1 mb-0 text-justify comment-text">
                                                            {{ $log->log_remarks }}
                                                        </p>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="mb-2 row">
                                                <div class="col-lg-12">
                                                    <div class="d-flex align-items-start h-100">
                                                        <label for="log_remarks"
                                                            class="form-label required-label">Receiver Remarks</label>
                                                    </div>
                                                </div>
                                                <div class="col-lg-12">
                                                    <textarea type="text" rows="7" class="form-control @if ($errors->has('log_remarks')) is-invalid @endif" name="log_remarks"
                                                        id="log_remarks">{{ old('log_remarks') }}</textarea>
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
                                <div class="gap-2 border-0 card-footer justify-content-end d-flex">
                                    <button type="submit" name="btn" value="submit" class="btn btn-success btn-sm">
                                        Submit
                                    </button>
                                    <a href="{!! route('approve.good.requests.direct.assign.index') !!}" class="btn btn-danger btn-sm">Cancel</a>
                                </div>

                            </div>
                        </div>
                    </div>
                </form>
            </section>

        </div>
    </div>
@stop
