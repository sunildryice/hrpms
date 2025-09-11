@php $authUser = auth()->user();@endphp
@extends('layouts.container')

@section('title', 'Good Request Detail: '. $goodRequest->getGoodRequestNumber())

@section('page_js')
    <script type="text/javascript">
        $(document).ready(function () {
            $('#navbarVerticalMenu').find('#good-requests-menu').addClass('active');

            var oTable = $('#goodRequestItemTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('good.requests.items.index', $goodRequest->id) }}",
                bFilter: false,
                bPaginate: false,
                bInfo: false,
                columns: [
                    {data: 'item_name', name: 'item_name'},
                    {data: 'unit', name: 'unit'},
                    {data: 'quantity', name: 'quantity'},
                    {data: 'assigned_quantity', name: 'assigned_quantity'},
                    {data: 'specification', name: 'specification'},
                ]
            });

            const form = document.getElementById('receiverNoteUpdateForm');
            const fv = FormValidation.formValidation(form, {
                fields: {
                    receiver_note: {
                        validators: {
                            notEmpty: {
                                message: 'Receiver note cannot be empty.'
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
                                    <a href="{{ route('good.requests.index') }}" class="text-decoration-none">Good
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
                                Good Request Details
                            </div>
                            @include("GoodRequest::Partials.detail")
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
                                                        @foreach ($goodRequestAssets as $key=>$goodRequestAsset)
                                                            <tr>
                                                                <td>{{ ++$key }}</td>
                                                                <td>{{ $goodRequestAsset->asset->getAssetNumber() }}</td>
                                                                <td>{{ $goodRequestAsset->asset->getSerialNumber() }}</td>
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

                        @if ($authUser->can('addReceiverNote', $goodRequest))
                            <div>
                                <form action="{{route('good.requests.receiver.note.update', $goodRequest->id)}}" method="POST" id="receiverNoteUpdateForm">
                                    @csrf
                                    @method('put')
                                    <div class="card">
                                        <div class="card-header fw-bold">
                                            Receiver Note
                                        </div>
                                        <div class="card-body">
                                            <div class="col-lg-12">
                                                <div class="row">
                                                    <div class="col-lg-3">
                                                        <label class="m-0" for="receiver_note">Receiver Note</label>
                                                    </div>
                                                    <div class="col-lg-9">
                                                        <input class="form-control" type="text" name="receiver_note" id="receiver_note" placeholder="Enter receiver note">
                                                        @if ($errors->has('receiver_note'))
                                                            <span class="text-danger">{{$errors->first('receiver_note')}}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-footer">
                                            <div style="display: flex; flex-direction: row; justify-content: flex-end;">
                                                <button class="btn btn-sm btn-primary" type="submit">
                                                    Save
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        @endif


                        <div class="card">
                            <div class="card-header fw-bold">
                                Good Request Process
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-12">
                                        @foreach($goodRequest->logs as $log)
                                            <div class="d-flex py-2 flex-row gap-2 mb-2 border-bottom ">
                                                <div width="40" height="40" class="rounded-circle mr-3 user-icon">
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
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </section>

        </div>
    </div>
@stop
