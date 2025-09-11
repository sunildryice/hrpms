@extends('layouts.container')

@section('title', 'Edit Asset HandOver')
@section('page_css')
@endsection
@section('page_js')
    <script type="text/javascript">
    $(document).ready(function () {
        $('#navbarVerticalMenu').find('#update-employees-exit-menu').addClass('active');
        $('#assetTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('profile.assets.index') }}",
                columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                    {data: 'asset_number', name: 'asset_number'},
                    {data: 'item_name', name: 'item_name'},
                    {data: 'remarks', name: 'remarks'},
                ]
            });
    });
    </script>
@endsection

@section('page-content')
    <div class="container-fluid">

        <div class="page-header pb-3 mb-3 border-bottom">
            <div class="d-flex flex-column flex-lg-row align-items-start align-items-lg-center gap-2">
            <div class="brd-crms flex-grow-1">
                <nav aria-label="breadcrumb">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}" class="text-decoration-none text-dark">Home</a></li>
{{--                            <li class="breadcrumb-item"><a href="#" class="text-decoration-none">@yield('title')</a></li>--}}
                            <li class="breadcrumb-item" aria-current="page">@yield('title')</li>
                        </ol>
                    </nav>
                    <h4 class="m-0 lh1 mt-1 fs-6 text-uppercase fw-bold text-primary">@yield('title')</h4>
                </div>
               {{-- <div class="ad-info justify-content-end">
                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#exampleModal"><i
                            class="bi-wrench-adjustable me-1"></i> New Employee Exit</button>
                </div> --}}
            </div>
        </div>
        <section class="registration">
            <div class="row">
                <div class="col-lg-3">
                    <div class="rounded border shadow-sm vertical-navigation sm-menu-vr pt-3 pb-3 bg-white">
                        <ul class="m-0 list-unstyled">
                            <li class="nav-item"><a href= "@if($authUser->can('update', $exitHandOverNote)){{route('exit.employee.handover.note.edit')}}  @else {{route('exit.employee.handover.note.show')}} @endif" class="nav-link  text-decoration-none"><i
                                        class="nav-icon bi-info-circle"></i> Handover Note</a></li>


                            <li class="nav-item"><a href="#" class="nav-link active text-decoration-none"><i
                                            class="nav-icon bi-people"></i> Asset Handover</a></li>
                            <li class="nav-item"><a
                                                href= "@if ($authUser->can('update', $exitInterview)) {{ route('exit.employee.interview.edit') }} @else
                                                {{ route('exit.employee.interview.show') }} @endif"
                                                class="nav-link text-decoration-none"><i class="nav-icon bi-people"></i> Exit
                                                interview</a>
                            <li class="nav-item"><a href="{{route('exit.payable.show', $exitHandOverNote->employeeExitPayable->id)}}" class="nav-link  text-decoration-none"><i
                                class="nav-icon bi bi-currency-exchange"></i>Payable</a></li>

                        </ul>
                    </div>
                </div>
                <div class="col-lg-9">
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table" id="assetTable">
                                    <thead>
                                    <tr>
                                        <th>{{ __('label.sn') }}</th>
                                        <th>{{ __('label.asset-number') }}</th>
                                        <th>{{ __('label.item-name') }}</th>
                                        <th>{{ __('label.remarks') }}</th>
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
                            Edit Asset HandOver Process
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-12">
                                    @foreach($exitAssetHandover->logs as $log)
                                        <div class="d-flex py-2 flex-row gap-2 mb-2 border-bottom ">
                                            <div width="40" height="40" class="rounded-circle mr-3 user-icon">
                                                <i class="bi-person"></i>
                                            </div>
                                            <div class="w-100">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div class="d-flex flex-row align-items-center">
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
@stop
