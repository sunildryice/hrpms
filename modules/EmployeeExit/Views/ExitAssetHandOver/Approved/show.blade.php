@extends('layouts.container')

@section('title', 'Show Exit Asset Handover')
@section('page_css')
@endsection
@section('page_js')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#navbarVerticalMenu').find('#approved-exit-asset').addClass('active');
            $('#goodRequestAssets').DataTable({});

        });
    </script>
@endsection

@section('page-content')
    <div class="container-fluid">
        <div class="pb-3 mb-3 page-header border-bottom">
            <div class="gap-2 d-flex flex-column flex-lg-row align-items-start align-items-lg-center">
                <div class="brd-crms flex-grow-1">
                    <nav aria-label="breadcrumb">
                        <ol class="m-0 breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{ route('dashboard.index') }}" class="text-decoration-none text-dark">Home</a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="{{ route('approved.exit.handover.asset.index') }}"
                                    class="text-decoration-none">Approved Asset Handover</a>
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
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-responsive table-bordered" id="goodRequestAssets"
                                    style="width: 100%">
                                    <thead>
                                        <tr>
                                            <th style="width: 1%">{{ __('label.sn') }}</th>
                                            <th style="width: 10%">{{ __('label.item-name') }}</th>
                                            <th style="width: 10%">{{ __('label.asset-number') }}</th>
                                            <th style="width: 10%">{{ __('label.handover-status') }}</th>
                                            <th>{{ __('label.remarks') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($assets as $index => $asset)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ $asset->asset->inventoryItem->getItemName() }}</td>
                                                <td>{{ $asset->getAssetNumber() }}</td>
                                                <td><span
                                                        class="{{ $asset->getStatusClass() }}">{{ $asset->getStatus() }}</span>
                                                </td>
                                                <td>{{ $asset->getRemarks() }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>

                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header fw-bold">
                            Asset Handover Process
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-12">
                                    @foreach ($exitAssetHandover->logs as $log)
                                        <div class="flex-row gap-2 py-2 mb-2 d-flex border-bottom">
                                            <div width="40" height="40" class="mr-3 rounded-circle user-icon">
                                                <i class="bi-person"></i>
                                            </div>
                                            <div class="w-100">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div class="flex-row d-flex align-items-center">
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
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </section>
    </div>
@stop
