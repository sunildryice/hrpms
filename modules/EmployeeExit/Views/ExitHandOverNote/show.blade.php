@extends('layouts.container')

@section('title', 'Show Employee Exit Hand Over Note')
@section('page_css')
@endsection
@section('page_js')
    <script type="text/javascript">
        $('#navbarVerticalMenu').find('[href="#navbarEmployeeExit"]').addClass('active').attr('aria-expanded', 'true');
        $('#navbarVerticalMenu').find('#navbarEmployeeExit').addClass('show');
        $('#navbarVerticalMenu').find('#update-employees-exit-menu').addClass('active');
        const form = document.getElementById('exitHandOverNoteEditForm');

        var oTable = $('#handoverProjectTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('exit.handover.note.index', $exitHandOverNote->id) }}",
            bFilter: false,
            bPaginate: false,
            bInfo: false,
            columns: [{
                    data: 'project',
                    name: 'project'
                },
                {
                    data: 'action_needed',
                    name: 'action_needed'
                },
                {
                    data: 'partners',
                    name: 'partners'
                },
                {
                    data: 'budget',
                    name: 'budget'
                },
                {
                    data: 'critical_issues',
                    name: 'critical_issues'
                },
                {
                    data: 'project_status',
                    name: 'project_status'
                },
                @if ($authUser->can('update', $exitHandOverNote))
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                @endif
            ]
        });

        var oTable = $('#handoverActivityTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('activity.exit.handover.note.index', $exitHandOverNote->id) }}",
            bFilter: false,
            bPaginate: false,
            bInfo: false,
            columns: [{
                    data: 'activity',
                    name: 'activity'
                },
                {
                    data: 'organization',
                    name: 'organization'
                },
                {
                    data: 'phone',
                    name: 'phone'
                },
                {
                    data: 'email',
                    name: 'email'
                },
                {
                    data: 'comments',
                    name: 'comments'
                },
                @if ($authUser->can('update', $exitHandOverNote))
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                @endif
            ]
        });

        var oTable = $('#handoverDocumentTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('document.exit.handover.note.index', $exitHandOverNote->id) }}",
            bFilter: false,
            bPaginate: false,
            bInfo: false,
            columns: [{
                    data: 'attachment_name',
                    name: 'attachment_name'
                },
                {
                    data: 'attachment_type',
                    name: 'attachment_type'
                },
                {
                    data: 'attachment',
                    name: 'attachment'
                },
                @if ($authUser->can('update', $exitHandOverNote))
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                @endif
            ]
        });

        $('#handoverDocumentTable').on('click', '.delete-record', function(e) {
            e.preventDefault();
            $object = $(this);
            var $url = $object.attr('data-href');
            var successCallback = function(response) {
                toastr.success(response.message, 'Success', {
                    timeOut: 5000
                });
                oTable.ajax.reload();
            }
            ajaxDeleteSweetAlert($url, successCallback);
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
                            <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}"
                                    class="text-decoration-none text-dark">Home</a></li>
                            {{--                            <li class="breadcrumb-item"><a href="#" class="text-decoration-none">@yield('title')</a></li> --}}
                            <li class="breadcrumb-item" aria-current="page">@yield('title')</li>
                        </ol>
                    </nav>
                    <h4 class="m-0 mt-1 lh1 fs-6 text-uppercase fw-bold text-primary">@yield('title')</h4>
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
                    <div class="pt-3 pb-3 bg-white rounded border shadow-sm vertical-navigation sm-menu-vr">
                        <ul class="m-0 list-unstyled">
                            <li class="nav-item"><a
                                    href="@if ($authUser->can('update', $exitHandOverNote)) {{ route('exit.employee.handover.note.edit') }} @else {{ route('exit.employee.handover.note.show') }} @endif"
                                    class="nav-link text-decoration-none active"><i class="nav-icon bi-info-circle"></i>
                                    Handover Note</a></li>
                            <!-- <li class="nav-item"><a href="#" class="nav-link text-decoration-none"><i
                                                class="nav-icon bi-pin-map"></i> Asset Handover</a></li> -->
                            <li class="nav-item"><a
                                    href="@if ($authUser->can('update', $exitInterview)) {{ route('exit.employee.handover.asset.edit') }} @else {{ route('exit.employee.handover.asset.show') }} @endif"
                                    class="nav-link text-decoration-none"><i class="nav-icon bi-people"></i>Asset
                                    Handover</a></li>
                            <li class="nav-item"><a
                                    href="@if ($authUser->can('update', $exitInterview)) {{ route('exit.employee.interview.edit') }} @else {{ route('exit.employee.interview.show') }} @endif"
                                    class="nav-link text-decoration-none"><i class="nav-icon bi-people"></i> Exit
                                    interview</a></li>
                            <li class="nav-item"><a
                                    href="{{ route('exit.payable.show', $exitHandOverNote->employeeExitPayable->id) }}"
                                    class="nav-link text-decoration-none"><i
                                        class="nav-icon bi bi-currency-exchange"></i>Payable</a></li>

                        </ul>
                    </div>
                </div>
                <div class="col-lg-9">
                    <div class="card">
                        <div class="card-body">
                            <div class="mb-3 row">
                                <div class="col-lg-3">
                                    <label for=""> Brief description of duties</label>
                                </div>
                                <div class="col-lg-9">
                                    <textarea rows="3" class="form-control" readonly>{{ $exitHandOverNote->duty_description }}</textarea>
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <div class="col-lg-3">
                                    <label for=""> Reporting procedures</label>
                                </div>

                                <div class="col-lg-9">
                                    <textarea rows="3" class="form-control" readonly>{{ $exitHandOverNote->reporting_procedures }}</textarea>
                                </div>
                            </div>

                            <div class="mb-3 row">
                                <div class="col-lg-3">
                                    <label for=""> Meeting Description </label>
                                </div>

                                <div class="col-lg-9">
                                    <textarea rows="3" class="form-control" readonly>{{ $exitHandOverNote->meeting_description }}</textarea>
                                </div>
                            </div>

                            <div class="mb-3 row">
                                <div class="col-lg-3">
                                    <label for=""> Contact After Exit </label>
                                </div>

                                <div class="col-lg-3">
                                    <input type="number" readonly value="{{ $exitHandOverNote->contact_after_exit }}"
                                        class="form-control">
                                </div>
                                <div class="col-lg-3">
                                    <label for=""> Approver </label>
                                </div>

                                <div class="col-lg-3">
                                    <input type="text" readonly value="{{ $exitHandOverNote->getApproverName() }}"
                                        class="form-control">
                                </div>
                            </div>

                            <div class="mb-3 table-responsive">
                                <span class="fw-bold">@lang('label.project-status')</span>
                                <table class="table table-borderedless" id="handoverProjectTable">
                                    <thead class="bg-light">
                                        <tr>

                                            <!-- <th style="width:45px;"></th> -->
                                            <th class="">Name of project</th>
                                            <th>Action needed</th>
                                            <th>Partners</th>
                                            <th>Budget</th>
                                            <th>Critical issues</th>
                                            <th>Status</th>
                                            @if ($authUser->can('update', $exitHandOverNote))
                                                <th style="width: 130px;">Action</th>
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>


                            <div class="mb-3 table-responsive">
                                <table class="table table-borderedless" id="handoverActivityTable">
                                    <thead class="bg-light">
                                        <tr>

                                            <!-- <th style="width:45px;"></th> -->
                                            <th class="">Name</th>
                                            <th>Organization</th>
                                            <th>Phone</th>
                                            <th>Email</th>
                                            <th>Comments</th>
                                            @if ($authUser->can('update', $exitHandOverNote))
                                                <th style="width: 130px;">Action</th>
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>

                            <!--   <div class="mb-3 row">
                                        <div class="py-2 d-flex">
                                            <div class="d-flex justify-content-end flex-grow-1">
                                                    <button data-toggle="modal"
                                                                    class="btn btn-primary btn-sm open-modal-form"
                                                                    href="{!! route('document.exit.handover.note.create', [$exitHandOverNote->id]) !!}"
                                                            ><i class="bi-plus"></i> Add New Documents
                                                    </button>

                                            </div>
                                        </div>
                                    </div> -->
                            <div class="mb-3 table-responsive">
                                <table class="table table-borderedless" id="handoverDocumentTable">
                                    <thead class="bg-light">
                                        <tr>

                                            <!-- <th style="width:45px;"></th> -->
                                            <th>Document Name</th>
                                            <th>Document Type</th>
                                            <th>Document</th>
                                            @if ($authUser->can('update', $exitHandOverNote))
                                                <th style="width: 130px;">Action</th>
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
                            Exit Handover Note Process
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-12">
                                    @foreach ($exitHandOverNote->logs as $log)
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
    {{-- @dump($exitHandOverNote) --}}
@stop
