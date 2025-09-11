@php $authUser = auth()->user();@endphp
@extends('layouts.container')

@section('title', 'Assign Direct Dispatch Good Request')

@section('page_js')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#navbarVerticalMenu').find('#approve-direct-dispatch-menu').addClass('active');

            const form = document.getElementById('approveForm');
            const fv = FormValidation.formValidation(form, {
                fields: {
                    status_id: {
                        validators: {
                            notEmpty: {
                                message: 'Status is required.'
                            }
                        }
                    },
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
                <form action="{{ route('good.requests.direct.dispatch.approve.store', $goodRequest->id) }}" method="POST"
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
                                                            <th scope="col">Available Qty.</th>
                                                            <th scope="col">Assigned Qty.</th>
                                                            <th scope="col">{{ __('label.specification') }}</th>
                                                            <th scope="col">Quantity Assigned</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        {{-- @if (count($goodRequest->goodRequestItems)) --}}
                                                        @foreach ($goodRequest->goodRequestItems as $goodRequestItem)
                                                            <tr>
                                                                <td>{{ $goodRequestItem->item_name }}</td>
                                                                <td>{{ $goodRequestItem->getUnit() }}</td>
                                                                <td>{{ $goodRequestItem->assignedInventoryItem?->getAvailableQuantity() }}</td>
                                                                <td>{{ $goodRequestItem->quantity }}</td>
                                                                <td>{{ $goodRequestItem->specification }}</td>
                                                                <td>
                                                                    <input class="form-control" type="number"
                                                                        value="{{ old('assigned_quantity.' . $goodRequestItem->id) ?? $goodRequestItem->quantity }}"
                                                                        name="{{ "assigned_quantity[{$goodRequestItem->id}]" }}"
                                                                        id="assigned_quantity">
                                                                    @if ($errors->has('assigned_quantity'))
                                                                        <div
                                                                            class="fv-plugins-message-container invalid-feedback">
                                                                            <div data-field="assigned_quantity">
                                                                                {!! $errors->first('assigned_quantity') !!}</div>
                                                                        </div>
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                        {{-- @else --}}
                                                        {{-- @php --}}
                                                        {{--     $goodRequestItem = $goodRequest->latestGoodRequestItem; --}}
                                                        {{-- @endphp --}}
                                                        {{-- <tr> --}}
                                                        {{--     <td>{{$goodRequestItem->item_name}}</td> --}}
                                                        {{--     <td>{{$goodRequestItem->getUnit()}}</td> --}}
                                                        {{--     <td>{{$goodRequestItem->quantity}}</td> --}}
                                                        {{--     <td>{{$goodRequestItem->specification}}</td> --}}
                                                        {{--     <td> --}}
                                                        {{--         <input class="form-control" type="number" value="{{old('assigned_quantity')}}" --}}
                                                        {{--                                                   name="assigned_quantity" id="assigned_quantity"> --}}
                                                        {{--         @if ($errors->has('assigned_quantity')) --}}
                                                        {{--             <div class="fv-plugins-message-container invalid-feedback"> --}}
                                                        {{--                 <div --}}
                                                        {{--                         data-field="assigned_quantity">{!! $errors->first('assigned_quantity') !!}</div> --}}
                                                        {{--             </div> --}}
                                                        {{--         @endif --}}
                                                        {{--     </td> --}}
                                                        {{-- </tr> --}}
                                                        {{-- @endif --}}
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @include('GoodRequest::Partials.employees')

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
                                                            class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                                                            <div
                                                                class="mb-2 d-flex flex-column flex-md-row align-items-start align-items-md-center gap-md-2 mb-md-0">
                                                                <span class="me-2">{{ $log->getCreatedBy() }}</span>
                                                                <span
                                                                    class="badge bg-primary c-badge">{!! $log->createdBy->employee->latestTenure->getDesignationName() !!}</span>
                                                            </div>
                                                            <small
                                                                title="{{ $log->created_at }}">{{ $log->created_at->format('M d, Y h:i A') }}</small>
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
                                                <div class="col-lg-3">
                                                    <div class="d-flex align-items-start h-100">
                                                        <label for="status_id"
                                                            class="form-label required-label">Status</label>
                                                    </div>
                                                </div>
                                                <div class="col-lg-9">
                                                    <select name="status_id" id="status_id" class="select2 form-control"
                                                        data-width="100%">
                                                        <option value="">Select a Status</option>
                                                        <option value="8"
                                                            @if (old('status_id') == config('constant.REJECTED_STATUS')) selected @endif>
                                                            Reject
                                                        </option>
                                                        <option value="6"
                                                            @if (old('status_id') == config('constant.APPROVED_STATUS')) selected @endif>
                                                            Assign
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

                                            <div class="mb-2 row">
                                                <div class="col-lg-3">
                                                    <div class="d-flex align-items-start h-100">
                                                        <label for="log_remarks"
                                                            class="form-label required-label">Remarks</label>
                                                    </div>
                                                </div>
                                                <div class="col-lg-9">
                                                    <textarea type="text" class="form-control @if ($errors->has('log_remarks')) is-invalid @endif" name="log_remarks"
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
                                    <a href="{!! route('good.requests.direct.dispatch.approve.index') !!}" class="btn btn-danger btn-sm">Cancel</a>
                                </div>

                            </div>
                        </div>
                    </div>
                </form>
            </section>

        </div>
    </div>
@stop
